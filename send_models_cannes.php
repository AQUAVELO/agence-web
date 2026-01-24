<?php
/**
 * Script temporaire pour envoyer les modèles d'emails de Cannes pour validation
 */

require '_settings.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$destinataire = 'aqua.cannes@gmail.com';
$center_id = 305; // Cannes

// Infos fictives pour le test
$client_name = "Jean Dupont";
$client_email = "jean.dupont@test.com";
$rdv_date = "Lundi 26/01/2026 à 10:30";

// Infos centre Cannes
$stmt = $database->prepare("SELECT * FROM am_centers WHERE id = ?");
$stmt->execute([$center_id]);
$center = $stmt->fetch();

$lieu = $center['address'];
$tel = $center['phone'];
$ville = $center['city'];

$models = [
    '1. Confirmation Immédiate' => [
        'subject' => "Confirmation de votre séance à Aquavelo $ville",
        'body' => "Bonjour $client_name,<br><br>Votre séance est confirmée pour le <b>$rdv_date</b>.<br>
                  Lieu : $lieu<br>Tél : $tel<br><br>
                  <b>Important :</b> Merci d'arriver 15 minutes avant le début du cours.<br><br>
                  <b>🎒 N'oubliez pas de venir équipé(e) avec :</b><br>
                  ✅ Votre maillot de bain,<br>
                  ✅ Une serviette,<br>
                  ✅ Un gel douche,<br>
                  ✅ Une bouteille d'eau,<br>
                  ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                  À très bientôt ! Cordialement Claude"
    ],
    '2. Rappel 24h avant' => [
        'subject' => "Votre séance Aquavelo demain !",
        'body' => "Bonjour Jean,<br><br>
                  Votre séance d'essai est réservée :<br><br>
                  🗓️ <b>$rdv_date</b><br><br>
                  Lieu : $lieu<br>
                  Tél : $tel<br><br>
                  Important : Merci d'arriver 15 minutes avant le début du cours.<br><br>
                  À très bientôt ! Cordialement Claude"
    ],
    '3. Rappel 3h avant' => [
        'subject' => "Rappel de séance découverte",
        'body' => "Bonjour Jean,<br><br>
                  Je vous rappelle votre rdv pour la séance découverte :<br><br>
                  🗓️ <b>$rdv_date</b><br><br>
                  Lieu : $lieu<br>
                  Tél : $tel<br><br>
                  Important : Merci d'arriver 15 minutes avant le début du cours.<br><br>
                  À très bientôt ! Cordialement Claude"
    ],
    '4. Suivi 3h après' => [
        'subject' => "Merci de votre visite chez Aquavelo ! 🚴‍♀️💦",
        'body' => "Bonjour Jean,<br><br>
                  Merci d’être venu(e) découvrir l'Aquavelo🚴‍♀️💦 ! J’espère que vous avez apprécié. Nous serons ravis de vous revoir très vite 🌊.<br><br>
                  N’hésitez pas à me contacter si vous avez des questions ou des commentaires, ou pour finaliser votre inscription en <a href='https://www.aquavelo.com/vente_formule' style='color:#00acdc; font-weight:bold; text-decoration:underline;'>cliquant ici</a> si cela n'a pas été fait.<br><br>
                  Cordialement,<br>
                  Claude<br>
                  Tél : 06 22 64 70 95"
    ],
    '5. Suivi J+2' => [
        'subject' => "Votre séance Aquavelo vous a plu ? 💦",
        'body' => "Bonjour Jean,<br><br>
                  J’espère que votre séance découverte Aquavelo vous a plu 💦 !<br>
                  Si vous avez un moment, donnez-nous votre avis par retour email — cela nous aide à progresser 🌟.<br><br>
                  Et si vous souhaitez continuer, vous pouvez dès maintenant vous inscrire en ligne via ce lien si cela n'a pas été fait :<br>
                  👉 <a href='https://www.aquavelo.com/vente_formule' style='color:#00acdc; font-weight:bold; text-decoration:underline;'>cliquer ici</a><br><br>
                  À très bientôt dans l’eau 🌊<br>
                  Claude<br>
                  Tél : 06 22 64 70 95"
    ]
];

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $settings['mjhost'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['mjusername'];
    $mail->Password = $settings['mjpassword'];
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo Cannes');
    $mail->addAddress($destinataire);
    $mail->addReplyTo($center['email'], 'Aquavelo Cannes');
    $mail->isHTML(true);

    foreach ($models as $name => $m) {
        $mail->Subject = "[MODÈLE CANNES] $name : " . $m['subject'];
        $mail->Body = "--- CECI EST UN MODÈLE POUR CANNES ---<br><br>" . $m['body'];
        $mail->send();
        echo "Modèle '$name' envoyé !<br>";
    }

} catch (Exception $e) {
    echo "Erreur : " . $mail->ErrorInfo;
}
