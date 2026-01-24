<?php
/**
 * Script de test pour l'envoi immédiat de l'email de suivi
 */
require '_settings.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$destinataire = isset($_GET['email']) ? $_GET['email'] : 'claude@alesiaminceur.com';

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
    
    $mail->Subject = "Merci de votre visite chez Aquavelo ! 🚴‍♀️💦";
    
    // On simule un prénom pour le test
    $prenom = "Claude";

    $mail->Body = "Bonjour " . $prenom . ",<br><br>
                  Merci d’être venu(e) découvrir l'Aquavelo🚴‍♀️💦 ! J’espère que vous avez apprécié. Nous serons ravis de vous revoir très vite 🌊.<br><br>
                  N’hésitez pas à me contacter si vous avez des questions ou des commentaires, ou pour finaliser votre inscription en <a href='https://www.aquavelo.com/vente_formule' style='color:#00acdc; font-weight:bold; text-decoration:underline;'>cliquant ici</a> si cela n'a pas été fait.<br><br>
                  Cordialement,<br>
                  Claude<br>
                  Tél : 06 22 64 70 95";
    
    $mail->send();
    echo "✅ Email de test envoyé avec succès à : " . $destinataire;
} catch (Exception $e) {
    echo "❌ Erreur lors de l'envoi : " . $mail->ErrorInfo;
}
?>
