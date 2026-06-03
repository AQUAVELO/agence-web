<?php
/**
 * Traitement POST formulaire cryolipolyse — exécuté avant tout HTML (index.php).
 */
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$cryo_success = false;
$cryo_error = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cryo_submit'])) {
    return;
}

$prenom = htmlspecialchars(trim($_POST['cryo_prenom'] ?? ''));
$nom = htmlspecialchars(trim($_POST['cryo_nom'] ?? ''));
$email = filter_var(trim($_POST['cryo_email'] ?? ''), FILTER_SANITIZE_EMAIL);
$telephone = htmlspecialchars(trim($_POST['cryo_telephone'] ?? ''));
$zone = htmlspecialchars(trim($_POST['cryo_zone'] ?? ''));
$horaire = htmlspecialchars(trim($_POST['cryo_horaire'] ?? ''));
$message = htmlspecialchars(trim($_POST['cryo_message'] ?? ''));

if ($prenom === '' || $nom === '' || $email === '' || $telephone === '' || $zone === '' || $horaire === '') {
    $cryo_error = 'Veuillez remplir tous les champs obligatoires.';
    return;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $cryo_error = 'Veuillez entrer une adresse email valide.';
    return;
}

$createMailer = static function (string $fromEmail, string $fromName) use ($settings): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $settings['mjhost'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['mjusername'];
    $mail->Password = $settings['mjpassword'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($fromEmail, $fromName);
    $mail->isHTML(true);

    return $mail;
};

$fromEmail = 'service.clients@aquavelo.com';
$fromName = 'Aquavelo Cannes';
$centreOk = false;
$clientOk = false;

$adminBody = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
        <div style='background-color: #e0f7fa; padding: 30px; border-radius: 15px 15px 0 0; text-align: center; border: 2px solid #00a8cc;'>
            <h1 style='color: #00a8cc; margin: 0; font-size: 24px;'>Nouvelle réservation Cryolipolyse</h1>
            <p style='color: #00a8cc; margin-top: 10px; font-weight: bold;'>Offre découverte à 99€</p>
        </div>
        <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 15px 15px;'>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Prénom :</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$prenom}</td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Nom :</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$nom}</td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Email :</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'><a href='mailto:{$email}'>{$email}</a></td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Téléphone :</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'><a href='tel:{$telephone}'>{$telephone}</a></td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Zone :</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$zone}</td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Horaire :</strong></td><td style='padding: 10px; border-bottom: 1px solid #eee;'>{$horaire}</td></tr>
                " . ($message !== '' ? "<tr><td style='padding: 10px;'><strong>Message :</strong></td><td style='padding: 10px;'>{$message}</td></tr>" : '') . "
            </table>
        </div>
    </div>
";

try {
    $mailCentre = $createMailer($fromEmail, $fromName);
    $mailCentre->addAddress('aqua.cannes@gmail.com', 'Aquavelo Cannes');
    $mailCentre->addBCC('claude@alesiaminceur.com', 'Controle Aquavelo');
    $fallback = trim((string) ($settings['prospect_admin_fallback_email'] ?? ''));
    if ($fallback !== '' && filter_var($fallback, FILTER_VALIDATE_EMAIL)
        && !in_array(strtolower($fallback), ['aqua.cannes@gmail.com', 'claude@alesiaminceur.com'], true)) {
        $mailCentre->addCC($fallback);
    }
    $mailCentre->addReplyTo($email, $prenom . ' ' . $nom);
    $mailCentre->Subject = "Nouvelle réservation Cryolipolyse - {$prenom} {$nom}";
    $mailCentre->Body = $adminBody;
    $mailCentre->AltBody = "Cryolipolyse\nPrénom: {$prenom}\nNom: {$nom}\nEmail: {$email}\nTél: {$telephone}\nZone: {$zone}\nHoraire: {$horaire}\nMessage: {$message}";
    $mailCentre->send();
    $centreOk = true;
    error_log('Cryolipolyse admin email OK | message_id=' . $mailCentre->getLastMessageID());
} catch (Exception $e) {
    error_log('Cryolipolyse admin email FAIL: ' . $e->getMessage());
}

try {
    $mailProspect = $createMailer($fromEmail, $fromName);
    $mailProspect->addAddress($email, $prenom . ' ' . $nom);
    $mailProspect->addReplyTo('aqua.cannes@gmail.com', 'Aquavelo Cannes');
    $mailProspect->Subject = 'Votre demande de séance Cryolipolyse a bien été reçue';
    $mailProspect->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <p style='color: #00a8cc; font-size: 16px; font-weight: bold;'>Merci {$prenom}, nous avons bien reçu votre demande.</p>
            <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                <p><strong>Zone :</strong> {$zone}</p>
                <p><strong>Horaire préféré :</strong> {$horaire}</p>
                <p><strong>Offre :</strong> Séance découverte à 99€ au lieu de 149€</p>
            </div>
            <p>Notre équipe vous contactera rapidement pour confirmer votre rendez-vous.</p>
            <p><a href='tel:0493930565' style='color: #00a8cc;'>04 93 93 05 65</a></p>
            <p style='color: #999; font-size: 0.85rem;'>Aquavelo Cannes — 60 avenue du Docteur Picaud, 06150 Cannes</p>
        </div>
    ";
    $mailProspect->AltBody = "Bonjour {$prenom},\n\nMerci pour votre demande Cryolipolyse.\nZone: {$zone}\nHoraire: {$horaire}\nOffre: 99€\n\nL'équipe Aquavelo Cannes";
    $mailProspect->send();
    $clientOk = true;
    error_log('Cryolipolyse client email OK | message_id=' . $mailProspect->getLastMessageID());
} catch (Exception $e) {
    error_log('Cryolipolyse client email FAIL: ' . $e->getMessage());
}

if ($centreOk) {
    $cryo_success = true;
    if (!$clientOk) {
        error_log("Cryolipolyse: admin OK mais email client non envoyé pour {$email}");
    }
} else {
    $cryo_error = 'Une erreur est survenue lors de l\'envoi. Merci de nous appeler au 04 93 93 05 65 pour réserver.';
}
