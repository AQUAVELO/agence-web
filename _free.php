<?php
/**
 * Page Séance Découverte Gratuite
 * Basé sur contact-send.php avec PHPMailer
 */

require '_settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Segment;
Segment::init("CvtZOzpEIJ0UHZuZCwSqQuq5F6o2FGsB");

// Variables pour les messages
$success_message = '';
$error_message = '';
$form_submitted = false;

// ⭐ Fonction de vérification reCAPTCHA v3
function verifyRecaptcha($token, $secret_key) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $token
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result === false) {
        return ['success' => false, 'score' => 0, 'error' => 'network_error'];
    }
    
    return json_decode($result, true);
}

// Traiter le formulaire si soumis en POST
if (isset($_POST['nom']) && empty($_POST['reason'])) {
    
    $form_submitted = true;
    
    // Déclarer variable d'erreurs
    $error = [];
    
    // ⭐ Vérification reCAPTCHA v3
    $recaptcha_token = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
    
    if (empty($recaptcha_token)) {
        $error['recaptcha'] = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        $recaptcha_response = verifyRecaptcha($recaptcha_token, $settings['recaptcha_secret_key']);
        
        if (!$recaptcha_response['success']) {
            $error['recaptcha'] = 'Vérification de sécurité échouée. Veuillez réessayer.';
            error_log("reCAPTCHA error: " . json_encode($recaptcha_response));
        } elseif ($recaptcha_response['score'] < $settings['recaptcha_score_threshold']) {
            $error['recaptcha'] = 'Activité suspecte détectée. Veuillez contacter le centre par téléphone.';
            error_log("reCAPTCHA low score: " . $recaptcha_response['score'] . " for IP: " . $_SERVER['REMOTE_ADDR']);
        }
    }
    
    // Récupérer les données du formulaire
    $input_nom = strip_tags(utf8_decode($_POST['nom']));
    $input_prenom = ''; // Pas de champ prénom séparé
    $input_name = $input_nom;
    $input_email = strip_tags($_POST['email']);
    $input_tel = strip_tags($_POST['phone']);
    $center = strip_tags($_POST['center']);
    $segment = isset($_POST['segment']) ? strip_tags($_POST['segment']) : 'free-trial';
    
    // Validation basique
    if (strlen($input_name) < 2) {
        $error['name'] = 'Veuillez nous indiquer votre nom.';
    }
    
    if (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = 'Veuillez nous indiquer une adresse email correcte.';
    }
    
    if (isset($_COOKIE["secure"]) && $_COOKIE["secure"] == true) {
        $error['spam'] = 'Veuillez patienter 60 secondes pour renouveler votre demande.';
    }
    
    $name = $input_name;
    $email = $input_email;
    $tel = $input_tel;
    
    // Récupérer les infos du centre depuis la base de données
    $center_contact = $database->prepare('SELECT * FROM am_centers WHERE id = ? AND online = ? AND aquavelo = ?');
    $center_contact->execute(array($center, 1, 1));
    $row_center_contact = $center_contact->fetch();
    
    $count_center_contact = $center_contact->rowCount();
    if ($count_center_contact != 1) {
        $error['center'] = 'Une erreur est survenue avec le centre sélectionné.';
    }
    
    if (!$error && $row_center_contact) {
        
        $city = $row_center_contact['city'];
        $email_center = $row_center_contact['email'];
        $address = $row_center_contact['address'];
        $hours = $row_center_contact['openhours'];
        $phone = $row_center_contact['phone'];
        
        // Générer une référence unique
        $reference = 'AQ' . date('dmhis');
        
        // Insérer dans la base de données
        $add_free = $database->prepare("INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $add_free->execute(array($reference, $center, 3, $name, $email, $tel, $segment));
        
        // Tracking Segment
        Segment::track(array(
            "anonymousId" => $segment,
            "event" => "Demo Requested",
            "properties" => array(
                "reference" => $reference,
                "center" => $center,
                "firstname" => $name,
                "email" => $email,
                "phone" => $tel
            )
        ));
        
        // Cookie anti-spam
        setcookie('secure', 'true', (time() + 15));
        
        // ========== EMAIL 1 : AU CENTRE ==========
        
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->Host = $settings['mjhost'];
        $mail->isHTML(true);
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Username = $settings['mjusername'];
        $mail->Password = $settings['mjpassword'];
        
        $mail->setFrom('service.clients@aquavelo.com', 'Service clients Aquavelo');
        $mail->addAddress($email_center, 'Aquavelo ' . $city);
        $mail->addReplyTo($email, $name);
        
        $mail->Subject = 'Aquavelo, Un contact pour votre centre de ' . $city . ' !';
        $mail->Body = '<p>Bonjour, </p><p>' . $name . ' <br/> Adresse &eacute;lectronique : <strong>' . $email . ' </strong> <br/> T&eacute;l&eacute;phone : <strong> ' . $tel . '</strong></p><p>La personne ci-dessus a command&eacute;e une s&eacute;ance d&eacute;couverte gratuite ainsi qu\'un bilan minceur dans votre centre. <br/><em>Nous vous invitons &agrave; la contacter pour prendre rendez-vous.</em></p><p>Cordialement,<br/>L\'&eacute;quipe Aquavelo</p><p><em>(Demande effectu&eacute;e &agrave; partir du site aquavelo.com, le ' . date("d-m-Y   H:i:s") . ')</em></p>';
        $mail->AltBody = 'Bonjour, ' . $name . ' ' . $email . ' ' . $tel . '. La personne ci-dessus a commandée une séance découverte gratuite ainsi qu\'un bilan minceur dans votre centre. Nous vous invitons à la contacter pour prendre rendez-vous. Cordialement, L\'équipe Aquavelo (Demande effectuée à partir du site www.aquavelo.com, le ' . date("d-m-Y   H:i:s") . ')';
        
        try {
            $mail->send();
        } catch (Exception $e) {
            error_log("Erreur envoi email au centre: {$mail->ErrorInfo}");
        }
        
        // ========== EMAIL 2 : AU CLIENT ==========
        
        $mail2 = new PHPMailer();
        $mail2->IsSMTP();
        $mail2->Host = $settings['mjhost'];
        $mail2->isHTML(true);
        $mail2->SMTPAuth = true;
        $mail2->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail2->Port = 587;
        $mail2->Username = $settings['mjusername'];
        $mail2->Password = $settings['mjpassword'];
        
        // Message par défaut
        $msg = '
            <p>Bonjour ' . $name . ',</p>
            
            <p>Nous sommes ravis de vous offrir une <strong>s&eacute;ance d&eacute;couverte gratuite</strong> au centre <strong>Aquavelo de ' . $city . '</strong>.</p>
            
            <p>Lors de votre visite, vous profiterez d&#39;un <strong>cours d&#39;aquabiking coach&eacute;</strong>, encadr&eacute; par nos professeurs de sport dipl&ocirc;m&eacute;s. Nous commencerons par un <strong>bilan personnalis&eacute;</strong> pour mieux comprendre vos besoins et vous aider &agrave; atteindre vos objectifs forme et bien-&ecirc;tre.</p>
            
            <p>Prenez d&egrave;s maintenant rendez-vous en appelant le <strong>' . $phone . '</strong>. N&#39;oubliez de venir &eacute;quip&eacute;(e) avec :</p>
            <ul>
              <li>Votre maillot de bain,</li>
              <li>Une serviette,</li>
              <li>Un gel douche,</li>
              <li>Une bouteille d&#39;eau,</li>
              <li>Et des chaussures adapt&eacute;es &agrave; l&#39;aquabiking.</li>
            </ul>
            
            <p><strong>Horaires d&#39;ouverture :</strong> ' . $hours . '<br>
            <strong>Adresse :</strong> ' . $address . '</p>
            
            <p><em>*Offre non cumulable. R&eacute;servez vite, les places sont limit&eacute;es.</em></p>
            <p>Cordialement,<br>
            L&#39;&eacute;quipe Aquavelo<br>
            <a href="http://aquavelo.com" target="_blank">www.aquavelo.com</a></p>
            
            <p><img src="cid:pubemailing" alt="Image Promotionnelle" style="margin-top: 20px; display: block;"></p>';
        
        // Tableau des centres promotionnels (avec Calendly)
        $promotions = [
            305 => "Cannes",
            347 => "Mandelieu",
            349 => "Vallauris"
        ];
        
        // Message spécial pour les centres avec Calendly
        if (isset($row_center_contact['id']) && array_key_exists($row_center_contact['id'], $promotions)) {
            $msg = '
            <p>Bonjour ' . $name . ',</p>
            
            <p>Nous sommes ravis de vous offrir une <strong>s&eacute;ance d&eacute;couverte gratuite</strong> au centre <strong>Aquav&eacute;lo de Cannes</strong>.</p>
            
            <p>Lors de votre visite, vous profiterez d&#39;un <strong>cours d&#39;aquabiking coach&eacute;</strong>, encadr&eacute; par nos professeurs de sport dipl&ocirc;m&eacute;s. Nous commencerons par un <strong>bilan personnalis&eacute;</strong> pour mieux comprendre vos besoins et vous aider &agrave; atteindre vos objectifs forme et bien-&ecirc;tre.</p>
            <p>Prenez d&egrave;s maintenant rendez-vous directement sur <a href="https://calendly.com/aqua-cannes" target="_blank">https://calendly.com/aqua-cannes</a>, ou en appelant le <strong>' . $phone . '</strong>.</p>
            
            <p>N&#39;oubliez pas de venir &eacute;quip&eacute;(e) avec :</p>
            <ul>
              <li>Votre maillot de bain,</li>
              <li>Une serviette,</li>
              <li>Un gel douche,</li>
              <li>Une bouteille d&#39;eau,</li>
              <li>Et des chaussures adapt&eacute;es &agrave; l&#39;aquabiking.</li>
            </ul>
            
            <p><strong>Adresse :</strong> 60 avenue du Docteur Raymond Picaud, Cannes<br>
            
            <p><em>*Offre non cumulable. R&eacute;servez vite, les places sont limit&eacute;es.</em></p>   
            <p>Cordialement,<br>
            L&#39;&eacute;quipe Aquav&eacute;lo<br>
            <a href="http://aquavelo.com" target="_blank">www.aquavelo.com</a></p>';
        }
        
        $mail2->setFrom('service.clients@aquavelo.com', 'Service clients Aquavelo');
        $mail2->addAddress($email, $name);
        $mail2->addReplyTo('service.clients@aquavelo.com', 'Service clients Aquavelo');
        $mail2->Subject = 'Aquavelo - Votre seance decouverte gratuite';
        $mail2->Body = $msg;
        $mail2->AltBody = strip_tags($msg);
        
        // Ajouter image si fichier existe
        if (file_exists('images/pubemailing.jpg')) {
            $mail2->AddEmbeddedImage('images/pubemailing.jpg', 'pubemailing', 'pubemailing.jpg');
        }
        
        try {
            $mail2->send();
            $success_message = 'Vous allez recevoir un message avec les coordonnées du centre pour prendre rendez-vous.';
            
            // Redirection après 3 secondes
            header("refresh:3;url=/?p=free&success=1");
            
        } catch (Exception $e) {
            error_log("Erreur envoi email au client: {$mail2->ErrorInfo}");
            $error_message = 'Nous avons rencontré un problème lors de l\'envoi de votre message.';
        }
        
    } else {
        // Erreurs trouvées
        $response = (isset($error['recaptcha'])) ? $error['recaptcha'] . "<br /> \n" : null;
        $response .= (isset($error['name'])) ? $error['name'] . "<br /> \n" : null;
        $response .= (isset($error['email'])) ? $error['email'] . "<br /> \n" : null;
        $response .= (isset($error['spam'])) ? $error['spam'] . "<br /> \n" : null;
        $response .= (isset($error['center'])) ? $error['center'] . "<br /> \n" : null;
        
        $error_message = $response;
    }
}

// Message de succès si redirigé
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success_message = 'Votre demande a bien été envoyée !';
}
?>

