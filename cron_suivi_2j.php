<?php
/**
 * Script d'envoi automatique 2 jours après la séance
 * À exécuter par Clever Cloud (Cron)
 */

require '_settings.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// On cherche les RDV passés (J+2) qui n'ont pas encore reçu l'email de suivi J+2
$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND followup_2d_sent = 0");
$stmt->execute();
$bookings = $stmt->fetchAll();

$now = new DateTime();
$count = 0;

foreach ($bookings as $booking) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    
    if (count($matches) === 3) {
        $rdv_start = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
        
        if ($rdv_start) {
            $diff = $rdv_start->diff($now);
            $days_passed = $diff->days;
            $is_past = ($now > $rdv_start);

            // Fenêtre d'envoi : environ 2 jours après (entre 44h et 60h après le début)
            $hours_passed = ($days_passed * 24) + $diff->h;

            if ($is_past && $hours_passed >= 44 && $hours_passed <= 60) {
                try {
                    $center_id = $booking['center_id'] ?: 305;
                    
                    // Pour Cannes/Mandelieu/Vallauris, utiliser les coordonnées de Cannes
                    $lookup_center_id = in_array((int)$center_id, [305, 347, 349]) ? 305 : $center_id;
                    
                    $stmt_c = $database->prepare("SELECT city, address, phone FROM am_centers WHERE id = ?");
                    $stmt_c->execute([$lookup_center_id]);
                    $center_info = $stmt_c->fetch();
                    
                    if (!$center_info) {
                        $center_info = ['city' => 'Cannes', 'address' => '60 avenue du Docteur Raymond Picaud, Cannes', 'phone' => '06 22 64 70 95'];
                    }

                    // Extraction du prénom (première partie avant l'espace)
                    $full_name = trim(explode('(RDV:', $booking['name'])[0]);
                    $name_parts = explode(' ', $full_name);
                    $prenom = $name_parts[0];

                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $settings['mjhost'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $settings['mjusername'];
                    $mail->Password = $settings['mjpassword'];
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo ' . $center_info['city']);
                    $mail->addAddress($booking['email']);
                    if (!empty($center_info['email'])) {
                        $mail->addReplyTo($center_info['email'], 'Aquavelo ' . $center_info['city']);
                    }
                    $mail->isHTML(true);
                    
                    $mail->Subject = "Votre séance Aquavelo vous a plu ? 💦";
                    
                    if (in_array((int)$center_id, [305, 347, 349])) {
                        $mail->Body = "Bonjour " . $prenom . ",<br><br>
                                      J’espère que votre séance découverte Aquavelo vous a plu 💦 !<br>
                                      Si vous avez un moment, donnez-nous votre avis par retour email — cela nous aide à progresser 🌟.<br><br>
                                      Et si vous souhaitez continuer, vous pouvez dès maintenant vous inscrire en ligne via ce lien si cela n'a pas été fait :<br>
                                      👉 <a href='https://www.aquavelo.com/vente_formule' style='color:#00acdc; font-weight:bold; text-decoration:underline;'>cliquer ici</a><br><br>
                                      À très bientôt dans l’eau 🌊<br>
                                      Claude<br>
                                      Tél : 06 22 64 70 95";
                    } else {
                        $mail->Body = "Bonjour " . $prenom . ",<br><br>
                                      J’espère que votre séance découverte Aquavelo vous a plu 💦 !<br>
                                      Si vous avez un moment, donnez-nous votre avis par retour email — cela nous aide à progresser 🌟.<br><br>
                                      N’hésitez pas à nous contacter si vous avez des questions ou des commentaires, ou pour finaliser votre inscription.<br><br>
                                      À très bientôt dans l’eau 🌊<br>
                                      Cordialement,<br>
                                      Aquavelo " . $center_info['city'] . "<br>
                                      Tél : " . $center_info['phone'];
                    }
                    
                    $mail->send();
                    
                    // Marquer comme envoyé
                    $upd = $database->prepare("UPDATE am_free SET followup_2d_sent = 1 WHERE id = ?");
                    $upd->execute([$booking['id']]);
                    $count++;
                    
                } catch (Exception $e) {
                    error_log("Erreur Cron Suivi 2J: " . $mail->ErrorInfo);
                }
            }
        }
    }
}

echo "Nombre d'emails de suivi J+2 envoyés : $count";
?>
