<?php
/**
 * Script d'envoi automatique ~48h après la séance d'essai
 * À exécuter par Clever Cloud (Cron)
 */

require_once '_settings.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND followup_48h_sent = 0");
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
            $hours_passed = ($diff->days * 24) + $diff->h;
            $is_past = ($now > $rdv_start);

            // Fenêtre d'envoi : ~48h après le début (44h à 52h)
            if ($is_past && $hours_passed >= 44 && $hours_passed <= 52) {
                try {
                    $center_id = $booking['center_id'] ?: 305;

                    $lookup_center_id = in_array((int)$center_id, [305, 347, 349]) ? 305 : $center_id;

                    $stmt_c = $database->prepare("SELECT city, address, phone, email FROM am_centers WHERE id = ?");
                    $stmt_c->execute([$lookup_center_id]);
                    $center_info = $stmt_c->fetch();

                    if (!$center_info) {
                        $center_info = [
                            'city' => 'Cannes',
                            'address' => '60 avenue du Docteur Raymond Picaud, Cannes',
                            'phone' => '06 22 64 70 95',
                            'email' => 'aqua.cannes@gmail.com',
                        ];
                    }

                    $prenom = explode(' ', trim(explode('(RDV:', $booking['name'])[0]))[0];
                    $lien_inscription = "https://www.aquavelo.com/vente_formule";

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
                    $mail->Subject = 'Suite à votre séance découverte Aquavelo 😊';

                    if (in_array((int)$center_id, [305, 347, 349])) {
                        $mail->Body = "Bonjour " . $prenom . ",<br><br>
                                      J’espère que vous allez bien 😊<br><br>
                                      Je me permets de revenir vers vous suite à votre séance découverte d’Aquavelo🚴‍♀️💦.<br><br>
                                      Beaucoup de personnes ressentent déjà les bienfaits après quelques séances : tonicité, bien-être, jambes plus légères et un vrai moment de détente 🌊.<br><br>
                                      Si vous souhaitez poursuivre l’expérience et finaliser votre inscription, vous pouvez le faire directement ici : <a href='" . $lien_inscription . "' style='color:#00acdc; font-weight:bold; text-decoration:underline;'>cliquer ici</a><br><br>
                                      Je reste bien sûr disponible si vous avez la moindre question ou besoin d’informations complémentaires.<br><br>
                                      Au plaisir de vous revoir prochainement 😊<br><br>
                                      Cordialement,<br>
                                      Claude<br>
                                      Tél : 06 22 64 70 95";
                    } else {
                        $mail->Body = "Bonjour " . $prenom . ",<br><br>
                                      J’espère que vous allez bien 😊<br><br>
                                      Nous revenons vers vous suite à votre séance découverte d’Aquavelo🚴‍♀️💦.<br><br>
                                      Beaucoup de personnes ressentent déjà les bienfaits après quelques séances : tonicité, bien-être, jambes plus légères et un vrai moment de détente 🌊.<br><br>
                                      Si vous souhaitez poursuivre l’expérience et finaliser votre inscription, vous pouvez le faire directement ici : <a href='" . $lien_inscription . "' style='color:#00acdc; font-weight:bold; text-decoration:underline;'>cliquer ici</a><br><br>
                                      N’hésitez pas à nous contacter si vous avez la moindre question ou besoin d’informations complémentaires.<br><br>
                                      Au plaisir de vous revoir prochainement 😊<br><br>
                                      Cordialement,<br>
                                      Aquavelo " . $center_info['city'] . "<br>
                                      Tél : " . $center_info['phone'];
                    }

                    $mail->send();

                    $upd = $database->prepare("UPDATE am_free SET followup_48h_sent = 1 WHERE id = ?");
                    $upd->execute([$booking['id']]);
                    $count++;

                } catch (Exception $e) {
                    error_log("Erreur Cron Suivi 48h: " . $mail->ErrorInfo);
                }
            }
        }
    }
}

echo "Nombre d'emails de suivi 48h envoyés : $count";
