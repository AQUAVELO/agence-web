<?php
/**
 * Interface d'administration discrète pour le planning
 */

require '_settings.php';
session_start();

// 1. GESTION DE LA CONNEXION
$password_secret = "aquavelo2026"; // Vous pourrez le changer ici
$authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;

if (isset($_POST['login_pass']) && $_POST['login_pass'] === $password_secret) {
    $_SESSION['admin_auth'] = true;
    $authenticated = true;
}

if (!$authenticated): ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2 style="margin-bottom: 20px;">Accès Privé</h2>
          <form method="POST">
            <input type="password" name="login_pass" placeholder="Mot de passe" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; font-weight: bold;">CONNEXION</button>
          </form>
        </div>
      </div>
    </section>
<?php return; endif;

// 2. LOGIQUE D'ADMINISTRATION (ANNULER / BLOQUER)
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $stmt = $database->prepare("DELETE FROM am_free WHERE id = ?");
        $stmt->execute([intval($_GET['id'])]);
    }
    if ($_GET['action'] === 'block' && isset($_GET['date']) && isset($_GET['hour'])) {
        $ref = 'BLOCK' . date('his');
        $name = "BLOCAGE MANUEL (RDV: " . $_GET['date'] . " à " . $_GET['hour'] . ")";
        $stmt = $database->prepare("INSERT INTO am_free (reference, center_id, name, email, phone, free) VALUES (?, 305, ?, 'admin@aquavelo.com', '0000', 3)");
        $stmt->execute([$ref, $name]);
    }
    header("Location: index.php?p=admin_planning");
    exit;
}

// 3. RÉCUPÉRATION DES DONNÉES
$bookings_query = $database->prepare("SELECT id, name FROM am_free WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%'");
$bookings_query->execute();
$raw_bookings = $bookings_query->fetchAll(PDO::FETCH_ASSOC);

$existing_bookings = [];
foreach ($raw_bookings as $b) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $b['name'], $matches);
    if ($matches) {
        $key = $matches[1] . '|' . $matches[2];
        $client_name = trim(explode('(RDV:', $b['name'])[0]);
        $existing_bookings[$key] = ['id' => $b['id'], 'name' => $client_name];
    }
}

// Configuration créneaux
$creneaux_semaine = ['09:45', '11:00', '12:15', '13:30', '14:45', '16:00', '17:15', '18:30'];
$creneaux_samedi  = ['09:45', '11:00', '12:15', '13:30'];

$calendar = [];
$today = new DateTime();
for ($i = 0; $i < 14; $i++) {
    $date = clone $today;
    $date->modify("+$i day");
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
    <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
        <h2 style="margin: 0; color: #00a8cc;"><i class="fa fa-cog"></i> Gestion Directe du Planning</h2>
        <a href="index.php" class="btn btn-default">Quitter l'admin</a>
      </div>

      <div style="display: flex; overflow-x: auto; gap: 15px; padding-bottom: 20px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 180px; border: 1px solid #eee; border-radius: 10px; padding: 10px; background: #f9f9f9;">
            <div style="text-align: center; font-weight: bold; color: #333; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                <?= $day['day_name'] ?><br><small><?= $day['full_date'] ?></small>
            </div>
            <div>
              <?php foreach ($day['slots'] as $slot) : 
                $key = $day['full_date'] . '|' . $slot;
                $is_taken = isset($existing_bookings[$key]);
              ?>
                <div style="margin-bottom: 8px; padding: 8px; border-radius: 5px; background: <?= $is_taken ? '#fff3cd' : 'white' ?>; border: 1px solid <?= $is_taken ? '#ffeeba' : '#eee' ?>; font-size: 0.85rem;">
                  <div style="font-weight: bold; margin-bottom: 5px;"><?= $slot ?></div>
                  <?php if ($is_taken) : ?>
                    <div style="color: #856404; margin-bottom: 8px;">👤 <?= $existing_bookings[$key]['name'] ?></div>
                    <a href="index.php?p=admin_planning&action=delete&id=<?= $existing_bookings[$key]['id'] ?>" 
                       onclick="return confirm('Supprimer ce RDV ?')" 
                       style="display: block; text-align: center; background: #ff4d4d; color: white; padding: 4px; border-radius: 3px; text-decoration: none; font-size: 0.75rem;">
                       DÉBLOQUER
                    </a>
                  <?php else : ?>
                    <a href="index.php?p=admin_planning&action=block&date=<?= $day['full_date'] ?>&hour=<?= $slot ?>" 
                       style="display: block; text-align: center; color: #00a8cc; padding: 4px; border: 1px dashed #00a8cc; border-radius: 3px; text-decoration: none; font-size: 0.75rem;">
                       BLOQUER
                    </a>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
