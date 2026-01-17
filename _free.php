<?php
/**
 * Page Séance Découverte Gratuite - Version Production avec Emails
 */

require '_settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
    
    $error = [];
    $center_id = isset($_POST['center']) ? intval($_POST['center']) : 0;
    
    // 1. Vérification du centre
    $center_contact = $database->prepare('SELECT * FROM am_centers WHERE id = ?');
    $center_contact->execute(array($center_id));
    $row_center_contact = $center_contact->fetch();
    
    if (!$row_center_contact) {
        $error[] = "Le centre sélectionné est invalide.";
    }
    
    $input_nom_complet = strip_tags(trim($_POST['nom'] ?? ''));
    $email = strip_tags(trim($_POST['email'] ?? ''));
    $tel = strip_tags(trim($_POST['phone'] ?? ''));
    $date_heure = isset($_POST['date_heure']) ? strip_tags($_POST['date_heure']) : '';
    $segment = isset($_POST['segment']) ? strip_tags($_POST['segment']) : 'free-trial';
    
    if (empty($error)) {
        $city = $row_center_contact['city'];
        $email_center = $row_center_contact['email'] ?: 'claude@alesiaminceur.com';
        $reference = 'AQ' . date('dmhis');
        $input_name_db = ($date_heure) ? $input_nom_complet . " (RDV: " . $date_heure . ")" : $input_nom_complet;

        try {
            // A. Enregistrement Table am_free
            $add_free = $database->prepare("INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $add_free->execute(array($reference, $center_id, 3, $input_name_db, $email, $tel, $segment));
            
            // B. Enregistrement Table client (Split Nom/Prénom)
            $check_client = $database->prepare("SELECT id FROM client WHERE email = ?");
            $check_client->execute([$email]);
            if ($check_client->rowCount() == 0) {
                $name_parts = explode(' ', $input_nom_complet, 2);
                $prenom_db = $name_parts[0];
                $nom_db = isset($name_parts[1]) ? $name_parts[1] : $name_parts[0];
                if (count($name_parts) == 1) $prenom_db = "-";

                $add_client = $database->prepare("INSERT INTO client (nom, prenom, tel, email, ville) VALUES (?, ?, ?, ?, ?)");
                $add_client->execute([$nom_db, $prenom_db, $tel, $email, $city]);
            }

            // C. ENVOI DES EMAILS (uniquement si Mailjet est configuré dans _settings.php)
            if (!empty($settings['mjusername'])) {
                try {
                    // Vérification si c'est une 2ème séance (Alerte Admin)
                    $is_second_session = false;
                    if ($date_heure) {
                        $check_double = $database->prepare("SELECT id FROM am_free WHERE email = ? AND name LIKE '%(RDV:%' AND id != ?");
                        $last_id = $database->lastInsertId();
                        $check_double->execute([$email, $last_id]);
                        if ($check_double->rowCount() > 0) {
                            $is_second_session = true;
                        }
                    }

                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $settings['mjhost'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $settings['mjusername'];
                    $mail->Password = $settings['mjpassword'];
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    // 1. Email pour l'ADMIN (Vous / Dirigeant)
                    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo Resa');
                    $mail->addAddress($email_center);
                    $mail->isHTML(true);
                    
                    $subject_admin = "Nouveau contact $city - $input_nom_complet";
                    if ($is_second_session) {
                        $subject_admin = "⚠️ ALERTE : Tentative de 2ème séance - $input_nom_complet";
                    }
                    
                    $mail->Subject = $subject_admin;

                    // Choix du modèle selon le centre
                    $special_centers = [305, 347, 349, 253];
                    if (in_array((int)$center_id, $special_centers)) {
                        // Modèle détaillé pour Cannes, Mandelieu, Vallauris, Antibes
                        $mail->Body = "<h3>" . ($is_second_session ? "<span style='color:red;'>⚠️ ATTENTION : CE CLIENT A DÉJÀ RÉSERVÉ UNE SÉANCE AUPARAVANT</span>" : "Nouveau prospect") . "</h3>
                                      <b>Nom:</b> $input_nom_complet<br>
                                      <b>Email:</b> $email<br>
                                      <b>Tel:</b> $tel<br>
                                      <b>Centre:</b> $city<br>
                                      <b>RDV choisi:</b> " . ($date_heure ?: 'Pas encore choisi');
                    } else {
                        // NOUVEAU MODÈLE POUR LES AUTRES DIRIGEANTS
                        $date_now = date('d-m-Y H:i:s');
                        $alert_msg = ($is_second_session) ? "<p style='color:red; font-weight:bold;'>⚠️ ATTENTION : CE CLIENT A DÉJÀ RÉSERVÉ UNE SÉANCE AUPARAVANT</p>" : "";
                        
                        $mail->Body = "Bonjour,<br><br>
                                      $alert_msg
                                      <b>$input_nom_complet</b><br>
                                      Adresse électronique : <b>$email</b><br>
                                      Téléphone : <b>$tel</b><br><br>
                                      La personne ci-dessus a commandée une séance découverte gratuite ainsi qu'un bilan minceur dans votre centre.<br>
                                      Nous vous invitons à la contacter pour prendre rendez-vous.<br><br>
                                      Cordialement,<br>
                                      L'équipe Aquavelo<br><br>
                                      <small>(Demande effectuée à partir du site aquavelo.com, le $date_now)</small>";
                    }
                    $mail->send();

                    // 2. Email pour le CLIENT
                    if ($date_heure) {
                        // ... (Code existant pour Cannes/Mandelieu/Vallauris)
                        $mail->clearAddresses();
                        $mail->addAddress($email);
                        $mail->Subject = "Confirmation de votre séance à Aquavelo $city";
                        $rdv_formatted = str_replace(['(', ')'], ['pour un cours ', ''], $date_heure);
                        $mail->Body = "Bonjour $input_nom_complet,<br><br>
                                      Votre séance est confirmée pour le <b>$rdv_formatted</b>.<br>
                                      Lieu : 60 Avenue du Dr Raymond Picaud, 06150 Cannes,<br>
                                      Bus : arrêt Leader ou Méridien Tél : 04 93 93 05 65<br><br>
                                      <b>Important :</b> Merci d'arriver 15 minutes avant le début du cours.<br><br>
                                      <b>🎒 À prévoir pour votre séance :</b><br>
                                      ✅ Maillot de bain<br>
                                      ✅ Serviette de bain<br>
                                      ✅ Gel douche<br>
                                      ✅ Bouteille d'eau<br>
                                      ✅ Chaussures aquabiking (si vous ne les avez pas nous vous les prêterons)<br><br>
                                      À très bientôt ! Cordialement Claude";
                        $mail->send();
                    } elseif ((int)$center_id === 253) {
                        // MODÈLE SPÉCIFIQUE ANTIBES
                        $mail->clearAddresses();
                        $mail->addAddress($email);
                        $mail->Subject = "Votre séance découverte gratuite chez Aquavelo Antibes";
                        
                        $mail->Body = "Bonjour $input_nom_complet,<br><br>
                                      Nous sommes ravis de vous offrir une séance découverte gratuite au centre Aquavélo de <b>Antibes</b>.<br><br>
                                      Lors de votre visite, vous profiterez d'un cours d'aquabiking coaché, encadré par nos professeurs de sport diplômés. Nous commencerons par un bilan personnalisé pour mieux comprendre vos besoins et vous aider à atteindre vos objectifs forme et bien-être.<br><br>
                                      <b>Prenez dès maintenant rendez-vous directement sur :</b><br>
                                      👉 <a href='https://calendly.com/aquavelo-antibes'>https://calendly.com/aquavelo-antibes</a><br>
                                      ou en appelant le <b>" . $row_center_contact['phone'] . "</b>.<br><br>
                                      N'oubliez pas de venir équipé(e) avec :<br>
                                      ✅ Votre maillot de bain,<br>
                                      ✅ Une serviette,<br>
                                      ✅ Un gel douche,<br>
                                      ✅ Une bouteille d'eau,<br>
                                      ✅ Et des chaussures adaptées à l'aquabiking.<br><br>
                                      <b>Adresse :</b> " . $row_center_contact['address'] . "<br>
                                      <i>*Offre non cumulable. Réservez vite, les places sont limitées.</i><br><br>
                                      Cordialement,<br>
                                      L'équipe Aquavélo<br>
                                      <a href='https://www.aquavelo.com'>www.aquavelo.com</a>";
                        $mail->send();
                    } else {
                        // MODÈLE PAR DÉFAUT POUR TOUS LES AUTRES CENTRES
                        $mail->clearAddresses();
                        $mail->addAddress($email);
                        $mail->Subject = "Votre séance découverte gratuite chez Aquavelo $city";
                        
                        $mail->Body = "Bonjour $input_nom_complet,<br><br>
                                      Nous sommes ravis de vous offrir une séance découverte gratuite au centre Aquavélo de <b>$city</b>.<br><br>
                                      Lors de votre visite, vous profiterez d'un cours d'aquabiking coaché, encadré par nos professeurs de sport diplômés. Nous commencerons par un bilan personnalisé pour mieux comprendre vos besoins et vous aider à atteindre vos objectifs forme et bien-être.<br><br>
                                      <b>Prenez dès maintenant rendez-vous directement en appelant le :</b><br>
                                      👉 <b>" . $row_center_contact['phone'] . "</b>.<br><br>
                                      N'oubliez pas de venir équipé(e) avec :<br>
                                      ✅ Votre maillot de bain,<br>
                                      ✅ Une serviette,<br>
                                      ✅ Un gel douche,<br>
                                      ✅ Une bouteille d'eau,<br>
                                      ✅ Et des chaussures adaptées à l'aquabiking.<br><br>
                                      <b>Adresse :</b> " . $row_center_contact['address'] . "<br>
                                      <i>*Offre non cumulable. Réservez vite, les places sont limitées.</i><br><br>
                                      Cordialement,<br>
                                      L'équipe Aquavélo<br>
                                      <a href='https://www.aquavelo.com'>www.aquavelo.com</a>";
                        $mail->send();
                    }
                } catch (Exception $e) {
                    // On ne bloque pas la navigation si l'email échoue
                    error_log("Erreur Email: " . $mail->ErrorInfo);
                }
            }

            // D. REDIRECTION
            if ($segment == 'calendrier-cannes') {
                $url = "index.php?p=merci_rdv&center=305&rdv=" . urlencode($date_heure) . "&nom=" . urlencode($input_nom_complet) . "&email=" . urlencode($email) . "&phone=" . urlencode($tel) . "&city=" . urlencode($city);
                echo "<script>window.location.href = '$url';</script>";
                exit;
            } elseif (in_array((int)$center_id, [305, 347, 349])) {
                $url = "index.php?p=calendrier_cannes&center=305&nom=" . urlencode($input_nom_complet) . "&email=" . urlencode($email) . "&phone=" . urlencode($tel);
                echo "<script>window.location.href = '$url';</script>";
                exit;
            } else {
                echo "<script>window.location.href = 'index.php?p=free&success=1&cid=$center_id';</script>";
                exit;
            }

        } catch (Exception $e) {
            $error_message = "Erreur technique : " . $e->getMessage();
        }
    }
}

if (isset($_GET['success'])) {
    $success_message = "Votre demande a bien été envoyée !";
    if (isset($_GET['cid'])) {
        $cid = intval($_GET['cid']);
        $stmt = $database->prepare("SELECT city, phone FROM am_centers WHERE id = ?");
        $stmt->execute([$cid]);
        $cinfo = $stmt->fetch();
        if ($cinfo) {
            $success_message .= "<br><br>Veuillez appeler le centre de <b>" . $cinfo['city'] . "</b> au <b>" . $cinfo['phone'] . "</b> pour confirmer votre rendez-vous.";
        }
    }
}
?>

<section class="content-area bg1" style="padding: 40px 0 100px 0;">
  <div class="container">
    <?php if ($error_message): ?>
      <div class="alert alert-danger" style="max-width: 600px; margin: 0 auto 20px; border-radius: 10px;"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
      <div class="alert alert-success" style="max-width: 600px; margin: 40px auto; border-radius: 15px; background: #d4edda; color: #155724; padding: 40px; text-align: center;">
        <i class="fa fa-check-circle" style="font-size: 4rem; display: block; margin-bottom: 20px;"></i>
        <h2>Merci !</h2>
        <p><?= $success_message ?></p>
        <div style="margin-top: 30px;"><a href="index.php" class="btn btn-primary">RETOUR À L'ACCUEIL</a></div>
      </div>
    <?php else: ?>
      <!-- Formulaire identique au local -->
      <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #00a8cc; margin-bottom: 25px;">Réservez votre séance gratuite</h2>
        <form role="form" method="POST" action="index.php?p=free" id="mainFreeForm">
            <div style="margin-bottom: 15px;">
                <label>Centre *</label>
                <select name="center" id="centerSelect" required style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;">
                    <option value="">-- Choisissez un centre --</option>
                    <?php foreach ($centers_list_d as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['city'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;"><label>Nom et Prénom *</label><input type="text" name="nom" required style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;"></div>
            <div style="margin-bottom: 15px;"><label>Email *</label><input type="email" name="email" required style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;"></div>
            <div style="margin-bottom: 25px;"><label>Téléphone *</label><input type="tel" name="phone" required style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px;"></div>
            <button type="submit" id="submitBtnText" style="width: 100%; height: 60px; background: #00a8cc; color: white; border: none; border-radius: 5px; font-weight: bold; font-size: 1.1rem; cursor: pointer;">RECEVOIR MON BON GRATUIT</button>
        </form>
      </div>
      <script>
      const centerSelect = document.getElementById('centerSelect');
      const submitBtnText = document.getElementById('submitBtnText');
      centerSelect.addEventListener('change', function() {
          if ([305, 347, 349].includes(parseInt(this.value))) submitBtnText.innerText = "RÉSERVER MA SÉANCE OFFERTE";
          else submitBtnText.innerText = "RECEVOIR MON BON GRATUIT";
      });
      </script>
    <?php endif; ?>
  </div>
</section>
