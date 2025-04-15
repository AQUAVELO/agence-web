<?php
// Paramètres Monetico
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'ALESIAMINCEUR');

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fonction de validation du MAC Monetico
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

// Fonction pour envoyer un e-mail de remerciement
function sendThankYouEmail($toEmail) {
    $mail = new PHPMailer(true);
    try {
        // Configuration SMTP Mailjet
        $mail->isSMTP();
        $mail->Host       = 'in-v3.mailjet.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'adf33e0c77039ed69396e3a8a07400cb';
        $mail->Password   = '05906e966c8e2933b1dc8b0f8bb1e18b';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom('jacquesverdier4@gmail.com', 'Aquavelo');
        $mail->addAddress($toEmail); // ✅ email transmis dynamiquement
        $mail->addReplyTo('jacquesverdier4@gmail.com', 'Aquavelo');

        $mail->isHTML(true);
        $mail->Subject = 'Merci pour votre achat';
        $mail->Body = "<p>Bonjour,</p>
            <p>Merci pour votre achat. Pour prendre rendez-vous pour votre séance de Cryo, veuillez envoyer un message WhatsApp à Loredana au <strong>07 55 00 73 87</strong>.</p>
            <p>Cordialement,<br>Claude de l'équipe AQUAVELO</p>";
        $mail->AltBody = "Merci pour votre achat. Pour prendre rendez-vous pour votre séance de Cryo, envoyez un message WhatsApp à Loredana au 07 55 00 73 87. Cordialement, Claude de l'équipe AQUAVELO.";

        $mail->send();
        file_put_contents('confirmation_debug.txt', "✅ Email envoyé à $toEmail\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        file_put_contents('confirmation_debug.txt', "❌ Erreur email : " . $mail->ErrorInfo . "\n", FILE_APPEND);
        return false;
    }
}

header('Content-Type: text/plain');

// Traitement de la requête POST venant de Monetico
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('confirmation_debug.txt', "POST reçu à " . date('Y-m-d H:i:s') . " :\n" . print_r($_POST, true) . "\n", FILE_APPEND);

    if (validateMAC($_POST, MONETICO_KEY)) {
        // ✅ Récupération email : depuis POST ou fallback
        $email = $_POST['email'] ?? null;
        file_put_contents('confirmation_debug.txt', "Email utilisé : $email\n", FILE_APPEND);
        sendThankYouEmail($email);

        echo "version=2\ncdr=0\n"; // OK pour Monetico
    } else {
        file_put_contents('confirmation_debug.txt', "❌ MAC invalide\n", FILE_APPEND);
        echo "version=2\ncdr=1\n";
    }
} else {
    file_put_contents('confirmation_debug.txt', "⚠️ Appel non POST\n", FILE_APPEND);
    echo "version=2\ncdr=1\n";
}
?>








