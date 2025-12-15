<!-- Hero Section avec image de fond -->
<section class="content-area brightText" data-bg="images/content/about-v2-title-bg.jpg" data-topspace="70" data-btmspace="50">
  <div class="container">
    <div class="flexslider std-slider center-controls" data-animation="fade" data-loop="true" data-animspeed="600" data-dircontrols="true" data-controls="true">
      <ul class="slides">
        <li>
          <blockquote class="huge text-center">
            <p>🎁 Profitez d'une séance découverte GRATUITE<br>
              Testez l'aquabiking sans engagement dans le centre de votre choix
            </p>
          </blockquote>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- Section principale -->
<section class="content-area bg1">
  <div class="container">
    <header class="page-header text-center">
      <h1 class="page-title">Séance Découverte Gratuite</h1>
      <h2>Essayez l'aquabiking pendant 45 minutes avec un coach professionnel</h2>
    </header>

    <!-- Formulaire de réservation -->
    <div class="row" style="margin-top: 50px;">
      <div class="col-md-8 col-md-offset-2">
        <div class="well" style="background: #f8f9fa; border: 3px solid #00d4ff; border-radius: 15px; padding: 40px;">
          <h3 class="text-center" style="color: #00a8cc; margin-bottom: 30px;">
            <i class="fa fa-calendar"></i> Réservez votre séance gratuite
          </h3>

          <form class="liveForm" role="form" action="/form/send.php" method="post" data-email-subject="Séance Découverte Gratuite" data-show-errors="true" id="freeTrialForm">
            <fieldset>
              
              <!-- Sélection du centre -->
              <div class="form-group">
                <label for="centre">Dans quel centre souhaitez-vous effectuer votre séance ? <span style="color: red;">*</span></label>
                <select name="field[]" class="form-control" id="centre" required style="font-size: 16px;">
                  <option value="">Sélectionnez un centre</option>
                  <?php foreach ($centers_list_d as $row_centers) { ?>
                    <option value="<?= $row_centers['city']; ?>"><?= $row_centers['city']; ?></option>
                  <?php } ?>
                </select>
              </div>

              <!-- Nom et prénom -->
              <div class="form-group">
                <label for="nom">Nom et Prénom <span style="color: red;">*</span></label>
                <input type="text" 
                       name="field[]" 
                       class="form-control" 
                       id="nom" 
                       placeholder="Votre nom et prénom" 
                       required 
                       autocomplete="name"
                       style="font-size: 16px;">
              </div>

              <!-- Email -->
              <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span></label>
                <input type="email" 
                       name="field[]" 
                       class="form-control" 
                       id="email" 
                       placeholder="votre@email.com" 
                       required 
                       autocomplete="email"
                       style="font-size: 16px;">
              </div>

              <!-- Téléphone - CORRIGÉ POUR iOS -->
              <div class="form-group">
                <label for="telephone">Téléphone <span style="color: red;">*</span></label>
                <input type="tel" 
                       name="field[]" 
                       class="form-control" 
                       id="telephone" 
                       placeholder="06 12 34 56 78" 
                       required 
                       pattern="[0-9\s\.\-\+]*"
                       inputmode="tel"
                       autocomplete="tel"
                       style="font-size: 16px;">
              </div>

              <!-- Date souhaitée -->
              <div class="form-group">
                <label for="date">Date souhaitée (optionnel)</label>
                <input type="text" 
                       name="field[]" 
                       class="form-control" 
                       id="date" 
                       placeholder="Ex: Lundi 15 janvier à 10h"
                       autocomplete="off"
                       style="font-size: 16px;">
                <p class="help-block">Notre équipe vous contactera pour confirmer la disponibilité</p>
              </div>

              <!-- Message -->
              <div class="form-group">
                <label for="message">Message (optionnel)</label>
                <textarea name="field[]" 
                          class="form-control" 
                          id="message" 
                          rows="3" 
                          placeholder="Votre message..."
                          autocomplete="off"
                          style="font-size: 16px;"></textarea>
              </div>

              <input type="hidden" name="reason" value="Séance découverte gratuite">
              <input type="hidden" name="segment" value="free-trial">

              <!-- Bouton submit -->
              <div class="text-center" style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary btn-lg" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); border: none; padding: 15px 50px; font-size: 1.2rem; border-radius: 50px;">
                  <i class="fa fa-check-circle"></i> Recevoir mon bon par email
                </button>
              </div>

              <!-- ⭐ Lien Calendly pour Cannes, Nice, Vallauris (centres 305, 347, 349) -->
              <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
                <p class="text-center" style="margin-top: 20px; color: #666;">
                  <small>Vous pouvez aussi réserver directement sur notre 
                  <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank" style="color: #00a8cc; font-weight: 600;">
                    calendrier en ligne <i class="fa fa-external-link"></i>
                  </a></small>
                </p>
              <?php endif; ?>

              <!-- ⭐ Lien SimplyBook pour Mérignac (centre 343) -->
              <?php if (isset($row_center['id']) && in_array($row_center['id'], [343])) : ?>
                <p class="text-center" style="margin-top: 20px; color: #666;">
                  <small>Vous pouvez aussi réserver directement sur notre 
                  <a href="https://aquavelomerignac33.simplybook.it/v2/" target="_blank" style="color: #00a8cc; font-weight: 600;">
                    calendrier en ligne <i class="fa fa-external-link"></i>
                  </a></small>
                </p>
              <?php endif; ?>

            </fieldset>
          </form>

          <div class="successMessage alert alert-success alert-dismissable" style="display: none; margin-top: 20px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Merci !</strong> Votre demande a été envoyée. Nous vous contacterons rapidement pour confirmer votre séance gratuite.
          </div>
          <div class="errorMessage alert alert-danger alert-dismissable" style="display: none; margin-top: 20px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Erreur !</strong> Une erreur est survenue. Veuillez réessayer ou nous contacter directement au 06 22 64 70 95.
          </div>

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
            <a class="pull-left" href="#"> <i class="fa fa-gift" style="color: #00d4ff;"></i> </a>
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
            <a class="pull-left" href="#"> <i class="fa fa-heart" style="color: #00d4ff;"></i> </a>
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
            <a class="pull-left" href="#"> <i class="fa fa-users" style="color: #00d4ff;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Ambiance conviviale</a></h4>
              <p>Cours collectifs en petits groupes. Rencontrez nos coachs et découvrez nos installations modernes.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: 30px;">
      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-shield" style="color: #00d4ff;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Sans traumatisme</a></h4>
              <p>Sport idéal pour les articulations. Pas de courbatures grâce à la résistance de l'eau.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-fire" style="color: #00d4ff;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Résultats rapides</a></h4>
              <p>Visible dès le premier mois avec une pratique régulière de 2 séances par semaine.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-clock-o" style="color: #00d4ff;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Horaires flexibles</a></h4>
              <p>Centres ouverts 7j/7, cours toutes les heures. Choisissez le créneau qui vous convient.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Section Ce qu'il faut savoir -->
