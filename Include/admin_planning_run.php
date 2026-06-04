<?php
/**
 * Auth + actions admin planning — exécuté avant tout HTML (index.php).
 */
require_once __DIR__ . '/../_settings.php';

if (!function_exists('aquavelo_admin_planning_redirect')) {
    function aquavelo_admin_planning_redirect(string $flash = '', string $type = 'ok'): void
    {
        if ($flash !== '') {
            $_SESSION['admin_planning_flash'] = $flash;
            $_SESSION['admin_planning_flash_type'] = $type;
        }
        $base = defined('BASE_PATH') ? (string) BASE_PATH : '/';
        header('Location: ' . $base . 'index.php?p=admin_planning');
        exit;
    }
}

if (isset($_GET['logout'])) {
    $_SESSION['admin_auth'] = false;
    unset($_SESSION['admin_auth'], $_SESSION['csrf_token']);
    aquavelo_admin_planning_redirect();
}

$password_secret = 'aquavelo2026';
$authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;
$login_error = null;

$shared_centers = [305, 347, 349];
$centers_names = [305 => 'Cannes', 347 => 'Mandelieu', 349 => 'Vallauris'];
try {
    $stmt_cn = $database->query('SELECT id, city FROM am_centers WHERE online = 1 AND aquavelo = 1');
    while ($row = $stmt_cn->fetch(PDO::FETCH_ASSOC)) {
        $centers_names[(int) $row['id']] = $row['city'];
    }
} catch (Throwable $e) {
    /* défaut */
}

$admin_special_lundi_essai = '2026-04-06';

if (isset($_POST['login_pass'])) {
    if ($_POST['login_pass'] === $password_secret) {
        $_SESSION['admin_auth'] = true;
        $authenticated = true;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        aquavelo_admin_planning_redirect('Connexion réussie.');
    }
    sleep(1);
    $login_error = 'Mot de passe incorrect';
}

