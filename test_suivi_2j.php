<?php
/**
 * Simulation d'envoi de l'email J+2
 */

require '_settings.php';

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$destinataire = "claude@alesiaminceur.com";
$prenom = "Claude";

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
    $mail->addAddress($destinataire);
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
    echo "Simulation d'email J+2 envoyée à $destinataire.";
} catch (Exception $e) { 
    echo "Erreur : " . $mail->ErrorInfo; 
}
?>
