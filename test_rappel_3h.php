<?php
/**
 * Simulation d'envoi du rappel 3h
 * Envoyé immédiatement à Claude pour vérification du contenu
 */

require '_settings.php';

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// CONFIGURATION DE LA SIMULATION
$destinataire = "claude@alesiaminceur.com";
$nom_client = "Claude (Test)";
$rdv_info = "Lundi 19/01/2026 à 09:45 (AQUAVELO)";
$city = "Cannes";

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $settings['mjhost'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['mjusername'];
    $mail->Password = $settings['mjpassword'];
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo');
    $mail->addAddress($destinataire);
    $mail->isHTML(true);
    
    $mail->Subject = "À tout à l'heure ! Votre séance Aquavelo dans 3 heures";
    
    // Construction des URLs (simulation)
    $url_annuler = "https://www.aquavelo.com/index.php?p=annulation&email=" . urlencode($destinataire) . "&rdv=" . urlencode($rdv_info) . "&city=" . urlencode($city);
    $url_modifier = "https://www.aquavelo.com/index.php?p=calendrier_cannes&center=305&nom=" . urlencode($nom_client) . "&email=" . urlencode($destinataire) . "&phone=0600000000&old_rdv=" . urlencode($rdv_info);

    $mail->Body = "Bonjour " . $nom_client . ",<br><br>
                  Votre séance Aquavelo commence dans environ <b>3 heures</b> !<br><br>
                  🗓️ <b>" . str_replace(['(', ')'], ['', ''], substr($rdv_info, strpos($rdv_info, "(RDV:") ?: 0)) . "</b><br><br>
                  Lieu : 60 Avenue du Dr Raymond Picaud, 06150 Cannes<br>
                  Tél : 04 93 93 05 65<br><br>
                  <b>Important :</b> Merci d'arriver 15 minutes avant le début du cours.<br><br>
                  <b>🎒 À prévoir pour votre séance :</b><br>
                  ✅ Votre maillot de bain,<br>
                  ✅ Une serviette,<br>
                  ✅ Un gel douche,<br>
                  ✅ Une bouteille d'eau,<br>
                  ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                  À très bientôt ! Cordialement Claude<br><br>
                  <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
                  <p style='color:#999; font-size:0.9rem;'>Un contretemps ?</p>
                  <table cellspacing='0' cellpadding='0'><tr>
                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_annuler' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Annuler</a></td>
                  <td width='10'></td>
                  <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_modifier' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Modifier</a></td>
                  </tr></table>";
    
    $mail->send();
    echo "Simulation de rappel 3h envoyée avec succès à $destinataire";
    
} catch (Exception $e) {
    echo "Erreur lors de l'envoi de la simulation : " . $mail->ErrorInfo;
}
?>