<!-- ⭐ reCAPTCHA v3 Script -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($settings['recaptcha_site_key']); ?>"></script>

<!-- Hero Section COMPACTE -->
<section class="content-area brightText" data-bg="images/content/about-v2-title-bg.jpg" data-topspace="30" data-btmspace="20" style="min-height: 150px;">
  <div class="container">
    <h1 style="color: white; text-align: center; font-size: 1.5rem; margin: 20px 0;">
      🎁 Séance Découverte GRATUITE
    </h1>
  </div>
</section>

<!-- Section principale -->
<section class="content-area bg1" style="padding: 20px 0 100px 0;">
  <div class="container">

    <!-- Message de succès -->
    <?php if (!empty($success_message)) : ?>
    <div class="alert" style="margin: 20px auto; max-width: 600px; text-align: center; background: linear-gradient(135deg, #00d4ff, #00a8cc); color: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,168,204,0.4);">
      <h3 style="color: #fff; font-size: 2.2rem; margin-bottom: 15px;"><i class="fa fa-check-circle"></i> Merci !</h3>
      <p style="color: #fff; font-size: 1.5rem;"><strong><?= $success_message; ?></strong></p>
    </div>
    <?php endif; ?>

    <!-- Message d'erreur -->
    <?php if (!empty($error_message)) : ?>
    <div class="alert alert-danger" style="margin: 20px auto; max-width: 600px;">
      <h3 style="color: #d32f2f;"><i class="fa fa-exclamation-triangle"></i> Erreur</h3>
      <p><?= $error_message; ?></p>
    </div>
    <?php endif; ?>

    <!-- Formulaire COMPACT -->
    <div style="max-width: 600px; margin: 0 auto;">
      
      <h2 style="text-align: center; color: #00a8cc; font-size: 1.4rem; margin-bottom: 20px;">
        Réservez votre séance gratuite
      </h2>
      
      <form role="form" id="freeTrialForm" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" novalidate>
        
        <!-- Centre -->
        <div style="margin-bottom: 12px;">
          <label for="center" style="font-size: 0.9rem; margin-bottom: 3px; display: block;">
            <i class="fa fa-map-marker"></i> Centre <span style="color: red;">*</span>
          </label>
          <select id="center" name="center" required style="width: 100%; height: 40px; font-size: 16px; padding: 8px; border: 2px solid #e0e0e0; border-radius: 5px;">
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($centers_list_d as $row_centers) { ?>
              <option value="<?= $row_centers['id']; ?>"><?= $row_centers['city']; ?></option>
            <?php } ?>
          </select>
          <span class="error-center" style="color: red; font-size: 11px; display: none;">Choisissez un centre</span>
          
          <!-- Section Calendly pour Cannes/Mandelieu/Vallauris -->
          <div id="calendrier_section" style="display: none; margin-top: 15px; padding: 15px; background: #e8f8fc; border-radius: 8px; border-left: 4px solid #00d4ff;">
            <p style="margin: 0; font-size: 0.95rem;">
              📅 Vous pouvez aussi réserver directement sur notre 
              <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank" style="color: #00a8cc; font-weight: 600;">
                calendrier en ligne <i class="fa fa-external-link"></i>
              </a>
            </p>
          </div>
        </div>
        
        <!-- Nom -->
        <div style="margin-bottom: 12px;">
          <label for="nom" style="font-size: 0.9rem; margin-bottom: 3px; display: block;">
            <i class="fa fa-user"></i> Nom et Prénom <span style="color: red;">*</span>
          </label>
          <input type="text" 
                 id="nom" 
                 name="nom" 
                 placeholder="Votre nom et prénom" 
                 required
                 autocomplete="name"
                 style="width: 100%; height: 40px; font-size: 16px; padding: 8px; border: 2px solid #e0e0e0; border-radius: 5px;">
          <span class="error-nom" style="color: red; font-size: 11px; display: none;">Entrez votre nom</span>
        </div>
        
        <!-- Email -->
        <div style="margin-bottom: 12px;">
          <label for="email" style="font-size: 0.9rem; margin-bottom: 3px; display: block;">
            <i class="fa fa-envelope"></i> Email <span style="color: red;">*</span>
          </label>
          <input type="email" 
                 id="email" 
                 name="email" 
                 placeholder="email@exemple.com" 
                 required
                 autocomplete="email"
                 style="width: 100%; height: 40px; font-size: 16px; padding: 8px; border: 2px solid #e0e0e0; border-radius: 5px;">
          <span class="error-email" style="color: red; font-size: 11px; display: none;">Email invalide</span>
        </div>
        
        <!-- Téléphone -->
        <div style="margin-bottom: 12px;">
          <label for="phone" style="font-size: 0.9rem; margin-bottom: 3px; display: block;">
            <i class="fa fa-phone"></i> Téléphone <span style="color: red;">*</span>
          </label>
          <input type="tel" 
                 id="phone" 
                 name="phone" 
                 placeholder="06 12 34 56 78" 
                 required
                 autocomplete="tel"
                 style="width: 100%; height: 40px; font-size: 16px; padding: 8px; border: 2px solid #e0e0e0; border-radius: 5px;">
          <span class="error-phone" style="color: red; font-size: 11px; display: none;">Entrez votre téléphone</span>
        </div>

        <input type="hidden" name="reason" value="">
        <input type="hidden" name="segment" value="free-trial">
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" value="">
        
        <!-- ⭐ BOUTON FIXE EN BAS -->
        <button type="submit" 
                id="submitBtn"
                style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000; background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border: none; padding: 20px; font-size: 1.4rem; font-weight: 600; width: 100%; margin: 0; border-radius: 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.2);">
          <i class="fa fa-check-circle"></i> RECEVOIR MON BON GRATUIT
        </button>

      </form>

    </div>

  </div>
