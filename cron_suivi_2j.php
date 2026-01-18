<?php
/**
 * Script d'envoi automatique 2 jours après la séance
 * À exécuter par Clever Cloud (Cron)
 */

require '_settings.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// On cherche les RDV passés (J+2) qui n'ont pas encore reçu l'email de suivi J+2
$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND followup_2d_sent = 0");
$stmt->execute();
$bookings = $stmt->fetchAll();

$now = new DateTime();
$count = 0;

foreach ($bookings as $booking) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    
    if (count($matches) === 3) {
        $rdv_start = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
        
        if ($rdv_start) {
            $diff = $rdv_start->diff($now);
            $days_passed = $diff->days;
            $is_past = ($now > $rdv_start);

            // Fenêtre d'envoi : environ 2 jours après (entre 44h et 60h après le début)
            $hours_passed = ($days_passed * 24) + $diff->h;

            if ($is_past && $hours_passed >= 44 && $hours_passed <= 60) {
                try {
                    // Extraction du prénom (première partie avant l'espace)
                    $full_name = trim(explode('(RDV:', $booking['name'])[0]);
                    $name_parts = explode(' ', $full_name);
                    $prenom = $name_parts[0];

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
                    
                    $mail->Subject = "Votre séance Aquavelo vous a plu ? 💦";
                    
                    $mail->Body = "Bonjour " . $prenom . ",<br><br>
                                  J’espère que votre séance découverte Aquavelo vous a plu 💦 !<br>
                                  Si vous avez un moment, donnez-nous votre avis par retour email — cela nous aide à progresser 🌟.<br><br>
                                  Et si vous souhaitez continuer, vous pouvez dès maintenant vous inscrire en ligne via ce lien si cela n'a pas été fait :<br>
                                  👉 <a href='https://www.aquavelo.com/vente_formule' style='color:#00acdc; font-weight:bold; text-decoration:underline;'>cliquer ici</a><br><br>
                                  À très bientôt dans l’eau 🌊<br>
                                  Claude<br>
                                  Tél : 06 22 64 70 95";
                    
                    $mail->send();
                    
                    // Marquer comme envoyé
                    $upd = $database->prepare("UPDATE am_free SET followup_2d_sent = 1 WHERE id = ?");
                    $upd->execute([$booking['id']]);
                    $count++;
                    
                } catch (Exception $e) {
                    error_log("Erreur Cron Suivi 2J: " . $mail->ErrorInfo);
                }
            }
        }
    }
}

echo "Nombre d'emails de suivi J+2 envoyés : $count";
?>
