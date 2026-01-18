<?php
/**
 * Admin Planning - Version Stable Sécurisée
 */

require '_settings.php';
// session_start() est maintenant géré par _settings.php pour éviter les sorties avant session

// 1. CONNEXION ET DÉCONNEXION
if (isset($_GET['logout'])) {
    $_SESSION['admin_auth'] = false;
    unset($_SESSION['admin_auth']);
    unset($_SESSION['csrf_token']);
    header("Location: index.php?p=admin_planning");
    exit;
}

$password_secret = "aquavelo2026";
$authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;

if (isset($_POST['login_pass'])) {
    if ($_POST['login_pass'] === $password_secret) {
        $_SESSION['admin_auth'] = true;
        $authenticated = true;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        // On ne régénère pas l'ID ici car cela cause des pertes de session sur certains serveurs lors des redirections JS
    } else {
        sleep(1);
        $login_error = "Mot de passe incorrect";
    }
}

// 2. ACTIONS (Suppression individuelle uniquement avec vérification CSRF)
if ($authenticated && isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        if (isset($_GET['token']) && $_GET['token'] === $_SESSION['csrf_token']) {
            $database->prepare("DELETE FROM am_free WHERE id = ?")->execute([intval($_GET['id'])]);
        }
    }
    // Utilisation d'une redirection JS immédiate pour rester dans l'index.php
    echo "<script>window.location.replace('index.php?p=admin_planning');</script>";
    exit;
}

if (!$authenticated): ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2 style="color: #00a8cc;">Administration Planning</h2>
          <?php if (isset($login_error)): ?>
            <div style="color: #d32f2f; margin-bottom: 15px; font-weight: bold;"><?= $login_error ?></div>
          <?php endif; ?>
          <form method="POST"><input type="password" name="login_pass" placeholder="Mot de passe" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;"><button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">CONNEXION</button></form>
        </div>
      </div>
    </section>
<?php return; endif;

// 3. RÉCUPÉRATION DES DONNÉES
$all_free_query = $database->prepare("SELECT * FROM am_free WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%'");
$all_free_query->execute();
$all_free = $all_free_query->fetchAll(PDO::FETCH_ASSOC);

$special_activities = [
    'Lundi'    => ['13:30' => 'AQUAGYM'],
    'Mardi'    => ['13:30' => 'AQUABOXING', '16:00' => 'AQUAGYM'],
    'Mercredi' => ['14:45' => 'AQUAGYM'],
    'Jeudi'    => ['14:45' => 'AQUAGYM'],
    'Vendredi' => ['17:15' => 'AQUAGYM'],
    'Samedi'   => ['13:30' => 'AQUAGYM'],
];

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

$bookings_visuel = [];
foreach ($all_free as $res) {
    foreach ($calendar as $day) {
        foreach ($day['slots'] as $slot) {
            $search = $day['full_date'] . " à " . $slot;
            if (strpos($res['name'], $search) !== false) {
                $bookings_visuel[$day['full_date'] . '|' . $slot] = $res;
            }
        }
    }
}
?>

<section class="content-area bg1" style="padding: 40px 0;">
  <div class="container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: #00a8cc; margin: 0;">🗓️ Planning d'Administration</h2>
        <a href="index.php?p=admin_planning&logout=1" class="btn btn-default">Déconnexion</a>
    </div>

    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <div style="display: flex; overflow-x: auto; gap: 10px; padding-bottom: 20px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 190px; border: 1px solid #f0f0f0; border-radius: 10px; padding: 10px; background: #fafafa;">
            <div style="text-align: center; font-weight: bold; border-bottom: 2px solid #eee; margin-bottom: 12px; padding-bottom: 8px;">
                <?= $day['day_name'] ?><br><small><?= $day['full_date'] ?></small>
            </div>
            <?php foreach ($day['slots'] as $slot) : 
                $key = $day['full_date'] . '|' . $slot;
                $res = $bookings_visuel[$key] ?? null;
            ?>
                <div style="padding: 10px; border-radius: 8px; margin-bottom: 8px; font-size: 0.8rem; background: <?= $res ? '#fff9c4' : '#fff' ?>; border: 1px solid <?= $res ? '#fbc02d' : '#eee' ?>; min-height: 90px; display: flex; flex-direction: column; justify-content: space-between;">
                  <div>
                      <b><?= $slot ?></b>
                      <?php if ($res) : ?>
                        <div style="margin-top: 5px; font-weight: bold; color: #333;"><?= trim(explode('(RDV:', $res['name'])[0]) ?></div>
                        <div style="color: #666; font-size: 0.75rem;"><?= $res['phone'] ?></div>
                      <?php else : ?>
                        <div style="color: #bbb; margin-top: 5px;">Disponible</div>
                      <?php endif; ?>
                  </div>
                  
                  <?php if ($res) : ?>
                    <div style="margin-top: 8px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 5px;">
                        <a href="index.php?p=admin_planning&action=delete&id=<?= $res['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" 
                           onclick="return confirm('Annuler ce RDV ?')" 
                           style="color: #d32f2f; font-size: 0.7rem; font-weight: bold; text-decoration: none;">❌ ANNULER</a>
                    </div>
                  <?php endif; ?>
                </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
