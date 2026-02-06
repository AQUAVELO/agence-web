<?php
/**
 * Page Séance Découverte Gratuite - Version Finale avec Annulation/Replanification
 */

require '_settings.php';

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error_message = '';
$success_message = '';

// Récupérer les messages depuis la session
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

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
    
    // Anti-spam pour Aix-en-Provence : Vérification captcha
    if ($row_center_contact && stripos($row_center_contact['city'], 'Aix') !== false) {
        $captcha_response = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
        if ($captcha_response !== '3') {
            $error[] = "Erreur de vérification : réponse incorrecte à la question anti-spam.";
        }
    }
    
    $input_nom_complet = strip_tags(trim($_POST['nom'] ?? ''));
    $email = strip_tags(trim($_POST['email'] ?? ''));
    $tel = strip_tags(trim($_POST['phone'] ?? ''));
    $date_heure = isset($_POST['date_heure']) ? strip_tags($_POST['date_heure']) : '';
    $segment = isset($_POST['segment']) ? strip_tags($_POST['segment']) : 'free-trial';
    
    // Vérifier qu'un email ne peut prendre qu'une seule séance d'essai
    if ($email && $segment !== 'calendrier-cannes' && $row_center_contact) {
        $check_existing = $database->prepare("SELECT COUNT(*) as count FROM am_free WHERE email = ? AND name LIKE '%(RDV:%'");
        $check_existing->execute([$email]);
        $existing = $check_existing->fetch();
        if ($existing && $existing['count'] > 0) {
            $tel_center = $row_center_contact['phone'] ?: '06 22 64 70 95';
            $error[] = "Vous ne pouvez pas effectuer une 2ème séance d'essai, pour plus d'infos appelez le " . $tel_center;
        }
    }
    
    // GESTION REPLANIFICATION : Si un ancien RDV est fourni, on le supprime d'abord
    $old_rdv = isset($_POST['old_rdv']) ? strip_tags($_POST['old_rdv']) : '';
    $rescheduling_alert = false;
    if ($old_rdv && $email) {
        $search_old = "%" . $old_rdv . "%";
        // Récupérer les infos avant suppression pour l'alerte
        $check_old = $database->prepare("SELECT name FROM am_free WHERE email = ? AND name LIKE ?");
        $check_old->execute([$email, $search_old]);
        if ($check_old->fetch()) {
            $rescheduling_alert = true;
            $del_old = $database->prepare("DELETE FROM am_free WHERE email = ? AND name LIKE ?");
            $del_old->execute([$email, $search_old]);
        }
    }

    // Convertir les erreurs en message d'affichage et rediriger
    if (!empty($error)) {
        // Sauvegarder l'erreur dans la session et rediriger (Pattern POST-Redirect-GET)
        $_SESSION['error_message'] = implode('<br>', $error);
        header('Location: ' . BASE_PATH . 'index.php?p=free&error=1');
        exit;
    }
    
    if (empty($error)) {
        $city = $row_center_contact['city'];
        $email_center = $row_center_contact['email'] ?: 'claude@alesiaminceur.com';
        $reference = 'AQ' . date('dmhis');
        $input_name_db = ($date_heure) ? $input_nom_complet . " (RDV: " . $date_heure . ")" : $input_nom_complet;

        // Mandelieu et Vallauris utilisent le planning de Cannes
        $center_id_db = in_array((int)$center_id, [347, 349]) ? 305 : $center_id;

        try {
            // A. Enregistrement Table am_free
            $add_free = $database->prepare("INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $add_free->execute(array($reference, $center_id_db, 3, $input_name_db, $email, $tel, $segment));
            
            // B. Enregistrement Table client
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

            // C. NOTIFICATIONS (Email et Telegram)
            
            // 1. Détermination du message Telegram
            $planning_centers = [305, 347, 349, 343, 253];
            if (in_array((int)$center_id, $planning_centers)) {
                if ($segment == 'calendrier-cannes') {
                    // Étape 2 : Le rendez-vous vient d'être pris
                    $tg_msg = "<b>✅ RDV CONFIRMÉ - $city</b>\n" . 
                              "👤 $input_nom_complet\n" . 
                              "📧 $email\n" .
                              "📞 $tel\n" . 
                              "🗓️ $date_heure";
                    if ($rescheduling_alert) {
                        $tg_msg = "<b>🔄 REPLANIFICATION - $city</b>\n" . 
                                  "👤 $input_nom_complet\n" . 
                                  "📧 $email\n" .
                                  "📞 $tel\n" . 
                                  "🗓️ Nouveau : $date_heure\n" .
                                  "❌ Ancien : $old_rdv";
                    }
                } else {
                    // Étape 1 : Inscription au formulaire (avant planning)
                    $tg_msg = "<b>🎁 NOUVEAU PROSPECT - $city</b>\n" . 
                              "👤 $input_nom_complet\n" . 
                              "📧 $email\n" .
                              "📞 $tel";
                    if ($is_second_session) {
                        $tg_msg = "<b>⚠️ ALERTE DOUBLE SÉANCE - $city</b>\n" . 
                                  "👤 $input_nom_complet ($email) a déjà réservé une séance auparavant.";
                    }
                }
                sendTelegram($tg_msg);
                
                // Notification spécifique pour le responsable d'Antibes (ID 253)
                if ((int)$center_id == 253) {
                    sendTelegram($tg_msg, '1449612043');
                }
            }

            // 2. Envoi des Emails
            if (!empty($settings['mjusername'])) {
                try {
                    $is_second_session = false;
                    if ($date_heure) {
                        $check_double = $database->prepare("SELECT id FROM am_free WHERE email = ? AND name LIKE '%(RDV:%' AND id != ?");
                        $last_id = $database->lastInsertId();
                        $check_double->execute([$email, $last_id]);
                        if ($check_double->rowCount() > 0) $is_second_session = true;
                    }

                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $settings['mjhost'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $settings['mjusername'];
                    $mail->Password = $settings['mjpassword'];
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    // Email pour l'ADMIN (Dirigeant)
                    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo ' . $city);
                    $mail->addAddress($email_center);
                    $mail->addReplyTo($email_center, 'Aquavelo ' . $city);
                    $mail->isHTML(true);
                    
                    $date_now = date('d-m-Y H:i:s');
                    
                    if ($segment == 'calendrier-cannes') {
                        // Email de CONFIRMATION de RDV (Une fois le créneau choisi)
                        $subject_admin = "✅ RDV CONFIRMÉ : $city - $input_nom_complet";
                        if ($rescheduling_alert) $subject_admin = "🔄 REPLANIFICATION : $city - $input_nom_complet";
                        $mail->Subject = $subject_admin;
                        
                        $mail->Body = "<h3>" . ($rescheduling_alert ? "🔄 Replanification de séance" : "Séance confirmée") . "</h3>
                                      <b>Nom:</b> $input_nom_complet<br>
                                      <b>Email:</b> $email<br>
                                      <b>Tel:</b> $tel<br>
                                      <b>Centre:</b> $city<br>
                                      <b>RDV choisi:</b> $date_heure";
                        if ($rescheduling_alert) {
                            $mail->Body .= "<br><br><b>Ancien RDV qui a été annulé :</b> " . htmlspecialchars($old_rdv);
                        }
                    } else {
                        // Email de NOUVEAU PROSPECT (Le modèle demandé)
                        $subject_admin = "Nouveau contact $city - $input_nom_complet";
                        if ($is_second_session) $subject_admin = "⚠️ ALERTE : Tentative de 2ème séance - $input_nom_complet";
                        $mail->Subject = $subject_admin;
                        
                        $mail->Body = "Bonjour,<br><br>" . 
                                      (($is_second_session) ? "<p style='color:red; font-weight:bold;'>⚠️ ATTENTION : CE CLIENT A DÉJÀ RÉSERVÉ UNE SÉANCE AUPARAVANT</p>" : "") . "
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

                    // 3. Email de bienvenue pour les centres HORS PLANNING (Cannes, Mandelieu, Vallauris, Mérignac, Antibes gérés plus bas)
                    if (!in_array((int)$center_id, [305, 347, 349, 343, 253]) && !$date_heure) {
                        $mail->clearAddresses();
                        $mail->addAddress($email);
                        $mail->Subject = "Votre séance découverte gratuite à Aquavelo $city";
                        
                        // Ajout spécifique du lien Calendly pour Antibes uniquement
                        $rdv_text = "Prenez dès maintenant rendez-vous directement en appelant le <b>" . $row_center_contact['phone'] . "</b>.";
                        if ($center_id == 253) { // 253 est l'ID d'Antibes
                            $rdv_text = "Prenez dès maintenant rendez-vous directement sur <a href='https://calendly.com/aquavelo-antibes'>https://calendly.com/aquavelo-antibes</a>, ou en appelant le <b>" . $row_center_contact['phone'] . "</b>.";
                        }
                        
                        $mail->Body = "Bonjour " . $input_nom_complet . ",<br><br>
                                      Nous sommes ravis de vous offrir une séance découverte gratuite au centre Aquavélo de <b>$city</b>.<br><br>
                                      Lors de votre visite, vous profiterez d'un cours d'aquabiking coaché, encadré par nos professeurs de sport diplômés. Nous commencerons par un bilan personnalisé pour mieux comprendre vos besoins et vous aider à atteindre vos objectifs forme et bien-être.<br><br>
                                      $rdv_text<br><br>
                                      <b>N'oubliez pas de venir équipé(e) avec :</b><br>
                                      ✅ Votre maillot de bain,<br>
                                      ✅ Une serviette,<br>
                                      ✅ Un gel douche,<br>
                                      ✅ Une bouteille d'eau,<br>
                                      ✅ Et des chaussures adaptées à l'aquabiking.<br><br>
                                      <b>Adresse :</b> " . $row_center_contact['address'] . ", " . $city . "<br><br>
                                      <i>*Offre non cumulable. Réservez vite, les places sont limitées.</i><br><br>
                                      Cordialement,<br>
                                      L'équipe Aquavélo<br>
                                      <a href='https://www.aquavelo.com'>www.aquavelo.com</a>";
                        $mail->send();
                    }

                    // Email pour le CLIENT (RDV CONFIRMÉ sur Planning)
                    if ($date_heure) {
                        $mail->clearAddresses();
                        $mail->addAddress($email);
                        $mail->addReplyTo($email_center, 'Aquavelo ' . $city);
                        $mail->Subject = "Confirmation de votre séance à Aquavelo $city";
                        $rdv_formatted = str_replace(['(', ')'], ['pour un cours ', ''], $date_heure);
                        
                        // Pour Cannes/Mandelieu/Vallauris, utiliser les coordonnées de Cannes
                        if (in_array((int)$center_id, [305, 347, 349])) {
                            $stmt_cannes = $database->prepare('SELECT address, city, phone FROM am_centers WHERE id = 305');
                            $stmt_cannes->execute();
                            $cannes_info = $stmt_cannes->fetch();
                            $lieu_rdv = $cannes_info['address'] . ", " . $cannes_info['city'];
                            $tel_rdv = $cannes_info['phone'];
                        } else {
                            // Infos centre pour l'email
                            $lieu_rdv = $row_center_contact['address'] . ", " . $row_center_contact['city'];
                            $tel_rdv = $row_center_contact['phone'];
                        }

                        // URLs pour Annuler / Modifier
                        $url_annuler = "https://www.aquavelo.com/index.php?p=annulation&email=" . urlencode($email) . "&rdv=" . urlencode($date_heure) . "&city=" . urlencode($city);
                        $url_modifier = "https://www.aquavelo.com/index.php?p=calendrier_cannes&center=" . $center_id . "&nom=" . urlencode($input_nom_complet) . "&email=" . urlencode($email) . "&phone=" . urlencode($tel) . "&old_rdv=" . urlencode($date_heure);

                        $signature = in_array((int)$center_id, [305, 347, 349]) ? "Cordialement Claude" : "Cordialement,<br>Aquavelo $city";
                        $mail->Body = "Bonjour $input_nom_complet,<br><br>Votre séance est confirmée pour le <b>$rdv_formatted</b>.<br>
                                      Lieu : $lieu_rdv<br>Tél : $tel_rdv<br><br>
                                      <b>Important :</b> Merci d'arriver 15 minutes avant le début du cours.<br><br>
                                      <b>🎒 N'oubliez pas de venir équipé(e) avec :</b><br>
                                      ✅ Votre maillot de bain,<br>
                                      ✅ Une serviette,<br>
                                      ✅ Un gel douche,<br>
                                      ✅ Une bouteille d'eau,<br>
                                      ✅ Et des chaussures adaptées à l'aquabiking (nous vous en prêterons si vous n'en avez pas).<br><br>
                                      À très bientôt ! $signature<br><br><hr style='border:none; border-top:1px solid #eee; margin:20px 0;'><p style='color:#999; font-size:0.9rem;'>Un contretemps ?</p>
                                      <table cellspacing='0' cellpadding='0'><tr>
                                      <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_annuler' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Annuler</a></td>
                                      <td width='10'></td>
                                      <td align='center' width='120' height='35' bgcolor='#f0f0f0' style='border-radius:5px;'><a href='$url_modifier' style='font-size:12px; font-weight:bold; font-family:sans-serif; text-decoration:none; line-height:35px; width:100%; display:inline-block; color:#666;'>Modifier</a></td>
                                      </tr></table>";
                        $mail->send();
                    }
                } catch (Exception $e) {
                    error_log("Erreur Email: " . $mail->ErrorInfo);
                }
            }

            // D. REDIRECTION
            if ($segment == 'calendrier-cannes') {
                // Mandelieu et Vallauris redirigent vers le planning de Cannes
                $center_id_redirect = in_array((int)$center_id, [347, 349]) ? 305 : $center_id;
                $url = BASE_PATH . "index.php?p=merci_rdv&center=" . $center_id_redirect . "&rdv=" . urlencode($date_heure) . "&nom=" . urlencode($input_nom_complet) . "&email=" . urlencode($email) . "&phone=" . urlencode($tel) . "&city=" . urlencode($city);
                echo "<script>window.location.replace('$url');</script>";
                exit;
            } elseif (in_array((int)$center_id, [305, 347, 349, 343, 253])) {
                // Mandelieu et Vallauris redirigent vers le planning de Cannes
                $center_id_redirect = in_array((int)$center_id, [347, 349]) ? 305 : $center_id;
                $url = BASE_PATH . "index.php?p=calendrier_cannes&center=" . $center_id_redirect . "&nom=" . urlencode($input_nom_complet) . "&email=" . urlencode($email) . "&phone=" . urlencode($tel);
                echo "<script>window.location.replace('$url');</script>";
                exit;
            } else {
                $url = BASE_PATH . "index.php?p=free&success=1&cid=$center_id";
                echo "<script>window.location.href = '$url';</script>";
                exit;
            }
        } catch (Exception $e) { $error_message = "Erreur technique : " . $e->getMessage(); }
    }
}

if (isset($_GET['success'])) {
    $success_message = "Votre demande a bien été envoyée !";
    if (isset($_GET['cid'])) {
        $stmt = $database->prepare("SELECT city, phone FROM am_centers WHERE id = ?");
        $stmt->execute([intval($_GET['cid'])]);
        $cinfo = $stmt->fetch();
        if ($cinfo) $success_message .= "<br><br>Veuillez appeler le centre de <b>" . $cinfo['city'] . "</b> au <b>" . $cinfo['phone'] . "</b> pour confirmer votre rendez-vous.";
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
        <h2>Merci !</h2><p><?= $success_message ?></p>
        <div style="margin-top: 30px;"><a href="index.php" class="btn btn-primary">RETOUR À L'ACCUEIL</a></div>
      </div>
    <?php else: ?>
      <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; color: #00a8cc; margin-bottom: 25px;">Réservez votre séance gratuite</h2>
        <form role="form" method="POST" action="<?= BASE_PATH ?>index.php?p=free" id="mainFreeForm">
            <div style="margin-bottom: 15px;"><label>Centre *</label>
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
            
            <!-- Captcha anti-spam pour Aix-en-Provence -->
            <div id="captchaField" style="margin-bottom: 25px; display: none; padding: 20px; background: #f8f9fa; border-radius: 5px; border: 2px solid #00a8cc;">
                <label style="font-weight: bold; color: #00a8cc;">🛡️ Question anti-spam : 2 + 1 = ? *</label>
                <input type="number" name="captcha" id="captchaInput" style="width: 100%; height: 45px; border: 1px solid #ddd; border-radius: 5px; padding: 0 10px; margin-top: 10px;" placeholder="Entrez votre réponse">
            </div>
            <button type="submit" id="submitBtnText" style="width: 100%; height: 60px; background: #00a8cc; color: white; border: none; border-radius: 5px; font-weight: bold; font-size: 1.1rem; cursor: pointer;">RECEVOIR MON BON GRATUIT</button>
        </form>
      </div>
      <script>
      const centerSelect = document.getElementById('centerSelect');
      const submitBtnText = document.getElementById('submitBtnText');
      const captchaField = document.getElementById('captchaField');
      const captchaInput = document.getElementById('captchaInput');
      
      centerSelect.addEventListener('change', function() {
          const centerId = parseInt(this.value);
          const centerText = this.options[this.selectedIndex].text;
          
          // Affichage du captcha pour Aix-en-Provence
          if (centerText && centerText.toLowerCase().includes('aix')) {
              captchaField.style.display = 'block';
              captchaInput.required = true;
          } else {
              captchaField.style.display = 'none';
              captchaInput.required = false;
              captchaInput.value = '';
          }
          
          // Changement du texte du bouton
          if ([305, 347, 349, 343].includes(centerId)) submitBtnText.innerText = "RÉSERVER MA SÉANCE OFFERTE";
          else submitBtnText.innerText = "RECEVOIR MON BON GRATUIT";
      });
      </script>
    <?php endif; ?>
  </div>
</section>
