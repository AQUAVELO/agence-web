<?php
/**
 * Script de rappel automatique 3h avant le RDV - VERSION AMELIOREE (MINUTES)
 */

require '_settings.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND reminder_3h_sent = 0");
$stmt->execute();
$bookings = $stmt->fetchAll();

$now = new DateTime();
$count = 0;

foreach ($bookings as $booking) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    
    if (count($matches) === 3) {
        $rdv_date = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
        
        if ($rdv_date) {
            // Calcul précis en minutes
            $diff = $now->diff($rdv_date);
            $total_minutes_until = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
            $is_future = ($rdv_date > $now);

            // Fenêtre d'envoi : 3h avant (150-210 min = 2h30 à 3h30 avant)
            // Centré sur 3h (180 min) avec une marge pour le cron horaire
            if ($is_future && $total_minutes_until >= 150 && $total_minutes_until <= 210) {
                try {
                    $center_id = $booking['center_id'] ?: 305;
                    $stmt_c = $database->prepare("SELECT city, address, phone FROM am_centers WHERE id = ?");
                    $stmt_c->execute([$center_id]);
                    $center_info = $stmt_c->fetch();
                    
                    if (!$center_info) {
                        $center_info = ['city' => 'Cannes', 'address' => '60 avenue du Docteur Raymond Picaud, Cannes', 'phone' => '04 93 93 05 65'];
                    }

                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $settings['mjhost'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $settings['mjusername'];
                    $mail->Password = $settings['mjpassword'];
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo ' . $center_info['city']);
                    $mail->addAddress($booking['email']);
                    if (!empty($center_info['email'])) {
                        $mail->addReplyTo($center_info['email'], 'Aquavelo ' . $center_info['city']);
                    }
                    $mail->isHTML(true);
                    
                    $mail->Subject = "Rappel de séance découverte";
                    
                    $rdv_brut = str_replace(['(RDV: ', ')'], ['', ''], substr($booking['name'], strpos($booking['name'], "(RDV:") + 6));
                    $rdv_final = str_replace(['(', ')'], ['', ''], $rdv_brut);
                    $client_first_name = explode(' ', trim(explode('(RDV:', $booking['name'])[0]))[0];

                    $url_annuler = "https://www.aquavelo.com/index.php?p=annulation&email=" . urlencode($booking['email']) . "&rdv=" . urlencode($rdv_brut) . "&city=" . urlencode($center_info['city']);
                    $url_modifier = "https://www.aquavelo.com/index.php?p=calendrier_cannes&center=" . $center_id . "&nom=" . urlencode($booking['name']) . "&email=" . urlencode($booking['email']) . "&phone=" . urlencode($booking['phone']) . "&old_rdv=" . urlencode($rdv_brut);

                    $mail->Body = "Bonjour " . $client_first_name . ",<br><br>
                                  Je vous rappelle votre rdv pour la séance découverte :<br><br>
                                  🗓️ <b>" . $rdv_final . "</b><br><br>
                                  Lieu : " . $center_info['address'] . "<br>
                                  Tél : " . $center_info['phone'] . "<br><br>
                                  Important : Merci d'arriver 15 minutes avant le début du cours.<br><br>
                                  <b>🎒 À prévoir pour votre séance :</b><br>
                                  ✅ Votre maillot de bain,<br>
                                  ✅ Une serviette,<br>
                                  ✅ Un gel douche,<br>
                                  ✅ Une bouteille d'eau,<br>
                                  ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                                  À très bientôt ! " . (in_array((int)$center_id, [305, 347, 349]) ? "Cordialement Claude" : "Cordialement,<br>Aquavelo " . $center_info['city']) . "<br><br>
                                  <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
                                  <p style='color:#999; font-size:0.9rem;'>Un contretemps ?</p>
                                  <table cellspacing='0' cellpadding='0'><tr>
                                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_annuler' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Annuler</a></td>
                                  <td width='10'></td>
                                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_modifier' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Modifier</a></td>
                                  </tr></table>";
                    
                    $mail->send();

                    // --- ENVOI SMS ---
                    if (!empty($booking['phone'])) {
                        $sms_text = "Bonjour " . $client_first_name . ", rappel de votre séance découverte Aquavelo aujourd’hui à " . $matches[2] . ". À très bientôt !";
                        sendSMS($booking['phone'], $sms_text);
                    }
                    // -----------------

                    $database->prepare("UPDATE am_free SET reminder_3h_sent = 1 WHERE id = ?")->execute([$booking['id']]);
                    $count++;
                } catch (Exception $e) {}
            }
        }
    }
}
echo "Rappels 3h envoyés : $count";
?>
