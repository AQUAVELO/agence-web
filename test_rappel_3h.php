<?php
/**
 * Simulation d'envoi du rappel 3h (Version Finale Mise à jour)
 * Envoyé immédiatement à Claude pour validation finale du contenu
 */

require '_settings.php';

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// CONFIGURATION DE LA SIMULATION
$destinataire = "claude@alesiaminceur.com";
$nom_client = "Rodriguez"; // Simule le nom du prospect
$rdv_info_brut = "Lundi 19/01/2026 à 09:45 (AQUAVELO)";
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
    
    // URLs pour la simulation
    $url_annuler = "https://www.aquavelo.com/index.php?p=annulation&email=" . urlencode($destinataire) . "&rdv=" . urlencode($rdv_info_brut) . "&city=" . urlencode($city);
    $url_modifier = "https://www.aquavelo.com/index.php?p=calendrier_cannes&center=305&nom=" . urlencode($nom_client) . "&email=" . urlencode($destinataire) . "&phone=0600000000&old_rdv=" . urlencode($rdv_info_brut);

    // Contenu exact demandé
    $mail->Body = "Bonjour " . $nom_client . ",<br><br>
                  Je vous rappelle votre rdv pour <b>" . $rdv_info_brut . "</b>.<br><br>
                  Lieu : 60 Avenue du Dr Raymond Picaud, 06150 Cannes,<br>
                  Bus : arrêt Leader ou Méridien.<br>
                  Tél : 04 93 93 05 65<br><br>
                  <b>Important :</b> Merci d'arriver 15 minutes avant le début du cours.<br><br>
                  <b>🎒 N'oubliez pas de venir équipé(e) avec :</b><br>
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
    echo "Simulation de rappel 3h envoyée à $destinataire avec le nouveau modèle.";
    
} catch (Exception $e) {
    echo "Erreur lors de l'envoi de la simulation : " . $mail->ErrorInfo;
}
?>
