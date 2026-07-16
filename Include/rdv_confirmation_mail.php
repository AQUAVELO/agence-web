<?php
/**
 * Envoi de l'email de confirmation client pour un RDV séance d'essai (planning).
 */
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../google_calendar_rdv_helpers.php';

if (!function_exists('aquavelo_rdv_mail_parse_email')) {
    function aquavelo_rdv_mail_parse_email(string $raw): string
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

if (!function_exists('aquavelo_send_rdv_confirmation_to_client')) {
    /**
     * @param array{id:int|string,name:string,email:string,phone?:string,center_id:int|string} $booking
     * @return array{ok:bool,message:string,email:string}
     */
    function aquavelo_send_rdv_confirmation_to_client(PDO $database, array $settings, array $booking): array
    {
        $bookingId = (int) ($booking['id'] ?? 0);
        $nameField = (string) ($booking['name'] ?? '');
        $email = trim((string) ($booking['email'] ?? ''));
        $phone = trim((string) ($booking['phone'] ?? ''));
        $centerId = (int) ($booking['center_id'] ?? 0);

        if ($bookingId <= 0 || $nameField === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'message' => 'Données réservation invalides.', 'email' => $email];
        }

        if (stripos($nameField, '(RDV:') === false) {
            return ['ok' => false, 'message' => 'Ce booking n\'a pas de créneau RDV.', 'email' => $email];
        }

        $parsed = aquavelo_gc_resolve_rdv_datetime($booking);
        if ($parsed === null) {
            return ['ok' => false, 'message' => 'Impossible de lire la date/heure du RDV.', 'email' => $email];
        }

        $clientName = trim(explode('(RDV:', $nameField)[0]);
        $dateHeure = preg_match('/\(RDV:\s*(.+)\)\s*$/u', $nameField, $m) ? trim($m[1]) : '';

        if ($dateHeure === '') {
            $days = ['Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi', 'Sunday' => 'Dimanche'];
            $dayFr = $days[$parsed['start']->format('l')] ?? $parsed['start']->format('l');
            $dateHeure = $dayFr . ' ' . $parsed['date'] . ' à ' . $parsed['time'];
        }

        $stmtCenter = $database->prepare('SELECT city, address, phone, email FROM am_centers WHERE id = ?');
        $stmtCenter->execute([$centerId]);
        $center = $stmtCenter->fetch(PDO::FETCH_ASSOC);
        if (!$center) {
            return ['ok' => false, 'message' => 'Centre introuvable (id=' . $centerId . ').', 'email' => $email];
        }

        $city = (string) ($center['city'] ?? 'Cannes');
        $rawEmailCenter = trim(str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', (string) ($center['email'] ?? '')));

        if (empty($settings['mjusername'])) {
            return ['ok' => false, 'message' => 'Configuration Mailjet absente.', 'email' => $email];
        }

        $niceStyleIds = [179, 272, 332, 305, 347, 349, 343, 253];
        $useNiceStyle = in_array($centerId, $niceStyleIds, true);
        $clientFromEmail = $useNiceStyle ? 'claude@alesiaminceur.com' : 'contact@aquavelo.com';

        if (in_array($centerId, [305, 347, 349], true)) {
            $stmtCannes = $database->prepare('SELECT address, city, phone FROM am_centers WHERE id = 305');
            $stmtCannes->execute();
            $cannesInfo = $stmtCannes->fetch(PDO::FETCH_ASSOC);
            if ($cannesInfo && !empty($cannesInfo['address'])) {
                $lieuRdv = $cannesInfo['address'] . ', ' . ($cannesInfo['city'] ?? 'Cannes');
                $telRdv = $cannesInfo['phone'] ?? '';
            } else {
                $lieuRdv = '60 avenue du Docteur Raymond Picaud, Cannes';
                $telRdv = '04 93 93 05 65';
            }
        } else {
            $lieuRdv = ($center['address'] ?? '') . ', ' . $city;
            $telRdv = $center['phone'] ?? '';
        }

        $urlAnnuler = 'https://www.aquavelo.com/index.php?p=annulation&email=' . urlencode($email) . '&rdv=' . urlencode($dateHeure) . '&city=' . urlencode($city);
        $urlModifier = 'https://www.aquavelo.com/index.php?p=calendrier_cannes&center=' . $centerId . '&nom=' . urlencode($clientName) . '&email=' . urlencode($email) . '&phone=' . urlencode($phone) . '&old_rdv=' . urlencode($dateHeure);
        $signature = in_array($centerId, [305, 347, 349], true) ? 'Cordialement Claude' : 'Cordialement,<br>Aquavelo ' . $city;
        $rdvFormatted = str_replace(['(', ')'], ['pour un cours ', ''], $dateHeure);

        $clientMail = new PHPMailer(true);
        $clientMail->isSMTP();
        $clientMail->Host = $settings['mjhost'];
        $clientMail->SMTPAuth = true;
        $clientMail->Username = $settings['mjusername'];
        $clientMail->Password = $settings['mjpassword'];
        $clientMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $clientMail->Port = 587;
        $clientMail->CharSet = 'UTF-8';
        $clientMail->setFrom($clientFromEmail, 'Aquavelo ' . $city);
        $clientMail->isHTML(true);
        $clientMail->addAddress($email);

        if ($useNiceStyle) {
            $clientMail->addBCC('claude@alesiaminceur.com');
            if (strtolower($email) !== 'claude@alesiaminceur.com') {
                $clientMail->addAddress('claude@alesiaminceur.com', 'Controle Aquavelo');
            }
        }

        $replyCenterEmail = aquavelo_rdv_mail_parse_email($rawEmailCenter);
        if ($replyCenterEmail !== '') {
            $clientMail->addReplyTo($replyCenterEmail, 'Aquavelo ' . $city);
        }

        $clientMail->Subject = 'Confirmation de votre séance à Aquavelo ' . $city;
        $clientMail->Body = "Bonjour $clientName,<br><br>Votre séance est confirmée pour le <b>$rdvFormatted</b>.<br>
                  Lieu : $lieuRdv<br>Tél : $telRdv<br><br>
                  <b>Important :</b> Merci d'arriver 15 minutes avant le début du cours.<br><br>
                  <b>🎒 N'oubliez pas de venir équipé(e) avec :</b><br>
                  ✅ Votre maillot de bain,<br>
                  ✅ Une serviette,<br>
                  ✅ Un gel douche,<br>
                  ✅ Une bouteille d'eau,<br>
                  ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                  À très bientôt ! $signature<br><br><hr style='border:none; border-top:1px solid #eee; margin:20px 0;'><p style='color:#999; font-size:0.9rem;'>Un contretemps ?</p>
                  <table cellspacing='0' cellpadding='0'><tr>
                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$urlAnnuler' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Annuler</a></td>
                  <td width='10'></td>
                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$urlModifier' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Modifier</a></td>
                  </tr></table>";
        $clientMail->AltBody = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $clientMail->Body)), ENT_QUOTES, 'UTF-8')));

        try {
            $clientMail->send();
            $msgId = $clientMail->getLastMessageID();
            error_log("Email confirmation client renvoyé $city: $email | booking_id=$bookingId | message_id=$msgId");

            return ['ok' => true, 'message' => 'Email envoyé (message_id=' . $msgId . ').', 'email' => $email];
        } catch (Exception $e) {
            $err = $clientMail->ErrorInfo ?: $e->getMessage();
            error_log("Erreur renvoi confirmation client $city: $email | booking_id=$bookingId | $err");

            return ['ok' => false, 'message' => $err, 'email' => $email];
        }
    }
}
