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

    function sendEmails($infos, $montant, $datePaiement) {
        $email     = $infos['email']     ?? '';
        $prenom    = $infos['prenom']    ?? '';
        $nom       = $infos['nom']       ?? '';
        $telephone = $infos['telephone'] ?? '';
        $detail    = $infos['detail']    ?? '';

        $messageClient = "<p>Bonjour $prenom $nom,</p>
        <p>Merci pour votre paiement de <strong>$montant</strong> pour la prestation suivante :</p>
        <p><em>$detail</em></p>
        <p>Effectué le <strong>$datePaiement</strong>.</p>
        <p>Nous restons à votre disposition au 04 93 93 05 65.</p>
        <p>À bientôt,<br>Cordialement,<br>Claude – Équipe AQUAVELO</p>";

        $messageAdmin = "<p>Nouveau paiement reçu :</p>
        <ul>
            <li>Nom : $prenom $nom</li>
            <li>Téléphone : $telephone</li>
            <li>Email : $email</li>
            <li>Montant : $montant</li>
            <li>Détail : $detail</li>
            <li>Date : $datePaiement</li>
        </ul>";

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

            // Envoi client
            $mail->setFrom('jacquesverdier4@gmail.com', 'Aquavelo');
            $mail->addAddress($email);
            $mail->addReplyTo('jacquesverdier4@gmail.com', 'Aquavelo');
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de votre paiement';
            $mail->Body = $messageClient;
            $mail->send();

            // Envoi admin
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
            $admin->Subject = "Nouveau paiement reçu - $montant";
            $admin->Body = $messageAdmin;
            $admin->send();

        } catch (Exception $e) {
            file_put_contents('confirmation_debug.txt', "Erreur email : " . $mail->ErrorInfo . "\n", FILE_APPEND);
        }
    }

    if (validateMAC($_POST, MONETICO_KEY)) {
        parse_str(str_replace(';', '&', $_POST['texte-libre']), $infos);

        file_put_contents('confirmation_debug.txt', "Infos décodées :\n" . print_r($infos, true), FILE_APPEND);

        $montant     = $_POST['montant'] ?? '';
        $datePaiement = date('d/m/Y');

        if (!empty($infos['email'])) {
            sendEmails($infos, $montant, $datePaiement);
        }

        echo "version=2\ncdr=0";
    } else {
        file_put_contents('confirmation_debug.txt', "MAC invalide\n", FILE_APPEND);
        echo "version=2\ncdr=1";
    }
}
?>


