<?php
require 'vendor/autoload.php';
require 'settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('confirmation_debug.txt', "POST reçu :\n" . print_r($_POST, true), FILE_APPEND);

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

    function sendEmails($toEmail, $prenom, $nom, $telephone, $detail, $montant, $codeValidation) {
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
            $mail->Body = "<p>Bonjour <strong>$prenom $nom</strong>,</p><p>Merci pour votre achat de <strong>$detail</strong> pour un montant de <strong>$montant</strong>.</p><p>Pour prendre rendez-vous, veuillez envoyer un message WhatsApp à <strong>Loredana</strong> au <strong>07 55 00 73 87</strong>.</p><hr><div style='border: 2px dashed #104e8b; padding: 20px; margin: 20px 0; background: #f4f8fb;'><h2 style='text-align:center; color:#104e8b;'>🎟️ Bon de réservation</h2><p><strong>Nom :</strong> $prenom $nom</p><p><strong>Téléphone :</strong> $telephone</p><p><strong>Email :</strong> $toEmail</p><p><strong>Offre :</strong> $detail</p><p><strong>Montant :</strong> $montant</p><p><strong>Centre :</strong> AQUAVELO - 60 avenue du Docteur Raymond Picaud à CANNES</p><p><strong>Code de validation :</strong> <span style='font-size: 1.3em; color: #cc3366;'>$codeValidation</span></p><p style='text-align:center; margin-top:15px;'>📍 Veuillez présenter ce bon lors de votre venue.</p></div><p>À bientôt,<br>Claude – Équipe AQUAVELO</p>";
            $mail->AltBody = "Bonjour $prenom $nom,\nMerci pour votre achat de $detail pour $montant.\n\nContactez Loredana au 07 55 00 73 87.\n\nCoordonnées :\nEmail : $toEmail\nTéléphone : $telephone\nCentre : AQUAVELO, 60 avenue du Docteur Raymond Picaud à CANNES\nCode de validation : $codeValidation\n\nVeuillez présenter ce code imprimé lors de votre venue.\n\nCordialement,\nClaude – Équipe AQUAVELO";
            $mail->send();

            // Email admin
            $admin = new PHPMailer(true);
            $admin->isSMTP();
            $admin->Host = 'in-v3.mailjet.com';
            $admin->SMTPAuth = true;
            $admin->Username = 'adf33e0c77039ed69396e3a8a07400cb';
            $admin->Password = '05906e966c8e2933b1dc8b0f8bb1e18b';
            $admin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $admin->Port = 587;
            $admin->CharSet = 'UTF-8';

            $admin->setFrom('jacquesverdier4@gmail.com', 'Aquavelo');
            $admin->addAddress('aqua.cannes@gmail.com');
            $admin->isHTML(true);
            $admin->Subject = "Nouvel achat – $prenom $nom";
            $admin->Body = "<p>Achat effectué :</p><ul><li>Nom et prénom : $nom $prenom</li><li>Email : $toEmail</li><li>Téléphone : $telephone</li><li>Détail : $detail</li><li>Montant : $montant</li><li>Code : $codeValidation</li></ul>";
            $admin->send();

        } catch (Exception $e) {
            file_put_contents('confirmation_debug.txt', "Erreur email : " . $mail->ErrorInfo . "\n", FILE_APPEND);
        }
    }

    if (validateMAC($_POST, MONETICO_KEY)) {
        parse_str(str_replace(';', '&', $_POST['texte-libre']), $infos);

        $email     = $infos['email']     ?? null;
        $prenom    = $infos['prenom']    ?? '';
        $nom       = $infos['nom']       ?? '';
        $telephone = $infos['telephone'] ?? '';
        $achat     = $infos['achat']     ?? '';
        $montant   = $infos['montant']   ?? '';

        if ($email) {
            $stmt = $conn->prepare("SELECT detail FROM formule WHERE email = :email ORDER BY id DESC LIMIT 1");
            $stmt->execute(['email' => $email]);
            $detail = $stmt->fetchColumn() ?: $achat;

            $codeValidation = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            sendEmails($email, $prenom, $nom, $telephone, $detail, $montant, $codeValidation);

            $stmt = $conn->prepare("UPDATE formule SET vente = 1 WHERE email = :email ORDER BY id DESC LIMIT 1");
            $stmt->execute(['email' => $email]);
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo "version=2\ncdr=0\n";
        exit;
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo "version=2\ncdr=1\n";
        exit;
    }
} else {
    header('Location: https://www.aquavelo.com/centres/Cannes');
    exit;
}

















