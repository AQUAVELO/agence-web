<?php
/**
 * Page Séance Découverte Gratuite
 * Traitement du formulaire de réservation
 */

// Variables pour les messages
$success_message = '';
$error_message = '';

// Traiter le formulaire si soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupérer les données du formulaire
    $center = isset($_POST['center']) ? trim($_POST['center']) : '';
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : 'Séance découverte gratuite';
    $segment = isset($_POST['segment']) ? trim($_POST['segment']) : 'free-trial';
    
    // Validation basique
    $errors = [];
    
    if (empty($center)) {
        $errors[] = 'Le centre est obligatoire';
    }
    
    if (empty($nom) || strlen($nom) < 2) {
        $errors[] = 'Le nom est obligatoire (minimum 2 caractères)';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email est obligatoire et doit être valide';
    }
    
    if (empty($phone) || strlen($phone) < 10) {
        $errors[] = 'Le téléphone est obligatoire (minimum 10 caractères)';
    }
    
    // Si pas d'erreurs, envoyer l'email
    if (empty($errors)) {
        
        // Récupérer le nom du centre depuis l'ID
        $center_query = $database->prepare('SELECT city FROM am_centers WHERE id = ?');
        $center_query->execute(array($center));
        $center_data = $center_query->fetch(PDO::FETCH_ASSOC);
        $center_name = $center_data ? $center_data['city'] : 'Centre non trouvé';
        
        // Préparer l'email
        $to = 'claude@alesiaminceur.com';
        $subject = 'Séance Découverte Gratuite - ' . $center_name;
        
        // Corps de l'email en HTML
        $email_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; margin-top: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #00a8cc; }
        .value { margin-top: 5px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎁 Nouvelle demande de séance gratuite</h1>
        </div>
        
        <div class="content">
            <div class="field">
                <div class="label">Centre choisi :</div>
                <div class="value">' . htmlspecialchars($center_name) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Nom et Prénom :</div>
                <div class="value">' . htmlspecialchars($nom) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Email :</div>
                <div class="value">' . htmlspecialchars($email) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Téléphone :</div>
                <div class="value">' . htmlspecialchars($phone) . '</div>
            </div>
            
            ' . (!empty($date) ? '
            <div class="field">
                <div class="label">Date souhaitée :</div>
                <div class="value">' . htmlspecialchars($date) . '</div>
            </div>
            ' : '') . '
            
            ' . (!empty($message) ? '
            <div class="field">
                <div class="label">Message :</div>
                <div class="value">' . nl2br(htmlspecialchars($message)) . '</div>
            </div>
            ' : '') . '
        </div>
        
        <div class="footer">
            <p>Email envoyé depuis le formulaire de séance découverte gratuite<br>
            Aquavelo - ' . date('d/m/Y à H:i') . '</p>
        </div>
    </div>
</body>
</html>
        ';
        
        // Headers pour l'email HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: noreply@aquavelo.com" . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        
        // Envoyer l'email
        $sent = mail($to, $subject, $email_body, $headers);
        
        if ($sent) {
            // Email de confirmation au client
            $client_subject = 'Confirmation - Votre demande de séance gratuite Aquavelo';
            $client_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .highlight { background: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎁 Merci ' . htmlspecialchars($nom) . ' !</h1>
        </div>
        
        <div class="content">
            <p>Votre demande de séance découverte gratuite a bien été enregistrée.</p>
            
            <div class="highlight">
                <strong>Centre choisi :</strong> ' . htmlspecialchars($center_name) . '<br>
                ' . (!empty($date) ? '<strong>Date souhaitée :</strong> ' . htmlspecialchars($date) . '<br>' : '') . '
            </div>
            
            <p><strong>Que se passe-t-il maintenant ?</strong></p>
            <ol>
                <li>Notre équipe du centre de ' . htmlspecialchars($center_name) . ' va vous contacter dans les 24h</li>
                <li>Vous conviendrez ensemble d\'un créneau qui vous arrange</li>
                <li>Vous recevrez votre bon de séance gratuite par email</li>
            </ol>
            
            <p><strong>Contact direct :</strong><br>
            Téléphone : 06 22 64 70 95<br>
            Email : claude@alesiaminceur.com</p>
            
            <p>À très bientôt dans l\'eau ! 💦</p>
            
            <p style="color: #666; font-size: 0.9em; margin-top: 30px;">
            L\'équipe Aquavelo<br>
            www.aquavelo.com
            </p>
        </div>
    </div>
</body>
</html>
            ';
            
            $client_headers = "MIME-Version: 1.0" . "\r\n";
            $client_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $client_headers .= "From: Aquavelo <noreply@aquavelo.com>" . "\r\n";
            
            mail($email, $client_subject, $client_body, $client_headers);
            
            $success_message = 'Votre demande a bien été envoyée ! Vous allez recevoir un email de confirmation.';
            
            // Rediriger vers la page de confirmation après 2 secondes
            header("refresh:2;url=/?p=free&success=1");
        } else {
            $error_message = 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.';
        }
        
    } else {
        // Afficher les erreurs
        $error_message = implode('<br>', $errors);
    }
}

// Afficher un message de succès si redirigé depuis le POST
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success_message = 'Votre demande a bien été envoyée ! Vous allez recevoir un email de confirmation.';
}
?>

