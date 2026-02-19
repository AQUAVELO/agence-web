<?php
/**
 * Page de traitement de l'annulation de RDV
 */

require '_settings.php';
require 'load_env.php'; // Charger les variables d'environnement

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Google\Client;
use Google\Service\Calendar;

$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$rdv = isset($_GET['rdv']) ? htmlspecialchars($_GET['rdv']) : '';
$city = isset($_GET['city']) ? htmlspecialchars($_GET['city']) : '';

$success = false;

if ($email && $rdv) {
    // 0. Récupérer les infos avant suppression (y compris google_event_id)
    $stmt = $database->prepare("SELECT name, phone, center_id, google_event_id, google_sync FROM am_free WHERE email = ? AND name LIKE ? LIMIT 1");
    $stmt->execute([$email, "%" . $rdv . "%"]);
    $booking = $stmt->fetch();

    if ($booking) {
        // 0.1 Supprimer l'événement de Google Calendar
        $center_id_int = (int)$booking['center_id'];
        $is_cannes_group = in_array($center_id_int, [305, 347, 349]);
        // Tenter la suppression si : event_id connu, ou centre du groupe Cannes (fallback par recherche)
        if (!empty($booking['google_event_id']) || $is_cannes_group) {
            try {
                $keyFile = __DIR__ . '/google_key.json';
                if (!file_exists($keyFile)) {
                    generateGoogleKeyFile();
                }
                if (file_exists($keyFile)) {
                    $gc_cancel_client = new Client();
                    $gc_cancel_client->setAuthConfig($keyFile);
                    $gc_cancel_client->addScope(Calendar::CALENDAR);
                    $service = new Calendar($gc_cancel_client);

                    // Déterminer l'agenda de destination
                    if ($is_cannes_group) {
                        $targetCalendarId = 'aqua.cannes@gmail.com';
                    } else {
                        $stmt_c = $database->prepare("SELECT email FROM am_centers WHERE id = ?");
                        $stmt_c->execute([$booking['center_id']]);
                        $c_info = $stmt_c->fetch();
                        $targetCalendarId = !empty($c_info['email']) ? $c_info['email'] : 'aqua.cannes@gmail.com';
                    }

                    $gc_deleted = false;

                    // Méthode 1 : suppression directe via google_event_id
                    if (!empty($booking['google_event_id'])) {
                        try {
                            $service->events->delete($targetCalendarId, $booking['google_event_id']);
                            error_log("✅ Annulation: Événement supprimé via event_id: " . $booking['google_event_id'] . " (Calendrier: $targetCalendarId)");
                            $gc_deleted = true;
                        } catch (\Exception $e2) {
                            error_log("⚠️ Annulation: suppression event_id échouée (" . $booking['google_event_id'] . "): " . $e2->getMessage());
                        }
                    }

                    // Méthode 2 (fallback) : recherche par date/heure si event_id absent ou suppression échouée
                    if (!$gc_deleted) {
                        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $rdv, $rdv_matches);
                        if (count($rdv_matches) === 3) {
                            $search_start = \DateTime::createFromFormat('d/m/Y H:i', $rdv_matches[1] . ' ' . $rdv_matches[2], new \DateTimeZone('Europe/Paris'));
                            $search_end   = clone $search_start;
                            $search_end->modify('+1 hour');

                            $gc_events = $service->events->listEvents($targetCalendarId, [
                                'timeMin'      => $search_start->format(\DateTime::RFC3339),
                                'timeMax'      => $search_end->format(\DateTime::RFC3339),
                                'singleEvents' => true,
                            ]);

                            // Nom du client (sans la partie "(RDV:...)")
                            $client_name = trim(explode('(RDV:', $booking['name'])[0]);
                            foreach ($gc_events->getItems() as $gc_evt) {
                                if (stripos($gc_evt->getSummary(), $client_name) !== false) {
                                    $service->events->delete($targetCalendarId, $gc_evt->getId());
                                    error_log("✅ Annulation fallback: événement trouvé et supprimé: " . $gc_evt->getId() . " pour $client_name");
                                    $gc_deleted = true;
                                    break;
                                }
                            }

                            if (!$gc_deleted) {
                                error_log("⚠️ Annulation: aucun événement trouvé dans Google Calendar pour '$client_name' le " . $rdv_matches[1] . " à " . $rdv_matches[2]);
                            }
                        } else {
                            error_log("⚠️ Annulation: format de date non reconnu dans rdv='$rdv'");
                        }
                    }
                }
            } catch (\Exception $e) {
                error_log("⚠️ Annulation: Erreur Google Calendar: " . $e->getMessage());
            }
        }

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
                
                // NOTIFICATION TELEGRAM (ANNULATION) - Uniquement pour Cannes, Mandelieu, Vallauris
                $planning_centers = [305, 347, 349];
                if (in_array((int)$booking['center_id'], $planning_centers)) {
                    $tg_msg = "<b>❌ ANNULATION $city</b>\n" .
                              "👤 " . trim(explode('(RDV:', $booking['name'])[0]) . "\n" .
                              "📧 " . $email . "\n" .
                              "🗓️ RDV : $rdv";
                    sendTelegram($tg_msg);
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