<section class="content-area bg1">
  <div class="container">
    <header class="page-header text-center">
      <h2 class="page-title">Ce qu'il faut savoir</h2>
    </header>

    <div class="row" style="margin-top: 40px;">
      <div class="col-md-6">
        <h3 style="color: #00a8cc;"><i class="fa fa-check-circle"></i> Que faut-il apporter ?</h3>
        <ul style="font-size: 1.1rem; line-height: 2;">
          <li><strong>Un maillot de bain</strong> - C'est tout !</li>
          <li>Une serviette (fournie si besoin)</li>
          <li>Des affaires de rechange</li>
          <li>Vos chaussures aquatiques (si vous en avez)</li>
        </ul>

        <h3 style="color: #00a8cc; margin-top: 40px;"><i class="fa fa-info-circle"></i> Bon à savoir</h3>
        <ul style="font-size: 1.1rem; line-height: 2;">
          <li>Vous êtes immergé jusqu'à la taille seulement</li>
          <li>Vos cheveux restent secs</li>
          <li>Vestiaires et douches disponibles</li>
          <li>Produits de douche fournis</li>
          <li>Casiers sécurisés</li>
        </ul>
      </div>

      <div class="col-md-6">
        <h3 style="color: #00a8cc;"><i class="fa fa-question-circle"></i> Questions fréquentes</h3>
        
        <div class="panel-group" id="faq">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#faq" href="#faq1">
                  Je ne sais pas nager, puis-je faire de l'aquabiking ?
                </a>
              </h4>
            </div>
            <div id="faq1" class="panel-collapse collapse in">
              <div class="panel-body">
                Absolument ! Vous êtes immergé jusqu'à la taille seulement et le vélo est stable. Pas besoin de savoir nager.
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#faq" href="#faq2">
                  Quelle est la durée de la séance gratuite ?
                </a>
              </h4>
            </div>
            <div id="faq2" class="panel-collapse collapse">
              <div class="panel-body">
                La séance découverte dure 45 minutes, comme nos séances habituelles.
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#faq" href="#faq3">
                  Dois-je m'engager après la séance gratuite ?
                </a>
              </h4>
            </div>
            <div id="faq3" class="panel-collapse collapse">
              <div class="panel-body">
                Aucun engagement ! La séance est totalement gratuite et sans obligation. Vous décidez ensuite si vous souhaitez continuer.
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#faq" href="#faq4">
                  Peut-on venir à plusieurs ?
                </a>
              </h4>
            </div>
            <div id="faq4" class="panel-collapse collapse">
              <div class="panel-body">
                Oui ! Vous pouvez venir avec un(e) ami(e). L'offre découverte est valable pour chaque nouvelle personne.
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#faq" href="#faq5">
                  Je suis enceinte, puis-je essayer ?
                </a>
              </h4>
            </div>
            <div id="faq5" class="panel-collapse collapse">
              <div class="panel-body">
                Oui, après accord de votre médecin. L'aquabiking est recommandé pour les femmes enceintes (en douceur).
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Section Témoignages -->
<section class="content-area bg2">
  <div class="container">
    <header class="page-header text-center">
      <h2 class="page-title">Ils ont essayé, ils adorent !</h2>
    </header>

    <div class="row" style="margin-top: 40px;">
      <div class="col-md-4">
        <div class="well text-center" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
          <div style="font-size: 3rem; color: #00d4ff;">⭐⭐⭐⭐⭐</div>
          <p style="font-style: italic; margin: 20px 0;">"J'ai adoré ma séance découverte ! L'ambiance est super, le coach très motivant. J'ai brûlé 450 calories en 45 minutes. Je me suis inscrite tout de suite !"</p>
          <p style="color: #00a8cc; font-weight: 600;">Sophie, 34 ans - Cannes</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="well text-center" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
          <div style="font-size: 3rem; color: #00d4ff;">⭐⭐⭐⭐⭐</div>
          <p style="font-style: italic; margin: 20px 0;">"J'avais peur de ne pas y arriver car je ne fais jamais de sport. Mais c'était top ! Pas de douleur grâce à l'eau, et je me sens déjà plus légère."</p>
          <p style="color: #00a8cc; font-weight: 600;">Marie, 42 ans - Nice</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="well text-center" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
          <div style="font-size: 3rem; color: #00d4ff;">⭐⭐⭐⭐⭐</div>
          <p style="font-style: italic; margin: 20px 0;">"Après un problème de genou, je ne pouvais plus faire de sport. L'aquabiking est parfait : zéro impact sur les articulations. Merci pour la séance gratuite !"</p>
          <p style="color: #00a8cc; font-weight: 600;">Laurent, 51 ans - Antibes</p>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Section Nos centres -->
