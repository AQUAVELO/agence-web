<?php
/**
 * Page Séance Découverte Gratuite - Version Finale avec Annulation/Replanification
 */

require_once '_settings.php';

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error_message = '';
$success_message = '';

if (!function_exists('aquavelo_parse_recipient_email')) {
    /**
     * Extrait une adresse valide depuis un fragment (email seul, "Nom <email>", espaces insécables).
     */
    function aquavelo_parse_recipient_email(string $raw): string
    {
        $raw = trim(str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', $raw));
        if ($raw === '') {
            return '';
        }
        if (filter_var($raw, FILTER_VALIDATE_EMAIL)) {
            return $raw;
        }
        if (preg_match('/<([^>]+@[^>]+)>/', $raw, $m)) {
            $inner = trim($m[1]);
            if (filter_var($inner, FILTER_VALIDATE_EMAIL)) {
                return $inner;
            }
        }
        if (preg_match('/[\w.+%-]+@[\w.-]+\.[\w.-]+/u', $raw, $m)) {
            $c = $m[0];
            if (filter_var($c, FILTER_VALIDATE_EMAIL)) {
                return $c;
            }
        }

        return '';
    }
}

if (!function_exists('aquavelo_is_google_calendar_address')) {
    /** Adresses techniques Google Agenda (non boîtes mail du dirigeant). */
    function aquavelo_is_google_calendar_address(string $email): bool
    {
        $e = strtolower(trim($email));
        if ($e === '') {
            return false;
        }
        // IDs agenda Google du type *@group.calendar.google.com, *@resource.calendar.google.com, etc.
        return (bool) preg_match('/@[a-z0-9.-]+\.calendar\.google\.com$/i', $e);
    }
}

if (!function_exists('aquavelo_recipient_raw_parts')) {
    /**
     * @return list<string>
     */
    function aquavelo_recipient_raw_parts(string $r): array
    {
        $r = trim($r);
        if ($r === '') {
            return [];
        }
        if (strpos($r, '<') !== false) {
            $parts = preg_split('/\s*[,;]\s*/', $r) ?: [];
        } else {
            $parts = preg_split('/[\s,;]+/', $r) ?: [];
        }
        $out = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part !== '') {
                $out[] = $part;
            }
        }

        return $out;
    }
}

