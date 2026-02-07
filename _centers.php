<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Nos Centres d'Aquabiking en France</h1>
    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li class="active">Centres</li>
    </ol>
  </div>
</header>

<!-- Bannière promo -->
<section class="content-area" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); padding: 30px 0;">
  <div class="container">
    <div class="text-center" style="color: white;">
      <h2 style="color: white; font-size: 2rem; margin: 0 0 10px 0;">
        🎁 Séance Découverte GRATUITE dans tous nos centres
      </h2>
      <p style="font-size: 1.2rem; margin: 0; opacity: 0.95;">
        Essayez l'aquabiking pendant 45 minutes • Sans engagement
      </p>
      <a href="/free" class="btn btn-lg" style="background: white; color: #00a8cc; border: none; padding: 15px 40px; font-size: 1.1rem; border-radius: 50px; font-weight: 600; margin-top: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
        <i class="fa fa-gift"></i> Réserver ma séance gratuite
      </a>
    </div>
  </div>
</section>

<!-- Section principale -->
<section class="content-area bg1">
  <div class="container">
    
    <!-- Filtres -->
    <ul id="galleryFilters" class="option-set clearfix list-unstyled list-inline text-center" style="margin-bottom: 40px;">
      <li style="display: inline-block; margin: 5px;">
        <a href="#filter=*" class="btn btn-default btn-primary" style="padding: 12px 30px; border-radius: 25px; font-weight: 600;">
          <i class="fa fa-globe"></i> Tous les centres (<?= count($centers_list_d) + 1; ?>)
        </a>
      </li>
      <li style="display: inline-block; margin: 5px;">
        <a href="#filter=.france" class="btn btn-default" style="padding: 12px 30px; border-radius: 25px; font-weight: 600;">
          <i class="fa fa-flag"></i> France (<?= count($centers_list_d); ?>)
        </a>
      </li>
      <li style="display: inline-block; margin: 5px;">
        <a href="#filter=.maroc" class="btn btn-default" style="padding: 12px 30px; border-radius: 25px; font-weight: 600;">
          <i class="fa fa-map-marker"></i> Maroc (1)
        </a>
      </li>
    </ul>

    <!-- Grille des centres -->
    <div id="galleryContainer" class="clearfix withSpaces col-3">
      
      <!-- Centres France -->
      <?php foreach ($centers_list_d as &$row_centers_list) { ?>
      <div class="galleryItem france">
        <article class="portfolio-item" style="border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s ease;">
          
          <!-- Image du centre -->
          <div class="portfolio-thumbnail" style="position: relative; overflow: hidden; background: linear-gradient(135deg, #00d4ff, #00a8cc);">
            <a href="/centres/<?= strtolower($row_centers_list['city']); ?>">
              <img src="/cloud/thumbnail/center_<?= $row_centers_list['id']; ?>/1.jpg" 
                   onerror="this.src='/images/aquagym-cours-collectif.jpg'"
                   alt="Centre Aquavélo <?= $row_centers_list['city']; ?> - Aquabiking et Aquagym"
                   style="width: 100%; height: 250px; object-fit: cover; transition: transform 0.3s ease;">
            </a>
            <a href="/centres/<?= strtolower($row_centers_list['city']); ?>" class="overlay-img">
              <span class="overlay-ico"><i class="fa fa-plus"></i></span>
            </a>
            
            <!-- Badge "Séance Gratuite" -->
            <div style="position: absolute; top: 15px; right: 15px; background: linear-gradient(135deg, #ff9800, #f57c00); color: white; padding: 8px 15px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; box-shadow: 0 3px 10px rgba(255, 152, 0, 0.4);">
              <i class="fa fa-gift"></i> Séance Gratuite
            </div>
          </div>

          <!-- Infos du centre -->
          <div style="padding: 20px; background: white;">
            <div class="entry-meta" style="margin-bottom: 10px;">
              <span class="cat-links">
                <i class="fa fa-map-marker" style="color: #00d4ff;"></i> 
                <a href="#" style="color: #00a8cc; font-weight: 600;">France</a>
              </span>
            </div>
            
            <h4 class="entry-title" style="margin: 0 0 15px 0; font-size: 1.3rem;">
              <a href="/centres/<?= strtolower($row_centers_list['city']); ?>" style="color: #333; font-weight: 700;">
                Aquavélo <?= $row_centers_list['city']; ?>
              </a>
            </h4>

            <p style="color: #666; margin-bottom: 15px; font-size: 0.95rem;">
              <i class="fa fa-bicycle" style="color: #00d4ff;"></i> 
              <?= $row_centers_list['TypeAQUAVELO']; ?>
            </p>

            <!-- Boutons d'action -->
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
              <a href="/centres/<?= strtolower($row_centers_list['city']); ?>" 
                 class="btn btn-sm" 
                 style="flex: 1; min-width: 120px; background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border: none; padding: 10px 20px; border-radius: 25px; font-weight: 600; text-align: center;">
                <i class="fa fa-eye"></i> Voir le centre
              </a>
              <a href="/free?city=<?= $row_centers_list['city']; ?>" 
                 class="btn btn-sm" 
                 style="flex: 1; min-width: 120px; background: #ff9800; color: white; border: none; padding: 10px 20px; border-radius: 25px; font-weight: 600; text-align: center;">
                <i class="fa fa-gift"></i> Essai gratuit
              </a>
            </div>
          </div>

        </article>
      </div>
      <?php } ?>

      <!-- Centre Maroc -->
      <div class="galleryItem maroc">
        <article class="portfolio-item" style="border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s ease;">
          
          <!-- Image du centre -->
          <div class="portfolio-thumbnail" style="position: relative; overflow: hidden;">
            <a href="#">
              <img src="images/content/works-03.jpg" 
                   alt="Centre Aquavélo Casablanca Maroc"
                   style="width: 100%; height: 250px; object-fit: cover; transition: transform 0.3s ease;">
            </a>
            <a href="#" class="overlay-img">
              <span class="overlay-ico"><i class="fa fa-plus"></i></span>
            </a>

            <!-- Badge "Bientôt disponible" -->
            <div style="position: absolute; top: 15px; right: 15px; background: linear-gradient(135deg, #9e9e9e, #757575); color: white; padding: 8px 15px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
              <i class="fa fa-clock-o"></i> Bientôt
            </div>
          </div>

          <!-- Infos du centre -->
          <div style="padding: 20px; background: white;">
            <div class="entry-meta" style="margin-bottom: 10px;">
              <span class="cat-links">
                <i class="fa fa-map-marker" style="color: #00d4ff;"></i> 
                <a href="#" style="color: #00a8cc; font-weight: 600;">Maroc</a>
              </span>
            </div>
            
            <h4 class="entry-title" style="margin: 0 0 15px 0; font-size: 1.3rem;">
              <a href="#" style="color: #333; font-weight: 700;">
                Aquavélo Casablanca
              </a>
            </h4>

            <p style="color: #666; margin-bottom: 15px; font-size: 0.95rem;">
              <i class="fa fa-bicycle" style="color: #00d4ff;"></i> 
              Racine
            </p>

            <!-- Bouton -->
            <a href="#" 
               class="btn btn-sm btn-block" 
               style="background: #e0e0e0; color: #666; border: none; padding: 10px 20px; border-radius: 25px; font-weight: 600; cursor: not-allowed;">
              <i class="fa fa-clock-o"></i> Bientôt disponible
            </a>
          </div>

        </article>
      </div>

    </div>
    <!-- / galleryContainer -->

  </div>
