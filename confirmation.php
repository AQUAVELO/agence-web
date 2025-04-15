<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

// Fonction d’extraction du MAC
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

    return isset($params['MAC']) && hash_equals($mac, strtoupper($params['MAC']));
}

// Envoi email
function sendThankYouEmail($toEmail) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'in-v3.mailjet.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'adf33e0c77039ed69396e3a8a07400cb';
        $mail->Password   = '05906e966c8e2933b1dc8b0f8bb1e18b';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('jacquesverdier4@gmail.com', 'Aquavelo');
        $mail->addAddress($toEmail);
        $mail->Subject = 'Merci pour votre achat';
        $mail->isHTML(true);
        $mail->Body = "<p>Merci pour votre achat. Pour réserver votre séance de Cryo, contactez Loredana au 07 55 00 73 87 (WhatsApp).</p>";

        $mail->send();
        file_put_contents('confirmation_debug.txt', "✅ Email envoyé à $toEmail\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents('confirmation_debug.txt', "❌ Erreur envoi : " . $mail->ErrorInfo . "\n", FILE_APPEND);
    }
}

// Traitement Monetico
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('confirmation_debug.txt', "Reçu POST :\n" . print_r($_POST, true), FILE_APPEND);

    if (validateMAC($_POST, MONETICO_KEY)) {
        // Extraire email depuis texte-libre
        $email = null;
        if (isset($_POST['texte-libre']) && preg_match('/email=([\w.\-+]+@[\w.\-]+\.\w+)/', $_POST['texte-libre'], $match)) {
            $email = $match[1];
        }

        if ($email) {
            sendThankYouEmail($email);
        } else {
            file_put_contents('confirmation_debug.txt', "❌ Email non trouvé dans texte-libre\n", FILE_APPEND);
        }

        echo "version=2\ncdr=0\n";
    } else {
        file_put_contents('confirmation_debug.txt', "❌ MAC invalide\n", FILE_APPEND);
        echo "version=2\ncdr=1\n";
    }
} else {
    echo "version=2\ncdr=1\n";
}















