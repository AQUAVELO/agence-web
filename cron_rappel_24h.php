<?php
/**
 * Script de rappel automatique 24h avant le RDV
 */

require_once '_settings.php';
require_once __DIR__ . '/Include/cron_email_helpers.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND reminder_sent = 0");
$stmt->execute();
$bookings = $stmt->fetchAll();

$now = new DateTime();
$count = 0;
$log = "[" . $now->format('Y-m-d H:i:s') . "] Lancement du Cron 24h\n";

foreach ($bookings as $booking) {
    $rdv_date = aquavelo_cron_parse_rdv_start($booking);
    
    if ($rdv_date) {
            $diff = $now->diff($rdv_date);
            $hours_until = ($diff->days * 24) + $diff->h;

            if ($hours_until >= 18 && $hours_until <= 30 && $rdv_date > $now) {
                try {
                    $center_id = (int) ($booking['center_id'] ?: 305);
                    $center_info = aquavelo_cron_lookup_center($database, $center_id);

                    $log .= "Tentative d'envoi à : " . $booking['email'] . " (RDV le " . $rdv_date->format('Y-m-d H:i') . ")\n";
                    $mail = aquavelo_cron_create_mailer($settings, $center_info);
                    $mail->addAddress($booking['email']);
                    
                    $mail->Subject = "Votre séance Aquavelo demain !";
                    
                    $rdv_brut = aquavelo_cron_rdv_label($booking);
                    $rdv_final = str_replace(['(', ')'], ['', ''], $rdv_brut);
                    $client_first_name = aquavelo_cron_client_first_name($booking);

                    $url_annuler = "https://www.aquavelo.com/index.php?p=annulation&email=" . urlencode($booking['email']) . "&rdv=" . urlencode($rdv_brut) . "&city=" . urlencode($center_info['city']);
                    $url_modifier = "https://www.aquavelo.com/index.php?p=calendrier_cannes&center=" . $center_id . "&nom=" . urlencode($booking['name']) . "&email=" . urlencode($booking['email']) . "&phone=" . urlencode($booking['phone']) . "&old_rdv=" . urlencode($rdv_brut);

                    $mail->Body = "Bonjour " . $client_first_name . ",<br><br>
                                  Votre séance d'essai est réservée :<br><br>
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
                                  À très bientôt ! " . aquavelo_cron_signature((int) $center_id, $center_info) . "<br><br>
                                  <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
                                  <p style='color:#999; font-size:0.9rem;'>Un contretemps ?</p>
                                  <table cellspacing='0' cellpadding='0'><tr>
                                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_annuler' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Annuler</a></td>
                                  <td width='10'></td>
                                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_modifier' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Modifier</a></td>
                                  </tr></table>";
                    
                    $mail->send();
                    $database->prepare("UPDATE am_free SET reminder_sent = 1 WHERE id = ?")->execute([$booking['id']]);
                    $count++;
                    $log .= "✅ Succès pour " . $booking['email'] . "\n";
                } catch (Exception $e) {
                    $log .= "❌ Erreur Mailer pour " . $booking['email'] . " : " . $mail->ErrorInfo . "\n";
                }
            } else {
                $log .= "Sauté : " . $booking['email'] . " (Hors fenêtre : " . $hours_until . "h avant)\n";
            }
    }
}
$log .= "Fin du Cron. Total envoyés : $count\n---\n";
file_put_contents('cron_log.txt', $log, FILE_APPEND);
echo "Rappels 24h envoyés : $count";
?>