</section>

<!-- Section CTA Ouvrir un Centre -->
<section class="content-area bg2" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white; padding: 60px 0;">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center">
        <h2 style="color: white; font-size: 2.5rem; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
          💼 Vous souhaitez ouvrir un centre Aquavélo ?
        </h2>
        <p style="font-size: 1.3rem; margin-bottom: 30px; opacity: 0.95; line-height: 1.8;">
          Rejoignez notre réseau de 17 centres en France<br>
          Investissement maîtrisé • Formation complète • Accompagnement total
        </p>
        <a href="/franchise" class="btn btn-lg" style="background: white; color: #ff9800; border: none; padding: 20px 50px; font-size: 1.3rem; border-radius: 50px; font-weight: 600; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
          <i class="fa fa-briefcase"></i> Découvrir la Franchise
        </a>
        <p style="margin-top: 20px; font-size: 1.1rem;">
          Ou contactez Claude Rodriguez au 
          <a href="tel:0622647095" style="color: white; text-decoration: underline; font-weight: 600;">
            06 22 64 70 95
          </a>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Section Pourquoi Aquavélo -->
<section class="content-area bg1">
  <div class="container">
    <header class="page-header text-center">
      <h2 class="page-title">Pourquoi choisir Aquavélo ?</h2>
      <p style="font-size: 1.2rem;">Le leader français de l'aquabiking depuis plus de 9 ans</p>
    </header>

    <div class="row" style="margin-top: 40px;">
      <div class="col-md-3 col-sm-6" style="margin-bottom: 30px;">
        <div class="text-center">
          <div style="font-size: 3.5rem; color: #00d4ff; margin-bottom: 15px;">
            <i class="fa fa-map-marker"></i>
          </div>
          <h3 style="color: #00a8cc; font-size: 2.5rem; margin: 0;">17+</h3>
          <p style="font-size: 1.1rem; color: #666; margin-top: 10px;">Centres en France</p>
        </div>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom: 30px;">
        <div class="text-center">
          <div style="font-size: 3.5rem; color: #00d4ff; margin-bottom: 15px;">
            <i class="fa fa-users"></i>
          </div>
          <h3 style="color: #00a8cc; font-size: 2.5rem; margin: 0;">5000+</h3>
          <p style="font-size: 1.1rem; color: #666; margin-top: 10px;">Clients satisfaits</p>
        </div>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom: 30px;">
        <div class="text-center">
          <div style="font-size: 3.5rem; color: #00d4ff; margin-bottom: 15px;">
            <i class="fa fa-star"></i>
          </div>
          <h3 style="color: #00a8cc; font-size: 2.5rem; margin: 0;">98%</h3>
          <p style="font-size: 1.1rem; color: #666; margin-top: 10px;">Taux satisfaction</p>
        </div>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom: 30px;">
        <div class="text-center">
          <div style="font-size: 3.5rem; color: #00d4ff; margin-bottom: 15px;">
            <i class="fa fa-calendar"></i>
          </div>
          <h3 style="color: #00a8cc; font-size: 2.5rem; margin: 0;">9+</h3>
          <p style="font-size: 1.1rem; color: #666; margin-top: 10px;">Années d'expérience</p>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: 50px;">
      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-fire" style="color: #00d4ff;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Résultats Rapides</a></h4>
              <p>Brûlez jusqu'à 600 calories par séance. Résultats visibles dès le premier mois avec 2 séances par semaine.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-shield" style="color: #00d4ff;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Sans Traumatisme</a></h4>
              <p>Idéal pour les articulations. Sport doux et efficace grâce à la résistance de l'eau. Pas de courbatures.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="iconBox type4">
          <div class="media">
            <a class="pull-left" href="#"> <i class="fa fa-clock-o" style="color: #00d4ff;"></i> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Horaires Flexibles</a></h4>
              <p>Centres ouverts 7j/7. Cours toutes les heures de 9h à 21h. Réservation en ligne simple et rapide.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Tracking Analytics -->
