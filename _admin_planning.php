<?php
/**
 * Admin Planning - Synchronisation Totale
 */

require '_settings.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. CONNEXION
$password_secret = "aquavelo2026";
$authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;
if (isset($_POST['login_pass']) && $_POST['login_pass'] === $password_secret) {
    $_SESSION['admin_auth'] = true;
    $authenticated = true;
}

if (!$authenticated): ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2>Accès Admin Planning</h2>
          <form method="POST"><input type="password" name="login_pass" placeholder="Mot de passe" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;"><button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">CONNEXION</button></form>
        </div>
      </div>
    </section>
<?php return; endif;

// 2. INJECTION DES BLOCAGES MANUELS (Une seule fois ou si absents)
$manual_to_inject = [
    '19/01/2026' => ['09:45', '11:00', '12:15', '14:45', '16:00', '17:15', '18:30'],
    '20/01/2026' => ['11:00', '13:30', '14:45'],
    '21/01/2026' => ['17:15'],
    '22/01/2026' => ['12:15'],
    '23/01/2026' => ['09:45', '11:00', '14:45', '18:30'],
    '24/01/2026' => ['09:45', '11:00', '12:15'],
    '26/01/2026' => ['17:15'],
    '27/01/2026' => ['13:30'],
    '30/01/2026' => ['17:15'],
    '31/01/2026' => ['09:45', '11:00', '12:15']
];

foreach ($manual_to_inject as $date => $hours) {
    foreach ($hours as $h) {
        $search = "%(RDV: $date à $h%";
        $check = $database->prepare("SELECT id FROM am_free WHERE name LIKE ?");
        $check->execute([$search]);
        if (!$check->fetch()) {
            $name = "RÉSERVÉ (RDV: $date à $h (AQUAVELO))";
            $ins = $database->prepare("INSERT INTO am_free (reference, center_id, name, email, phone, free) VALUES (?, 305, ?, 'deja@reserve.com', '0000', 3)");
            $ins->execute(['MANUAL'.rand(100,999), $name]);
        }
    }
}

// 3. ACTIONS
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $database->prepare("DELETE FROM am_free WHERE id = ?")->execute([intval($_GET['id'])]);
    echo "<script>window.location.href='index.php?p=admin_planning';</script> text-align: center;";
    exit;
}

// 4. RÉCUPÉRATION DES DONNÉES
$all_free_query = $database->prepare("SELECT * FROM am_free WHERE center_id IN (305, 347, 349) ORDER BY id DESC");
$all_free_query->execute();
$all_free = $all_free_query->fetchAll(PDO::FETCH_ASSOC);

// Construction du planning
$bookings_visuel = [];
foreach ($all_free as $b) {
    if (strpos($b['name'], '(RDV:') !== false) {
        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $b['name'], $matches);
        if ($matches) {
            $key = $matches[1] . '|' . $matches[2];
            $bookings_visuel[$key] = $b;
        }
    }
}

$creneaux_semaine = ['09:45', '11:00', '12:15', '13:30', '14:45', '16:00', '17:15', '18:30'];
$creneaux_samedi  = ['09:45', '11:00', '12:15', '13:30'];
$calendar = [];
$today = new DateTime();
for ($i = 0; $i < 14; $i++) {
    $date = clone $today; $date->modify("+$i day");
    $day_num = $date->format('N');
    if ($day_num <= 6) {
        $calendar[] = [
            'full_date' => $date->format('d/m/Y'),
            'day_name'  => ($day_num == 6) ? 'Samedi' : ($day_num == 1 ? 'Lundi' : ($day_num == 2 ? 'Mardi' : ($day_num == 3 ? 'Mercredi' : ($day_num == 4 ? 'Jeudi' : 'Vendredi')))),
            'slots'     => ($day_num == 6) ? $creneaux_samedi : $creneaux_semaine
        ];
    }
}
?>

<section class="content-area bg1" style="padding: 40px 0;">
  <div class="container">
    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <h2 style="color: #00a8cc; margin-bottom: 20px; text-align: center;">🗓️ Planning d'Administration (Cannes / Mandelieu / Vallauris)</h2>
      
      <div style="display: flex; overflow-x: auto; gap: 10px; padding-bottom: 20px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 180px; border: 1px solid #eee; border-radius: 10px; padding: 10px; background: #fafafa;">
            <div style="text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; margin-bottom: 10px; padding-bottom: 5px;">
                <?= $day['day_name'] ?><br><small><?= $day['full_date'] ?></small>
            </div>
            <?php foreach ($day['slots'] as $slot) : 
                $key = $day['full_date'] . '|' . $slot;
                $res = $bookings_visuel[$key] ?? null;
                $is_manual = ($res && $res['email'] == 'deja@reserve.com');
            ?>
                <div style="padding: 8px; border-radius: 6px; margin-bottom: 6px; font-size: 0.75rem; background: <?= $res ? ($is_manual ? '#eceff1' : '#fff9c4') : '#fff' ?>; border: 1px solid <?= $res ? ($is_manual ? '#b0bec5' : '#fbc02d') : '#eee' ?>;">
                  <b><?= $slot ?></b>
                  <?php if ($res) : ?>
                    <div style="margin-top: 5px; font-weight: bold;"><?= $is_manual ? 'BLOCAGE MANUEL' : trim(explode('(RDV:', $res['name'])[0]) ?></div>
                    <div style="color: #666;"><?= $is_manual ? '' : $res['phone'] ?></div>
                    <div style="margin-top: 5px;">
                        <a href="index.php?p=admin_planning&action=delete&id=<?= $res['id'] ?>" onclick="return confirm('Annuler ce RDV ou débloquer ce créneau ?')" style="color: #d32f2f;">❌ Débloquer</a>
                    </div>
                  <?php else : ?>
                    <div style="color: #bbb;">Libre</div>
                  <?php endif; ?>
                </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
