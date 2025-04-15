<?php
// Paramètres Monetico
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'ALESIAMINCEUR');

require '_settings.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fonction de vérification du MAC
function validateMAC($params, $keyHex) {
    $recognizedKeys = [
        'TPE', 'contexte_commande', 'date', 'montant', 'reference', 'texte-libre', 'code-retour',
        'cvx', 'vld', 'brand', 'status3ds', 'numauto', 'originecb', 'bincb', 'hpancb', 'ipclient',
        'originetr', 'cbmasquee', 'modepaiement', 'authentification', 'usage', 'typecompte',
        'ecard', 'MAC'
    ];

    $macFields = [];
    foreach ($recognizedKeys as $key) {
        if (isset($params[$key])) {
            $macFields[$key] = mb_convert_encoding($params[$key], 'UTF-8', 'auto');
        }
    }

    ksort($macFields, SORT_STRING);
    $chaine = '';
    foreach ($macFields as $k => $v) {
        if ($k !== 'MAC') {
            $chaine .= "$k=$v*";
        }
    }
    $chaine = rtrim($chaine, '*');

    $binaryKey = pack('H*', $keyHex);
    $mac = strtoupper(hash_hmac('sha1', $chaine, $binaryKey));

    file_put_contents('confirmation_debug.txt', "==== APPEL CGI2 ====\n", FILE_APPEND);
    file_put_contents('confirmation_debug.txt', "CHAINE SIGNEE:\n$chaine\nMAC attendu:\n$mac\nMAC reçu:\n" . ($params['MAC'] ?? 'NON FOURNI') . "\n\n", FILE_APPEND);

    return isset($params['MAC']) && hash_equals($mac, strtoupper($params['MAC']));
}

// Fonction pour envoyer un email de remerciement
function sendThankYouEmail($toEmail, $settings) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $settings['mjhost'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['mjusername'];
        $mail->Password = $settings['mjpassword'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($settings['mjfrom'], 'Service clients Aquavelo');
        $mail->addAddress($toEmail);
        $mail->addReplyTo($settings['mjfrom'], 'Service clients Aquavelo');

        $mail->isHTML(true);
        $mail->Subject = 'Merci pour votre achat';
        $mail->Body = "<p>Bonjour,</p>
            <p>Merci pour votre achat. Pour prendre rendez-vous pour votre séance de Cryo, veuillez envoyer un message WhatsApp à Loredana au <strong>07 55 00 73 87</strong>.</p>
            <p>Cordialement,<br>Claude de l'équipe AQUAVELO</p>";
        $mail->AltBody = 'Merci pour votre achat. Pour prendre rendez-vous pour votre séance de Cryo, envoyez un message WhatsApp à Loredana au 07 55 00 73 87. Cordialement, Claude de l\'équipe AQUAVELO';

        $mail->send();

        file_put_contents('confirmation_debug.txt', "EMAIL ENVOYÉ À : $toEmail\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        file_put_contents('confirmation_debug.txt', "ERREUR PHPMailer : " . $mail->ErrorInfo . "\n", FILE_APPEND);
        return false;
    }
}

// Configuration Mailjet
$settings = [
    'mjhost' => 'in-v3.mailjet.com',
    'mjusername' => 'adf33e0c77039ed69396e3a8a07400cb',
    'mjpassword' => '05906e966c8e2933b1dc8b0f8bb1e18b',
    'mjfrom' => 'jacquesverdier4@gmail.com'
];

header('Content-Type: text/plain');

// Traitement de la requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('confirmation_debug.txt', "POST reçu à " . date('Y-m-d H:i:s') . " :\n" . print_r($_POST, true) . "\n", FILE_APPEND);

    if (validateMAC($_POST, MONETICO_KEY)) {
        if (isset($_POST['email'])) {
            sendThankYouEmail($_POST['email'], $settings);
        } else {
            file_put_contents('confirmation_debug.txt', "EMAIL NON DÉFINI dans POST\n", FILE_APPEND);
        }

        echo "version=2\ncdr=0\n"; // OK
    } else {
        file_put_contents('confirmation_debug.txt', "MAC INVALID\n", FILE_APPEND);
        echo "version=2\ncdr=1\n"; // Erreur MAC
    }
} else {
    file_put_contents('confirmation_debug.txt', "APPEL NON POST\n", FILE_APPEND);
    echo "version=2\ncdr=1\n"; // Appel incorrect
}
?>