if (!function_exists('aquavelo_add_mail_recipients')) {
    function aquavelo_add_mail_recipients(PHPMailer $mail, string $recipients, string $fallback = ''): int
    {
        $added = 0;
        $r = trim($recipients);
        if ($r === '') {
            if ($fallback !== '' && filter_var($fallback, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($fallback);
                return 1;
            }

            return 0;
        }

        foreach (aquavelo_recipient_raw_parts($r) as $part) {
            $email = aquavelo_parse_recipient_email($part);
            if ($email === '' || aquavelo_is_google_calendar_address($email)) {
                continue;
            }

            $mail->addAddress($email);
            $added++;
        }

        if ($added === 0 && $fallback !== '' && filter_var($fallback, FILTER_VALIDATE_EMAIL)) {
            $mail->addAddress($fallback);
            $added++;
        }

        return $added;
    }
}

if (!function_exists('aquavelo_first_human_center_reply_email')) {
    /** Première adresse « humaine » du champ centre (ignore les IDs Google Agenda). */
    function aquavelo_first_human_center_reply_email(string $raw): string
    {
        foreach (aquavelo_recipient_raw_parts($raw) as $part) {
            $e = aquavelo_parse_recipient_email($part);
            if ($e !== '' && !aquavelo_is_google_calendar_address($e)) {
                return $e;
            }
        }

        return '';
    }
}

if (!function_exists('aquavelo_is_prospect_admin_technical_to')) {
    /**
     * Boîte d’expédition / contrôle Mailjet « nice » : ne doit pas être le seul destinataire To de la notif dirigeant.
     */
    function aquavelo_is_prospect_admin_technical_to(string $em): bool
    {
        return strtolower(trim($em)) === 'claude@alesiaminceur.com';
    }
}

if (!function_exists('aquavelo_collect_center_director_emails')) {
    /**
     * Adresses dirigeant extraites de am_centers.email (hors Google Agenda), valides pour SMTP.
     *
     * @return list<string>
     */
    function aquavelo_collect_center_director_emails(string $rawEmailCenter): array
    {
        $seen = [];
        $out = [];
        foreach (aquavelo_recipient_raw_parts($rawEmailCenter) as $part) {
            $em = aquavelo_parse_recipient_email($part);
            if ($em === '' || aquavelo_is_google_calendar_address($em)) {
                continue;
            }
            if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            if (aquavelo_is_prospect_admin_technical_to($em)) {
                continue;
            }
            $k = strtolower($em);
            if (isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $out[] = $em;
        }

        return $out;
    }
}

if (!function_exists('aquavelo_create_mailer')) {
    function aquavelo_create_mailer(array $settings, string $city, string $fromEmail = 'service.clients@aquavelo.com', string $fromNamePrefix = 'Aquavelo'): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $settings['mjhost'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['mjusername'];
        $mail->Password = $settings['mjpassword'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($fromEmail, $fromNamePrefix . ' ' . $city);
        $mail->isHTML(true);

        return $mail;
    }
}

if (!function_exists('aquavelo_set_mail_body')) {
    function aquavelo_set_mail_body(PHPMailer $mail, string $html): void
    {
        $mail->Body = $html;
        $mail->AltBody = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html)), ENT_QUOTES, 'UTF-8')));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
    
    $error = [];
    $center_id = isset($_POST['center']) ? intval($_POST['center']) : 0;
    
    // 1. Vérification du centre
    $center_contact = $database->prepare('SELECT * FROM am_centers WHERE id = ?');
    $center_contact->execute(array($center_id));
    $row_center_contact = $center_contact->fetch();
    
    if (!$row_center_contact) {
        $error[] = "Le centre sélectionné est invalide.";
    }
    
    // Anti-spam pour Aix-en-Provence : Vérification captcha
    if ($row_center_contact && stripos($row_center_contact['city'], 'Aix') !== false) {
        $captcha_response = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
        if ($captcha_response !== '3') {
            $error_message = "Erreur de vérification : réponse incorrecte à la question anti-spam.";
        }
    }
    
    $input_nom_complet = strip_tags(trim($_POST['nom'] ?? ''));
    $email = strip_tags(trim($_POST['email'] ?? ''));
    $tel = strip_tags(trim($_POST['phone'] ?? ''));
    $segment = isset($_POST['segment']) ? trim(strip_tags((string) $_POST['segment'])) : 'free-trial';
    $date_heure = isset($_POST['date_heure']) ? trim(strip_tags((string) $_POST['date_heure'])) : '';
    if ($segment === 'calendrier-cannes' && $date_heure === '') {
        $error[] = 'Veuillez sélectionner un créneau horaire sur le calendrier avant de valider.';
    }

    if (empty($input_nom_complet)) $error[] = "Le nom et prénom sont obligatoires.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $error[] = "Une adresse email valide est obligatoire.";
    if (empty($tel)) $error[] = "Le numéro de téléphone est obligatoire.";
    if (!empty($error)) $error_message = implode('<br>', $error);

    // GESTION REPLANIFICATION : Si un ancien RDV est fourni, on le supprime d'abord
    $old_rdv = isset($_POST['old_rdv']) ? strip_tags($_POST['old_rdv']) : '';
    $rescheduling_alert = false;
    if ($old_rdv && $email) {
        $search_old = "%" . $old_rdv . "%";
        // Récupérer les infos avant suppression (y compris google_event_id)
        $check_old = $database->prepare("SELECT name, google_event_id, google_sync, center_id FROM am_free WHERE email = ? AND name LIKE ?");
        $check_old->execute([$email, $search_old]);
        $old_booking = $check_old->fetch();
        
        if ($old_booking) {
            $rescheduling_alert = true;
            $old_center_id_int = (int)$old_booking['center_id'];
            $old_is_cannes_group = in_array($old_center_id_int, [305, 347, 349]);

            // Supprimer l'événement de Google Calendar (par event_id ou fallback par date/heure)
            if (!empty($old_booking['google_event_id']) || $old_is_cannes_group) {
                try {
                    if (!class_exists('Google\Client')) {
                        if (file_exists(__DIR__ . '/vendor/autoload.php')) require_once __DIR__ . '/vendor/autoload.php';
                    }
                    if (!function_exists('generateGoogleKeyFile')) {
                        require_once __DIR__ . '/load_env.php';
                    }

                    $keyFile = __DIR__ . '/google_key.json';
                    if (!file_exists($keyFile)) generateGoogleKeyFile();

                    if (file_exists($keyFile)) {
                        $replan_gc_client = new Google\Client();
                        $replan_gc_client->setAuthConfig($keyFile);
                        $replan_gc_client->addScope(Google\Service\Calendar::CALENDAR);
                        $replan_service = new Google\Service\Calendar($replan_gc_client);

                        // Déterminer l'agenda de destination
                        if ($old_is_cannes_group) {
                            $targetCalendarId = 'aqua.cannes@gmail.com';
                        } else {
                            $stmt_c = $database->prepare("SELECT email FROM am_centers WHERE id = ?");
                            $stmt_c->execute([$old_booking['center_id']]);
                            $c_info = $stmt_c->fetch();
                            $targetCalendarId = !empty($c_info['email']) ? $c_info['email'] : 'aqua.cannes@gmail.com';
                        }

                        $replan_deleted = false;

                        // Méthode 1 : suppression directe via google_event_id
                        if (!empty($old_booking['google_event_id'])) {
                            try {
                                $replan_service->events->delete($targetCalendarId, $old_booking['google_event_id']);
                                error_log("✅ Replanification: ancien événement supprimé via event_id: " . $old_booking['google_event_id']);
                                $replan_deleted = true;
                            } catch (\Exception $e2) {
                                error_log("⚠️ Replanification: suppression event_id échouée: " . $e2->getMessage());
                            }
                        }

                        // Méthode 2 (fallback) : recherche par date/heure dans Google Calendar
                        if (!$replan_deleted) {
                            preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $old_rdv, $old_rdv_matches);
                            if (count($old_rdv_matches) === 3) {
                                $old_search_start = \DateTime::createFromFormat('d/m/Y H:i', $old_rdv_matches[1] . ' ' . $old_rdv_matches[2], new \DateTimeZone('Europe/Paris'));
                                $old_search_end   = clone $old_search_start;
                                $old_search_end->modify('+1 hour');

                                $old_events = $replan_service->events->listEvents($targetCalendarId, [
                                    'timeMin'      => $old_search_start->format(\DateTime::RFC3339),
                                    'timeMax'      => $old_search_end->format(\DateTime::RFC3339),
                                    'singleEvents' => true,
                                ]);

                                $old_client_name = trim(explode('(RDV:', $old_booking['name'])[0]);
                                foreach ($old_events->getItems() as $old_evt) {
                                    if (stripos($old_evt->getSummary(), $old_client_name) !== false) {
                                        $replan_service->events->delete($targetCalendarId, $old_evt->getId());
                                        error_log("✅ Replanification fallback: ancien événement supprimé: " . $old_evt->getId() . " pour $old_client_name");
                                        $replan_deleted = true;
                                        break;
                                    }
                                }

                                if (!$replan_deleted) {
                                    error_log("⚠️ Replanification: aucun événement trouvé pour '$old_client_name' le " . $old_rdv_matches[1] . " à " . $old_rdv_matches[2]);
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    error_log("⚠️ Replanification: Erreur Google Calendar: " . $e->getMessage());
                }
            }

            // Supprimer l'ancien RDV de la base de données
            $del_old = $database->prepare("DELETE FROM am_free WHERE email = ? AND name LIKE ?");
            $del_old->execute([$email, $search_old]);
        }
    }

    if (empty($error) && empty($error_message)) {
        $is_second_session = false; // Variable pour compatibilité (fonctionnalité désactivée)
        $city = $row_center_contact['city'];
        $rawEmailCenter = isset($row_center_contact['email']) ? trim(str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', (string) $row_center_contact['email'])) : '';
        $email_center = $rawEmailCenter !== '' ? $rawEmailCenter : 'claude@alesiaminceur.com';
        $reference = 'AQ' . date('dmhis');
        $input_name_db = ($date_heure) ? $input_nom_complet . " (RDV: " . $date_heure . ")" : $input_nom_complet;

        // Mandelieu et Vallauris utilisent le planning de Cannes
        $center_id_db = in_array((int)$center_id, [347, 349]) ? 305 : $center_id;

        try {
            // A. Vérification double inscription pour Cannes/Mandelieu/Vallauris
            $is_double_booking = false;
            $previous_booking_date = '';
            
            if (in_array((int)$center_id, [305, 347, 349])) {
                // Récupérer la dernière séance (RDV) existante pour cet email
                $check_previous = $database->prepare("SELECT name FROM am_free WHERE email = ? AND name LIKE '%(RDV:%' ORDER BY id DESC LIMIT 1");
                $check_previous->execute([$email]);
                $previous = $check_previous->fetch();
                
                if ($previous) {
                    $is_double_booking = true;
                    // Extraire la date du champ name : "Nom (RDV: jj/mm/aaaa à hh:mm)"
                    $parts = explode('(RDV:', $previous['name']);
                    if (isset($parts[1])) {
                        $previous_booking_date = trim(str_replace(')', '', $parts[1]));
                    }
                }
            }
            
            // B. Enregistrement Table am_free
            $add_free = $database->prepare("INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $add_free->execute(array($reference, $center_id_db, 3, $input_name_db, $email, $tel, $segment));

            // Récupérer l'ID du booking de manière fiable (lastInsertId + fallback SELECT)
            $new_booking_id = (int)$database->lastInsertId();
            if (!$new_booking_id) {
                $stmt_bid = $database->prepare("SELECT id FROM am_free WHERE email = ? AND reference = ? ORDER BY id DESC LIMIT 1");
                $stmt_bid->execute([$email, $reference]);
                $row_bid = $stmt_bid->fetch();
                $new_booking_id = $row_bid ? (int)$row_bid['id'] : 0;
            }

            // B.2 Synchronisation immédiate Google Calendar (Cannes, Mandelieu, Vallauris)
            if ($date_heure && in_array((int)$center_id, [305, 347, 349]) && $new_booking_id) {
                try {
                    if (!class_exists('Google\Client')) {
                        if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                            require_once __DIR__ . '/vendor/autoload.php';
                        }
                    }
                    // Charger les variables d'env ET générer google_key.json si nécessaire
                    if (!function_exists('generateGoogleKeyFile')) {
                        require_once __DIR__ . '/load_env.php';
                    }

                    $keyFile = __DIR__ . '/google_key.json';
                    if (!file_exists($keyFile)) {
                        generateGoogleKeyFile();
                    }
                    if (!file_exists($keyFile)) {
                        throw new \Exception("google_key.json manquant même après generateGoogleKeyFile()");
                    }

                    $gc_client = new Google\Client();
                    $gc_client->setAuthConfig($keyFile);
                    $gc_client->addScope(Google\Service\Calendar::CALENDAR);
                    $gc_service = new Google\Service\Calendar($gc_client);

                    // Récupérer l'adresse du centre
                    $stmt_gc = $database->prepare("SELECT address FROM am_centers WHERE id = ?");
                    $stmt_gc->execute([$center_id_db]);
                    $gc_center = $stmt_gc->fetch();
                    $gc_location = $gc_center['address'] ?? '60 Avenue du Dr Raymond Picaud, 06150 Cannes';

                    // Parser la date/heure (format "Lundi 16/02/2026 à 10:15 (AQUAVELO)" — tolère 18h30, espaces insécables)
                    require_once __DIR__ . '/google_calendar_rdv_helpers.php';
                    $gcParsed = aquavelo_gc_parse_rdv_from_name($date_heure);
                    if ($gcParsed === null && !empty($input_name_db)) {
                        $gcParsed = aquavelo_gc_parse_rdv_from_name($input_name_db);
                    }
                    if ($gcParsed !== null) {
                        $rdv_start = $gcParsed['start'];
                        $rdv_end   = clone $rdv_start;
                        $rdv_end->modify('+45 minutes');

                        $gc_event = new Google\Service\Calendar\Event([
                            'summary'     => '🏊 ' . $input_nom_complet . ' - ' . $city,
                            'location'    => $gc_location,
                            'description' => "Client: $input_nom_complet\nEmail: $email\nTél: $tel\nCentre: $city\nID: $new_booking_id",
                            'start' => [
                                'dateTime' => $rdv_start->format(\DateTime::RFC3339),
                                'timeZone' => 'Europe/Paris',
                            ],
                            'end' => [
                                'dateTime' => $rdv_end->format(\DateTime::RFC3339),
                                'timeZone' => 'Europe/Paris',
                            ],
                        ]);

                        $createdEvent = $gc_service->events->insert('aqua.cannes@gmail.com', $gc_event);
                        $googleEventId = $createdEvent->getId();

                        // Sauvegarder l'ID Google Calendar en base de données
                        $upd = $database->prepare("UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?");
                        $upd->execute([$googleEventId, $new_booking_id]);

                        error_log("✅ Google Calendar: RDV créé (ID:$new_booking_id) pour $input_nom_complet ($city) - Event: $googleEventId - Rows updated: " . $upd->rowCount());
                    } else {
                        error_log("⚠️ Google Calendar: impossible de parser la date (date_heure='$date_heure', name_db='" . substr($input_name_db, 0, 200) . "') booking_id=$new_booking_id");
                    }
                } catch (\Exception $e) {
                    // Ne pas bloquer la réservation si Google Calendar échoue — la ligne reste google_sync=0, le cron / bouton admin peuvent rattraper
                    error_log("⚠️ Google Calendar sync échouée pour ID $new_booking_id: " . $e->getMessage() . " | date_heure=$date_heure | center=$center_id | trace=" . $e->getFile() . ':' . $e->getLine());
                }
            }

            // C. Enregistrement Table client
            $check_client = $database->prepare("SELECT id FROM client WHERE email = ?");
            $check_client->execute([$email]);
            if ($check_client->rowCount() == 0) {
                $name_parts = explode(' ', $input_nom_complet, 2);
                $prenom_db = $name_parts[0];
                $nom_db = isset($name_parts[1]) ? $name_parts[1] : $name_parts[0];
                if (count($name_parts) == 1) $prenom_db = "-";
                $add_client = $database->prepare("INSERT INTO client (nom, prenom, tel, email, ville) VALUES (?, ?, ?, ?, ?)");
                $add_client->execute([$nom_db, $prenom_db, $tel, $email, $city]);
            }

            // D. NOTIFICATIONS (Email et Telegram)
            
            // 1. Alerte Telegram pour double inscription (Cannes/Mandelieu/Vallauris)
            if ($is_double_booking) {
                $alert_msg = "<b>🚨 ALERTE DOUBLE INSCRIPTION - $city</b>\n" . 
                             "👤 $input_nom_complet\n" . 
                             "📧 $email\n" .
                             "📞 $tel\n" .
                             "⚠️ Cette personne s'inscrit pour une 2ème séance d'essai !\n";
                if (!empty($previous_booking_date)) {
                    $alert_msg .= "📅 1ère séance : $previous_booking_date";
                }
                sendTelegram($alert_msg);
            }
            
            // 2. Détermination du message Telegram normal
            $planning_centers = [305, 347, 349, 343, 253]; // Cannes, Mandelieu, Vallauris, Mérignac, Antibes
            if (in_array((int)$center_id, $planning_centers)) {
                if ($segment == 'calendrier-cannes') {
                    // Étape 2 : Le rendez-vous vient d'être pris
                    $tg_msg = "<b>✅ RDV CONFIRMÉ - $city</b>\n" . 
                              "👤 $input_nom_complet\n" . 
                              "📧 $email\n" .
                              "📞 $tel\n" . 
                              "🗓️ $date_heure";
                    if ($rescheduling_alert) {
                        $tg_msg = "<b>🔄 REPLANIFICATION - $city</b>\n" . 
                                  "👤 $input_nom_complet\n" . 
                                  "📧 $email\n" .
                                  "📞 $tel\n" . 
                                  "🗓️ Nouveau : $date_heure\n" .
                                  "❌ Ancien : $old_rdv";
                    }
                } else {
                    // Étape 1 : Inscription au formulaire (avant planning)
                    $tg_msg = "<b>🎁 NOUVEAU PROSPECT - $city</b>\n" . 
                              "👤 $input_nom_complet\n" . 
                              "📧 $email\n" .
                              "📞 $tel";
                }
                sendTelegram($tg_msg);
            } elseif ((int)$center_id === 332) {
                // Valbonne : pas dans le planning Telegram Cannes — alerte équipe (dirigeant ne reçoit pas toujours l’email admin)
                if ($segment == 'calendrier-cannes') {
                    $tg_msg = "<b>✅ RDV CONFIRMÉ - $city</b>\n" .
                              "👤 $input_nom_complet\n" .
                              "📧 $email\n" .
                              "📞 $tel\n" .
                              "🗓️ $date_heure";
                    if ($rescheduling_alert) {
                        $tg_msg = "<b>🔄 REPLANIFICATION - $city</b>\n" .
                                  "👤 $input_nom_complet\n" .
                                  "📧 $email\n" .
                                  "📞 $tel\n" .
                                  "🗓️ Nouveau : $date_heure\n" .
                                  "❌ Ancien : $old_rdv";
                    }
                } else {
                    $tg_msg = "<b>🎁 NOUVEAU PROSPECT - $city</b>\n" .
                              "👤 $input_nom_complet\n" .
                              "📧 $email\n" .
                              "📞 $tel";
                }
                sendTelegram($tg_msg);
            }

            // 3. Envoi des Emails
            if (!empty($settings['mjusername'])) {
                // Garde stricte : ne jamais envoyer si nom ou email sont vides
                if (empty($input_nom_complet) || empty($email)) {
                    error_log("⚠️ Email non envoyé : nom ou email manquant. nom='$input_nom_complet' email='$email'");
                } else {
                try {

                    // Nice (179), Collioure (272), Valbonne (332) + planning 5 centres : expéditeur Mailjet vérifié (claude@…), meilleure délivrabilité
                    $nice_style_email_center_ids = [179, 272, 332, 305, 347, 349, 343, 253];
                    $use_nice_style_email = in_array((int)$center_id, $nice_style_email_center_ids, true);

                    // Email pour l'ADMIN (dirigeant) : une enveloppe SMTP par adresse valide (évite qu'un To invalide bloque tout le lot)
                    $directorEmails = aquavelo_collect_center_director_emails($rawEmailCenter);
                    if ($directorEmails === [] && $rawEmailCenter !== '') {
                        $hadParsable = false;
                        foreach (aquavelo_recipient_raw_parts($rawEmailCenter) as $admPart) {
                            if (aquavelo_parse_recipient_email($admPart) !== '') {
                                $hadParsable = true;
                                break;
                            }
                        }
                        if (!$hadParsable) {
                            error_log("Centre id=$center_id ($city): email centre present mais non reconnu (am_centers.email).");
                        } else {
                            error_log("Centre id=$center_id ($city): uniquement adresse(s) Google Agenda dans am_centers.email — repli notification admin.");
                        }
                    }

                    $fbCcRaw = trim((string) ($settings['prospect_admin_fallback_email'] ?? ''));
                    if ($fbCcRaw === '') {
                        $fbCcRaw = 'directionalesiaminceur@gmail.com';
                    }
                    $fbCc = aquavelo_parse_recipient_email($fbCcRaw);
                    if ($fbCc !== '' && aquavelo_is_google_calendar_address($fbCc)) {
                        $fbCc = '';
                    }

                    $date_now = date('d-m-Y H:i:s');

                    if ($segment == 'calendrier-cannes') {
                        $subject_admin = "✅ RDV CONFIRMÉ : $city - $input_nom_complet";
                        if ($rescheduling_alert) {
                            $subject_admin = "🔄 REPLANIFICATION : $city - $input_nom_complet";
                        }
                        $body_admin = "<h3>" . ($rescheduling_alert ? "🔄 Replanification de séance" : "Séance confirmée") . "</h3>
                                      <b>Nom :</b> $input_nom_complet<br>
                                      <b>Email :</b> $email<br>
                                      <b>Tél :</b> $tel<br>
                                      <b>Centre :</b> $city<br>
                                      <b>RDV choisi :</b> $date_heure";
                        if ($rescheduling_alert) {
                            $body_admin .= "<br><br><b>Ancien RDV annulé :</b> " . htmlspecialchars($old_rdv);
                        }
                    } else {
                        $subject_admin = "Nouveau contact $city - $input_nom_complet";
                        if ($is_second_session) {
                            $subject_admin = "⚠️ ALERTE : Tentative de 2ème séance - $input_nom_complet";
                        }
                        $body_admin = "Bonjour,<br><br>" .
                                      (($is_second_session) ? "<p style='color:red; font-weight:bold;'>⚠️ ATTENTION : CE CLIENT A DÉJÀ RÉSERVÉ UNE SÉANCE AUPARAVANT</p>" : "") .
                                      "<b>Nom :</b> " . htmlspecialchars($input_nom_complet) . "<br>
                                      <b>Adresse électronique :</b> " . htmlspecialchars($email) . "<br>
                                      <b>Téléphone :</b> " . htmlspecialchars($tel) . "<br><br>
                                      La personne ci-dessus a commandé une séance découverte gratuite ainsi qu'un bilan minceur dans votre centre.<br>
                                      Nous vous invitons à la contacter pour prendre rendez-vous.<br><br>
                                      Cordialement,<br>
                                      L'équipe Aquavelo<br><br>
                                      <small>(Demande effectuée à partir du site aquavelo.com, le $date_now)</small>";
                    }

                    $adminAddrLine = static function (array $rows): string {
                        $out = [];
                        foreach ($rows as $row) {
                            if (!empty($row[0])) {
                                $out[] = (string) $row[0];
                            }
                        }

                        return implode(', ', $out);
                    };

                    $addDirectionCcIfMissing = static function (PHPMailer $mail, string $fbCcParsed): void {
                        if ($fbCcParsed === '' || !filter_var($fbCcParsed, FILTER_VALIDATE_EMAIL)) {
                            return;
                        }
                        if (strtolower($fbCcParsed) === 'aqua.cannes@gmail.com') {
                            return;
                        }
                        if (aquavelo_is_google_calendar_address($fbCcParsed)) {
                            return;
                        }
                        $fbSeen = [];
                        foreach (array_merge($mail->getToAddresses(), $mail->getCcAddresses(), $mail->getBccAddresses()) as $fbRow) {
                            if (!empty($fbRow[0])) {
                                $fbSeen[strtolower((string) $fbRow[0])] = true;
                            }
                        }
                        if (empty($fbSeen[strtolower($fbCcParsed)])) {
                            $mail->addCC($fbCcParsed);
                        }
                    };

                    $sendOneAdminMail = static function (PHPMailer $mail) use ($city, $center_id, $adminAddrLine): void {
                        $adminTo = array_map(static function ($row) {
                            return $row[0] ?? '';
                        }, $mail->getToAddresses());
                        $adminTo = array_values(array_filter($adminTo));
                        if ($adminTo === []) {
                            error_log("⚠️ Email admin $city : aucun destinataire To (centre_id=$center_id) — envoi ignoré.");

                            return;
                        }
                        $ccLog = $adminAddrLine($mail->getCcAddresses());
                        $bccLog = $adminAddrLine($mail->getBccAddresses());
                        $logDest = 'To: ' . implode(', ', $adminTo);
                        if ($ccLog !== '') {
                            $logDest .= ' | Cc: ' . $ccLog;
                        }
                        if ($bccLog !== '') {
                            $logDest .= ' | Bcc: ' . $bccLog;
                        }
                        $mail->send();
                        error_log("Email admin envoyé $city ($logDest) | message_id=" . $mail->getLastMessageID());
                    };

                    if ($directorEmails !== []) {
                        foreach ($directorEmails as $dirTo) {
                            $mail = aquavelo_create_mailer($settings, $city);
                            $mail->addAddress($dirTo);
                            if ($use_nice_style_email && !aquavelo_is_prospect_admin_technical_to($dirTo)) {
                                $mail->addBCC('claude@alesiaminceur.com');
                            }
                            //$addDirectionCcIfMissing($mail, $fbCc);
                            $mail->addReplyTo($email, $input_nom_complet);
                            $mail->Subject = $subject_admin;
                            $mail->Body = $body_admin;
                            try {
                                $sendOneAdminMail($mail);
                            } catch (Exception $adminEmailException) {
                                error_log("Erreur Email admin $city (dir: $dirTo): " . $mail->ErrorInfo);
                            }
                        }
                    } else {
                        $mail = aquavelo_create_mailer($settings, $city);
                        $adminRecipientsAdded = 0;
                        $adminHasNonClaudeTo = false;
                        $fbAdm = aquavelo_parse_recipient_email($fbCcRaw);
                        if ($fbAdm !== '' && !aquavelo_is_google_calendar_address($fbAdm) && !aquavelo_is_prospect_admin_technical_to($fbAdm)) {
                            $mail->addAddress($fbAdm);
                            $adminRecipientsAdded++;
                            $adminHasNonClaudeTo = true;
                        }
                        if ($adminRecipientsAdded === 0) {
                            aquavelo_add_mail_recipients($mail, $email_center, 'claude@alesiaminceur.com');
                            foreach ($mail->getToAddresses() as $admRow) {
                                if (!empty($admRow[0]) && !aquavelo_is_prospect_admin_technical_to((string) $admRow[0])) {
                                    $adminHasNonClaudeTo = true;
                                    break;
                                }
                            }
                            $adminRecipientsAdded = count($mail->getToAddresses());
                        }
                        // Si tous les To sont uniquement l’adresse technique, ajouter le repli direction en To (sinon seul Cc reçoit le détail prospect)
                        $hasHumanTo = false;
                        foreach ($mail->getToAddresses() as $admRow) {
                            if (!empty($admRow[0]) && !aquavelo_is_prospect_admin_technical_to((string) $admRow[0])) {
                                $hasHumanTo = true;
                                break;
                            }
                        }
                        if (!$hasHumanTo && $mail->getToAddresses() !== []) {
                            $fbHuman = aquavelo_parse_recipient_email($fbCcRaw);
                            if ($fbHuman !== '' && !aquavelo_is_google_calendar_address($fbHuman)
                                && !aquavelo_is_prospect_admin_technical_to($fbHuman)
                                && filter_var($fbHuman, FILTER_VALIDATE_EMAIL)) {
                                $toLower = [];
                                foreach ($mail->getToAddresses() as $admRow) {
                                    if (!empty($admRow[0])) {
                                        $toLower[] = strtolower((string) $admRow[0]);
                                    }
                                }
                                if (!in_array(strtolower($fbHuman), $toLower, true)) {
                                    $mail->addAddress($fbHuman);
                                    $adminHasNonClaudeTo = true;
                                }
                            }
                        }
                        if ($use_nice_style_email && $adminHasNonClaudeTo) {
                            $mail->addBCC('claude@alesiaminceur.com');
                        }
                        //$addDirectionCcIfMissing($mail, $fbCc);
                        $mail->addReplyTo($email, $input_nom_complet);
                        $mail->Subject = $subject_admin;
                        $mail->Body = $body_admin;
                        try {
                            $sendOneAdminMail($mail);
                        } catch (Exception $adminEmailException) {
                            error_log("Erreur Email admin $city: " . $mail->ErrorInfo);
                        }
                    }

                    $clientFromEmail = $use_nice_style_email ? 'claude@alesiaminceur.com' : 'contact@aquavelo.com';

                    // 3. Email de bienvenue pour les centres HORS PLANNING (Cannes, Mandelieu, Vallauris, Mérignac, Antibes gérés plus bas)
                    if (!in_array((int)$center_id, [305, 347, 349, 343, 253]) && !$date_heure) {
                        $clientMail = aquavelo_create_mailer($settings, $city, $clientFromEmail, 'Aquavelo');
                        $clientMail->addAddress($email);
                        if ($use_nice_style_email) {
                            $clientMail->addBCC('claude@alesiaminceur.com');
                            if (strtolower($email) !== 'claude@alesiaminceur.com') {
                                $clientMail->addAddress('claude@alesiaminceur.com', 'Controle Aquavelo');
                            }
                        }
                        $replyCenterEmail = aquavelo_first_human_center_reply_email($rawEmailCenter);
                        if ($replyCenterEmail === '') {
                            $replyCenterEmail = aquavelo_parse_recipient_email($email_center);
                        }
                        if (filter_var($replyCenterEmail, FILTER_VALIDATE_EMAIL) && !aquavelo_is_google_calendar_address($replyCenterEmail)) {
                            $clientMail->addReplyTo($replyCenterEmail, 'Aquavelo ' . $city);
                        }

                        // Centres pas encore ouverts : email spécifique
                        $is_coming_soon_center = stripos($city, 'Aix') !== false;

                        if ($is_coming_soon_center) {
                            $clientMail->Subject = "Votre demande - Centre Aquavelo $city";
                            aquavelo_set_mail_body($clientMail, "Bonjour " . htmlspecialchars($input_nom_complet) . ",<br><br>
                                          Nous sommes ravis de votre intérêt pour une séance découverte gratuite au centre Aquavélo de <b>$city</b>, cependant le centre n'est pas encore ouvert sur $city, nous reviendrons vers vous lors de son ouverture.<br><br>
                                          Cordialement,<br>
                                          L'équipe Aquavélo<br>
                                          <a href='https://www.aquavelo.com'>www.aquavelo.com</a>");
                        } else {
                            $clientMail->Subject = "Votre séance découverte gratuite à Aquavelo $city";

                            $rdv_text = "Prenez dès maintenant rendez-vous directement en appelant le <b>" . $row_center_contact['phone'] . "</b>.";
                            if ($center_id == 253) {
                                $rdv_text = "Prenez dès maintenant rendez-vous directement sur <a href='https://calendly.com/aquavelo-antibes'>https://calendly.com/aquavelo-antibes</a>, ou en appelant le <b>" . $row_center_contact['phone'] . "</b>.";
                            }

                            aquavelo_set_mail_body($clientMail, "Bonjour " . htmlspecialchars($input_nom_complet) . ",<br><br>
                                          Nous sommes ravis de vous offrir une séance découverte gratuite au centre Aquavélo de <b>$city</b>.<br><br>
                                          Lors de votre visite, vous profiterez d'un cours d'aquabiking coaché, encadré par nos professeurs de sport diplômés. Nous commencerons par un bilan personnalisé pour mieux comprendre vos besoins et vous aider à atteindre vos objectifs forme et bien-être.<br><br>
                                          $rdv_text<br><br>
                                          <b>N'oubliez pas de venir équipé(e) avec :</b><br>
                                          ✅ Votre maillot de bain,<br>
                                          ✅ Une serviette,<br>
                                          ✅ Un gel douche,<br>
                                          ✅ Une bouteille d'eau,<br>
                                          ✅ Et des chaussures adaptées à l'aquabiking.<br><br>
                                          <b>Adresse :</b> " . $row_center_contact['address'] . ", " . $city . "<br><br>
                                          <i>*Offre non cumulable. Réservez vite, les places sont limitées.</i><br><br>
                                          Cordialement,<br>
                                          L'équipe Aquavélo<br>
                                          <a href='https://www.aquavelo.com'>www.aquavelo.com</a>");
                        }
                        try {
                            $clientMail->send();
                            error_log("Email client envoyé $city: $email | message_id=" . $clientMail->getLastMessageID());
                        } catch (Exception $clientEmailException) {
                            $clientError = $clientMail->ErrorInfo ?: $clientEmailException->getMessage();
                            error_log("Erreur Email client $city: " . $clientError);
                            if ($use_nice_style_email && function_exists('sendTelegram')) {
                                sendTelegram("<b>⚠️ Email client $city non envoyé</b>\n📧 $email\nErreur: " . htmlspecialchars($clientError));
                            }
                        }
                    }

                    // Email pour le CLIENT (RDV CONFIRMÉ sur Planning)
                    if ($date_heure) {
                        $clientMail = aquavelo_create_mailer($settings, $city, $clientFromEmail, 'Aquavelo');
                        $clientMail->addAddress($email);
                        if ($use_nice_style_email) {
                            $clientMail->addBCC('claude@alesiaminceur.com');
                            if (strtolower($email) !== 'claude@alesiaminceur.com') {
                                $clientMail->addAddress('claude@alesiaminceur.com', 'Controle Aquavelo');
                            }
                        }
                        $replyCenterEmail = aquavelo_first_human_center_reply_email($rawEmailCenter);
                        if ($replyCenterEmail === '') {
                            $replyCenterEmail = aquavelo_parse_recipient_email($email_center);
                        }
                        if (filter_var($replyCenterEmail, FILTER_VALIDATE_EMAIL) && !aquavelo_is_google_calendar_address($replyCenterEmail)) {
                            $clientMail->addReplyTo($replyCenterEmail, 'Aquavelo ' . $city);
                        }
                        $clientMail->Subject = "Confirmation de votre séance à Aquavelo $city";
                        $rdv_formatted = str_replace(['(', ')'], ['pour un cours ', ''], $date_heure);
                        
                        // Pour Cannes/Mandelieu/Vallauris, utiliser les coordonnées de Cannes
                        if (in_array((int)$center_id, [305, 347, 349])) {
                            $stmt_cannes = $database->prepare('SELECT address, city, phone FROM am_centers WHERE id = 305');
                            $stmt_cannes->execute();
                            $cannes_info = $stmt_cannes->fetch();
                            if ($cannes_info && !empty($cannes_info['address'])) {
                                $lieu_rdv = $cannes_info['address'] . ", " . ($cannes_info['city'] ?? 'Cannes');
                                $tel_rdv = $cannes_info['phone'] ?? '';
                            } else {
                                $lieu_rdv = '60 avenue du Docteur Raymond Picaud, Cannes';
                                $tel_rdv = '04 93 93 05 65';
                            }
                        } else {
                            // Infos centre pour l'email
                            $lieu_rdv = $row_center_contact['address'] . ", " . $row_center_contact['city'];
                            $tel_rdv = $row_center_contact['phone'];
                        }

                        // URLs pour Annuler / Modifier
                        $url_annuler = "https://www.aquavelo.com/index.php?p=annulation&email=" . urlencode($email) . "&rdv=" . urlencode($date_heure) . "&city=" . urlencode($city);
                        $url_modifier = "https://www.aquavelo.com/index.php?p=calendrier_cannes&center=" . $center_id . "&nom=" . urlencode($input_nom_complet) . "&email=" . urlencode($email) . "&phone=" . urlencode($tel) . "&old_rdv=" . urlencode($date_heure);

                        $signature = in_array((int)$center_id, [305, 347, 349]) ? "Cordialement Claude" : "Cordialement,<br>Aquavelo $city";
                        aquavelo_set_mail_body($clientMail, "Bonjour $input_nom_complet,<br><br>Votre séance est confirmée pour le <b>$rdv_formatted</b>.<br>
                                      Lieu : $lieu_rdv<br>Tél : $tel_rdv<br><br>
                                      <b>Important :</b> Merci d'arriver 15 minutes avant le début du cours.<br><br>
                                      <b>🎒 N'oubliez pas de venir équipé(e) avec :</b><br>
                                      ✅ Votre maillot de bain,<br>
                                      ✅ Une serviette,<br>
                                      ✅ Un gel douche,<br>
                                      ✅ Une bouteille d'eau,<br>
                                      ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                                      À très bientôt ! $signature<br><br><hr style='border:none; border-top:1px solid #eee; margin:20px 0;'><p style='color:#999; font-size:0.9rem;'>Un contretemps ?</p>
                                      <table cellspacing='0' cellpadding='0'><tr>
                                      <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_annuler' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Annuler</a></td>
                                      <td width='10'></td>
                                      <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_modifier' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Modifier</a></td>
                                      </tr></table>");
                        try {
                            $clientMail->send();
                            error_log("Email confirmation client envoyé $city: $email | message_id=" . $clientMail->getLastMessageID());
                        } catch (Exception $clientEmailException) {
                            $clientError = $clientMail->ErrorInfo ?: $clientEmailException->getMessage();
                            error_log("Erreur Email confirmation client $city: " . $clientError);
                            if ($use_nice_style_email && function_exists('sendTelegram')) {
                                sendTelegram("<b>⚠️ Email confirmation $city non envoyé</b>\n📧 $email\nErreur: " . htmlspecialchars($clientError));
                            }
                        }
                    }
                } catch (Exception $e) {
                    error_log("Erreur Email $city: " . $e->getMessage());
                }
                } // fin garde stricte nom/email
            }

            // D. REDIRECTION
            if ($segment == 'calendrier-cannes') {
                // Mandelieu et Vallauris redirigent vers le planning de Cannes
                $center_id_redirect = in_array((int)$center_id, [347, 349]) ? 305 : $center_id;
                $url = BASE_PATH . "index.php?p=merci_rdv&center=" . $center_id_redirect . "&rdv=" . urlencode($date_heure) . "&nom=" . urlencode($input_nom_complet) . "&email=" . urlencode($email) . "&phone=" . urlencode($tel) . "&city=" . urlencode($city);
                echo "<script>window.location.replace('$url');</script>";
                exit;
            } elseif (in_array((int)$center_id, [305, 347, 349, 343, 253])) {
                // Mandelieu et Vallauris redirigent vers le planning de Cannes
                $center_id_redirect = in_array((int)$center_id, [347, 349]) ? 305 : $center_id;
                $url = BASE_PATH . "index.php?p=calendrier_cannes&center=" . $center_id_redirect . "&nom=" . urlencode($input_nom_complet) . "&email=" . urlencode($email) . "&phone=" . urlencode($tel);
                echo "<script>window.location.replace('$url');</script>";
                exit;
            } else {
                $url = BASE_PATH . "index.php?p=free&success=1&cid=$center_id";
                echo "<script>window.location.href = '$url';</script>";
                exit;
            }
        } catch (Exception $e) { $error_message = "Erreur technique : " . $e->getMessage(); }
    }
}

if (isset($_GET['success'])) {
    $success_message = "Votre demande a bien été envoyée !";
    if (isset($_GET['cid'])) {
        $stmt = $database->prepare("SELECT city, phone FROM am_centers WHERE id = ?");
        $stmt->execute([intval($_GET['cid'])]);
        $cinfo = $stmt->fetch();
        if ($cinfo) $success_message .= "<br><br>Veuillez appeler le centre de <b>" . $cinfo['city'] . "</b> au <b>" . $cinfo['phone'] . "</b> pour confirmer votre rendez-vous.";
    }
}
?>

<section class="content-area bg1" style="padding: 40px 0 100px 0;">
  <div class="container">
    <?php if ($error_message): ?>
      <div class="alert alert-danger" style="max-width: 600px; margin: 0 auto 20px; border-radius: 10px;"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
      <div class="alert alert-success" style="max-width: 600px; margin: 40px auto; border-radius: 15px; background: #d4edda; color: #155724; padding: 40px; text-align: center;">
        <i class="fa fa-check-circle" style="font-size: 4rem; display: block; margin-bottom: 20px;"></i>
        <h2>Merci !</h2><p><?= $success_message ?></p>
        <div style="margin-top: 30px;"><a href="index.php" class="btn btn-primary">RETOUR À L'ACCUEIL</a></div>
      </div>
    <?php else: ?>
      <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #00a8cc; margin-bottom: 25px;">Réservez votre séance gratuite</h2>
        <form role="form" method="POST" action="<?= BASE_PATH ?>index.php?p=free" id="mainFreeForm">
            <div style="margin-bottom: 15px;"><label>Centre *</label>
                <select name="center" id="centerSelect" required style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;">
                    <option value="">-- Choisissez un centre --</option>
                    <?php foreach ($centers_list_d as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['city'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;"><label>Nom et Prénom *</label><input type="text" name="nom" id="inputNom" required minlength="2" style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;"></div>
            <div style="margin-bottom: 15px;"><label>Email *</label><input type="email" name="email" id="inputEmail" required style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;"></div>
            <div style="margin-bottom: 25px;"><label>Téléphone *</label><input type="tel" name="phone" id="inputPhone" required minlength="8" pattern="[0-9+\s\-]{8,}" style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;"></div>
            
            <!-- Captcha anti-spam pour Aix-en-Provence -->
            <div id="captchaField" style="margin-bottom: 25px; display: none; padding: 20px; background: #f8f9fa; border-radius: 5px; border: 2px solid #00a8cc;">
                <label style="font-weight: bold; color: #00a8cc;">🛡️ Question anti-spam : 2 + 1 = ? *</label>
                <input type="number" name="captcha" id="captchaInput" style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px; margin-top: 10px;" placeholder="Entrez votre réponse">
            </div>
            <button type="submit" id="submitBtnText" style="width: 100%; height: 60px; background: #00a8cc; color: white; border: none; border-radius: 5px; font-weight: bold; font-size: 1.1rem; cursor: pointer;">RECEVOIR MON BON GRATUIT</button>
        </form>
      </div>
      <script>
      const centerSelect = document.getElementById('centerSelect');
      const submitBtnText = document.getElementById('submitBtnText');
      const captchaField = document.getElementById('captchaField');
      const captchaInput = document.getElementById('captchaInput');
      
      document.getElementById('mainFreeForm').addEventListener('submit', function(e) {
          var nom = document.getElementById('inputNom').value.trim();
          var email = document.getElementById('inputEmail').value.trim();
          var phone = document.getElementById('inputPhone').value.trim();
          var msgs = [];
          if (!nom) msgs.push('Veuillez saisir votre nom et prénom.');
          if (!email) msgs.push('Veuillez saisir votre adresse email.');
          if (!phone || phone.length < 8) msgs.push('Veuillez saisir un numéro de téléphone valide.');
          if (msgs.length > 0) { e.preventDefault(); alert(msgs.join('\n')); return false; }
      });

      centerSelect.addEventListener('change', function() {
          const centerId = parseInt(this.value);
          const centerText = this.options[this.selectedIndex].text;
          
          // Affichage du captcha pour Aix-en-Provence
          if (centerText && centerText.toLowerCase().includes('aix')) {
              captchaField.style.display = 'block';
              captchaInput.required = true;
          } else {
              captchaField.style.display = 'none';
              captchaInput.required = false;
              captchaInput.value = '';
          }
          
          // Changement du texte du bouton
          if ([305, 347, 349, 343].includes(centerId)) submitBtnText.innerText = "RÉSERVER MA SÉANCE OFFERTE";
          else submitBtnText.innerText = "RECEVOIR MON BON GRATUIT";
      });
      </script>
    <?php endif; ?>
  </div>
</section>