if ($authenticated) {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if (isset($_POST['action_google_sync']) && $_POST['action_google_sync'] === '1') {
        $token_ok = isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'];
        if ($token_ok) {
            require_once __DIR__ . '/../google_calendar_rdv_helpers.php';
            $sync_out = aquavelo_gc_sync_pending_rdvs($database);
            $msg = 'Synchronisation Google : ' . $sync_out['synced'] . ' RDV traité(s)';
            if ($sync_out['errors'] > 0) {
                $msg .= ', ' . $sync_out['errors'] . ' erreur(s)';
            }
            aquavelo_admin_planning_redirect($msg);
        }
        aquavelo_admin_planning_redirect('Session expirée : reconnectez-vous.', 'error');
    }

    if (isset($_POST['action_noshow'])) {
        $token_ok = isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'];
        if ($token_ok) {
            $booking_id = (int) $_POST['booking_id'];
            $email_text = trim($_POST['email_text'] ?? '');
            $stmt_get = $database->prepare('SELECT * FROM am_free WHERE id = ?');
            $stmt_get->execute([$booking_id]);
            $booking = $stmt_get->fetch(PDO::FETCH_ASSOC);
            if ($booking && $email_text !== '' && !empty($settings['mjusername'])) {
                try {
                    $email_html = nl2br(htmlspecialchars($email_text, ENT_QUOTES, 'UTF-8'));
                    $email_html = preg_replace(
                        '/(https?:\/\/[^\s<]+)/',
                        '<a href="$1" style="color:#00a8cc;">$1</a>',
                        $email_html
                    );
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $settings['mjhost'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $settings['mjusername'];
                    $mail->Password = $settings['mjpassword'];
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';
                    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo Cannes');
                    $mail->addAddress($booking['email']);
                    $mail->addReplyTo('claude@alesiaminceur.com', 'Claude - Aquavelo');
                    $mail->isHTML(true);
                    $mail->Subject = "Votre séance d'essai à Aquavelo";
                    $mail->Body = $email_html;
                    $mail->send();
                    aquavelo_admin_planning_redirect('Email envoyé à ' . $booking['email']);
                } catch (Throwable $e) {
                    error_log('Admin planning noshow mail: ' . $e->getMessage());
                    aquavelo_admin_planning_redirect('Erreur envoi email : ' . $e->getMessage(), 'error');
                }
            }
        }
        aquavelo_admin_planning_redirect('Email non envoyé (données ou session invalides).', 'error');
    }

    if (isset($_POST['action_move']) && $_POST['action_move'] === 'move_rdv') {
        $token_ok = isset($_POST['token']) && $_POST['token'] === $_SESSION['csrf_token'];
        if (!$token_ok) {
            aquavelo_admin_planning_redirect('Session expirée : rechargez la page et réessayez.', 'error');
        }

        $booking_id = (int) ($_POST['booking_id'] ?? 0);
        $new_date = trim((string) ($_POST['new_date'] ?? ''));
        $new_time = trim((string) ($_POST['new_time'] ?? ''));

        if ($booking_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $new_date) || !preg_match('/^\d{1,2}:\d{2}$/', $new_time)) {
            aquavelo_admin_planning_redirect('Date ou horaire invalide.', 'error');
        }

        $stmt_get = $database->prepare('SELECT * FROM am_free WHERE id = ?');
        $stmt_get->execute([$booking_id]);
        $booking = $stmt_get->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            aquavelo_admin_planning_redirect('Réservation introuvable.', 'error');
        }

        $c_info = ['city' => 'Cannes', 'address' => ''];
        $stmt_c = $database->prepare('SELECT email, address, city FROM am_centers WHERE id = ?');
        $stmt_c->execute([$booking['center_id']]);
        $row_c = $stmt_c->fetch(PDO::FETCH_ASSOC);
        if ($row_c) {
            $c_info = $row_c;
        }

        $client_name = trim(explode('(RDV:', (string) $booking['name'])[0]);
        $new_datetime_str = $new_date . ' ' . $new_time;
        $new_dt = DateTime::createFromFormat('Y-m-d G:i', $new_datetime_str)
            ?: DateTime::createFromFormat('Y-m-d H:i', $new_datetime_str);

        if (!$new_dt) {
            aquavelo_admin_planning_redirect('Créneau invalide : ' . $new_datetime_str, 'error');
        }

        $new_date_fr = $new_dt->format('d/m/Y');
        $new_time_db = $new_dt->format('H:i');
        $new_name_db = $client_name . ' (RDV: ' . $new_date_fr . ' à ' . $new_time_db . ')';

        $update = $database->prepare(
            'UPDATE am_free SET name = ?, date = ?, google_sync = 0, reminder_sent = 0, reminder_3h_sent = 0,
             after_session_sent = 0, followup_48h_sent = 0, followup_2d_sent = 0, followup_7d_sent = 0 WHERE id = ?'
        );
        $update->execute([$new_name_db, $new_date . ' ' . $new_time_db . ':00', $booking_id]);

        try {
            if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
                require_once __DIR__ . '/../vendor/autoload.php';
                require_once __DIR__ . '/../load_env.php';
                $keyFile = __DIR__ . '/../google_key.json';
                if (is_readable($keyFile)) {
                    $client = new Google\Client();
                    $client->setAuthConfig($keyFile);
                    $client->addScope(Google\Service\Calendar::CALENDAR);
                    $service = new Google\Service\Calendar($client);
                    $targetCalendarId = in_array((int) $booking['center_id'], [305, 347, 349], true)
                        ? 'aqua.cannes@gmail.com'
                        : (!empty($c_info['email']) ? $c_info['email'] : 'aqua.cannes@gmail.com');
                    if (!empty($booking['google_event_id'])) {
                        try {
                            $service->events->delete($targetCalendarId, $booking['google_event_id']);
                        } catch (Throwable $e) {
                            /* ignore */
                        }
                    }
                    $rdv_start = new DateTime($new_date . ' ' . $new_time_db, new DateTimeZone('Europe/Paris'));
                    $rdv_end = (clone $rdv_start)->modify('+45 minutes');
                    $event = new Google\Service\Calendar\Event([
                        'summary' => '🏊 ' . $client_name . ' - ' . ($c_info['city'] ?? 'Cannes'),
                        'location' => $c_info['address'] ?? 'Cannes',
                        'description' => "Client: $client_name\nEmail: {$booking['email']}\nTél: {$booking['phone']}\nID: $booking_id\n(Déplacé par Admin)",
                        'start' => ['dateTime' => $rdv_start->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                        'end' => ['dateTime' => $rdv_end->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                    ]);
                    $createdEvent = $service->events->insert($targetCalendarId, $event);
                    $database->prepare('UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?')
                        ->execute([$createdEvent->getId(), $booking_id]);
                }
            }
        } catch (Throwable $e) {
            error_log('Erreur Google Calendar Move: ' . $e->getMessage());
        }

        $mail_ok = false;
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
                $mail->Subject = 'Nouveau créneau confirmé : Votre séance à Aquavelo';
                $mail->Body = "Bonjour <b>$client_name</b>,<br><br>
                    Suite à notre échange, nous vous confirmons le déplacement de votre séance découverte.<br><br>
                    <b>Nouveau Rendez-vous :</b><br>
                    📅 <b>$new_date_fr</b><br>
                    🕐 <b>$new_time_db</b><br>
                    📍 <b>Aquavelo " . ($c_info['city'] ?? '') . "</b><br><br>
                    Nous avons hâte de vous accueillir !<br><br>
                    Cordialement,<br>L'équipe Aquavelo";
                $mail->send();
                $mail_ok = true;
            } catch (Throwable $e) {
                error_log('Erreur Email Move: ' . $e->getMessage());
            }
        }

        $flash = 'RDV déplacé avec succès.';
        $flash .= $mail_ok ? ' Client notifié par email.' : ' (email client non envoyé — vérifiez les logs.)';
        aquavelo_admin_planning_redirect($flash);
    }

    if (isset($_GET['action'])) {
        $token_ok = isset($_GET['token']) && $_GET['token'] === $_SESSION['csrf_token'];
        if ($token_ok) {
            if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
                $id = (int) $_GET['id'];
                $check = $database->prepare('SELECT id, name, google_event_id, center_id FROM am_free WHERE id = ?');
                $check->execute([$id]);
                $booking_to_del = $check->fetch(PDO::FETCH_ASSOC);
                if ($booking_to_del) {
                    $try_google = !empty($booking_to_del['google_event_id'])
                        || in_array((int) $booking_to_del['center_id'], [305, 347, 349], true);
                    if ($try_google && file_exists(__DIR__ . '/../vendor/autoload.php')) {
                        require_once __DIR__ . '/../load_env.php';
                        require_once __DIR__ . '/../google_calendar_rdv_helpers.php';
                        $gc_service = aquavelo_gc_bootstrap();
                        if ($gc_service) {
                            aquavelo_gc_delete_booking_event($gc_service, $database, $booking_to_del);
                        }
                    }
                }
                $database->prepare('DELETE FROM am_free WHERE id = ?')->execute([$id]);
                aquavelo_admin_planning_redirect('Réservation supprimée.');
            }
            if ($_GET['action'] === 'lock' && isset($_GET['date'], $_GET['time'])) {
                try {
                    $date_str = $_GET['dayname'] . ' ' . $_GET['date'] . ' à ' . $_GET['time'] . ' (' . ($_GET['activity'] ?? '') . ')';
                    $lock_name = 'BLOQUE (ADMIN) (RDV: ' . $date_str . ')';
                    $ref = 'LOCK' . date('dmhis');
                    $stmt = $database->prepare(
                        'INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)'
                    );
                    $stmt->execute([$ref, 305, 3, $lock_name, 'admin@aquavelo.com', '0493930565', 'admin-lock']);
                    aquavelo_admin_planning_redirect('Créneau verrouillé.');
                } catch (Throwable $e) {
                    error_log('Admin planning lock: ' . $e->getMessage());
                    aquavelo_admin_planning_redirect('Erreur verrouillage : ' . $e->getMessage(), 'error');
                }
            }
        }
        aquavelo_admin_planning_redirect('Action refusée (session ou paramètres invalides).', 'error');
    }
}
