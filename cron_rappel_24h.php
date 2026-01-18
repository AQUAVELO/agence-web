<?php
/**
 * Script de rappel automatique 24h avant le RDV
 * À exécuter par Clever Cloud (Cron)
 */

require '_settings.php';

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. On cherche les RDV prévus dans environ 24h qui n'ont pas encore reçu de rappel
// Le format dans la base est "Jour DD/MM/YYYY à HH:mm (ACTIVITE)"
// On récupère tout ce qui n'est pas marqué comme "rappel envoyé"
$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND reminder_sent = 0");
$stmt->execute();
$bookings = $stmt->fetchAll();

$now = new DateTime();
$count = 0;

foreach ($bookings as $booking) {
    // Extraction de la date : "Mardi 20/01/2026 à 09:45 (AQUAVELO)" -> "20/01/2026 09:45"
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    
    if (count($matches) === 3) {
        $rdv_date_str = $matches[1] . ' ' . $matches[2];
        $rdv_date = DateTime::createFromFormat('d/m/Y H:i', $rdv_date_str);
        
        if ($rdv_date) {
            $diff = $now->diff($rdv_date);
            $hours_until = ($diff->days * 24) + $diff->h;

            // Si le RDV est entre 20h et 26h dans le futur (on vise la fenêtre des 24h)
            if ($hours_until >= 20 && $hours_until <= 26 && $rdv_date > $now) {
                
                // ENVOI DE L'EMAIL
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
                    
                    $mail->Subject = "Rappel : Votre séance Aquavelo demain !";
                    
                    // Formatage pour l'email
                    $rdv_info = str_replace(['(', ')'], ['', ''], substr($booking['name'], strpos($booking['name'], "(RDV:") + 6));

                    $mail->Body = "Bonjour " . explode(' ', $booking['name'])[0] . ",<br><br>
                                  Ceci est un petit rappel pour votre séance de demain :<br>
                                  🗓️ <b>$rdv_info</b><br><br>
                                  Lieu : 60 Avenue du Dr Raymond Picaud, 06150 Cannes<br>
                                  Tél : 04 93 93 05 65<br><br>
                                  <b>🎒 Rappel équipement :</b><br>
                                  ✅ Maillot, Serviette, Gel douche, Bouteille d'eau.<br><br>
                                  À demain ! Cordialement Claude";
                    
                    $mail->send();
                    
                    // Marquer comme envoyé
                    $upd = $database->prepare("UPDATE am_free SET reminder_sent = 1 WHERE id = ?");
                    $upd->execute([$booking['id']]);
                    $count++;
                    
                } catch (Exception $e) {
                    error_log("Erreur Cron Rappel: " . $mail->ErrorInfo);
                }
            }
        }
    }
}

echo "Nombre de rappels envoyés : $count";
?>
