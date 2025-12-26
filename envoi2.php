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
    'mjpassword' => '05906e966c8e2933b1dc8b0f8bb1e18b',
    'mjfrom' => 'jacquesverdier4@gmail.com'  // Remplacez par l'adresse email de l'expéditeur
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
        $mail->CharSet = 'UTF-8'; // Définir l'encodage UTF-8
        $mail->setFrom($settings['mjfrom'], 'Service clients Aquavelo');
        $mail->addAddress($toEmail, $toName);
        $mail->addReplyTo($settings['mjfrom'], 'Service clients Aquavelo');

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Suivi de vos mensurations';
        
        $mail->Body = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message de Remerciement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        a {
            color: #1a73e8;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .signature {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Bonjour,</p>
        <p>IMC >20 </p>
       // <p>Lorsque vous faites partie de la catégorie “poids normal”, plusieurs facteurs entrent en jeu : l’âge et le sexe notamment. Nous l’avons vu plus haut, l’IMC idéal d’une jeune femme oscille entre 19 et 24. Pour les hommes dans la même tranche d'âge, l’IMC idéal est légèrement plus haut, entre 20 et 25.

Il s’agit d’un panel de valeurs à titre indicatif pour déterminer si votre taille et votre poids sont en harmonie. Généralement, la corpulence normale résulte d’une alimentation équilibrée avec des apports nutritifs appropriés et très peu de problèmes de santé. Une hygiène de vie saine qui passe par l'assiette et la pratique sportive constitue le meilleur moyen de conserver un poids normal à long terme.</p>
<p class="signature">Cordialement, Claude de AQUAVELO</p>
        
    </div>
</body>
</html>
';

        $mail->AltBody = 'Merci de votre confiance.';

        // Envoyer l'email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Erreur lors de l'envoi de l'email: {$mail->ErrorInfo}";
    }
}

// Envoi d'email à claude@alesiaminceur.com
//$toEmail = "claude@alesiaminceur.com";
$toName = "Claude Alesiaminceur";

$result = sendThankYouEmail($toEmail, $toName, $settings);
if ($result === true) {
    echo "Email envoyé avec succès.";
} else {
    echo $result;
}
?>



