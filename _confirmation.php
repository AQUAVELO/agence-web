<?php
ob_start();
require 'vendor/autoload.php';
require 'settings.php';

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
    $codeValidation = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'in-v3.mailjet.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'adf33e0c77039ed69396e3a8a07400cb';
        $mail->Password   = '05906e966c8e2933b1dc8b0f8bb1e18b';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('jacquesverdier4@gmail.com', 'Aquavelo');
        $mail->addAddress($toEmail);
        $mail->addReplyTo('jacquesverdier4@gmail.com', 'Aquavelo');

        $mail->isHTML(true);
        $mail->Subject = 'Merci pour votre achat';
        $mail->Body = "<p>Bonjour <strong>$prenom $nom</strong>,</p><p>Merci pour votre achat de <strong>$achat</strong> pour un montant de <strong>$montant</strong>.</p><p>Pour prendre rendez-vous, veuillez envoyer un message WhatsApp à <strong>Loredana</strong> au <strong>07 55 00 73 87</strong>.</p><hr><div style='border: 2px dashed #104e8b; padding: 20px; margin: 20px 0; background: #f4f8fb;'><h2 style='text-align:center; color:#104e8b;'>🎟️ Bon de réservation</h2><p><strong>Nom :</strong> $prenom $nom</p><p><strong>Téléphone :</strong> $telephone</p><p><strong>Email :</strong> $toEmail</p><p><strong>Offre :</strong> $achat</p><p><strong>Montant :</strong> $montant</p><p><strong>Centre :</strong> AQUAVELO - <a href='https://maps.google.com/?q=60 avenue du Docteur Raymond Picaud, Cannes' target='_blank'>60 avenue du Docteur Raymond Picaud à CANNES</a></p><p><strong>Code de validation :</strong> <span style='font-size: 1.3em; color: #cc3366;'>$codeValidation</span></p><p style='text-align:center; margin-top:15px;'>📍 Veuillez présenter ce bon lors de votre venue.</p></div><p>À bientôt,<br>Claude – Équipe AQUAVELO</p>";

        $mail->AltBody = "Bonjour $prenom $nom,\nMerci pour votre achat de $achat pour $montant.\n\nContactez Loredana au 07 55 00 73 87.\n\nCoordonnées :\nEmail : $toEmail\nTéléphone : $telephone\nCentre : AQUAVELO, 60 avenue du Docteur Raymond Picaud à CANNES\nCode de validation : $codeValidation\n\nVeuillez présenter ce code imprimé lors de votre venue.\n\nCordialement,\nClaude – Équipe AQUAVELO";

        $mail->send();

        $adminMail = new PHPMailer(true);
        $adminMail->isSMTP();
        $adminMail->Host       = 'in-v3.mailjet.com';
        $adminMail->SMTPAuth   = true;
        $adminMail->Username   = 'adf33e0c77039ed69396e3a8a07400cb';
        $adminMail->Password   = '05906e966c8e2933b1dc8b0f8bb1e18b';
        $adminMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $adminMail->Port       = 587;
        $adminMail->CharSet    = 'UTF-8';

        $adminMail->setFrom('jacquesverdier4@gmail.com', 'Aquavelo');
        $adminMail->addAddress('aqua.cannes@gmail.com');

        $adminMail->isHTML(true);
        $adminMail->Subject = "Nouvel achat – $prenom $nom";
        $adminMail->Body = "<p>Un achat vient d'être effectué :</p><ul><li>Nom et prénom : <strong>$nom $prenom</strong></li><li>Email : $toEmail</li><li>Téléphone : $telephone</li><li>Produit : <strong>$achat</strong></li><li>Montant : <strong>$montant</strong></li><li>Code de validation : $codeValidation</li><li>Centre : <a href='https://maps.google.com/?q=60 avenue du Docteur Raymond Picaud, Cannes' target='_blank'>60 avenue du Docteur Raymond Picaud à CANNES</a></li><li>Tél. du centre : 04 93 93 05 65</li></ul>";
        $adminMail->send();

        return $codeValidation;
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
            $codeValidation = sendThankYouEmail($email, $prenom, $nom, $telephone, $achat, $montant);
            $stmt = $conn->prepare("UPDATE formule SET vente = 1 WHERE email = :email ORDER BY id DESC LIMIT 1");
            $stmt->execute(['email' => $email]);
        } else {
            file_put_contents('confirmation_debug.txt', "❌ Email manquant, pas d'envoi\n", FILE_APPEND);
        }

        if (empty($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'InetCPT') !== false) {
            header('Content-Type: text/plain; charset=utf-8');
            echo "version=2\ncdr=0\n";
            file_put_contents('confirmation_output.txt', ob_get_clean());
            exit;
        }

        file_put_contents('confirmation_output.txt', ob_get_clean());
        exit;
    } else {
        file_put_contents('confirmation_debug.txt', "❌ MAC invalide\n", FILE_APPEND);
        header('Content-Type: text/plain; charset=utf-8');
        echo "version=2\ncdr=1\n";
        file_put_contents('confirmation_output.txt', ob_get_clean());
        exit;
    }
} else {
    header('Location: https://www.aquavelo.com/centres/Cannes');
    exit;
}















