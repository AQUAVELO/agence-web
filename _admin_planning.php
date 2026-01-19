<?php
/**
 * Admin Planning - Synchronisation Totale avec Restriction 9h45/10h00
 */

require '_settings.php';

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
    } else {
        sleep(1);
        $login_error = "Mot de passe incorrect";
    }
}

// 2. ACTIONS
if ($authenticated && isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        if (isset($_GET['token']) && $_GET['token'] === $_SESSION['csrf_token']) {
            $database->prepare("DELETE FROM am_free WHERE id = ?")->execute([intval($_GET['id'])]);
        }
    }
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

// 3. CONFIGURATION DES PLANNINGS (Synchronisé avec le calendrier client)
$old_creneaux_semaine = ['09:45', '11:00', '12:15', '13:30', '14:45', '16:00', '17:15', '18:30'];
$old_creneaux_samedi  = ['09:45', '11:00', '12:15', '13:30'];
$old_special_activities = [
    'Lundi'    => ['13:30' => 'AQUAGYM'],
    'Mardi'    => ['13:30' => 'AQUABOXING', '16:00' => 'AQUAGYM'],
    'Mercredi' => ['14:45' => 'AQUAGYM'],
    'Jeudi'    => ['14:45' => 'AQUAGYM'],
    'Vendredi' => ['17:15' => 'AQUAGYM'],
    'Samedi'   => ['13:30' => 'AQUAGYM'],
];

$new_planning = [
    'Lundi' => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAGYM','14:45' => 'AQUAVELO','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
    'Mardi' => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUABOXING','14:45' => 'AQUAVELO','16:00' => 'AQUAGYM','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
    'Mercredi' => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAGYM','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
    'Jeudi' => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAGYM','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
    'Vendredi' => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAVELO','16:00' => 'AQUAVELO','17:15' => 'AQUAGYM','18:30' => 'AQUAVELO'],
    'Samedi' => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:15' => 'AQUAGYM']
];

$calendar = [];
$today = new DateTime();
$switch_date = new DateTime('2026-02-01');

for ($i = 0; $i < 21; $i++) {
    $date = clone $today; $date->modify("+$i day");
    $day_name_en = $date->format('l');
    $day_num = (int)$date->format('N');
    
    // On saute les dimanches
    if ($day_num === 7) continue;

    $days_fr = ['Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'];
    $day_fr = $days_fr[$day_name_en];

    $current_slots = [];
    if ($date >= $switch_date) {
        if (isset($new_planning[$day_fr])) {
            foreach ($new_planning[$day_fr] as $h => $act) {
                $current_slots[] = ['time' => $h, 'activity' => $act];
            }
        }
    } else {
        if ($day_num <= 6) {
            $times = ($day_num == 6) ? $old_creneaux_samedi : $old_creneaux_semaine;
            foreach ($times as $t) {
                if ($t >= '09:45') $current_slots[] = ['time' => $t, 'activity' => ($old_special_activities[$day_fr][$t] ?? 'AQUAVELO')];
            }
        }
    }
    if (!empty($current_slots)) {
        $calendar[] = ['full_date' => $date->format('d/m/Y'), 'day_name' => $day_fr, 'slots' => $current_slots];
    }
}

// 4. RÉCUPÉRATION DES RÉSERVATIONS
$all_free_query = $database->prepare("SELECT * FROM am_free WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%'");
$all_free_query->execute();
$all_free = $all_free_query->fetchAll(PDO::FETCH_ASSOC);

$bookings_visuel = [];
foreach ($all_free as $res) {
    foreach ($calendar as $day) {
        foreach ($day['slots'] as $s) {
            if (strpos($res['name'], $day['full_date'] . " à " . $s['time']) !== false) {
                $bookings_visuel[$day['full_date'] . '|' . $s['time']] = $res;
            }
        }
    }
}
?>

<section class="content-area bg1" style="padding: 40px 0;">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: #00a8cc; margin: 0;">🗓️ Admin Planning</h2>
        <a href="index.php?p=admin_planning&logout=1" class="btn btn-default">Déconnexion</a>
    </div>

    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <div style="display: flex; overflow-x: auto; gap: 10px; padding-bottom: 20px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 190px; border: 1px solid #f0f0f0; border-radius: 10px; padding: 10px; background: #fafafa;">
            <div style="text-align: center; font-weight: bold; border-bottom: 2px solid #eee; margin-bottom: 12px; padding-bottom: 8px;">
                <?= $day['day_name'] ?><br><small><?= $day['full_date'] ?></small>
            </div>
            <?php foreach ($day['slots'] as $s) : 
                $key = $day['full_date'] . '|' . $s['time'];
                $res = $bookings_visuel[$key] ?? null;
            ?>
                <div style="padding: 10px; border-radius: 8px; margin-bottom: 8px; font-size: 0.8rem; background: <?= $res ? '#fff9c4' : '#fff' ?>; border: 1px solid <?= $res ? '#fbc02d' : '#eee' ?>; min-height: 95px; display: flex; flex-direction: column; justify-content: space-between;">
                  <div>
                      <b><?= $s['time'] ?></b> <span style="font-size: 0.65rem; color: #999;"><?= $s['activity'] ?></span>
                      <?php if ($res) : ?>
                        <div style="margin-top: 5px; font-weight: bold; color: #333; line-height: 1.1;"><?= trim(explode('(RDV:', $res['name'])[0]) ?></div>
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
