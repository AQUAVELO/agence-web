<?php
/**
 * Script de rappel automatique 3h avant le RDV
 */
require '_settings.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) { require_once 'vendor/autoload.php'; }

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

            // Fenêtre de 2h à 4h avant
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
                    
                    $rdv_info = str_replace(['(', ')'], ['', ''], substr($booking['name'], strpos($booking['name'], "(RDV:") + 6));

                    $mail->Body = "Bonjour " . explode(' ', $booking['name'])[0] . ",<br><br>
                                  Votre séance Aquavelo commence dans 3 heures !<br>
                                  🗓️ <b>$rdv_info</b><br><br>
                                  <b>🎒 N'oubliez pas de venir équipé(e) avec :</b><br>
                                  ✅ Votre maillot de bain,<br>
                                  ✅ Une serviette,<br>
                                  ✅ Un gel douche,<br>
                                  ✅ Une bouteille d'eau,<br>
                                  ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                                  À tout à l'heure ! Cordialement Claude";
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
