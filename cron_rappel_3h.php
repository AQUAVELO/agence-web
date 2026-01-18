<?php
/**
 * Script de rappel automatique 3h avant le RDV
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
            $diff = $now->diff($rdv_date);
            $hours_until = ($diff->days * 24) + $diff->h;

            if ($hours_until >= 2 && $hours_until <= 4 && $rdv_date > $now) {
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $settings['mjhost'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $settings['mjusername'];
                    $mail->Password = $settings['mjpassword'];
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo');
                    $mail->addAddress($booking['email']);
                    $mail->isHTML(true);
                    
                    $mail->Subject = "À tout à l'heure ! Votre séance Aquavelo dans 3 heures";
                    
                    $rdv_brut = str_replace(['(RDV: ', ')'], ['', ''], substr($booking['name'], strpos($booking['name'], "(RDV:") + 6));
                    $rdv_final = str_replace(['(', ')'], ['', ''], $rdv_brut);
                    $client_first_name = explode(' ', trim(explode('(RDV:', $booking['name'])[0]))[0];

                    $url_annuler = "https://www.aquavelo.com/index.php?p=annulation&email=" . urlencode($booking['email']) . "&rdv=" . urlencode($rdv_brut) . "&city=Cannes";
                    $url_modifier = "https://www.aquavelo.com/index.php?p=calendrier_cannes&center=305&nom=" . urlencode($booking['name']) . "&email=" . urlencode($booking['email']) . "&phone=" . urlencode($booking['phone']) . "&old_rdv=" . urlencode($rdv_brut);

                    $mail->Body = "Bonjour " . $client_first_name . ",<br><br>
                                  Je vous rappelle votre rdv pour la séance découverte :<br><br>
                                  🗓️ <b>" . $rdv_final . "</b><br><br>
                                  Lieu : 60 Avenue du Dr Raymond Picaud, 06150 Cannes<br>
                                  Tél : 04 93 93 05 65<br><br>
                                  Important : Merci d'arriver 15 minutes avant le début du cours.<br><br>
                                  <b>🎒 À prévoir pour votre séance :</b><br>
                                  ✅ Votre maillot de bain,<br>
                                  ✅ Une serviette,<br>
                                  ✅ Un gel douche,<br>
                                  ✅ Une bouteille d'eau,<br>
                                  ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                                  À très bientôt ! Cordialement Claude<br><br>
                                  <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
                                  <p style='color:#999; font-size:0.9rem;'>Un contretemps ?</p>
                                  <table cellspacing='0' cellpadding='0'><tr>
                                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_annuler' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Annuler</a></td>
                                  <td width='10'></td>
                                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_modifier' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Modifier</a></td>
                                  </tr></table>";
                    
                    $mail->send();
                    $database->prepare("UPDATE am_free SET reminder_3h_sent = 1 WHERE id = ?")->execute([$booking['id']]);
                    $count++;
                } catch (Exception $e) {}
            }
        }
    }
}
echo "Rappels 3h envoyés : $count";
?>
