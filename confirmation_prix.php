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

    function sendEmails($email, $montant, $datePaiement) {
        $message = "<p>Bonjour Claude RODRIGUEZ,</p>
        <p>Merci pour votre paiement de <strong>$montant</strong> en date du <strong>$datePaiement</strong>.</p>
       
        <p>Je reste à votre disposition au 04 93 93 05 65.</p>
        <p>À bientôt,<br>Cordialement<br>Claude – Équipe AQUAVELO</p>";

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
            $mail->addAddress($email);
            $mail->addReplyTo('jacquesverdier4@gmail.com', 'Aquavelo');

            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de votre paiement';
            $mail->Body = $message;
            $mail->send();

            // Copie à Claude
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
            $admin->Body = $message;
            $admin->send();

        } catch (Exception $e) {
            file_put_contents('confirmation_debug.txt', "Erreur email : " . $mail->ErrorInfo . "\n", FILE_APPEND);
        }
    }

    
       if (validateMAC($_POST, MONETICO_KEY)) {
    parse_str(str_replace(';', '&', $_POST['texte-libre']), $infos);

    // Log pour debug
    file_put_contents('debug_infos.txt', print_r($infos, true), FILE_APPEND);

    $email     = $infos['email']     ?? null;
    $prenom    = $infos['prenom']    ?? '';
    $nom       = $infos['nom']       ?? '';
    $telephone = $infos['telephone'] ?? '';
    $montant   = $_POST['montant']   ?? '';
    $date      = $_POST['date']      ?? '';

    if ($email) {
        // EMAIL CLIENT
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
            $mail->addAddress($email);
            $mail->addReplyTo('jacquesverdier4@gmail.com', 'Aquavelo');

            $mail->isHTML(true);
            $mail->Subject = 'Merci pour votre achat';
            $mail->Body = "<p>Bonjour $prenom $nom,</p>
                <p>Merci pour votre paiement de <strong>$montant</strong> en date du <strong>$date</strong>.</p>
                <p>Lors de votre 1ère séance il faudra amener un RIB pour les autres échéances.</p>
                <p>Je reste à votre disposition au 04 93 93 05 65.</p>
                <p>À bientôt,<br>Cordialement Claude – Équipe AQUAVELO</p>";
            $mail->send();

            // EMAIL ADMIN
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
            $admin->Body = "<p>Achat effectué :</p>
                <ul>
                    <li>Nom et prénom : $nom $prenom</li>
                    <li>Email : $email</li>
                    <li>Téléphone : $telephone</li>
                    <li>Montant payé : $montant</li>
                    <li>Date : $date</li>
                </ul>";
            $admin->send();

        } catch (Exception $e) {
            file_put_contents('confirmation_debug.txt', "Erreur email : " . $mail->ErrorInfo . "\n", FILE_APPEND);
        }

        // MAJ BASE FORMULE
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
