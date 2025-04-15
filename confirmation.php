<?php
// Interface de confirmation (interface « Retour » Monetico)
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'ALESIAMINCEUR');

require '_settings.php';

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function validateMAC($params, $keyHex) {
    $recognizedKeys = [
        'TPE', 'contexte_commande', 'date', 'montant', 'reference', 'texte-libre', 'code-retour',
        'cvx', 'vld', 'brand', 'status3ds', 'numauto', 'originecb', 'bincb', 'hpancb', 'ipclient',
        'originetr', 'cbmasquee', 'modepaiement', 'authentification', 'usage', 'typecompte',
        'ecard', 'MAC'
    ];

    $macFields = [];
    foreach ($recognizedKeys as $key) {
        if (isset($_POST[$key])) {
            $macFields[$key] = mb_convert_encoding($_POST[$key], 'UTF-8', 'auto');
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
    file_put_contents('confirmation_debug.txt', "CHAINE SIGNEE:\n$chaine\n\nMAC attendu:\n$mac\nMAC reçu:\n" . $_POST['MAC'] . "\n", FILE_APPEND);
    return hash_equals($mac, strtoupper($_POST['MAC']));
}




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

        $mail->AltBody = 'Merci pour votre achat. Pour prendre rendez-vous pour votre séance de Cryo, veuillez envoyer un message WhatsApp à Loredana au 07 55 00 73 87. Cordialement, Claude de l\'équipe AQUAVELO';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

header('Content-Type: text/plain');

file_put_contents('confirmation_debug.txt', "EMAIL DÉTECTÉ : " . ($_POST['email'] ?? 'non défini') . "\n", FILE_APPEND);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['MAC']) && validateMAC($_POST, MONETICO_KEY)) {
        // Envoi d'email de remerciement
        $settings = [
            'mjhost' => 'in-v3.mailjet.com',
            'mjusername' => 'adf33e0c77039ed69396e3a8a07400cb',
            'mjpassword' => '05906e966c8e2933b1dc8b0f8bb1e18b',
            'mjfrom' => 'jacquesverdier4@gmail.com'
        ];
       

        if (isset($_POST['email'])) {
            sendThankYouEmail($_POST['aqua.cannes@gmail.com'], $settings);
        }

        echo "version=2\ncdr=0\n";
    } else {
        echo "version=2\ncdr=1\n";
    }
} else {
    echo "version=2\ncdr=1\n";
}
?>






