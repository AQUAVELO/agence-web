<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP de Mailjet
    $mail->isSMTP();
    $mail->Host       = 'in-v3.mailjet.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'adf33e0c77039ed69396e3a8a07400cb'; // Clé API publique Mailjet
    $mail->Password   = '05906e966c8e2933b1dc8b0f8bb1e18b'; // Clé API secrète Mailjet
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Paramètres de l’email
    $mail->setFrom('jacquesverdier4@gmail.com', 'Aquavelo');
    $mail->addAddress('aqua.cannes@gmail.com', 'Test de réception'); // Destinataire

    $mail->isHTML(true);
    $mail->Subject = '✅ Test PHPMailer avec SMTP Mailjet';
    $mail->Body    = '<h3>Ceci est un test SMTP avec PHPMailer via Mailjet</h3><p>Si vous recevez ce message, c\'est que la configuration fonctionne 🎉.</p>';
    $mail->AltBody = 'Ceci est un test SMTP avec PHPMailer via Mailjet.';

    $mail->send();
    echo '✅ Email envoyé avec succès';
} catch (Exception $e) {
    echo "❌ L'envoi a échoué. Erreur : {$mail->ErrorInfo}";
}
?>

