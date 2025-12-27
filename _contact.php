<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
?>

<!-- Header -->
<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Contactez-nous</h1>
    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li class="active">Contact</li>
    </ol>
  </div>
</header>

<!-- Bannière Contact -->
<section class="content-area" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); padding: 40px 0; margin-bottom: 0;">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center" style="color: white;">
        <h2 style="color: white; font-size: 2rem; margin: 0 0 15px 0;">
          <i class="fa fa-comments"></i> Besoin d'Aide ou d'Informations ?
        </h2>
        <p style="font-size: 1.2rem; margin-bottom: 10px; opacity: 0.95;">
          Notre équipe vous répond sous 24h • Questions, réservations, franchise
        </p>
        <p style="font-size: 1.1rem; opacity: 0.9;">
          <i class="fa fa-phone"></i> <strong>06 22 64 70 95</strong> • 
          <i class="fa fa-envelope"></i> <a href="mailto:claude@alesiaminceur.com" style="color: white; text-decoration: underline;">claude@alesiaminceur.com</a>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Section Contact Principal -->
<section class="content-area bg1">
  <div class="container">

    <!-- Introduction -->
    <header class="page-header text-center" id="contact">
      <h2 class="page-title" style="color: #00a8cc; margin-bottom: 20px;">Une Question ? Écrivez-nous !</h2>
      <p class="larger" style="font-size: 1.1rem; color: #666;">
        Remplissez le formulaire ci-dessous en indiquant la ville concernée, nous vous répondrons rapidement.
      </p>
    </header>

    <div class="row" style="margin-top: 40px;">
      
      <!-- Colonne Formulaire -->
      <div class="col-md-8">
        <div class="contactForm" style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
          
          <!-- Messages de succès/erreur -->
          <div class="successMessage alert alert-success alert-dismissable" style="display: none; border-radius: 10px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle"></i> <strong>Merci !</strong> Nous vous répondrons dès que possible.
          </div>
          <div class="errorMessage alert alert-danger alert-dismissable" style="display: none; border-radius: 10px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-triangle"></i> <strong>Erreur :</strong> Une erreur est survenue.
          </div>

          <!-- Formulaire -->
          <form action="#contact" method="post">
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nom" style="color: #00a8cc; font-weight: 600;">
                    <i class="fa fa-user"></i> Nom et Prénom *
                  </label>
                  <input type="text" 
                         class="form-control" 
                         id="nom"
                         placeholder="Votre nom complet" 
                         name="nom" 
                         value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>"
                         required
                         style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; font-size: 1rem;" />
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email" style="color: #00a8cc; font-weight: 600;">
                    <i class="fa fa-envelope"></i> Email *
                  </label>
                  <input type="email" 
                         class="form-control" 
                         id="email"
                         placeholder="votre.email@exemple.com" 
                         name="email" 
                         value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                         required
                         style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; font-size: 1rem;" />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="ville" style="color: #00a8cc; font-weight: 600;">
                    <i class="fa fa-map-marker"></i> Ville Concernée
                  </label>
                  <input type="text" 
                         class="form-control" 
                         id="ville"
                         placeholder="Ex: Cannes, Nice, Antibes..." 
                         name="ville" 
                         value="<?php echo htmlspecialchars($_POST['ville'] ?? ''); ?>"
                         style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; font-size: 1rem;" />
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="sujet" style="color: #00a8cc; font-weight: 600;">
                    <i class="fa fa-tag"></i> Sujet
                  </label>
                  <select class="form-control" 
                          id="sujet" 
                          name="sujet"
                          style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; font-size: 1rem;">
                    <option value="">Sélectionnez un sujet</option>
                    <option value="information">Demande d'information</option>
                    <option value="reservation">Réservation séance gratuite</option>
                    <option value="tarifs">Question sur les tarifs</option>
                    <option value="franchise">Ouvrir un centre (Franchise)</option>
                    <option value="recrutement">Candidature emploi</option>
                    <option value="autre">Autre</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="message" style="color: #00a8cc; font-weight: 600;">
                <i class="fa fa-comment"></i> Message *
              </label>
              <textarea class="form-control" 
                        id="message"
                        placeholder="Décrivez votre demande en détail..." 
                        name="message" 
                        rows="6"
                        required
                        style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; font-size: 1rem;"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </div>

            <!-- Champ anti-spam caché -->
            <input type="hidden" name="raison">

            <!-- Bouton submit -->
            <button type="submit" 
                    name="send" 
                    class="btn btn-lg btn-block" 
                    style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border: none; padding: 18px; font-size: 1.2rem; border-radius: 50px; font-weight: 600; box-shadow: 0 5px 20px rgba(0, 168, 204, 0.4); margin-top: 20px;">
              <i class="fa fa-paper-plane"></i> Envoyer mon Message
            </button>

            <p style="text-align: center; margin-top: 20px; color: #666; font-size: 0.95rem;">
              <i class="fa fa-lock"></i> Vos données sont sécurisées et ne seront jamais partagées
            </p>
          </form>

          <!-- Traitement PHP -->
          <?php
          if (isset($_POST['send']) && empty($_POST['raison'])) {
            
            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->Host = "in-v3.mailjet.com";
            $mail->isHTML(true);
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->Username = getenv("MAILJET_USERNAME");
            $mail->Password = getenv("MAILJET_PASSWORD");

            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $ville = trim($_POST['ville'] ?? '');
            $sujet = trim($_POST['sujet'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $err = 0;
            $errors = '';

            if (empty($nom) || empty($email) || empty($message)) {
              $errors = '<i class="fa fa-exclamation-circle"></i> Veuillez remplir tous les champs obligatoires (Nom, Email, Message).';
              $err++;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              $errors .= '<br /><i class="fa fa-exclamation-circle"></i> L\'adresse email n\'est pas valide.';
              $err++;
            }

            if ($err == 0) {
              try {
                $mail->setFrom('contact@aquavelo.com', 'Aquavelo - Contact');
                $mail->addReplyTo($email, $nom);
                $mail->addAddress('claude@alesiaminceur.com', 'Claude Rodriguez');

                $object = "Contact Aquavelo.com - " . ($sujet ? ucfirst($sujet) : "Demande générale");
                $mail->Subject = $object;

                $sujetLabel = $sujet ? "<strong>Sujet :</strong> " . htmlspecialchars(ucfirst($sujet)) . "<br />" : "";
                $villeLabel = $ville ? "<strong>Ville :</strong> " . htmlspecialchars($ville) . "<br />" : "";

                $messageHTML = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                  <div style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center;">
                    <h2 style="margin: 0;">Nouveau Contact Aquavelo.com</h2>
                  </div>
                  <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px;">
                    <p style="font-size: 16px; color: #333;"><strong>Nom :</strong> ' . htmlspecialchars($nom) . '</p>
                    <p style="font-size: 16px; color: #333;"><strong>Email :</strong> <a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></p>
                    ' . $villeLabel . '
                    ' . $sujetLabel . '
                    <hr style="border: 1px solid #e0e0e0; margin: 20px 0;">
                    <p style="font-size: 16px; color: #333;"><strong>Message :</strong></p>
                    <p style="font-size: 15px; color: #666; line-height: 1.6; background: #f8f9fa; padding: 15px; border-radius: 5px;">' 
                    . nl2br(htmlspecialchars($message)) . 
                    '</p>
                  </div>
                  <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
                    Message envoyé depuis le formulaire de contact Aquavelo.com
                  </div>
                </div>';

                $mail->msgHTML($messageHTML);

                if ($mail->send()) {
                  echo '<div class="alert alert-success" style="border-radius: 10px; margin-top: 20px; border: 2px solid #00d4ff; background: #e8f8fc;">
                          <i class="fa fa-check-circle" style="color: #00a8cc; font-size: 1.5rem;"></i> 
                          <strong>Message envoyé avec succès !</strong><br/>
                          Nous vous répondrons dans les plus brefs délais. Merci !
                        </div>';
                  
                  // Tracking analytics
                  echo '<script>
                    if (typeof gtag !== "undefined") {
                      gtag("event", "form_submission", {
                        "event_category": "conversion",
                        "event_label": "contact_form",
                        "value": 1
                      });
                    }
                  </script>';
                } else {
                  echo '<div class="alert alert-danger" style="border-radius: 10px; margin-top: 20px;">
                          <i class="fa fa-exclamation-triangle"></i> 
                          <strong>Erreur :</strong> Votre message n\'a pas pu être envoyé. Veuillez réessayer ou nous contacter directement.
                        </div>';
                }
              } catch (Exception $e) {
                echo '<div class="alert alert-danger" style="border-radius: 10px; margin-top: 20px;">
                        <i class="fa fa-exclamation-triangle"></i> 
                        <strong>Erreur technique :</strong> ' . $mail->ErrorInfo . '
                      </div>';
              }
            } else {
              echo '<div class="alert alert-danger" style="border-radius: 10px; margin-top: 20px;">
                      <i class="fa fa-exclamation-triangle"></i> 
                      <strong>Il y a ' . $err . ' erreur' . ($err > 1 ? 's' : '') . ' dans le formulaire :</strong><br/>' 
                      . $errors . 
                    '</div>';
            }
          }
          ?>

        </div>
      </div>

      <!-- Colonne Informations -->
      <div class="col-md-4">
        
        <!-- Card Contact Rapide -->
        <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 25px;">
          <h3 style="color: #00a8cc; margin-top: 0; font-size: 1.4rem;">
            <i class="fa fa-phone-square"></i> Contact Rapide
          </h3>
          <p style="font-size: 1.05rem; color: #666; margin-bottom: 20px;">
            Besoin d'une réponse immédiate ? Appelez-nous !
          </p>
          <a href="tel:0622647095" 
             style="display: block; background: linear-gradient(135deg, #ff9800, #f57c00); color: white; text-align: center; padding: 18px; border-radius: 50px; font-size: 1.3rem; font-weight: 600; text-decoration: none; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(255, 152, 0, 0.4);">
            <i class="fa fa-phone"></i> 06 22 64 70 95
          </a>
          <p style="text-align: center; color: #999; font-size: 0.9rem; margin: 0;">
            <i class="fa fa-clock-o"></i> Lun-Ven : 9h-19h • Sam : 10h-18h
          </p>
        </div>

        <!-- Card Recrutement -->
        <div id="emploi" style="background: linear-gradient(135deg, #e8f8fc, #d4f1f9); padding: 30px; border-radius: 15px; border: 2px solid #00d4ff; margin-bottom: 25px;">
          <h3 style="color: #00a8cc; margin-top: 0; font-size: 1.3rem;">
            <i class="fa fa-briefcase"></i> Recrutement
          </h3>
          <p style="font-size: 1rem; color: #666; line-height: 1.6;">
            Nous recherchons des <strong>maîtres nageurs</strong> pour notre développement national.
          </p>
          <p style="margin: 15px 0 0 0;">
            <a href="mailto:claude@alesiaminceur.com" 
               style="color: #00a8cc; font-weight: 600; text-decoration: underline;">
              <i class="fa fa-envelope"></i> Envoyer CV + photo
            </a>
          </p>
        </div>

        <!-- Card Infos Légales -->
        <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 25px;">
          <h3 style="color: #00a8cc; margin-top: 0; font-size: 1.3rem;">
            <i class="fa fa-building"></i> Gestionnaire du Site
          </h3>
          <dl style="margin: 0;">
            <dd style="margin-bottom: 8px;"><strong>Aqua Cannes</strong></dd>
            <dd style="margin-bottom: 8px; color: #666;">60 avenue du Docteur Raymond Picaud</dd>
            <dd style="margin-bottom: 8px; color: #666;">06150 Cannes</dd>
            <dd style="margin-bottom: 8px; color: #666;">Capital 15 000 €</dd>
            <dd style="margin-bottom: 8px; color: #666;">RCS 822 269 528</dd>
            <dd style="margin-bottom: 8px; color: #666;">TVA FR44822269528</dd>
          </dl>
          
          <hr style="margin: 20px 0; border-color: #e0e0e0;">
          
          <p style="margin: 15px 0 0 0;">
            <strong style="color: #00a8cc;"><i class="fa fa-envelope"></i> Email :</strong><br/>
            <a href="mailto:claude@alesiaminceur.com" style="color: #00a8cc; text-decoration: underline;">
              claude@alesiaminceur.com
            </a>
          </p>
        </div>

        <!-- Boutons Modals -->
        <div style="text-align: center;">
          <button type="button" 
                  data-toggle="modal" 
                  data-target="#infos" 
                  class="btn btn-default btn-block"
                  style="margin-bottom: 15px; padding: 15px; border-radius: 10px; border: 2px solid #00d4ff; color: #00a8cc; font-weight: 600; background: white;">
            <i class="fa fa-info-circle"></i> Mentions Légales
          </button>
          
          <button type="button" 
                  data-toggle="modal" 
                  data-target="#politique" 
                  class="btn btn-default btn-block"
                  style="padding: 15px; border-radius: 10px; border: 2px solid #00d4ff; color: #00a8cc; font-weight: 600; background: white;">
            <i class="fa fa-shield"></i> Politique de Confidentialité
          </button>
        </div>

      </div>

    </div>

  </div>
</section>

<!-- CTA Franchise -->
<section class="content-area bg2" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white; padding: 60px 0;">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center">
        <h2 style="color: white; font-size: 2.2rem; margin-bottom: 20px;">
          <i class="fa fa-lightbulb-o"></i> Vous Souhaitez Ouvrir un Centre Aquavélo ?
        </h2>
        <p style="font-size: 1.2rem; margin-bottom: 30px; opacity: 0.95;">
          Rejoignez notre réseau de 17 centres en France<br/>
          Investissement maîtrisé • Formation complète • Accompagnement total
        </p>
        <a href="/franchise" class="btn btn-lg" style="background: white; color: #ff9800; border: none; padding: 20px 50px; font-size: 1.3rem; border-radius: 50px; font-weight: 600; box-shadow: 0 5px 20px rgba(0,0,0,0.2); text-decoration: none;">
          <i class="fa fa-briefcase"></i> Découvrir la Franchise
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Modal Mentions Légales -->
<div class="modal fade" id="infos" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border-radius: 15px 15px 0 0; padding: 25px;">
        <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
        <h4 class="modal-title" style="color: white; font-size: 1.5rem;">
          <i class="fa fa-info-circle"></i> Mentions Légales
        </h4>
      </div>
      <div class="modal-body" style="padding: 30px; max-height: 70vh; overflow-y: auto;">
        <h3>1. Présentation du site</h3>
        <p>En vertu de l'article 6 de la loi n° 2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique, il est précisé aux utilisateurs du site <a href="https://aquavelo.com/">https://aquavelo.com</a> l'identité des différents intervenants dans le cadre de sa réalisation et de son suivi :</p>
        <p><strong>Propriétaire :</strong> AQUA CANNES</p>
        <p><strong>Créateur :</strong> Claude RODRIGUEZ</p>
        <p><strong>Responsable publication :</strong> AQUA CANNES</p>
        <p><strong>Webmaster :</strong> Claude RODRIGUEZ - 60 avenue du Docteur Raymond Picaud à CANNES</p>
        <p><strong>Hébergeur :</strong> O2switch - Chemin des Pardiaux, 63000 Clermont-Ferrand, France</p>

        <h3>2. Conditions générales d'utilisation</h3>
        <p>L'utilisation du site implique l'acceptation pleine et entière des conditions générales d'utilisation ci-après décrites. Ces conditions d'utilisation sont susceptibles d'être modifiées ou complétées à tout moment.</p>

        <h3>3. Description des services fournis</h3>
        <p>Le site <a href="https://aquavelo.com/">https://aquavelo.com</a> a pour objet de fournir des informations concernant des services sur l'Aquavelo destiné aux particuliers.</p>

        <h3>4. Litige avec son centre</h3>
        <p>En cas de litige avec son centre, l'abonné(e) a la possibilité d'utiliser les services d'un médiateur gratuitement : <strong>Médiation de la consommation CM2C</strong> - 14 rue Saint Jean 75017 Paris - <a href="https://www.cm2c.net" target="_blank">https://www.cm2c.net</a></p>

        <h3>5. Propriété intellectuelle</h3>
        <p>AQUAVELO est propriétaire des droits de propriété intellectuelle ou détient les droits d'usage sur tous les éléments accessibles sur le site. Toute reproduction non autorisée sera considérée comme constitutive d'une contrefaçon.</p>

        <h3>6. Gestion des données personnelles</h3>
        <p>Conformément aux dispositions de la loi 78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés, tout utilisateur dispose d'un droit d'accès, de rectification et d'opposition aux données personnelles le concernant.</p>

        <h3>7. Droit applicable</h3>
        <p>Tout litige en relation avec l'utilisation du site est soumis au droit français. Attribution exclusive de juridiction aux tribunaux compétents de Paris.</p>
      </div>
      <div class="modal-footer" style="padding: 20px;">
        <button type="button" class="btn btn-primary" data-dismiss="modal" style="background: #00a8cc; border: none; padding: 12px 30px; border-radius: 25px;">
          <i class="fa fa-times"></i> Fermer
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Politique de Confidentialité -->
<div class="modal fade" id="politique" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border-radius: 15px 15px 0 0; padding: 25px;">
        <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
        <h4 class="modal-title" style="color: white; font-size: 1.5rem;">
          <i class="fa fa-shield"></i> Politique de Confidentialité
        </h4>
      </div>
      <div class="modal-body" style="padding: 30px; max-height: 70vh; overflow-y: auto;">
        <h3>Politique de confidentialité</h3>
        <p>Nous nous engageons à respecter la confidentialité des renseignements personnels que nous collectons.</p>

        <h4>Collecte des renseignements personnels</h4>
        <p>Nous collectons les renseignements suivants :</p>
        <ul>
          <li>Nom et Prénom</li>
          <li>Numéro de téléphone</li>
          <li>Adresse email</li>
          <li>Ville de résidence</li>
        </ul>

        <h4>Finalités</h4>
        <p>Nous utilisons les renseignements collectés pour :</p>
        <ul>
          <li>Prise de contact</li>
          <li>Répondre à vos demandes</li>
          <li>Envoi d'emailings marketing (avec consentement)</li>
        </ul>

        <h4>Cookies</h4>
        <p>Nous recueillons certaines informations par le biais de cookies :</p>
        <ul>
          <li>Adresse IP</li>
          <li>Système d'exploitation</li>
          <li>Pages visitées</li>
          <li>Heure et jour de connexion</li>
        </ul>

        <h4>Droit d'opposition et de retrait</h4>
        <p>Vous pouvez exercer vos droits en nous écrivant à : <a href="mailto:claude@alesiaminceur.com">claude@alesiaminceur.com</a></p>

        <h4>Sécurité</h4>
        <p>Vos données sont conservées dans un environnement sécurisé. Nous utilisons :</p>
        <ul>
          <li>Protocole SSL (Secure Sockets Layer)</li>
          <li>Sauvegarde informatique</li>
          <li>Pare-feu (Firewalls)</li>
        </ul>

        <h4>Législation</h4>
        <p>Nous respectons les dispositions du RGPD : <a href="https://www.cnil.fr/fr/textes-officiels-europeens-protection-donnees" target="_blank">https://www.cnil.fr/fr/textes-officiels-europeens-protection-donnees</a></p>
      </div>
      <div class="modal-footer" style="padding: 20px;">
        <button type="button" class="btn btn-primary" data-dismiss="modal" style="background: #00a8cc; border: none; padding: 12px 30px; border-radius: 25px;">
          <i class="fa fa-times"></i> Fermer
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Tracking Analytics -->
<script>
// Track soumission formulaire
document.querySelector('form[action="#contact"]').addEventListener('submit', function() {
  if (typeof gtag !== 'undefined') {
    gtag('event', 'form_start', {
      'event_category': 'engagement',
      'event_label': 'contact_form'
    });
  }
});

// Track clic téléphone
document.querySelectorAll('a[href^="tel:"]').forEach(function(link) {
  link.addEventListener('click', function() {
    if (typeof gtag !== 'undefined') {
      gtag('event', 'phone_click', {
        'event_category': 'conversion',
        'event_label': 'contact_page'
      });
    }
  });
});

// Track clic email
document.querySelectorAll('a[href^="mailto:"]').forEach(function(link) {
  link.addEventListener('click', function() {
    if (typeof gtag !== 'undefined') {
      gtag('event', 'email_click', {
        'event_category': 'engagement',
        'event_label': 'contact_page'
      });
    }
  });
});
</script>

<style>
/* Styles spécifiques page contact */
.form-control:focus {
  border-color: #00d4ff;
  box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 168, 204, 0.5) !important;
}

@media (max-width: 768px) {
  .modal-dialog {
    margin: 10px;
  }
  
  .modal-body {
    max-height: 60vh !important;
  }
}
</style>


