<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Assure-toi que PHPMailer est installé via Composer

// Vérifier que c’est bien pour Antibes
if (!isset($_POST['center']) || $_POST['center'] != 253) {
    die("❌ Centre non valide.");
}

// Identifiants LEAD (Antibes)
$apiKey  = "5ff74ddb-0a0a-4e85-b68b-0eb5e1ea43c6";
$clubKey = "aquavelo-antibes";

// Récupération des données du formulaire
$nomComplet = $_POST['nom'] ?? '';
$email      = $_POST['email'] ?? '';
$phone      = $_POST['phone'] ?? '';

$parts = explode(' ', trim($nomComplet), 2);
$lastname  = $parts[0] ?? '';
$firstname = $parts[1] ?? '';

// =======================
// 1. ENVOI VERS LEAD
// =======================
$data = [
    "apiKey"    => $apiKey,
    "club"      => $clubKey,
    "firstname" => $firstname,
    "lastname"  => $lastname,
    "email"     => $email,
    "phone"     => $phone,
    "source"    => "WEBSITE_FORM",
    "source_url"=> "https://www.aquavelo.com/centres/antibes",
    "message"   => "Demande séance découverte via formulaire Antibes"
];

$ch = curl_init("https://lead.masalledesport.com/api/lead/webhooks/clubEntry");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// =======================
// 2. ENVOI DES EMAILS
// =======================
$mail = new PHPMailer(true);

try {
    // Config SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ton.email@gmail.com';
    $mail->Password   = 'TON_MOT_DE_PASSE_APPLI';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Expéditeur
    $mail->setFrom('ton.email@gmail.com', 'Aquavelo Antibes');

    // Destinataires
    $mail->addAddress("aqua.cannes@gmail.com", "Admin Aquavelo"); // Admin
    if (!empty($email)) {
        $mail->addAddress($email, $firstname . " " . $lastname); // Prospect
    }

    // Contenu email
    $mail->isHTML(true);
    $mail->Subject = "Votre séance découverte Aquavelo Antibes";
    $mail->Body    = "
        <h3>Bonjour $firstname,</h3>
        <p>Merci pour votre inscription à une séance découverte chez <b>Aquavelo Antibes</b>.</p>
        <p>Nous vous recontacterons rapidement pour confirmer votre créneau.</p>
        <hr>
        <p><b>Détails du prospect :</b><br>
        Nom : $lastname<br>
        Prénom : $firstname<br>
        Email : $email<br>
        Téléphone : $phone</p>
        <hr>
        <p>Transmis à LEAD (code réponse : $httpcode)</p>
    ";

    $mail->send();
    echo "✅ Inscription transmise avec succès.";
} catch (Exception $e) {
    echo "❌ Lead envoyé, mais erreur email : {$mail->ErrorInfo}";
}