<section class="content-area bg1">
  <div class="container">
    <header class="page-header text-center">
      <h2 class="page-title">Nos centres près de chez vous</h2>
      <p style="font-size: 1.2rem;">Plus de 17 centres en France pour votre séance découverte</p>
    </header>

    <div class="row" style="margin-top: 40px;">
      <?php 
      $count = 0;
      foreach ($centers_list_d as $row_center_list) { 
        if ($count >= 6) break; // Afficher seulement 6 centres
        $count++;
      ?>
        <div class="col-md-4 col-sm-6" style="margin-bottom: 30px;">
          <div class="well" style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #00d4ff;">
            <h4 style="color: #00a8cc; margin-bottom: 10px;">
              <i class="fa fa-map-marker"></i> <?= $row_center_list['city']; ?>
            </h4>
            <p style="margin-bottom: 10px;">
              <i class="fa fa-clock-o"></i> Ouvert 7j/7<br>
              <i class="fa fa-users"></i> Cours collectifs
            </p>
            <a href="/centres/<?= $row_center_list['city']; ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border: none;">
              Voir le centre
            </a>
          </div>
        </div>
      <?php } ?>
    </div>

    <div class="text-center" style="margin-top: 30px;">
      <a href="/centres" class="btn btn-lg btn-primary" style="background: #00a8cc; border: none; padding: 15px 40px; border-radius: 50px;">
        <i class="fa fa-map-marker"></i> Voir tous nos centres
      </a>
    </div>

  </div>
