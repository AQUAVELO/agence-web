<?php
require '_settings.php';

require 'vendor/autoload.php';  // Assurez-vous que Composer autoload est inclus

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Paramètres de configuration Mailjet
$settings = [
    'mjhost' => 'in-v3.mailjet.com',
    'mjusername' => 'adf33e0c77039ed69396e3a8a07400cb',  // Remplacez par votre API Key Mailjet
    'mjpassword' => '05906e966c8e2933b1dc8b0f8bb1e18b'
    'mjfrom' => 'aqua.cannes@gmail.com'  // Remplacez par l'adresse email de l'expéditeur
];

// Fonction pour envoyer un email simple "Merci de votre confiance"
function sendThankYouEmail($toEmail, $toName, $settings) {
    $mail = new PHPMailer(true);
    try {
        // Configuration du serveur SMTP de Mailjet
        $mail->isSMTP();
        $mail->Host = $settings['mjhost'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['mjusername'];
        $mail->Password = $settings['mjpassword'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuration de l'email
        $mail->setFrom($settings['mjfrom'], 'Service clients Aquavelo');
        $mail->addAddress($toEmail, $toName);
        $mail->addReplyTo($settings['mjfrom'], 'Service clients Aquavelo');

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Merci de votre confiance';
        $mail->Body    = '<p>Merci de votre confiance.</p>';
        $mail->AltBody = 'Merci de votre confiance.';

        // Envoyer l'email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Erreur lors de l'envoi de l'email: {$mail->ErrorInfo}";
    }
}

// Envoi d'email à claude@alesiaminceur.com
$toEmail = "claude@alesiaminceur.com";
$toName = "Claude Alesiaminceur";

$result = sendThankYouEmail($toEmail, $toName, $settings);
if ($result === true) {
    echo "Email envoyé avec succès.";
} else {
    echo $result;
}
?>

