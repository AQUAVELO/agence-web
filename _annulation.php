<?php
/**
 * Page de traitement de l'annulation de RDV
 */

require '_settings.php';

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$rdv = isset($_GET['rdv']) ? htmlspecialchars($_GET['rdv']) : '';
$city = isset($_GET['city']) ? htmlspecialchars($_GET['city']) : '';

$success = false;

if ($email && $rdv) {
    // 0. Récupérer les infos avant suppression pour l'email
    $stmt = $database->prepare("SELECT name, phone, center_id FROM am_free WHERE email = ? AND name LIKE ? LIMIT 1");
    $stmt->execute([$email, "%" . $rdv . "%"]);
    $booking = $stmt->fetch();

    if ($booking) {
        // 1. Suppression de la réservation dans am_free
        $search_rdv = "%" . $rdv . "%";
        $del = $database->prepare("DELETE FROM am_free WHERE email = ? AND name LIKE ?");
        $del->execute([$email, $search_rdv]);
        
        $success = true;

        // 2. Envoi d'un email d'alerte à l'admin
        if (!empty($settings['mjusername'])) {
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $settings['mjhost'];
                $mail->SMTPAuth = true;
                $mail->Username = $settings['mjusername'];
                $mail->Password = $settings['mjpassword'];
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo Annulation');
                $mail->addAddress('claude@alesiaminceur.com'); // Email admin
                $mail->isHTML(true);
                $mail->Subject = "⚠️ ANNULATION : $city - " . trim(explode('(RDV:', $booking['name'])[0]);
                
                $mail->Body = "<h3>Une annulation a été effectuée</h3>
                              <b>Client :</b> " . htmlspecialchars($booking['name']) . "<br>
                              <b>Email :</b> " . htmlspecialchars($email) . "<br>
                              <b>Tel :</b> " . htmlspecialchars($booking['phone']) . "<br>
                              <b>RDV annulé :</b> " . htmlspecialchars($rdv) . "<br>
                              <b>Centre :</b> " . htmlspecialchars($city);
                
                $mail->send();
                
                // NOTIFICATION TELEGRAM (ANNULATION) - Uniquement pour Cannes, Mandelieu, Vallauris, Antibes
                $planning_centers = [305, 347, 349, 343, 253];
                if (in_array((int)$booking['center_id'], $planning_centers)) {
                    $tg_msg = "<b>❌ ANNULATION $city</b>\n" .
                              "👤 " . trim(explode('(RDV:', $booking['name'])[0]) . "\n" .
                              "📧 " . $email . "\n" .
                              "🗓️ RDV : $rdv";
                    sendTelegram($tg_msg);
                    
                    // Notification spécifique pour le responsable d'Antibes (ID 253)
                    if ((int)$booking['center_id'] == 253) {
                        sendTelegram($tg_msg, '1449612043');
                    }
                }
            } catch (Exception $e) {
                error_log("Erreur Email Annulation: " . $mail->ErrorInfo);
            }
        }
    }
}
?>

<section class="content-area bg1" style="padding: 100px 0;">
  <div class="container">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 50px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;">
      
      <?php if ($success) : ?>
        <div style="font-size: 4rem; color: #ff9800; margin-bottom: 20px;">
          <i class="fa fa-calendar-times-o"></i>
        </div>
        <h2 style="color: #333; margin-bottom: 20px;">Votre rendez-vous a été annulé</h2>
        <p style="font-size: 1.1rem; color: #666; margin-bottom: 30px;">
            Le créneau du <b><?= htmlspecialchars($rdv) ?></b> a bien été libéré dans notre planning.
        </p>
        <a href="index.php?p=page&city=<?= urlencode($city) ?>" class="btn btn-primary" style="padding: 15px 40px; border-radius: 50px; font-weight: bold; background: #00a8cc; border: none; color: white; text-decoration: none;">
          RETOUR AU CENTRE
        </a>
      <?php else : ?>
        <h2 style="color: #d9534f;">Erreur lors de l'annulation</h2>
        <p>Nous n'avons pas pu identifier votre rendez-vous. Merci de nous contacter par téléphone.</p>
        <a href="index.php" class="btn btn-default">Retour à l'accueil</a>
      <?php endif; ?>

    </div>
  </div>
</section>