</section>

<!-- Call to Action Final -->
<section class="content-area bg2" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 60px 0;">
  <div class="container">
    <div class="text-center">
      <h2 style="font-size: 2.5rem; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
        Prêt(e) à essayer l'aquabiking ?
      </h2>
      <p style="font-size: 1.3rem; margin-bottom: 30px; opacity: 0.95;">
        Réservez votre séance découverte gratuite dès maintenant
      </p>
      <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="btn btn-lg" style="background: white; color: #00a8cc; border: none; padding: 20px 50px; font-size: 1.3rem; border-radius: 50px; font-weight: 600; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
        <i class="fa fa-arrow-up"></i> Remplir le formulaire
      </a>
      <p style="margin-top: 20px; font-size: 1.1rem;">
        Ou appelez-nous au <a href="tel:0622647095" style="color: white; text-decoration: underline; font-weight: 600;">06 22 64 70 95</a>
      </p>
    </div>
  </div>
</section>

<!-- Tracking Analytics -->
<script>
// Validation iOS-friendly
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('freeTrialForm');
  
  if (form) {
    form.addEventListener('submit', function(e) {
      // Vérification basique avant soumission
      var centre = document.getElementById('centre').value;
      var nom = document.getElementById('nom').value;
      var email = document.getElementById('email').value;
      var telephone = document.getElementById('telephone').value;
      
      if (!centre || !nom || !email || !telephone) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires.');
        return false;
      }
      
      // Validation email
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Veuillez entrer une adresse email valide.');
        return false;
      }
      
      // Analytics tracking
      if (typeof gtag !== 'undefined') {
        gtag('event', 'form_submission', {
          'event_category': 'conversion',
          'event_label': 'free_trial_request'
        });
      }
      
      // Laisser le formulaire se soumettre normalement
      return true;
    });
  }
});
</script>

<style>
/* Styles spécifiques pour la page free */
.well {
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.panel-default > .panel-heading {
  background: #f8f9fa;
  border-color: #00d4ff;
}

.panel-title > a {
  color: #00a8cc;
  font-weight: 600;
  text-decoration: none;
}

.panel-title > a:hover {
  color: #00d4ff;
}

.iconBox.type4 .media .pull-left i {
  font-size: 3rem;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 168, 204, 0.4) !important;
}

/* ⭐ IMPORTANT iOS : Font-size minimum 16px pour éviter le zoom */
.form-control, 
select.form-control,
input.form-control,
textarea.form-control {
  font-size: 16px !important;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/* Responsive */
@media (max-width: 768px) {
  .well {
    padding: 20px !important;
  }
  
  .page-title {
    font-size: 1.8rem !important;
  }
  
  h2 {
    font-size: 1.5rem !important;
  }
}
</style>