<script>
// Track clics sur "Voir le centre"
document.querySelectorAll('a[href*="/centres/"]').forEach(function(link) {
  link.addEventListener('click', function() {
    if (typeof gtag !== 'undefined') {
      var city = this.getAttribute('href').split('/').pop();
      gtag('event', 'view_center', {
        'event_category': 'engagement',
        'event_label': city
      });
    }
  });
});

// Track clics sur "Essai gratuit"
document.querySelectorAll('a[href*="/free"]').forEach(function(link) {
  link.addEventListener('click', function() {
    if (typeof gtag !== 'undefined') {
      gtag('event', 'click_free_trial', {
        'event_category': 'conversion',
        'event_label': 'from_centers_page'
      });
    }
  });
});

// Track clics sur "Franchise"
document.querySelectorAll('a[href*="/franchise"]').forEach(function(link) {
  link.addEventListener('click', function() {
    if (typeof gtag !== 'undefined') {
      gtag('event', 'click_franchise', {
        'event_category': 'conversion',
        'event_label': 'from_centers_page'
      });
    }
  });
});
</script>

<style>
/* Styles spécifiques page centres */
.portfolio-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 168, 204, 0.3) !important;
}

.portfolio-item:hover img {
  transform: scale(1.05);
}

.overlay-img {
  opacity: 0;
  transition: all 0.3s ease;
}

.portfolio-item:hover .overlay-img {
  opacity: 1;
}

.iconBox.type4 .media .pull-left i {
  font-size: 3rem;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2) !important;
}

/* Responsive */
@media (max-width: 768px) {
  .page-title {
    font-size: 1.5rem !important;
  }
  
  h2 {
    font-size: 1.8rem !important;
  }
  
  .portfolio-item {
    margin-bottom: 30px;
  }
}
</style>