</section>

<!-- Styles -->
<style>
body {
  padding-bottom: 80px;
}

select, input {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

select:focus, input:focus {
  border-color: #00d4ff !important;
  outline: none;
}

#submitBtn:active {
  transform: scale(0.98);
}

@media (max-width: 768px) {
  body {
    padding-bottom: 90px;
  }
}
</style>

<!-- JavaScript validation avec reCAPTCHA v3 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('freeTrialForm');
    
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Toujours empêcher la soumission par défaut
        
        var isValid = true;
        var firstError = null;

        // Masquer toutes les erreurs
        document.querySelectorAll('[class^="error-"]').forEach(function(el) {
            el.style.display = 'none';
        });

        // Validation CENTRE
        var center = document.getElementById('center');
        if (center && center.value === "") {
            document.querySelector('.error-center').style.display = 'block';
            center.style.borderColor = 'red';
            isValid = false;
            if(!firstError) firstError = center;
        } else if(center) {
            center.style.borderColor = '#e0e0e0';
        }

        // Validation NOM
        var nom = document.getElementById('nom');
        if (nom && nom.value.trim().length < 2) {
            document.querySelector('.error-nom').style.display = 'block';
            nom.style.borderColor = 'red';
            isValid = false;
            if(!firstError) firstError = nom;
        } else if(nom) {
            nom.style.borderColor = '#e0e0e0';
        }

        // Validation EMAIL
        var email = document.getElementById('email');
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email.value)) {
            document.querySelector('.error-email').style.display = 'block';
            email.style.borderColor = 'red';
            isValid = false;
            if(!firstError) firstError = email;
        } else if(email) {
            email.style.borderColor = '#e0e0e0';
        }

        // Validation TÉLÉPHONE
        var phone = document.getElementById('phone');
        var phoneRegex = /^[\d\s\.\-\+\(\)]{10,}$/;
        if (phone && !phoneRegex.test(phone.value)) {
            document.querySelector('.error-phone').style.display = 'block';
            phone.style.borderColor = 'red';
            isValid = false;
            if(!firstError) firstError = phone;
        } else if(phone) {
            phone.style.borderColor = '#e0e0e0';
        }

        if (!isValid) {
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(function() { firstError.focus(); }, 300);
            }
            return false;
        }
        
        // Désactiver le bouton pendant envoi
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> VÉRIFICATION...';
        
        // ⭐ Exécuter reCAPTCHA v3 avant soumission
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= htmlspecialchars($settings['recaptcha_site_key']); ?>', {action: 'submit_free_trial'}).then(function(token) {
                // Injecter le token dans le formulaire
                document.getElementById('g-recaptcha-response').value = token;
                
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ENVOI EN COURS...';
                
                // Track conversion
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'form_submission', {
                        'event_category': 'conversion',
                        'event_label': 'free_trial_request'
                    });
                }
                
                // Soumettre le formulaire
                form.submit();
            }).catch(function(error) {
                console.error('reCAPTCHA error:', error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-check-circle"></i> RECEVOIR MON BON GRATUIT';
                alert('Erreur de vérification. Veuillez réessayer.');
            });
        });
        
        return false;
    });

    // Effacer erreurs à la saisie
    ['center', 'nom', 'email', 'phone'].forEach(function(id) {
        var input = document.getElementById(id);
        if(input) {
            input.addEventListener('input', function() {
                this.style.borderColor = '#e0e0e0';
                var errorClass = '.error-' + id;
                var error = document.querySelector(errorClass);
                if(error) error.style.display = 'none';
            });
        }
    });

    // Afficher/masquer section Calendly pour Cannes, Mandelieu, Vallauris
    var centerSelect = document.getElementById('center');
    var calendrierSection = document.getElementById('calendrier_section');
    var centersWithCalendly = [305, 347, 349]; // Cannes, Mandelieu, Vallauris

    function toggleCalendrier() {
        if (centerSelect && calendrierSection) {
            var selectedCenter = parseInt(centerSelect.value);
            if (centersWithCalendly.includes(selectedCenter)) {
                calendrierSection.style.display = 'block';
            } else {
                calendrierSection.style.display = 'none';
            }
        }
    }

    if (centerSelect) {
        centerSelect.addEventListener('change', toggleCalendrier);
        // Vérifier au chargement
        toggleCalendrier();
    }
});
</script>
