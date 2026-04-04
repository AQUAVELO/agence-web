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
try {
    $stmt_cn = $database->query('SELECT id, city FROM am_centers WHERE online = 1 AND aquavelo = 1');
    while ($row = $stmt_cn->fetch(PDO::FETCH_ASSOC)) {
        $centers_names[(int) $row['id']] = $row['city'];
    }
} catch (Throwable $e) {
    /* garde $centers_names par défaut */
}

/** Lundi de Pâques : affichage admin séances d'essai jusqu'à 13 h + résas tous centres */
$admin_special_lundi_essai = '2026-04-06';

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

    // ACTION: synchroniser manuellement les RDV vers Google Agenda (Cannes / Mandelieu / Vallauris)
    if (isset($_POST['action_google_sync']) && $_POST['action_google_sync'] === '1') {
        $token_ok = (isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token']);
        if ($token_ok) {
            require_once __DIR__ . '/google_calendar_rdv_helpers.php';
            $sync_out = aquavelo_gc_sync_pending_rdvs($database);
            $msg = 'Synchronisation Google : ' . $sync_out['synced'] . ' RDV traité(s)';
            if ($sync_out['errors'] > 0) {
                $msg .= ', ' . $sync_out['errors'] . ' erreur(s)';
            }
            echo '<script>alert(' . json_encode($msg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '); window.location.replace("index.php?p=admin_planning");</script>';
            exit;
        }
    }
    
    // ACTION: EMAIL NO-SHOW (absent au RDV)
    if (isset($_POST['action_noshow'])) {
        $token_ok = (isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token']);
        if ($token_ok) {
            $booking_id = intval($_POST['booking_id']);
            $email_text = trim($_POST['email_text'] ?? '');

            $stmt_get = $database->prepare("SELECT * FROM am_free WHERE id = ?");
            $stmt_get->execute([$booking_id]);
            $booking = $stmt_get->fetch();

            if ($booking && !empty($email_text) && !empty($settings['mjusername'])) {
                try {
                    // Convertir les URLs en liens cliquables et les sauts de ligne en <br>
                    $email_html = nl2br(htmlspecialchars($email_text, ENT_QUOTES, 'UTF-8'));
                    $email_html = preg_replace(
                        '/(https?:\/\/[^\s<]+)/',
                        '<a href="$1" style="color:#00a8cc;">$1</a>',
                        $email_html
                    );

                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = $settings['mjhost'];
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $settings['mjusername'];
                    $mail->Password   = $settings['mjpassword'];
                    $mail->Port       = 587;
                    $mail->CharSet    = 'UTF-8';

                    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo Cannes');
                    $mail->addAddress($booking['email']);
                    $mail->addReplyTo('claude@alesiaminceur.com', 'Claude - Aquavelo');
                    $mail->isHTML(true);
                    $mail->Subject = "Votre séance d'essai à Aquavelo";
                    $mail->Body    = $email_html;

                    $mail->send();
                    echo "<script>alert('✅ Email envoyé à " . addslashes($booking['email']) . "'); window.location.replace('index.php?p=admin_planning');</script>";
                } catch (Exception $e) {
                    echo "<script>alert('❌ Erreur envoi : " . addslashes($mail->ErrorInfo) . "'); window.history.back();</script>";
                }
                exit;
            }
        }
    }

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
                $update = $database->prepare("UPDATE am_free SET name = ?, date = ?, google_sync = 0, reminder_sent = 0, reminder_3h_sent = 0, after_session_sent = 0, followup_2d_sent = 0, followup_7d_sent = 0 WHERE id = ?");
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
                
                // --- Google Agenda : suppression par event_id ou recherche créneau + nom (groupe Cannes) ---
                $check = $database->prepare("SELECT id, name, google_event_id, center_id FROM am_free WHERE id = ?");
                $check->execute([$id]);
                $booking_to_del = $check->fetch(PDO::FETCH_ASSOC);
                
                if ($booking_to_del) {
                    $try_google = !empty($booking_to_del['google_event_id'])
                        || in_array((int) $booking_to_del['center_id'], [305, 347, 349], true);
                    if ($try_google && file_exists(__DIR__ . '/vendor/autoload.php')) {
                        require_once __DIR__ . '/load_env.php';
                        require_once __DIR__ . '/google_calendar_rdv_helpers.php';
                        $gc_service = aquavelo_gc_bootstrap();
                        if ($gc_service) {
                            aquavelo_gc_delete_booking_event($gc_service, $database, $booking_to_del);
                        }
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
          <form method="POST" action="index.php?p=admin_planning" autocomplete="on">
            <input type="text" name="username" value="admin" autocomplete="username" style="display:none;">
            <input type="password" name="login_pass" id="login_pass" placeholder="Mot de passe" required autocomplete="current-password" style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">CONNEXION</button>
          </form>
          <p style="margin-top: 22px; margin-bottom: 0; font-size: 0.9rem;">
            <a href="<?= htmlspecialchars(BASE_PATH) ?>index.php?p=admin_reglements" style="color: #104e8b;">→ Admin liens de règlement</a>
            <span style="color: #999;"> (mot de passe distinct)</span>
          </p>
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
    'Mardi' => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUABOXING','14:45' => 'AQUAVELO','16:00' => 'AQUAGYM','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
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
        $rawD = $date->format('Y-m-d');
        if ($rawD === $admin_special_lundi_essai) {
            $current_slots = array_values(array_filter($current_slots, static function ($slot) {
                return strcmp($slot['time'], '13:30') < 0;
            }));
        }
        if (!empty($current_slots)) {
            $calendar[] = [
                'full_date' => $date->format('d/m/Y'),
                'raw_date'  => $rawD,
                'day_name'  => $day_fr,
                'slots'     => $current_slots,
                'special_essai_jusqua_13h' => ($rawD === $admin_special_lundi_essai),
            ];
        }
    }
}

// 4. RÉCUPÉRATION DES RÉSERVATIONS (Cannes/Mandelieu/Vallauris + le 06/04/2026 tous centres)
$all_free_query = $database->prepare("SELECT * FROM am_free WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%'");
$all_free_query->execute();
$all_free = $all_free_query->fetchAll(PDO::FETCH_ASSOC);

$by_id = [];
foreach ($all_free as $r) {
    $by_id[(int) $r['id']] = $r;
}
$extra = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND name LIKE '%06/04/2026%' AND center_id NOT IN (305, 347, 349)");
$extra->execute();
foreach ($extra->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $by_id[(int) $r['id']] = $r;
}
$all_free = array_values($by_id);

$bookings_visuel = [];
foreach ($all_free as $res) {
    foreach ($calendar as $day) {
        foreach ($day['slots'] as $s) {
            if (strpos($res['name'], $day['full_date'] . ' à ' . $s['time']) !== false) {
                $k = $day['full_date'] . '|' . $s['time'];
                if (!isset($bookings_visuel[$k])) {
                    $bookings_visuel[$k] = [];
                }
                $bookings_visuel[$k][] = $res;
            }
        }
    }
}
?>

<section class="content-area bg1" style="padding: 40px 0;">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: #00a8cc; margin: 0;">🗓️ Admin Planning (Cannes / Mandelieu / Vallauris) v2</h2>
        <a href="index.php?p=admin_planning&logout=1" class="btn btn-default">Déconnexion</a>
    </div>

    <div style="margin-bottom: 18px; padding: 14px 18px; background: #e3f2fd; border-radius: 10px; display: flex; flex-wrap: wrap; align-items: center; gap: 14px; border: 1px solid #90caf9;">
        <span style="font-size: 0.9rem; color: #0d47a1; max-width: 560px;">Pousse vers <strong>aqua.cannes@gmail.com</strong> les RDV <strong>sans</strong> <code>google_event_id</code> (y compris anciennes lignes marquées synchro à tort). Cron ~15 min ou bouton ci-dessous. Si l’événement existe déjà sur le créneau, la ligne est reliée sans doublon.</span>
        <form method="post" action="index.php?p=admin_planning" style="margin:0;">
            <input type="hidden" name="action_google_sync" value="1">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary" style="background:#1565c0;border:none;">↗ Synchroniser Google Agenda</button>
        </form>
    </div>

    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <div style="display: flex; overflow-x: auto; gap: 10px; padding-bottom: 20px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 190px; border: 1px solid #f0f0f0; border-radius: 10px; padding: 10px; background: #fafafa;">
            <div style="text-align: center; font-weight: bold; border-bottom: 2px solid #eee; margin-bottom: 12px; padding-bottom: 8px;">
                <?= $day['day_name'] ?><br><small><?= $day['full_date'] ?></small>
            </div>
            <?php if (!empty($day['special_essai_jusqua_13h'])) : ?>
            <div style="font-size: 0.68rem; color: #b71c1c; margin: -4px 0 10px; line-height: 1.35; text-align: center; background: #ffebee; padding: 6px; border-radius: 6px;">Séances d’essai <strong>jusqu’à 13 h</strong> — réservations de <strong>tous les centres</strong> Aquavelo visibles pour ce jour.</div>
            <?php endif; ?>
            <?php foreach ($day['slots'] as $s) : 
                $key = $day['full_date'] . '|' . $s['time'];
                $slot_entries = $bookings_visuel[$key] ?? [];
            ?>
            <?php if (empty($slot_entries)) :
                $res = null;
                $is_locked = false;
                $center_label = '';
                $client_name_only = '';
            ?>
                <div style="padding: 10px; border-radius: 8px; margin-bottom: 8px; font-size: 0.8rem; background: #fff; border: 1px solid #eee; min-height: 105px; display: flex; flex-direction: column; justify-content: space-between;">
                  <div>
                      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <b><?= $s['time'] ?></b>
                      </div>
                      <span style="font-size: 0.65rem; color: #999;"><?= $s['activity'] ?></span>
                      <div style="color: #bbb; margin-top: 5px;">Disponible</div>
                  </div>
                  <div style="margin-top: 8px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 5px; display: flex; justify-content: space-between;">
                        <a href="index.php?p=admin_planning&action=lock&date=<?= $day['full_date'] ?>&dayname=<?= $day['day_name'] ?>&time=<?= $s['time'] ?>&activity=<?= $s['activity'] ?>&token=<?= $_SESSION['csrf_token'] ?>" 
                           onclick="return confirm('Verrouiller ce créneau pour les clients ?')"
                           style="color: #00a8cc; font-size: 0.7rem; font-weight: bold; text-decoration: none;">🔒 VERROUILLER</a>
                  </div>
                </div>
            <?php else : ?>
                <?php foreach ($slot_entries as $res) :
                $is_locked = (strpos($res['name'], 'VERROUILLÉ') !== false || strpos($res['name'], 'BLOQUE') !== false);
                $center_label = isset($centers_names[$res['center_id']]) ? $centers_names[$res['center_id']] : ('#' . (int) $res['center_id']);
                $client_name_only = trim(explode('(RDV:', $res['name'])[0]);
            ?>
                <div style="padding: 10px; border-radius: 8px; margin-bottom: 8px; font-size: 0.8rem; background: <?= $is_locked ? '#f5f5f5' : '#fff9c4' ?>; border: 1px solid <?= $is_locked ? '#ddd' : '#fbc02d' ?>; min-height: 105px; display: flex; flex-direction: column; justify-content: space-between;">
                  <div>
                      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <b><?= $s['time'] ?></b>
                        <?php if ($center_label && !$is_locked): ?>
                            <span style="font-size: 0.6rem; background: #00a8cc; color: white; padding: 1px 4px; border-radius: 3px;"><?= htmlspecialchars($center_label, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                      </div>
                      <span style="font-size: 0.65rem; color: #999;"><?= $s['activity'] ?></span>
                        <div style="margin-top: 5px; font-weight: bold; color: <?= $is_locked ? '#999' : '#333' ?>; line-height: 1.1;">
                            <?= htmlspecialchars($client_name_only, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <?php if (!$is_locked): ?>
                            <div style="color: #666; font-size: 0.75rem;"><?= htmlspecialchars((string) $res['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                            <!-- Indicateurs relances : avant RDV, puis après séance (3h ap., J+2, J+7) -->
                            <?php
                            $ok24 = (int) ($res['reminder_sent'] ?? 0) === 1;
                            $ok3hAvant = (int) ($res['reminder_3h_sent'] ?? 0) === 1;
                            $ok3hApres = array_key_exists('after_session_sent', $res) && (int) $res['after_session_sent'] === 1;
                            $okJ2 = array_key_exists('followup_2d_sent', $res) && (int) $res['followup_2d_sent'] === 1;
                            $okJ7 = array_key_exists('followup_7d_sent', $res) && (int) $res['followup_7d_sent'] === 1;
                            ?>
                            <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 4px; align-items: center;" title="Couleur vive = relance envoyée par le cron">
                                <span title="E-mail ~24 h avant le RDV (cron_rappel_24h)" style="font-size: 9px; padding: 2px 5px; border-radius: 3px; font-weight: 600; border: 1px solid <?= $ok24 ? '#2e7d32' : '#ccc' ?>; background: <?= $ok24 ? '#4CAF50' : '#f5f5f5' ?>; color: <?= $ok24 ? '#fff' : '#555' ?>;">24h</span>
                                <span title="E-mail + SMS ~3 h avant le RDV (cron_rappel_3h)" style="font-size: 9px; padding: 2px 5px; border-radius: 3px; font-weight: 600; border: 1px solid <?= $ok3hAvant ? '#2e7d32' : '#ccc' ?>; background: <?= $ok3hAvant ? '#4CAF50' : '#f5f5f5' ?>; color: <?= $ok3hAvant ? '#fff' : '#555' ?>;">3h av.</span>
                                <span title="E-mail ~3 h après la séance, fenêtre 3–6 h après le début du RDV (cron_apres_seance)" style="font-size: 9px; padding: 2px 5px; border-radius: 3px; font-weight: 600; border: 1px solid <?= $ok3hApres ? '#1565c0' : '#ccc' ?>; background: <?= $ok3hApres ? '#1976d2' : '#f5f5f5' ?>; color: <?= $ok3hApres ? '#fff' : '#555' ?>;">3h ap.</span>
                                <span title="E-mail suivi J+2 (~44–60 h après le début du RDV) (cron_suivi_2j)" style="font-size: 9px; padding: 2px 5px; border-radius: 3px; font-weight: 600; border: 1px solid <?= $okJ2 ? '#e65100' : '#ccc' ?>; background: <?= $okJ2 ? '#f57c00' : '#f5f5f5' ?>; color: <?= $okJ2 ? '#fff' : '#555' ?>;">J+2</span>
                                <span title="E-mail suivi J+7 (~7 jours après le début du RDV) (cron_suivi_7j)" style="font-size: 9px; padding: 2px 5px; border-radius: 3px; font-weight: 600; border: 1px solid <?= $okJ7 ? '#4a148c' : '#ccc' ?>; background: <?= $okJ7 ? '#7b1fa2' : '#f5f5f5' ?>; color: <?= $okJ7 ? '#fff' : '#555' ?>;">J+7</span>
                            </div>
                            <?php
                            $gcalEv = trim((string) ($res['google_event_id'] ?? ''));
                            $gcalOk = (int) ($res['google_sync'] ?? 0) === 1 && $gcalEv !== '';
                            ?>
                            <div style="margin-top: 6px;">
                                <span title="<?= $gcalOk ? 'Événement lié sur aqua.cannes@gmail.com' : 'Pas encore sur Google Agenda — cliquez « Synchroniser Google Agenda » en haut' ?>" style="font-size: 9px; padding: 2px 6px; border-radius: 3px; font-weight: 700; border: 1px solid <?= $gcalOk ? '#2e7d32' : '#ef6c00' ?>; background: <?= $gcalOk ? '#e8f5e9' : '#fff3e0' ?>; color: <?= $gcalOk ? '#1b5e20' : '#e65100' ?>;"><?= $gcalOk ? 'Agenda OK' : 'Agenda ?' ?></span>
                            </div>
                        <?php endif; ?>
                  </div>
                  
                  <div style="margin-top: 8px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 5px; display: flex; justify-content: space-between;">
                        <a href="index.php?p=admin_planning&action=delete&id=<?= (int) $res['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" 
                           onclick="return confirm('<?= $is_locked ? 'Déverrouiller ce créneau ?' : 'Annuler ce RDV ?' ?>')" 
                           style="color: #d32f2f; font-size: 0.7rem; font-weight: bold; text-decoration: none;">
                           <?= $is_locked ? '🔓 DÉVERROUILLER' : '❌ ANNULER' ?>
                        </a>
                        <?php if (!$is_locked): ?>
                            <a href="#" onclick="openMoveModal(<?= (int) $res['id'] ?>, '<?= htmlspecialchars($client_name_only, ENT_QUOTES) ?>')" 
                               style="color: #00a8cc; font-size: 0.7rem; font-weight: bold; text-decoration: none;">
                               🔄 DÉPLACER
                            </a>
                        <?php endif; ?>
                  </div>
                  <?php if ($res && !$is_locked): ?>
                  <div style="border-top: 1px solid rgba(0,0,0,0.05); padding-top: 5px; margin-top: 4px;">
                    <a href="#" onclick="openNoShowModal(<?= $res['id'] ?>, '<?= htmlspecialchars(explode(' ', $client_name_only)[0], ENT_QUOTES) ?>', '<?= htmlspecialchars($res['email'], ENT_QUOTES) ?>')"
                       style="color: #e65100; font-size: 0.7rem; font-weight: bold; text-decoration: none;">
                       👻 ABSENT
                    </a>
                  </div>
                  <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
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

<section class="content-area bg1" style="padding-top: 0; padding-bottom: 60px;">
  <div class="container">
    <div style="background: #fff; padding: 24px 28px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); border-left: 4px solid #104e8b;">
      <h3 style="color: #104e8b; margin-top: 0; margin-bottom: 10px;">Liens de règlement personnalisés</h3>
      <p style="color: #666; margin-bottom: 18px; font-size: 0.95rem; line-height: 1.5;">
        Création de liens de paiement sécurisés (Monetico), envoi SMS au client, suivi des règlements.
      </p>
      <a href="<?= htmlspecialchars(BASE_PATH) ?>index.php?p=admin_reglements" class="btn btn-primary" style="background: #00a8cc; border: none; padding: 10px 20px;">
        Ouvrir l’admin — liens de règlement
      </a>
    </div>
  </div>
</section>

<!-- MODAL NO-SHOW (Absent au RDV) -->
<div class="modal fade" id="noShowModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #e65100; color: white;">
        <button type="button" class="close" data-dismiss="modal" style="color:white;">&times;</button>
        <h4 class="modal-title">👻 Client absent — Envoyer un email de relance</h4>
      </div>
      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="action_noshow" value="1">
          <input type="hidden" name="booking_id" id="noShowBookingId">
          <input type="hidden" name="token" value="<?= $_SESSION['csrf_token'] ?>">

          <p style="margin-bottom: 5px; color: #666; font-size: 0.85rem;">
            Destinataire : <b id="noShowEmail" style="color: #333;"></b>
          </p>
          <p style="margin-bottom: 10px; color: #666; font-size: 0.85rem;">
            Vous pouvez modifier le message avant l'envoi :
          </p>
          <textarea name="email_text" id="noShowText" class="form-control" rows="8"
                    style="font-size: 0.9rem; line-height: 1.6; resize: vertical;"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-warning" style="background: #e65100; color: white; border: none;">
            📨 Envoyer l'email
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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

function openNoShowModal(id, prenom, email) {
    document.getElementById('noShowBookingId').value = id;
    document.getElementById('noShowEmail').innerText = email;
    document.getElementById('noShowText').value =
        "Bonjour " + prenom + ",\n\n" +
        "Vous avez oublié votre séance d'essai à Aquavelo, cela arrive !\n\n" +
        "Aussi je vous propose de prendre un nouveau rendez-vous en cliquant ici :\nhttps://www.aquavelo.com/free\n\n" +
        "Cordialement,\nClaude\nTél : 04 93 93 05 65";
    $('#noShowModal').modal('show');
}

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