<!-- Hero Section avec image de fond -->
<section class="content-area brightText" data-bg="images/content/about-v2-title-bg.jpg" data-topspace="50" data-btmspace="30">
  <div class="container">
    <div class="flexslider std-slider center-controls" data-animation="fade" data-loop="true" data-animspeed="600" data-dircontrols="true" data-controls="true">
      <ul class="slides">
        <li>
          <blockquote class="huge text-center">
            <p style="font-size: 1.5rem; margin: 10px 0;">🎁 Profitez d'une séance découverte GRATUITE<br>
              Testez l'aquabiking sans engagement
            </p>
          </blockquote>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- Section principale -->
<section class="content-area bg1" style="padding: 30px 0;">
  <div class="container">
    <header class="page-header text-center" style="margin-bottom: 20px;">
      <h1 class="page-title" style="font-size: 1.8rem; margin-bottom: 10px;">Séance Découverte Gratuite</h1>
      <h2 style="font-size: 1.2rem;">Essayez l'aquabiking pendant 45 minutes avec un coach</h2>
    </header>

    <!-- Message de succès -->
    <?php if (!empty($success_message)) : ?>
    <div class="row" style="margin-top: 30px;">
      <div class="col-md-8 col-md-offset-2">
        <div class="alert alert-success" style="padding: 20px; border-radius: 10px; background: #d4edda; border: 2px solid #4caf50;">
          <h3 style="color: #4caf50; margin-top: 0;"><i class="fa fa-check-circle"></i> <?= $success_message; ?></h3>
          <p>Notre équipe vous contactera dans les plus brefs délais.</p>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Message d'erreur -->
    <?php if (!empty($error_message)) : ?>
    <div class="row" style="margin-top: 30px;">
      <div class="col-md-8 col-md-offset-2">
        <div class="alert alert-danger" style="padding: 20px; border-radius: 10px;">
          <h3 style="color: #d32f2f; margin-top: 0;"><i class="fa fa-exclamation-triangle"></i> Erreur</h3>
          <p><?= $error_message; ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Formulaire de réservation -->
    <div class="row" style="margin-top: 50px;">
      <div class="col-md-8 col-md-offset-2">
        <div class="form-container">
          <h2><i class="fa fa-calendar-check-o"></i> Réservez Votre Séance Gratuite</h2>
          
          <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
            <div class="alert alert-info" style="border-radius: 10px; margin-bottom: 20px;">
              <i class="fa fa-info-circle"></i> Vous pouvez aussi réserver sur notre 
              <strong><a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank" style="color: #00a8cc;">
                calendrier en ligne <i class="fa fa-external-link"></i>
              </a></strong>
            </div>
          <?php endif; ?>
          
          <?php if (isset($row_center['id']) && in_array($row_center['id'], [343])) : ?>
            <div class="alert alert-info" style="border-radius: 10px; margin-bottom: 20px;">
              <i class="fa fa-info-circle"></i> Vous pouvez aussi réserver sur notre 
              <strong><a href="https://aquavelomerignac33.simplybook.it/v2/" target="_blank" style="color: #00a8cc;">
                calendrier en ligne <i class="fa fa-external-link"></i>
              </a></strong>
            </div>
          <?php endif; ?>
        
          <!-- ⭐ FORMULAIRE BASÉ SUR _page.php -->
          <form role="form" id="freeTrialForm" class="free-trial-form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" novalidate>
            
            <div class="form-group">
              <label for="center"><i class="fa fa-map-marker"></i> Centre <span style="color: red;">*</span></label>
              <select class="form-control" id="center" name="center" style="font-size: 16px;">
                <option value="">-- Sélectionnez un centre --</option>
                <?php foreach ($centers_list_d as $row_centers) { ?>
                  <option value="<?= $row_centers['id']; ?>"><?= $row_centers['city']; ?></option>
                <?php } ?>
              </select>
              <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez sélectionner un centre</span>
            </div>
            
            <div class="form-group">
              <label for="nom"><i class="fa fa-user"></i> Nom et Prénom <span style="color: red;">*</span></label>
              <input type="text" 
                     class="form-control" 
                     id="nom" 
                     name="nom" 
                     placeholder="Votre nom et prénom"
                     autocomplete="name"
                     style="font-size: 16px;">
              <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez entrer votre nom</span>
            </div>
            
            <div class="form-group">
              <label for="email"><i class="fa fa-envelope"></i> Email <span style="color: red;">*</span></label>
              <input type="email" 
                     class="form-control" 
                     id="email" 
                     name="email" 
                     placeholder="exemple@email.com"
                     autocomplete="email"
                     style="font-size: 16px;">
              <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez entrer un email valide</span>
            </div>
              
            <div class="form-group">
              <label for="phone"><i class="fa fa-phone"></i> Téléphone <span style="color: red;">*</span></label>
              <input type="tel" 
                     class="form-control" 
                     id="phone" 
                     name="phone" 
                     placeholder="06 12 34 56 78"
                     autocomplete="tel"
                     style="font-size: 16px;">
              <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez entrer votre téléphone</span>
            </div>

            <div class="form-group">
              <label for="date"><i class="fa fa-calendar"></i> Date souhaitée (optionnel)</label>
              <input type="text" 
                     class="form-control" 
                     id="date" 
                     name="date" 
                     placeholder="Ex: Lundi 15 janvier à 10h"
                     autocomplete="off"
                     style="font-size: 16px;">
              <p class="help-block">Notre équipe vous contactera</p>
            </div>

            <div class="form-group">
              <label for="message"><i class="fa fa-comment"></i> Message (optionnel)</label>
              <textarea class="form-control" 
                        id="message" 
                        name="message" 
                        rows="2" 
                        placeholder="Votre message..."
                        autocomplete="off"
                        style="font-size: 16px;"></textarea>
            </div>
          
            <input type="hidden" name="reason" value="Séance découverte gratuite">
            <input type="hidden" name="segment" value="free-trial">
            
            <button type="submit" class="btn btn-submit" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border: none; padding: 15px; font-size: 1.2rem; font-weight: 600; border-radius: 50px; width: 100%; margin-top: 20px;">
              <i class="fa fa-check-circle"></i> Recevoir mon Bon par Email
            </button>

            <p style="text-align: center; margin-top: 10px; margin-bottom: 5px; color: #666; font-size: 0.85rem;">
              <i class="fa fa-lock"></i> Sans engagement
            </p>
          </form>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Section Pourquoi essayer -->
