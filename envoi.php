<?php

require '_settings.php';  // Charger les paramètres de configuration

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Segment;
Segment::init("CvtZvzpEIJ0UHZuZCwSqQuq5F6o2FGsB");

// Fonction pour envoyer un email simple "Merci de votre confiance"
function sendThankYouEmail($toEmail, $toName, $settings) {
    $mail = new PHPMailer(true);
    try {
        
        // Configuration du serveur SMTP de Mailjet
  
        $mail->IsSMTP();
        $mail->Host = $settings['mjhost'];
        $mail->isHTML(true);                                  // Set email format to HTML
        
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port = 587;
        $mail->Username = $settings['mjusername'];
        $mail->Password = $settings['mjpassword'];

        

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
$settings = [
    'mjhost' => 'in-v3.mailjet.com',  // Adresse du serveur SMTP de Mailjet
    'mjusername' => 'your-mailjet-username',  // Remplacez par votre nom d'utilisateur Mailjet
    'mjpassword' => 'your-mailjet-password',  // Remplacez par votre mot de passe Mailjet
    'mjfrom' => 'your-email@example.com'  // Remplacez par l'adresse email de l'expéditeur
];

$result = sendThankYouEmail($toEmail, $toName, $settings);
if ($result === true) {
    echo "Email envoyé avec succès.";
} else {
    echo $result;
}
?>
