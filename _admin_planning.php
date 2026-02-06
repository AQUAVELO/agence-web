<?php
/**
 * Admin Planning - Synchronisation Totale avec Restriction 9h45/10h00
 */

require '_settings.php';

// 1. AUTHENTIFICATION ET CONFIGURATION
if (isset($_GET['logout'])) {
    $_SESSION['admin_auth'] = false;
    unset($_SESSION['admin_auth']);
    unset($_SESSION['csrf_token']);
    header("Location: index.php?p=admin_planning");
    exit;
}

$password_secret = "aquavelo2026";
$authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;

// Liste des centres partageant le même planning
$shared_centers = [305, 347, 349];
$centers_names = [305 => 'Cannes', 347 => 'Mandelieu', 349 => 'Vallauris'];

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

// 2. ACTIONS (Suppression et Verrouillage)
if ($authenticated && isset($_GET['action'])) {
    $token_ok = (isset($_GET['token']) && $_GET['token'] === $_SESSION['csrf_token']);
    
    if ($token_ok) {
        if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            
            // --- SYNCHRO GOOGLE : Suppression de l'événement si présent ---
            $check = $database->prepare("SELECT google_event_id FROM am_free WHERE id = ?");
            $check->execute([$id]);
            $booking_to_del = $check->fetch();
            
            if ($booking_to_del && !empty($booking_to_del['google_event_id'])) {
                try {
                    if (file_exists('vendor/autoload.php')) {
                        require_once 'vendor/autoload.php';
                        $client = new Google\Client();
                        $client->setAuthConfig('google_key.json');
                        $client->addScope(Google\Service\Calendar::CALENDAR);
                        $service = new Google\Service\Calendar($client);
                        $service->events->delete('aqua.cannes@gmail.com', $booking_to_del['google_event_id']);
                    }
                } catch (Exception $e) {
                    // On ignore l'erreur si l'événement a déjà été supprimé manuellement sur Google
                }
            }
            // ---------------------------------------------------------------

            $database->prepare("DELETE FROM am_free WHERE id = ?")->execute([$id]);
        } 
        elseif ($_GET['action'] === 'lock' && isset($_GET['date']) && isset($_GET['time'])) {
            try {
                // Création d'un blocage manuel (Sans emoji pour éviter les erreurs d'encodage SQL)
                $date_str = $_GET['dayname'] . " " . $_GET['date'] . " à " . $_GET['time'] . " (" . $_GET['activity'] . ")";
                $lock_name = "BLOQUE (ADMIN) (RDV: " . $date_str . ")";
                $ref = 'LOCK' . date('dmhis');
                
                $stmt = $database->prepare("INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$ref, 305, 3, $lock_name, 'admin@aquavelo.com', '0493930565', 'admin-lock']);
            } catch (Exception $e) {
                // En cas d'erreur, on l'affiche 2 secondes avant de rediriger
                die("Erreur lors du verrouillage : " . $e->getMessage());
            }
        }
    }
    // Redirection JavaScript plus robuste
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

// 4. RÉCUPÉRATION DES RÉSERVATIONS (Cannes, Mandelieu, Vallauris uniquement)
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
        <h2 style="color: #00a8cc; margin: 0;">🗓️ Admin Planning (Cannes / Mandelieu / Vallauris)</h2>
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
                $is_locked = ($res && (strpos($res['name'], 'VERROUILLÉ') !== false || strpos($res['name'], 'BLOQUE') !== false));
                $center_label = ($res && isset($centers_names[$res['center_id']])) ? $centers_names[$res['center_id']] : '';
            ?>
                <div style="padding: 10px; border-radius: 8px; margin-bottom: 8px; font-size: 0.8rem; background: <?= $res ? ($is_locked ? '#f5f5f5' : '#fff9c4') : '#fff' ?>; border: 1px solid <?= $res ? ($is_locked ? '#ddd' : '#fbc02d') : '#eee' ?>; min-height: 105px; display: flex; flex-direction: column; justify-content: space-between;">
                  <div>
                      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <b><?= $s['time'] ?></b>
                        <?php if ($center_label && !$is_locked): ?>
                            <span style="font-size: 0.6rem; background: #00a8cc; color: white; padding: 1px 4px; border-radius: 3px;"><?= $center_label ?></span>
                        <?php endif; ?>
                      </div>
                      <span style="font-size: 0.65rem; color: #999;"><?= $s['activity'] ?></span>
                      
                      <?php if ($res) : ?>
                        <div style="margin-top: 5px; font-weight: bold; color: <?= $is_locked ? '#999' : '#333' ?>; line-height: 1.1;">
                            <?= trim(explode('(RDV:', $res['name'])[0]) ?>
                        </div>
                        <?php if (!$is_locked): ?>
                            <div style="color: #666; font-size: 0.75rem;"><?= $res['phone'] ?></div>
                            <!-- Indicateurs de relances Cron -->
                            <div style="margin-top: 8px; display: flex; gap: 4px;" title="Statut des relances (24h, 3h, Après, J+2, J+7)">
                                <span style="font-size: 9px; padding: 1px 3px; border-radius: 3px; background: <?= $res['reminder_sent'] ? '#4CAF50' : '#eee' ?>; color: white;">24h</span>
                                <span style="font-size: 9px; padding: 1px 3px; border-radius: 3px; background: <?= $res['reminder_3h_sent'] ? '#4CAF50' : '#eee' ?>; color: white;">3h</span>
                                <span style="font-size: 9px; padding: 1px 3px; border-radius: 3px; background: <?= $res['after_session_sent'] ? '#4CAF50' : '#eee' ?>; color: white;">Post</span>
                                <span style="font-size: 9px; padding: 1px 3px; border-radius: 3px; background: <?= $res['followup_2d_sent'] ? '#4CAF50' : '#eee' ?>; color: white;">J+2</span>
                                <span style="font-size: 9px; padding: 1px 3px; border-radius: 3px; background: <?= $res['followup_7d_sent'] ? '#4CAF50' : '#eee' ?>; color: white;">J+7</span>
                            </div>
                        <?php endif; ?>
                      <?php else : ?>
                        <div style="color: #bbb; margin-top: 5px;">Disponible</div>
                      <?php endif; ?>
                  </div>
                  
                  <div style="margin-top: 8px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 5px;">
                    <?php if ($res) : ?>
                        <a href="index.php?p=admin_planning&action=delete&id=<?= $res['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" 
                           onclick="return confirm('<?= $is_locked ? 'Déverrouiller ce créneau ?' : 'Annuler ce RDV ?' ?>')" 
                           style="color: #d32f2f; font-size: 0.7rem; font-weight: bold; text-decoration: none;">
                           <?= $is_locked ? '🔓 DÉVERROUILLER' : '❌ ANNULER' ?>
                        </a>
                    <?php else : ?>
                        <a href="index.php?p=admin_planning&action=lock&date=<?= $day['full_date'] ?>&dayname=<?= $day['day_name'] ?>&time=<?= $s['time'] ?>&activity=<?= $s['activity'] ?>&token=<?= $_SESSION['csrf_token'] ?>" 
                           onclick="return confirm('Verrouiller ce créneau pour les clients ?')"
                           style="color: #00a8cc; font-size: 0.7rem; font-weight: bold; text-decoration: none;">🔒 VERROUILLER</a>
                    <?php endif; ?>
                  </div>
                </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ⭐ NOUVEAU : Statistiques du mois en cours ⭐ -->
<section class="content-area bg1" style="padding-bottom: 60px;">
  <div class="container">
    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <h3 style="color: #00a8cc; margin-top: 0; margin-bottom: 20px;"><i class="fa fa-bar-chart"></i> Statistiques du mois (Séances Gratuites)</h3>
      
      <?php
      $current_month = date('m/Y');
      $days_in_month = date('t');
      
      // On récupère TOUS les prospects du mois pour TOUS les centres actifs
      $target_centers = [305, 347, 349, 253, 345, 271, 343, 308, 338, 324, 341, 179, 339, 320, 312, 321, 315];
      $center_labels = [
          305 => 'Cannes', 
          347 => 'Mandelieu', 
          349 => 'Vallauris', 
          253 => 'Antibes', 
          345 => 'Aix', 
          271 => 'Toulouse',
          343 => 'Mérignac',
          308 => 'St Raphaël',
          338 => 'Puget',
          324 => 'Villebon',
          341 => 'Senlis',
          179 => 'Nice',
          339 => 'Hyères',
          320 => 'Dijon',
          312 => 'Valence',
          321 => 'Grasse',
          315 => 'St Étienne'
      ];
      
      $in_clause = implode(',', $target_centers);
      // Utilisation de la colonne 'date' et 'email' pour filtrer les doublons
      $stats_query = $database->prepare("SELECT center_id, email, date FROM am_free WHERE center_id IN ($in_clause) AND date LIKE ? AND name NOT LIKE '%BLOQUE%' AND name NOT LIKE '%VERROUILLÉ%'");
      $stats_query->execute([date('Y-m') . "%"]);
      $all_stats = $stats_query->fetchAll(PDO::FETCH_ASSOC);

      $stats_data = [];
      $seen_today = []; // Pour gérer l'unicité par jour/centre/email

      foreach ($all_stats as $s) {
          $day = (int)date('d', strtotime($s['date']));
          $cid = $s['center_id'];
          $email = strtolower(trim($s['email']));
          
          if (!isset($stats_data[$day])) {
              $stats_data[$day] = array_fill_keys($target_centers, 0);
          }
          
          // Clé d'unicité : même jour, même centre, même email
          $uniq_key = $day . '-' . $cid . '-' . $email;
          
          if (!isset($seen_today[$uniq_key])) {
              if (isset($stats_data[$day][$cid])) {
                  $stats_data[$day][$cid]++;
                  $seen_today[$uniq_key] = true;
              }
          }
      }
      ?>

      <div class="table-responsive">
        <table class="table table-bordered table-striped" style="font-size: 0.7rem;">
          <thead style="background: #00a8cc; color: white !important;">
            <tr>
              <th style="color: white !important;">Jour</th>
              <?php foreach ($center_labels as $id => $label) : ?>
                <th class="text-center" style="color: white !important;"><?= $label ?></th>
              <?php endforeach; ?>
              <th class="text-center" style="background: #008ba3; color: white !important;">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $total_month = array_fill_keys($target_centers, 0);
            $total_month['global'] = 0;
            
            for ($d = 1; $d <= $days_in_month; $d++) : 
                $c = $stats_data[$d] ?? array_fill_keys($target_centers, 0);
                $day_total = array_sum($c);
                
                foreach ($target_centers as $id) {
                    $total_month[$id] += $c[$id];
                }
                $total_month['global'] += $day_total;

                if ($day_total > 0 || $d <= (int)date('d')) :
            ?>
              <tr>
                <td><b><?= sprintf("%02d", $d) ?>/<?= $current_month ?></b></td>
                <?php foreach ($target_centers as $id) : ?>
                  <td class="text-center"><?= $c[$id] ?: '-' ?></td>
                <?php endforeach; ?>
                <td class="text-center" style="font-weight: bold; background: #f0fbfc;"><?= $day_total ?: '-' ?></td>
              </tr>
            <?php endif; endfor; ?>
          </tbody>
          <tfoot style="background: #eee; font-weight: bold;">
            <tr>
              <td>TOTAL MOIS</td>
              <?php foreach ($target_centers as $id) : ?>
                <td class="text-center"><?= $total_month[$id] ?></td>
              <?php endforeach; ?>
              <td class="text-center" style="background: #00a8cc; color: white !important;"><?= $total_month['global'] ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- ⭐ NOUVEAU : Journal détaillé des Relances Cron ⭐ -->
<section class="content-area bg1" style="padding-bottom: 100px;">
  <div class="container">
    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <h3 style="color: #00a8cc; margin-top: 0; margin-bottom: 20px;"><i class="fa fa-envelope-o"></i> Suivi des Relances (Dernières réservations)</h3>
      
      <?php
      // On récupère les 50 derniers RDV pour voir le statut des emails
      $journal_query = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' ORDER BY id DESC LIMIT 50");
      $journal_query->execute();
      $last_rdvs = $journal_query->fetchAll(PDO::FETCH_ASSOC);
      ?>

      <div class="table-responsive">
        <table class="table table-hover" style="font-size: 0.85rem;">
          <thead>
            <tr style="background: #f8f9fa;">
              <th>Client</th>
              <th>Centre</th>
              <th>RDV</th>
              <th class="text-center">24h</th>
              <th class="text-center">3h/SMS</th>
              <th class="text-center">Post</th>
              <th class="text-center">J+2</th>
              <th class="text-center">J+7</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($last_rdvs as $rdv) : 
                $center_name = $center_labels[$rdv['center_id']] ?? 'Centre #'.$rdv['center_id'];
                $client_info = trim(explode('(RDV:', $rdv['name'])[0]);
                preg_match('/\d{2}\/\d{2}\/\d{4} à \d{2}:\d{2}/', $rdv['name'], $date_match);
                $rdv_date = $date_match[0] ?? '-';
            ?>
              <tr>
                <td><b><?= $client_info ?></b><br><small class="text-muted"><?= $rdv['email'] ?></small></td>
                <td><?= $center_name ?></td>
                <td><?= $rdv_date ?></td>
                <td class="text-center"><?= $rdv['reminder_sent'] ? '✅' : '⏳' ?></td>
                <td class="text-center"><?= $rdv['reminder_3h_sent'] ? '✅' : '⏳' ?></td>
                <td class="text-center"><?= $rdv['after_session_sent'] ? '✅' : '⏳' ?></td>
                <td class="text-center"><?= $rdv['followup_2d_sent'] ? '✅' : '⏳' ?></td>
                <td class="text-center"><?= $rdv['followup_7d_sent'] ? '✅' : '⏳' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