<section class="content-area bg2">
  <div class="container">
    <header class="page-header text-center">
      <h2 class="page-title">Pourquoi essayer une séance gratuite ?</h2>
    </header>

    <div class="row" style="margin-top: 40px;">
      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-gift" style="color: #00d4ff; font-size: 3rem;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">100% Gratuit</a></h4>
              <p>Aucun engagement, aucun frais. Venez découvrir l'aquabiking en conditions réelles avec un coach professionnel.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-heart" style="color: #00d4ff; font-size: 3rem;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Testez les bienfaits</a></h4>
              <p>Brûlez jusqu'à 500 calories en 45 minutes. Découvrez l'effet drainant et anti-cellulite dès la première séance.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-users" style="color: #00d4ff; font-size: 3rem;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Ambiance conviviale</a></h4>
              <p>Cours collectifs en petits groupes. Rencontrez nos coachs et découvrez nos installations modernes.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Styles -->
<style>
.form-container {
  background: white;
  padding: 30px;
  border-radius: 15px;
  border: 3px solid #00d4ff;
  box-shadow: 0 10px 30px rgba(0, 168, 204, 0.2);
  max-height: 90vh;
  overflow-y: auto;
}

.form-container h2 {
  color: #00a8cc;
  text-align: center;
  margin-bottom: 20px;
  font-size: 1.5rem;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  margin-bottom: 5px;
  font-size: 0.95rem;
}

.form-control {
  font-size: 16px !important;
  height: 42px;
  border-radius: 8px;
  border: 2px solid #e0e0e0;
  -webkit-appearance: none;
  padding: 8px 12px;
}

