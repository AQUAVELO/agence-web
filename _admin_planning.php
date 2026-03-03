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

// 2. ACTIONS (Suppression, Verrouillage, Déplacement)
if ($authenticated) {
    
    // ACTION: DÉPLACER UN RDV (POST)
    if (isset($_POST['action_move']) && $_POST['action_move'] === 'move_rdv') {
        $token_ok = (isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token']);
        
        if ($token_ok) {
            $booking_id = intval($_POST['booking_id']);
            $new_date = $_POST['new_date']; // Format YYYY-MM-DD
            $new_time = $_POST['new_time']; // Format HH:MM
            
            // 1. Récupérer les infos actuelles
            $stmt_get = $database->prepare("SELECT * FROM am_free WHERE id = ?");
            $stmt_get->execute([$booking_id]);
            $booking = $stmt_get->fetch();
            
            if ($booking) {
                // Formater la nouvelle date pour le champ 'name'
                $client_name = trim(explode('(RDV:', $booking['name'])[0]);
                $new_datetime_str = $new_date . ' ' . $new_time;
                $new_dt = DateTime::createFromFormat('Y-m-d H:i', $new_datetime_str);
                $new_date_fr = $new_dt->format('d/m/Y');
                
                $new_name_db = $client_name . " (RDV: " . $new_date_fr . " à " . $new_time . ")";
                
                // 2. Mise à jour Base de Données
                $update = $database->prepare("UPDATE am_free SET name = ?, date = ?, google_sync = 0, reminder_sent = 0, reminder_3h_sent = 0 WHERE id = ?");
                $update->execute([$new_name_db, $new_date . ' ' . $new_time . ':00', $booking_id]);
                
                // 3. Gestion Google Calendar
                try {
                    if (file_exists('vendor/autoload.php')) {
                        require_once 'vendor/autoload.php';
                        require_once 'load_env.php';
                        
                        $keyFile = __DIR__ . '/google_key.json';
                        $client = new Google\Client();
                        $client->setAuthConfig($keyFile);
                        $client->addScope(Google\Service\Calendar::CALENDAR);
                        $service = new Google\Service\Calendar($client);
                        
                        // Déterminer l'agenda
                        $stmt_c = $database->prepare("SELECT email, address, city FROM am_centers WHERE id = ?");
                        $stmt_c->execute([$booking['center_id']]);
                        $c_info = $stmt_c->fetch();
                        
                        if (in_array((int)$booking['center_id'], [305, 347, 349])) {
                            $targetCalendarId = 'aqua.cannes@gmail.com';
                        } else {
                            $targetCalendarId = !empty($c_info['email']) ? $c_info['email'] : 'aqua.cannes@gmail.com';
                        }
                        
                        // A. Supprimer l'ancien événement
                        if (!empty($booking['google_event_id'])) {
                            try {
                                $service->events->delete($targetCalendarId, $booking['google_event_id']);
                            } catch (Exception $e) { /* Ignore */ }
                        }
                        
                        // B. Créer le nouvel événement
                        $rdv_start = new DateTime($new_date . ' ' . $new_time, new DateTimeZone('Europe/Paris'));
                        $rdv_end = clone $rdv_start;
                        $rdv_end->modify('+45 minutes');
                        
                        $event = new Google\Service\Calendar\Event([
                            'summary' => '🏊 ' . $client_name . ' - ' . ($c_info['city'] ?? 'Cannes'),
                            'location' => $c_info['address'] ?? 'Cannes',
                            'description' => "Client: $client_name\nEmail: {$booking['email']}\nTél: {$booking['phone']}\nID: $booking_id\n(Déplacé par Admin)",
                            'start' => ['dateTime' => $rdv_start->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                            'end' => ['dateTime' => $rdv_end->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                        ]);
                        
                        $createdEvent = $service->events->insert($targetCalendarId, $event);
                        $new_google_id = $createdEvent->getId();
                        
                        // Mettre à jour l'ID Google
                        $database->prepare("UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?")->execute([$new_google_id, $booking_id]);
                    }
                } catch (Exception $e) {
                    error_log("Erreur Google Calendar Move: " . $e->getMessage());
                }
                
                // 4. Envoi Email Confirmation
                if (!empty($settings['mjusername'])) {
                    try {
                        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = $settings['mjhost'];
                        $mail->SMTPAuth = true;
                        $mail->Username = $settings['mjusername'];
                        $mail->Password = $settings['mjpassword'];
                        $mail->Port = 587;
                        $mail->CharSet = 'UTF-8';
                        
                        $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo ' . ($c_info['city'] ?? ''));
                        $mail->addAddress($booking['email']);
                        $mail->isHTML(true);
                        $mail->Subject = "Nouveau créneau confirmé : Votre séance à Aquavelo";
                        
                        $mail->Body = "Bonjour <b>$client_name</b>,<br><br>
                        Suite à notre échange, nous vous confirmons le déplacement de votre séance découverte.<br><br>
                        <b>Nouveau Rendez-vous :</b><br>
                        📅 <b>$new_date_fr</b><br>
                        🕐 <b>$new_time</b><br>
                        📍 <b>Aquavelo " . ($c_info['city'] ?? '') . "</b><br><br>
                        Nous avons hâte de vous accueillir !<br><br>
                        Cordialement,<br>L'équipe Aquavelo";
                        
                        $mail->send();
                    } catch (Exception $e) {
                        error_log("Erreur Email Move: " . $e->getMessage());
                    }
                }
                
                echo "<script>alert('RDV déplacé avec succès ! Client notifié par email.'); window.location.replace('index.php?p=admin_planning');</script>";
                exit;
            }
        }
    }

    // ACTION: SUPPRIMER ou VERROUILLER (GET)
    if (isset($_GET['action'])) {
        $token_ok = (isset($_GET['token']) && $_GET['token'] === $_SESSION['csrf_token']);
        
        if ($token_ok) {
            if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
                $id = intval($_GET['id']);
                
                // --- SYNCHRO GOOGLE : Suppression de l'événement si présent ---
                $check = $database->prepare("SELECT google_event_id, center_id FROM am_free WHERE id = ?");
                $check->execute([$id]);
                $booking_to_del = $check->fetch();
                
                if ($booking_to_del && !empty($booking_to_del['google_event_id'])) {
                    try {
                        if (file_exists('vendor/autoload.php')) {
                            require_once 'vendor/autoload.php';
                            require_once 'load_env.php';
                            
                            $keyFile = __DIR__ . '/google_key.json';
                            $client = new Google\Client();
                            $client->setAuthConfig($keyFile);
                            $client->addScope(Google\Service\Calendar::CALENDAR);
                            $service = new Google\Service\Calendar($client);
                            
                            // Déterminer l'agenda de destination
                            if (in_array((int)$booking_to_del['center_id'], [305, 347, 349])) {
                                $targetCalendarId = 'aqua.cannes@gmail.com';
                            } else {
                                $stmt_c = $database->prepare("SELECT email FROM am_centers WHERE id = ?");
                                $stmt_c->execute([$booking_to_del['center_id']]);
                                $c_info = $stmt_c->fetch();
                                $targetCalendarId = !empty($c_info['email']) ? $c_info['email'] : 'aqua.cannes@gmail.com';
                            }
                            
                            $service->events->delete($targetCalendarId, $booking_to_del['google_event_id']);
                        }
                    } catch (Exception $e) {
                        error_log("⚠️ Admin: Erreur suppression Google Calendar: " . $e->getMessage());
                    }
                }
                
                $database->prepare("DELETE FROM am_free WHERE id = ?")->execute([$id]);
            } 
            elseif ($_GET['action'] === 'lock' && isset($_GET['date']) && isset($_GET['time'])) {
                try {
                    $date_str = $_GET['dayname'] . " " . $_GET['date'] . " à " . $_GET['time'] . " (" . $_GET['activity'] . ")";
                    $lock_name = "BLOQUE (ADMIN) (RDV: " . $date_str . ")";
                    $ref = 'LOCK' . date('dmhis');
                    
                    $stmt = $database->prepare("INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$ref, 305, 3, $lock_name, 'admin@aquavelo.com', '0493930565', 'admin-lock']);
                } catch (Exception $e) {
                    die("Erreur lors du verrouillage : " . $e->getMessage());
                }
            }
        }
        echo "<script>window.location.replace('index.php?p=admin_planning');</script>";
        exit;
    }
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
        $calendar[] = ['full_date' => $date->format('d/m/Y'), 'raw_date' => $date->format('Y-m-d'), 'day_name' => $day_fr, 'slots' => $current_slots];
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
                $client_name_only = $res ? trim(explode('(RDV:', $res['name'])[0]) : '';
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
                            <?= $client_name_only ?>
                        </div>
                        <?php if (!$is_locked): ?>
                            <div style="color: #666; font-size: 0.75rem;"><?= $res['phone'] ?></div>
                            <!-- Indicateurs de relances Cron -->
                            <div style="margin-top: 8px; display: flex; gap: 4px;" title="Statut des relances">
                                <span style="font-size: 9px; padding: 1px 3px; border-radius: 3px; background: <?= $res['reminder_sent'] ? '#4CAF50' : '#eee' ?>; color: white;">24h</span>
                                <span style="font-size: 9px; padding: 1px 3px; border-radius: 3px; background: <?= $res['reminder_3h_sent'] ? '#4CAF50' : '#eee' ?>; color: white;">3h</span>
                            </div>
                        <?php endif; ?>
                      <?php else : ?>
                        <div style="color: #bbb; margin-top: 5px;">Disponible</div>
                      <?php endif; ?>
                  </div>
                  
                  <div style="margin-top: 8px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 5px; display: flex; justify-content: space-between;">
                    <?php if ($res) : ?>
                        <a href="index.php?p=admin_planning&action=delete&id=<?= $res['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" 
                           onclick="return confirm('<?= $is_locked ? 'Déverrouiller ce créneau ?' : 'Annuler ce RDV ?' ?>')" 
                           style="color: #d32f2f; font-size: 0.7rem; font-weight: bold; text-decoration: none;">
                           <?= $is_locked ? '🔓 DÉVERROUILLER' : '❌ ANNULER' ?>
                        </a>
                        <?php if (!$is_locked): ?>
                            <a href="#" onclick="openMoveModal(<?= $res['id'] ?>, '<?= htmlspecialchars($client_name_only, ENT_QUOTES) ?>')" 
                               style="color: #00a8cc; font-size: 0.7rem; font-weight: bold; text-decoration: none;">
                               🔄 DÉPLACER
                            </a>
                        <?php endif; ?>
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

<!-- Statistiques et Journal (Code existant inchangé) -->
<section class="content-area bg1" style="padding-bottom: 60px;">
  <div class="container">
    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <h3 style="color: #00a8cc; margin-top: 0; margin-bottom: 20px;"><i class="fa fa-bar-chart"></i> Statistiques du mois</h3>
      <?php
      // Stats code... (inchangé pour gagner de la place, mais à remettre si besoin complet)
      // Je remets le code de stats exact pour ne rien casser
      $current_month = date('m/Y');
      $days_in_month = date('t');
      $target_centers = [305, 347, 349, 253, 345, 271, 343, 308, 338, 324, 341, 179, 339, 320, 312, 321, 315];
      $center_labels = [305 => 'Cannes', 347 => 'Mandelieu', 349 => 'Vallauris', 253 => 'Antibes', 345 => 'Aix', 271 => 'Toulouse', 343 => 'Mérignac', 308 => 'St Raphaël', 338 => 'Puget', 324 => 'Villebon', 341 => 'Senlis', 179 => 'Nice', 339 => 'Hyères', 320 => 'Dijon', 312 => 'Valence', 321 => 'Grasse', 315 => 'St Étienne'];
      $in_clause = implode(',', $target_centers);
      $stats_query = $database->prepare("SELECT center_id, email, date FROM am_free WHERE center_id IN ($in_clause) AND date LIKE ? AND name NOT LIKE '%BLOQUE%' AND name NOT LIKE '%VERROUILLÉ%'");
      $stats_query->execute([date('Y-m') . "%"]);
      $all_stats = $stats_query->fetchAll(PDO::FETCH_ASSOC);
      $stats_data = []; $seen_today = [];
      foreach ($all_stats as $s) {
          $day = (int)date('d', strtotime($s['date'])); $cid = $s['center_id']; $email = strtolower(trim($s['email']));
          if (!isset($stats_data[$day])) $stats_data[$day] = array_fill_keys($target_centers, 0);
          $uniq_key = $day . '-' . $cid . '-' . $email;
          if (!isset($seen_today[$uniq_key]) && isset($stats_data[$day][$cid])) { $stats_data[$day][$cid]++; $seen_today[$uniq_key] = true; }
      }
      ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped" style="font-size: 0.7rem;">
          <thead style="background: #00a8cc; color: white !important;">
            <tr><th style="color: white !important;">Jour</th><?php foreach ($center_labels as $id => $label) : ?><th class="text-center" style="color: white !important;"><?= $label ?></th><?php endforeach; ?><th class="text-center" style="background: #008ba3; color: white !important;">Total</th></tr>
          </thead>
          <tbody>
            <?php 
            $total_month = array_fill_keys($target_centers, 0); $total_month['global'] = 0;
            for ($d = 1; $d <= $days_in_month; $d++) : 
                $c = $stats_data[$d] ?? array_fill_keys($target_centers, 0);
                $day_total = array_sum($c);
                foreach ($target_centers as $id) { $total_month[$id] += $c[$id]; }
                $total_month['global'] += $day_total;
                if ($day_total > 0 || $d <= (int)date('d')) :
            ?>
              <tr><td><b><?= sprintf("%02d", $d) ?>/<?= $current_month ?></b></td><?php foreach ($target_centers as $id) : ?><td class="text-center"><?= $c[$id] ?: '-' ?></td><?php endforeach; ?><td class="text-center" style="font-weight: bold; background: #f0fbfc;"><?= $day_total ?: '-' ?></td></tr>
            <?php endif; endfor; ?>
          </tbody>
          <tfoot style="background: #eee; font-weight: bold;">
            <tr><td>TOTAL MOIS</td><?php foreach ($target_centers as $id) : ?><td class="text-center"><?= $total_month[$id] ?></td><?php endforeach; ?><td class="text-center" style="background: #00a8cc; color: white !important;"><?= $total_month['global'] ?></td></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- MODAL DE DÉPLACEMENT -->
<div class="modal fade" id="moveModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #00a8cc; color: white;">
        <button type="button" class="close" data-dismiss="modal" style="color:white;">&times;</button>
        <h4 class="modal-title">🔄 Déplacer le Rendez-vous</h4>
      </div>
      <form method="POST">
          <div class="modal-body">
            <input type="hidden" name="action_move" value="move_rdv">
            <input type="hidden" name="booking_id" id="moveBookingId">
            <input type="hidden" name="token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <p>Déplacer le RDV de <b id="moveClientName"></b> vers :</p>
            
            <div class="form-group">
                <label>Nouvelle Date :</label>
                <input type="date" name="new_date" id="newDateInput" class="form-control" required min="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="form-group">
                <label>Nouvel Horaire :</label>
                <select name="new_time" id="newTimeSelect" class="form-control" required>
                    <!-- Rempli par JS -->
                </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" style="background: #00a8cc;">Confirmer le déplacement</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
// Planning configuration pour le JS
const planningConfig = <?= json_encode($new_planning) ?>;
const oldConfig = {
    'semaine': ['09:45', '11:00', '12:15', '13:30', '14:45', '16:00', '17:15', '18:30'],
    'samedi': ['09:45', '11:00', '12:15', '13:30']
};
const switchDate = '2026-02-01';

function openMoveModal(id, name) {
    document.getElementById('moveBookingId').value = id;
    document.getElementById('moveClientName').innerText = name;
    
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('newDateInput');
    dateInput.value = today;
    
    // Trigger update of times
    updateAvailableTimes();
    
    $('#moveModal').modal('show');
}

document.getElementById('newDateInput').addEventListener('change', updateAvailableTimes);

function updateAvailableTimes() {
    const dateVal = document.getElementById('newDateInput').value;
    if (!dateVal) return;
    
    const dateObj = new Date(dateVal);
    const dayOfWeek = dateObj.getDay(); // 0 = Dimanche, 1 = Lundi...
    const daysFr = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    const dayName = daysFr[dayOfWeek];
    
    const select = document.getElementById('newTimeSelect');
    select.innerHTML = '';
    
    if (dayOfWeek === 0) {
        select.innerHTML = '<option value="">Fermé le dimanche</option>';
        return;
    }
    
    let slots = [];
    
    // Logique simplifiée : si date >= switchDate, on utilise new_planning
    if (dateVal >= switchDate) {
        if (planningConfig[dayName]) {
            slots = Object.keys(planningConfig[dayName]);
        }
    } else {
        // Ancien planning
        slots = (dayOfWeek === 6) ? oldConfig.samedi : oldConfig.semaine;
    }
    
    slots.forEach(time => {
        let opt = document.createElement('option');
        opt.value = time;
        opt.innerText = time;
        select.appendChild(opt);
    });
}
</script>
