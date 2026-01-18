<?php
/**
 * Script de rappel automatique 24h avant le RDV
 * À exécuter par Clever Cloud (Cron)
 */

require '_settings.php';

// Force le fuseau horaire de Paris pour la comparaison
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. On cherche les RDV prévus dans environ 24h qui n'ont pas encore reçu de rappel
// Le format dans la base est "Jour DD/MM/YYYY à HH:mm (ACTIVITE)"
// On récupère tout ce qui n'est pas marqué comme "rappel envoyé"
$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND reminder_sent = 0");
$stmt->execute();
$bookings = $stmt->fetchAll();

$now = new DateTime();
$count = 0;

foreach ($bookings as $booking) {
    // Extraction de la date : "Mardi 20/01/2026 à 09:45 (AQUAVELO)" -> "20/01/2026 09:45"
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    
    if (count($matches) === 3) {
        $rdv_date_str = $matches[1] . ' ' . $matches[2];
        $rdv_date = DateTime::createFromFormat('d/m/Y H:i', $rdv_date_str);
        
        if ($rdv_date) {
            $diff = $now->diff($rdv_date);
            $hours_until = ($diff->days * 24) + $diff->h;

            // Si le RDV est entre 20h et 26h dans le futur (on vise la fenêtre des 24h)
            if ($hours_until >= 20 && $hours_until <= 26 && $rdv_date > $now) {
                
                // ENVOI DE L'EMAIL
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
                    $mail->addAddress($booking['email']);
                    $mail->isHTML(true);
                    
                    $mail->Subject = "Rappel : Votre séance Aquavelo demain !";
                    
                    // Formatage pour l'email
                    $rdv_info = str_replace(['(', ')'], ['', ''], substr($booking['name'], strpos($booking['name'], "(RDV:") + 6));
                    
                    // Extraction précise pour les URLs
                    preg_match('/\(RDV: (.*?)\)\z/', $booking['name'], $rdv_match);
                    $date_heure_exact = $rdv_match[1] ?? '';
                    $nom_prospect = trim(explode('(RDV:', $booking['name'])[0]);

                    // URLs Annuler / Modifier
                    $url_annuler = "https://www.aquavelo.com/index.php?p=annulation&email=" . urlencode($booking['email']) . "&rdv=" . urlencode($date_heure_exact) . "&city=Cannes";
                    $url_modifier = "https://www.aquavelo.com/index.php?p=calendrier_cannes&center=305&nom=" . urlencode($nom_prospect) . "&email=" . urlencode($booking['email']) . "&phone=" . urlencode($booking['phone']) . "&old_rdv=" . urlencode($date_heure_exact);

                    $mail->Body = "Bonjour " . explode(' ', $booking['name'])[0] . ",<br><br>
                                  Ceci est un petit rappel pour votre séance de demain :<br>
                                  🗓️ <b>$rdv_info</b><br><br>
                                  Lieu : 60 Avenue du Dr Raymond Picaud, 06150 Cannes<br>
                                  Tél : 04 93 93 05 65<br><br>
                                  <b>🎒 N'oubliez pas de venir équipé(e) avec :</b><br>
                                  ✅ Votre maillot de bain,<br>
                                  ✅ Une serviette,<br>
                                  ✅ Un gel douche,<br>
                                  ✅ Une bouteille d'eau,<br>
                                  ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                                  À demain ! Cordialement Claude<br><br>
                                  <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
                                  <p style='color:#999; font-size:0.9rem;'>Un contretemps ?</p>
                                  <table cellspacing='0' cellpadding='0'>
                                    <tr>
                                      <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'>
                                        <a href='$url_annuler' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Annuler</a>
                                      </td>
                                      <td width='10'></td>
                                      <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'>
                                        <a href='$url_modifier' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Modifier</a>
                                      </td>
                                    </tr>
                                  </table>";
                    
                    $mail->send();
                    
                    // Marquer comme envoyé
                    $upd = $database->prepare("UPDATE am_free SET reminder_sent = 1 WHERE id = ?");
                    $upd->execute([$booking['id']]);
                    $count++;
                    
                } catch (Exception $e) {
                    error_log("Erreur Cron Rappel: " . $mail->ErrorInfo);
                }
            }
        }
    }
}

echo "Nombre de rappels envoyés : $count";
?>