.form-control:focus {
  border-color: #00d4ff;
  box-shadow: 0 0 0 0.2rem rgba(0, 212, 255, 0.25);
}

textarea.form-control {
  height: auto;
  min-height: 80px;
}

.help-block {
  margin-top: 5px;
  margin-bottom: 0;
  font-size: 0.85rem;
}

.btn-submit {
  margin-top: 15px;
  margin-bottom: 10px;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 168, 204, 0.5);
}

/* Mobile : encore plus compact */
@media (max-width: 768px) {
  .form-container {
    padding: 20px 15px;
    max-height: 85vh;
  }
  
  .form-container h2 {
    font-size: 1.3rem;
    margin-bottom: 15px;
  }
  
  .form-group {
    margin-bottom: 12px;
  }
  
  .form-control {
    height: 40px;
  }
  
  textarea.form-control {
    min-height: 70px;
  }
  
  .btn-submit {
    padding: 12px;
    font-size: 1.1rem;
  }
}
</style>

<!-- JavaScript COPIÉ de _page.php -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('freeTrialForm');
    
    if (!form) return;

    form.addEventListener('submit', function(e) {
        var isValid = true;
        var firstError = null;

        // Validation du CENTRE
        var centerSelect = document.getElementById('center');
        var centerError = centerSelect ? centerSelect.nextElementSibling : null;
        if (centerSelect && centerSelect.value === "") {
            if(centerError) centerError.style.display = 'block';
            if(centerSelect) centerSelect.style.borderColor = 'red';
            isValid = false;
            if(!firstError) firstError = centerSelect;
        } else {
            if(centerError) centerError.style.display = 'none';
            if(centerSelect) centerSelect.style.borderColor = '';
        }

        // Validation du NOM
        var nomInput = document.getElementById('nom');
        var nomError = nomInput ? nomInput.nextElementSibling : null;
        if (nomInput && nomInput.value.trim().length < 2) {
            if(nomError) nomError.style.display = 'block';
            if(nomInput) nomInput.style.borderColor = 'red';
            isValid = false;
            if(!firstError) firstError = nomInput;
        } else {
            if(nomError) nomError.style.display = 'none';
            if(nomInput) nomInput.style.borderColor = '';
        }

        // Validation EMAIL
        var emailInput = document.getElementById('email');
        var emailError = emailInput ? emailInput.nextElementSibling : null;
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailInput && !emailRegex.test(emailInput.value)) {
            if(emailError) emailError.style.display = 'block';
            if(emailInput) emailInput.style.borderColor = 'red';
            isValid = false;
            if(!firstError) firstError = emailInput;
        } else {
            if(emailError) emailError.style.display = 'none';
            if(emailInput) emailInput.style.borderColor = '';
        }

        // Validation TÉLÉPHONE
        var phoneInput = document.getElementById('phone');
        var phoneError = phoneInput ? phoneInput.nextElementSibling : null;
        var phoneRegex = /^[\d\s\.\-\+\(\)]{10,}$/; 
        
        if (phoneInput && !phoneRegex.test(phoneInput.value)) {
            if(phoneError) phoneError.style.display = 'block';
            if(phoneInput) phoneInput.style.borderColor = 'red';
            isValid = false;
            if(!firstError) firstError = phoneInput;
        } else {
            if(phoneError) phoneError.style.display = 'none';
            if(phoneInput) phoneInput.style.borderColor = '';
        }

        // SI INVALIDE
        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
            
            if (firstError) {
                setTimeout(function() {
                    try {
                        firstError.scrollIntoView({ behavior: 'auto', block: 'center' });
                        setTimeout(function() { 
                            firstError.focus(); 
                        }, 100);
                    } catch(err) {
                        firstError.focus();
                    }
                }, 100);
            }
            return false;
        }
        
        // Track conversion
        if (typeof gtag !== 'undefined') {
            gtag('event', 'form_submission', {
                'event_category': 'conversion',
                'event_label': 'free_trial_request'
            });
        }
        
        return true;
    });

    // Effacer les erreurs lors de la saisie
    var inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            var error = this.nextElementSibling;
            if (error && error.classList && error.classList.contains('error-message')) {
                error.style.display = 'none';
                this.style.borderColor = '';
            }
        });
        
        input.addEventListener('change', function() {
            var error = this.nextElementSibling;
            if (error && error.classList && error.classList.contains('error-message')) {
                error.style.display = 'none';
                this.style.borderColor = '';
            }
        });
    });
});
</script>
