<?php
require_once('settings.php');
require_once('PHPMailer/PHPMailerAutoload.php');

// Clé secrète Monetico déjà définie dans settings.php

// Récupération des données POST
$tpe            = $_POST['TPE'] ?? '';
$date           = $_POST['date'] ?? '';
$montant        = $_POST['montant'] ?? '';
$reference      = $_POST['reference'] ?? '';
$texteLibre     = $_POST['texte-libre'] ?? '';
$codeRetour     = $_POST['code-retour'] ?? '';
$cvx            = $_POST['cvx'] ?? '';
$vld            = $_POST['vld'] ?? '';
$brand          = $_POST['brand'] ?? '';
$status3ds      = $_POST['status3ds'] ?? '';
$numauto        = $_POST['numauto'] ?? '';
$motifrefus     = $_POST['motifrefus'] ?? '';
$originecb      = $_POST['originecb'] ?? '';
$bincb          = $_POST['bincb'] ?? '';
$hpancb         = $_POST['hpancb'] ?? '';
$ipclient       = $_POST['ipclient'] ?? '';
$originetr      = $_POST['originetr'] ?? '';
$veres          = $_POST['veres'] ?? '';
$pades          = $_POST['pades'] ?? '';
$sign           = $_POST['MAC'] ?? '';

// Reconstitution de la chaîne pour calcul du MAC
$chaine = implode('*', [
    $tpe, $date, $montant, $reference, $texteLibre, $codeRetour, $cvx, $vld,
    $brand, $status3ds, $numauto, $motifrefus, $originecb, $bincb, $hpancb,
    $ipclient, $originetr, $veres, $pades, ''
]);

$computed_mac = strtoupper(hash_hmac('sha1', $chaine, pack("H*", MONETICO_KEY)));

// Debug log
file_put_contents('confirmation_debug.txt', date('Y-m-d H:i:s') . ' | MAC reçu : ' . $sign . ' | MAC calculé : ' . $computed_mac . PHP_EOL, FILE_APPEND);

// Vérification du MAC
if (!hash_equals($computed_mac, $sign)) {
    echo "<h1>Erreur de vérification</h1><p>Le paiement n'a pas pu être vérifié (MAC invalide).</p>";
    exit;
}

// Paiement validé côté Monetico ?
if ($codeRetour !== 'paiement') {
    echo "<h1>Paiement refusé</h1><p>Votre paiement n'a pas été accepté. Veuillez réessayer.</p>";
    exit;
}

// Extraction des infos du texte-libre
parse_str($texteLibre, $infos);
$prenom    = $infos['prenom'] ?? '';
$nom       = $infos['nom'] ?? '';
$email     = $infos['email'] ?? '';
$telephone = $infos['telephone'] ?? '';
$montant   = $infos['montant'] ?? '';

// Code de validation (ex : hash de la référence)
$code_validation = substr(md5($reference), 0, 8);

// MAJ base de données : validation de la vente
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    file_put_contents('confirmation_debug.txt', 'Erreur DB: ' . $mysqli->connect_error . PHP_EOL, FILE_APPEND);
} else {
    $stmt = $mysqli->prepare("UPDATE ventes_cryo SET vente = 1 WHERE reference = ?");
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
}

// Envoi email client
$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = MAILJET_HOST;
$mail->SMTPAuth = true;
$mail->Username = MAILJET_USER;
$mail->Password = MAILJET_PASS;
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('contact@aquavelo.com', 'Aquavelo');
$mail->addReplyTo('contact@aquavelo.com', 'Aquavelo');
$mail->addAddress($email, $prenom . ' ' . $nom);

$mail->isHTML(true);
$mail->Subject = 'Confirmation de votre séance Cryolipolyse';
$mail->Body    = "<p>Bonjour $prenom $nom,</p>
                  <p>Nous confirmons la réception de votre paiement de <strong>$montant</strong> pour votre séance de Cryolipolyse.</p>
                  <p>Votre code de validation : <strong>$code_validation</strong></p>
                  <p>Nous vous contacterons prochainement pour fixer votre rendez-vous.</p>
                  <p>Merci de votre confiance.<br>L'équipe Aquavelo</p>";

$mail->send();

// Envoi email admin
$mail_admin = new PHPMailer();
$mail_admin->isSMTP();
$mail_admin->Host = MAILJET_HOST;
$mail_admin->SMTPAuth = true;
$mail_admin->Username = MAILJET_USER;
$mail_admin->Password = MAILJET_PASS;
$mail_admin->SMTPSecure = 'tls';
$mail_admin->Port = 587;

$mail_admin->setFrom('contact@aquavelo.com', 'Aquavelo');
$mail_admin->addAddress('admin@aquavelo.com', 'Admin Aquavelo');

$mail_admin->isHTML(true);
$mail_admin->Subject = 'Nouvelle commande Cryo validée';
$mail_admin->Body    = "<p>Nouvelle commande confirmée :</p>
                        <ul>
                          <li>Référence : $reference</li>
                          <li>Client : $prenom $nom</li>
                          <li>Email : $email</li>
                          <li>Téléphone : $telephone</li>
                          <li>Montant : $montant</li>
                          <li>Code validation : $code_validation</li>
                        </ul>";

$mail_admin->send();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Merci pour votre commande</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f9f9f9; }
        .box { background: #fff; padding: 30px; border-radius: 10px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #2E8B57; }
        p { font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Merci <?php echo htmlspecialchars($prenom); ?> !</h1>
        <p>Votre paiement de <strong><?php echo htmlspecialchars($montant); ?></strong> a bien été reçu.</p>
        <p>Votre code de validation : <strong><?php echo htmlspecialchars($code_validation); ?></strong></p>
        <p>Nous reviendrons vers vous très vite pour fixer votre séance de Cryolipolyse.</p>
        <p>À bientôt,<br>L'équipe Aquavelo</p>
    </div>
</body>
</html>

















