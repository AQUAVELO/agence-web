<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

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

function sendThankYouEmail($toEmail, $prenom, $nom, $telephone, $achat, $montant) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'in-v3.mailjet.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'adf33e0c77039ed69396e3a8a07400cb';
        $mail->Password   = '05906e966c8e2933b1dc8b0f8bb1e18b';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom('jacquesverdier4@gmail.com', 'Aquavelo');
        $mail->addAddress($toEmail);
        $mail->addReplyTo('jacquesverdier4@gmail.com', 'Aquavelo');

        $mail->isHTML(true);
        $mail->Subject = 'Merci pour votre achat';

        $mail->Body = "
        <p>Bonjour <strong>$prenom $nom</strong>,</p>
        <p>Merci pour votre achat de <strong>$achat</strong> pour un montant de <strong>$montant</strong>.</p>
        <p>Pour prendre rendez-vous, veuillez envoyer un message WhatsApp à <strong>Loredana</strong> au <strong>07 55 00 73 87</strong>.</p>
        <p><strong>Résumé de vos coordonnées :</strong><br>
        📧 Email : $toEmail<br>
        📱 Téléphone : $telephone</p>
        <p>À bientôt,<br>Claude – Équipe AQUAVELO</p>
        ";

        $mail->AltBody = "Bonjour $prenom $nom,\nMerci pour votre achat de $achat pour $montant.\nContactez Loredana au 07 55 00 73 87.\nEmail : $toEmail\nTéléphone : $telephone\nCordialement, Claude – AQUAVELO";

        $mail->send();
        file_put_contents('confirmation_debug.txt', "✅ Email envoyé à $toEmail\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents('confirmation_debug.txt', "❌ Erreur email : " . $mail->ErrorInfo . "\n", FILE_APPEND);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('confirmation_debug.txt', "POST reçu :\n" . print_r($_POST, true), FILE_APPEND);

    if (validateMAC($_POST, MONETICO_KEY)) {
        $infos = [];
        if (isset($_POST['texte-libre'])) {
            parse_str(str_replace(';', '&', $_POST['texte-libre']), $infos);
        }

        $email     = $infos['email']     ?? null;
        $prenom    = $infos['prenom']    ?? '';
        $nom       = $infos['nom']       ?? '';
        $telephone = $infos['telephone'] ?? '';
        $achat     = $infos['achat']     ?? 'Inconnu';
        $montant   = $infos['montant']   ?? '0.00 EUR';

        file_put_contents('confirmation_debug.txt', "Infos client :\n" . print_r($infos, true), FILE_APPEND);

        if ($email) {
            sendThankYouEmail($email, $prenom, $nom, $telephone, $achat, $montant);
        } else {
            file_put_contents('confirmation_debug.txt', "❌ Email manquant, pas d'envoi\n", FILE_APPEND);
        }

        header('Location: merci.php');
        exit;
    } else {
        file_put_contents('confirmation_debug.txt', "❌ MAC invalide\n", FILE_APPEND);
        echo "version=2\ncdr=1\n";
    }
} else {
    echo "version=2\ncdr=1\n";
}















