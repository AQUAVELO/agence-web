<?php
/**
 * Script d'envoi automatique 3h après la séance
 * À exécuter par Clever Cloud (Cron)
 */

require '_settings.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// On cherche les RDV passés qui n'ont pas encore reçu l'email de suivi
$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND after_session_sent = 0");
$stmt->execute();
$bookings = $stmt->fetchAll();

$now = new DateTime();
$count = 0;

foreach ($bookings as $booking) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    
    if (count($matches) === 3) {
        $rdv_start = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
        
        if ($rdv_start) {
            // On calcule le temps écoulé depuis le DEBUT du cours
            // Si le cours dure 45-60min, "3h après la séance" revient à environ 4h après le début.
            $diff = $rdv_start->diff($now);
            $hours_passed = ($diff->days * 24) + $diff->h;
            $is_past = ($now > $rdv_start);

            // Fenêtre d'envoi : entre 3h et 6h après le début (pour couvrir la fin de séance + 3h)
            if ($is_past && $hours_passed >= 3 && $hours_passed <= 6) {
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
                    
                    $mail->Subject = "Merci de votre visite chez Aquavelo ! 🚴‍♀️💦";
                    
                    $mail->Body = "Bonjour,<br><br>
                                  Merci d’être venu(e) découvrir l'Aquavelo🚴‍♀️💦 ! J’espère que vous avez apprécié.<br><br>
                                  Nous serons ravis de vous revoir très vite 🌊.<br><br>
                                  N’hésitez pas à me contacter si vous avez des questions ou des commentaires, ou pour finaliser votre inscription en <a href='https://www.aquavelo.com/vente_formule' style='color:#00acdc; font-weight:bold; text-decoration:underline;'>cliquant ici</a> si cela n'a pas été fait.<br><br>
                                  Cordialement,<br>
                                  Claude<br>
                                  Tél : 06 22 64 70 95";
                    
                    $mail->send();
                    
                    // Marquer comme envoyé
                    $upd = $database->prepare("UPDATE am_free SET after_session_sent = 1 WHERE id = ?");
                    $upd->execute([$booking['id']]);
                    $count++;
                    
                } catch (Exception $e) {
                    error_log("Erreur Cron Après Séance: " . $mail->ErrorInfo);
                }
            }
        }
    }
}

echo "Nombre d'emails de suivi envoyés : $count";
?>
