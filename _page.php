<!-- Page spécifique centre -->
  
  
  <!-- Schema.org JSON-LD -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SportsActivityLocation",
    "name": "Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>",
    "description": "Centre d'aquabiking et aquagym proposant des séances pour affiner et raffermir votre silhouette",
    "image": [
      "https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg",
      "https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/2.jpg"
    ],
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
      "addressLocality": "<?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>",
      "addressCountry": "FR"
    },
    "telephone": "<?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
    "openingHoursSpecification": {
      "@type": "OpeningHoursSpecification",
      "description": "<?= htmlspecialchars($row_center['openhours'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
    },
    "priceRange": "$$",
    "url": "https://www.aquavelo.com/centres/<?= htmlspecialchars(strtolower($city ?? ''), ENT_QUOTES, 'UTF-8'); ?>",
    <?php if (!empty($row_center['facebook'])): ?>
    "sameAs": ["https://www.facebook.com/<?= htmlspecialchars($row_center['facebook'], ENT_QUOTES, 'UTF-8'); ?>"],
    <?php endif; ?>
    "hasOfferCatalog": {
      "@type": "OfferCatalog",
      "name": "Services Aquavélo",
      "itemListElement": [{
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "name": "Séance découverte gratuite",
          "description": "Essayez l'aquabiking et l'aquagym gratuitement pendant 45 minutes"
        },
        "price": "0",
        "priceCurrency": "EUR"
      }]
    }
  }
  </script>

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [{
      "@type": "ListItem",
      "position": 1,
      "name": "Accueil",
      "item": "https://www.aquavelo.com/"
    },{
      "@type": "ListItem",
      "position": 2,
      "name": "Centres",
      "item": "https://www.aquavelo.com/centres"
    },{
      "@type": "ListItem",
      "position": 3,
      "name": "<?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>"
    }]
  }
  </script>

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [{
      "@type": "Question",
      "name": "Dois-je savoir nager pour faire de l'aquavélo ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Non, il n'est pas nécessaire de savoir nager. Le niveau d'eau arrive à la taille et vous êtes installé sur un vélo stable."
      }
    },{
      "@type": "Question",
      "name": "Que faut-il apporter pour une séance d'aquavélo ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Prévoyez un maillot de bain, une serviette et optionnellement des chaussures d'eau. Nous mettons à disposition des casiers sécurisés."
      }
    },{
      "@type": "Question",
      "name": "L'aquavélo est-il efficace pour perdre du poids ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, l'aquavélo permet de brûler entre 400 et 600 calories par séance de 45 minutes. C'est très efficace pour la perte de poids."
      }
    }]
  }
  </script>

  <?php if (isset($row_center['id']) && $row_center['id'] == 253) : ?>
    <script>
      !function(f,b,e,v,n,t,s) {
        if(f.fbq)return;
        n=f.fbq=function(){
          n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments);
        };
        if(!f._fbq)f._fbq=n;
        n.push=n;
        n.loaded=!0;
        n.version='2.0';
        n.queue=[];
        t=b.createElement(e);t.async=!0;
        t.src=v;
        s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s);
      }(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '259009481449831');
      fbq('track', 'PageView');
    </script>
  <?php endif; ?>
  
  <style>
    /* ⭐ Styles améliorés pour la conversion */
    .promo-banner {
      background: linear-gradient(135deg, #00d4ff, #00a8cc);
      color: white;
      padding: 20px;
      text-align: center;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 5px 20px rgba(0, 168, 204, 0.3);
      transition: all 0.3s ease;
    }

    .promo-banner h2 {
      color: white;
      font-size: 2rem;
      margin: 0 0 10px 0;
    }

    .promo-banner p {
      font-size: 1.2rem;
      margin: 0;
      opacity: 0.95;
    }

    .form-container {
      background: white;
      padding: 30px;
      border-radius: 15px;
      border: 3px solid #00d4ff;
      box-shadow: 0 10px 30px rgba(0, 168, 204, 0.2);
      margin-bottom: 30px;
    }

    .form-container h2 {
      color: #00a8cc;
      text-align: center;
      margin-bottom: 25px;
    }

    .form-control {
      height: 45px;
      border-radius: 8px;
      border: 2px solid #e0e0e0;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #00d4ff;
      box-shadow: 0 0 0 0.2rem rgba(0, 212, 255, 0.25);
    }

    .btn-submit {
      background: linear-gradient(135deg, #00d4ff, #00a8cc);
      color: white;
      border: none;
      padding: 15px;
      font-size: 1.2rem;
      font-weight: 600;
      border-radius: 50px;
      width: 100%;
      margin-top: 20px;
      box-shadow: 0 5px 15px rgba(0, 168, 204, 0.3);
      transition: all 0.3s ease;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 168, 204, 0.5);
    }

    .benefits-list {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }

    .benefits-list li {
      padding: 12px 0;
      font-size: 1.05rem;
      border-bottom: 1px solid #f0f0f0;
    }

    .benefits-list li:before {
      content: "✓";
      color: #00d4ff;
      font-weight: bold;
      margin-right: 10px;
      font-size: 1.3rem;
    }

    .urgency-box {
      background: #fff3cd;
      border-left: 4px solid #ff9800;
      padding: 15px;
      border-radius: 8px;
      margin: 20px 0;
    }

    .urgency-box strong {
      color: #ff9800;
    }

    .trust-badges {
      display: flex;
      justify-content: space-around;
      margin: 30px 0;
      flex-wrap: wrap;
    }

    .trust-badge {
      text-align: center;
      padding: 15px;
      flex: 1;
      min-width: 150px;
    }

    .trust-badge i {
      font-size: 2.5rem;
      color: #00d4ff;
      margin-bottom: 10px;
    }

    /* ⭐⭐⭐ OPTION A : BOUTON CTA MOBILE ⭐⭐⭐ */
    
    /* Bouton CTA caché par défaut (desktop) */
    .mobile-cta-button {
      display: none;
    }

    /* MOBILE : Ordre + Bouton CTA */
    @media (max-width: 768px) {
      /* ⭐ Rendre le bandeau promo cliquable sur mobile */
      .promo-banner {
        cursor: pointer;
        user-select: none;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
      }

      .promo-banner:hover,
      .promo-banner:active {
        transform: scale(0.98);
        box-shadow: 0 3px 15px rgba(0, 168, 204, 0.4);
      }

      .promo-banner h2 {
        font-size: 1.5rem;
      }
      
      .form-container {
        padding: 20px 15px;
      }

      /* Container flex pour réorganiser */
      .mobile-reorder {
        display: flex;
        flex-direction: column;
      }
      
      /* 1. Planning EN PREMIER */
      .planning-section {
        order: 1;
        margin-bottom: 25px;
      }
      
      /* 2. Formulaire/CTA EN DEUXIÈME */
      .form-section {
        order: 2;
        margin-bottom: 25px;
      }
      
      /* 3. Autres contenus */
      .other-content {
        order: 3;
      }

      /* ⭐ Formulaire CACHÉ par défaut sur mobile */
      .form-container {
        display: none;
      }

      /* ⭐ Bouton CTA VISIBLE sur mobile */
      .mobile-cta-button {
        display: block;
        background: linear-gradient(135deg, #00d4ff, #00a8cc);
        color: white;
        padding: 25px 20px;
        text-align: center;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 168, 204, 0.4);
        font-size: 1.3rem;
        font-weight: 700;
        text-decoration: none;
        border: none;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .mobile-cta-button:active {
        transform: scale(0.98);
      }

      .mobile-cta-button .icon {
        font-size: 2rem;
        display: block;
        margin-bottom: 10px;
        animation: bounce 2s infinite;
      }

      .mobile-cta-button .subtitle {
        font-size: 0.9rem;
        margin-top: 10px;
        opacity: 0.95;
        font-weight: 400;
      }

      @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
      }

      /* ⭐ Quand le formulaire est ouvert */
      .form-section.form-opened .form-container {
        display: block;
        animation: slideDown 0.4s ease;
      }

      .form-section.form-opened .mobile-cta-button {
        display: none;
      }

      @keyframes slideDown {
        from {
          opacity: 0;
          transform: translateY(-30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      /* Urgence box et badges simplifiés sur mobile */
      .urgency-box {
        padding: 12px;
        font-size: 0.95rem;
      }

      .trust-badges {
        flex-direction: column;
        margin: 20px 0;
      }

      .trust-badge {
        flex: 100%;
        margin-bottom: 15px;
        padding: 10px;
      }

      .trust-badge i {
        font-size: 2rem;
      }
    }

    /* DESKTOP : Ordre normal */
    @media (min-width: 769px) {
      /* Bouton CTA caché sur desktop */
      .mobile-cta-button {
        display: none !important;
      }

      /* Formulaire toujours visible sur desktop */
      .form-container {
        display: block !important;
      }

      /* Ordre desktop : Formulaire d'abord */
      .form-section {
        order: 1;
      }
      
      .planning-section {
        order: 2;
      }
      
      .other-content {
        order: 3;
      }
    }
  </style>
</head>
<body>

<!-- Header -->
<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
    <ol class="breadcrumb pull-right">
      <li><a href="<?= BASE_PATH ?>">Accueil</a></li>
      <li><a href="<?= BASE_PATH ?>centres">Centres</a></li>
      <li class="active"><?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
    </ol>
  </div>
</header>

<!-- Bannière promo -->
<section class="content-area bg1">
  <div class="container">
    <?php if (isset($_GET['success_rdv']) && $_GET['success_rdv'] == 1) : ?>
      <div class="alert alert-success" style="text-align: center; border-radius: 15px; background: #d4edda; color: #155724; padding: 30px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <i class="fa fa-check-circle" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
        <h2 style="color: #155724; margin-top: 0;">Félicitations !</h2>
        <p style="font-size: 1.2rem;">Votre réservation a été enregistrée avec succès.<br>Un email de confirmation vient de vous être envoyé.</p>
      </div>
    <?php endif; ?>
    
    <a href="<?= BASE_PATH ?>?p=free" class="promo-banner" style="display: block; text-decoration: none; cursor: pointer;" aria-label="Réserver une séance découverte gratuite">
      <h2>🎁 Séance Découverte GRATUITE 45min</h2>
      <p>✓ Aquabiking + Aquagym • ✓ Sans engagement • ✓ Coaching personnalisé</p>
    </a>
  </div>
</section>

<!-- Photos du centre -->
<section class="content-area" style="padding-top: 0;">
  <div class="container">
    <div class="row mt-3">
      <div class="col-md-3 col-6 text-center">
        <img src="<?= BASE_PATH ?>cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg" 
             alt="Salle d'aquabiking centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> avec vélos aquatiques" 
             class="img-fluid img-same" 
             style="border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);"
             width="300" 
             height="200"
             loading="eager">
      </div>

      <div class="col-md-3 col-6 text-center">
        <?php if (isset($row_center['id']) && $row_center['id'] != 305) : ?>
          <img src="<?= BASE_PATH ?>cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/2.jpg" 
               alt="Espace aquagym centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
               class="img-fluid img-same" 
               style="border-radius: 10px;"
               width="300" 
               height="200"
               loading="lazy">
        <?php else : ?>
          <img src="<?= BASE_PATH ?>images/Cannes1.jpg" 
               alt="Intérieur centre Aquavélo Cannes" 
               class="img-fluid img-same" 
               style="border-radius: 10px;"
               width="300" 
               height="200"
               loading="lazy">
        <?php endif; ?>
      </div>

      <?php if (isset($row_center['id']) && !in_array($row_center['id'], [305, 347, 349])) : ?>
        <div class="col-md-3 col-6 text-center">
          <img src="<?= BASE_PATH ?>cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/3.jpg" 
               alt="Équipements vestiaires centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
               class="img-fluid img-same" 
               style="border-radius: 10px;"
               width="300" 
               height="200"
               loading="lazy">
        </div>
      <?php endif; ?>

      <?php 
      $promotions = [
          305 => "Cannes",
          253 => "Antibes",
          179 => "Nice",
          347 => "Mandelieu",
          349 => "Vallauris",
          343 => "Merignac"
      ];

      if (isset($row_center['id']) && array_key_exists($row_center['id'], $promotions)) : ?>
        <div class="col-md-3 col-6 text-center">
          <a href="https://www.aquavelo.com/seance-decouverte/<?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>" 
             title="Offre séance découverte gratuite Aquavélo <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>">
            <img src="<?= BASE_PATH ?>images/promoJan24.webp" 
                 alt="Promotion séance découverte gratuite 45min Aquavélo <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>" 
                 class="img-fluid img-same" 
                 style="border-radius: 10px; border: 3px solid #ff9800;"
                 width="300" 
                 height="200"
                 loading="lazy">
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Contenu principal -->
<section class="content-area bg1">
  <div class="container">
    <div class="row">
      
      <!-- Colonne gauche : Planning + CTA/Formulaire + Contenu -->
      <div class="col-md-6 mobile-reorder">
        
        <!-- ⭐ 1. PLANNING (EN PREMIER sur mobile, EN DEUXIÈME sur desktop) -->
       <!-- Dans la section planning, remplacer la partie existante par : -->

        <!-- ⭐ 1. PLANNING (EN PREMIER sur mobile, EN DEUXIÈME sur desktop) -->
        <a id="planning-cours"></a>
        <div class="planning-section">
          <?php if (isset($row_center['id'])) : ?>
            
            <?php 
            $center_id = (int)$row_center['id'];
            $planning_image = "";
            
            // On vérifie si une image de planning spécifique existe pour ce centre
            if (is_file(__DIR__ . "/images/planning_{$center_id}.png")) {
                $planning_image = BASE_PATH . "images/planning_{$center_id}.png";
            } elseif (is_file(__DIR__ . "/images/planning_{$center_id}.jpg")) {
                $planning_image = BASE_PATH . "images/planning_{$center_id}.jpg";
            } elseif ($center_id == 253) { // Secours pour Antibes si le fichier n'est pas encore détecté
                $planning_image = BASE_PATH . "images/planning_253.png";
            } elseif (in_array($center_id, [305, 347, 349])) {
                $planning_image = BASE_PATH . "images/PLANNINGCANNES0125.jpg";
            } elseif ($center_id == 179) {
                $planning_image = BASE_PATH . "images/planningNice.jpg";
            } elseif ($center_id == 271) {
                $planning_image = BASE_PATH . "images/planningToulouse.jpg";
            }

            if (!empty($planning_image)) : ?>
              <div class="text-center" style="margin-bottom: 30px;">
                <h3 style="color: #00a8cc; margin-bottom: 20px;">📅 Planning des Cours</h3>
                <img src="<?= $planning_image ?>" 
                     alt="Planning hebdomadaire cours aquabiking aquagym <?= htmlspecialchars($row_center['city']) ?>" 
                     class="img-fluid" 
                     style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);"
                     loading="lazy">
              </div>
            <?php endif; ?>

          <?php endif; ?>
        </div>



        
        <!-- ⭐ 2. BOUTON CTA / FORMULAIRE (EN DEUXIÈME sur mobile, EN PREMIER sur desktop) -->
        <div class="form-section">
          
          <!-- ⭐⭐⭐ BOUTON CTA MOBILE (visible uniquement sur mobile) -->
          <button type="button" class="mobile-cta-button" onclick="openFormMobile()" aria-label="Ouvrir le formulaire de réservation">
            <div class="icon">📅</div>
            <div>Réserver Ma Séance Gratuite</div>
            <div class="subtitle">👆 Cliquez pour remplir le formulaire</div>
          </button>

          <!-- Formulaire de réservation (visible desktop, caché mobile jusqu'au clic) -->
          <div class="form-container">
            <h2><i class="fa fa-calendar-check-o"></i> Réservez Votre Séance Gratuite</h2>
          
            <form role="form" id="contactForm" class="contact-form-planning" method="POST" action="<?= BASE_PATH ?>index.php?p=free" novalidate>
              <div class="form-group">
                <label for="center"><i class="fa fa-map-marker"></i> Centre <span style="color: red;">*</span></label>
                <select class="form-control" id="center" name="center">
                  <?php if (isset($centers_list_d)) : ?>
                    <option value="">-- Sélectionnez un centre --</option>
                    <?php foreach ($centers_list_d as $free_d) : ?>
                      <option <?php if (isset($_GET['city']) && strtolower($_GET['city']) == strtolower($free_d['city'])) echo 'selected'; ?> value="<?= htmlspecialchars($free_d['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($free_d['city'], ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
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
                       value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez entrer votre nom</span>
              </div>
              
              <div class="form-group">
                <label for="email"><i class="fa fa-envelope"></i> Email <span style="color: red;">*</span></label>
                <input type="email" 
                       class="form-control" 
                       id="email" 
                       name="email" 
                       placeholder="exemple@email.com" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez entrer un email valide</span>
              </div>
                
              <div class="form-group">
                <label for="phone"><i class="fa fa-phone"></i> Téléphone <span style="color: red;">*</span></label>
                <input type="tel" 
                       class="form-control" 
                       id="phone" 
                       name="phone" 
                       placeholder="06 12 34 56 78" 
                       value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez entrer votre téléphone</span>
              </div>
            
              <input type="hidden" name="reason" id="reason">
              <input type="hidden" name="segment" id="segment">
              
              <button type="submit" id="submitBtnPage" class="btn btn-submit" aria-label="Recevoir mon bon par email">
                <i class="fa fa-check-circle"></i> <?= in_array($row_center['id'], [305, 347, 349, 343]) ? "RÉSERVER MA SÉANCE OFFERTE" : "Recevoir mon Bon par Email" ?>
              </button>

              <p style="text-align: center; margin-top: 15px; color: #666; font-size: 0.9rem;">
                <i class="fa fa-lock"></i> Vos données sont sécurisées • Sans engagement
              </p>
            </form>
          </div>

          <!-- Urgence / Rareté -->
          <div class="urgency-box">
            <i class="fa fa-clock-o"></i> <strong>Places limitées !</strong> 
            Profitez de cette offre découverte gratuite tant qu'elle est disponible.
          </div>

          <!-- Badges de confiance -->
          <div class="trust-badges">
            <div class="trust-badge">
              <i class="fa fa-shield"></i>
              <p><strong>100% Sécurisé</strong><br>Données protégées</p>
            </div>
            <div class="trust-badge">
              <i class="fa fa-gift"></i>
              <p><strong>Sans Engagement</strong><br>Aucun frais caché</p>
            </div>
            <div class="trust-badge">
              <i class="fa fa-star"></i>
              <p><strong>Satisfaction</strong><br>98% de clients satisfaits</p>
            </div>
          </div>
        </div>
        
        <!-- ⭐ 3. AUTRES CONTENUS -->
        <div class="other-content">
          <!-- Section Pourquoi choisir -->
          <div style="margin-top: 40px;">
            <h3 style="color: #00a8cc;"><i class="fa fa-star"></i> Pourquoi Choisir l'Aquavélo à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> ?</h3>
            
            <ul class="benefits-list">
              <li><strong>Brûlez jusqu'à 600 calories</strong> par séance de 45 minutes</li>
              <li><strong>Réduisez la cellulite</strong> grâce à l'effet drainant de l'eau</li>
              <li><strong>Tonifiez vos muscles</strong> sans impact sur les articulations</li>
              <li><strong>Améliorez votre circulation sanguine</strong> et votre retour veineux</li>
              <li><strong>Sculptez votre silhouette</strong> : cuisses, fessiers, abdos</li>
              <li><strong>Détendez-vous</strong> dans une eau chauffée entre 28 et 32°C</li>
            </ul>

            <div style="background: #f0f8ff; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #00d4ff;">
              <h4 style="color: #00a8cc; margin-top: 0;"><i class="fa fa-users"></i> Pour Qui ?</h4>
              <p>L'aquabiking convient à <strong>tous les âges et tous les niveaux</strong> : débutants, sportifs confirmés, personnes en surpoids, seniors, femmes enceintes (avec accord médical). <strong>Pas besoin de savoir nager</strong> - l'eau arrive à la taille et vous êtes installé sur un vélo stable.</p>
            </div>
          </div>

          <!-- FAQ -->
          <div style="margin-top: 40px; background: #f8f9fa; padding: 25px; border-radius: 10px;">
            <h3 style="color: #00a8cc; margin-top: 0;"><i class="fa fa-question-circle"></i> Questions Fréquentes</h3>
            
            <h4 style="color: #00a8cc; margin-top: 20px; font-size: 1.1rem;">Dois-je savoir nager ?</h4>
            <p><strong>Non</strong>, il n'est pas nécessaire de savoir nager. Le niveau d'eau arrive à la taille et vous êtes installé sur un vélo stable.</p>

            <h4 style="color: #00a8cc; margin-top: 20px; font-size: 1.1rem;">Que faut-il apporter ?</h4>
            <p>Un <strong>maillot de bain</strong>, une <strong>serviette</strong> et optionnellement des <strong>chaussures d'eau</strong>. Casiers sécurisés fournis.</p>

            <h4 style="color: #00a8cc; margin-top: 20px; font-size: 1.1rem;">Combien de séances par semaine ?</h4>
            <p>Pour des résultats optimaux : <strong>2 à 3 séances par semaine</strong>. Premiers résultats visibles après 8-10 séances.</p>

            <h4 style="color: #00a8cc; margin-top: 20px; font-size: 1.1rem;">L'aquavélo fait-il maigrir ?</h4>
            <p><strong>Oui</strong>, brûlez <strong>400 à 600 calories par séance</strong>. Très efficace pour la perte de poids associé à une alimentation équilibrée.</p>
          </div>

          <!-- ⭐ Témoignages Google Business - Centre Cannes -->
          <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349, 343])) : ?>
          <div style="margin-top: 40px;">
            <h3 style="color: #00a8cc; margin-bottom: 25px;">
              <i class="fa fa-google"></i> Avis Google 
              <span style="background: #ffc107; color: #333; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; margin-left: 10px;">
                ⭐ 4.8/5
              </span>
            </h3>
            
            <!-- Témoignage 1 -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); margin-bottom: 15px; border-left: 4px solid #00d4ff;">
              <div style="display: flex; align-items: center; margin-bottom: 12px;">
                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #00d4ff, #00a8cc); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">S</div>
                <div style="margin-left: 12px;">
                  <strong style="color: #333;">Sophie M.</strong>
                  <div style="color: #ffc107; font-size: 0.9rem;">⭐⭐⭐⭐⭐</div>
                </div>
              </div>
              <p style="margin: 0; color: #555; font-style: italic; line-height: 1.6;">"Super centre, personnel très accueillant et professionnel. J'ai perdu 8 kg en 3 mois grâce aux séances d'aquabike. L'ambiance est top et les coachs sont motivants. Je recommande à 100% !"</p>
            </div>

            <!-- Témoignage 2 -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); margin-bottom: 15px; border-left: 4px solid #00d4ff;">
              <div style="display: flex; align-items: center; margin-bottom: 12px;">
                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #ff6b35, #f7931e); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">M</div>
                <div style="margin-left: 12px;">
                  <strong style="color: #333;">Marie-Claire D.</strong>
                  <div style="color: #ffc107; font-size: 0.9rem;">⭐⭐⭐⭐⭐</div>
                </div>
              </div>
              <p style="margin: 0; color: #555; font-style: italic; line-height: 1.6;">"Excellente expérience ! Les cours sont variés et adaptés à tous les niveaux. Ma cellulite a nettement diminué après 2 mois. Le cadre est agréable et l'eau est toujours à bonne température."</p>
            </div>

            <!-- Témoignage 3 -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); margin-bottom: 15px; border-left: 4px solid #00d4ff;">
              <div style="display: flex; align-items: center; margin-bottom: 12px;">
                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #9c27b0, #673ab7); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">L</div>
                <div style="margin-left: 12px;">
                  <strong style="color: #333;">Laurence B.</strong>
                  <div style="color: #ffc107; font-size: 0.9rem;">⭐⭐⭐⭐⭐</div>
                </div>
              </div>
              <p style="margin: 0; color: #555; font-style: italic; line-height: 1.6;">"Je viens depuis 1 an et je ne m'en lasse pas ! Les séances passent vite grâce à la bonne humeur des coachs. Parfait pour se remettre en forme sans se faire mal aux articulations. Bravo à toute l'équipe !"</p>
            </div>

            <!-- Lien vers Google -->
            <div style="text-align: center; margin-top: 20px;">
              <a href="https://www.google.com/search?q=aquavelo+cannes+avis" target="_blank" rel="noopener" style="color: #00a8cc; text-decoration: none; font-weight: 600;">
                <i class="fa fa-external-link"></i> Voir tous les avis sur Google
              </a>
            </div>
          </div>
          <?php endif; ?>

        </div>

      </div>

      <!-- Colonne droite : Infos centre + Map -->
      <div class="col-md-6">
        
        <!-- Informations du centre -->
        <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
          <h3 style="color: #00a8cc; margin-top: 0;"><i class="fa fa-info-circle"></i> Informations du Centre</h3>
          
          <dl style="margin-top: 25px;">
            <dt style="color: #00a8cc; font-size: 1.1rem; margin-bottom: 8px;"><i class="fa fa-map-marker"></i> Adresse</dt>
            <dd style="margin-bottom: 25px; padding-left: 25px;"><?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
            
            <dt style="color: #00a8cc; font-size: 1.1rem; margin-bottom: 8px;"><i class="fa fa-phone"></i> Téléphone</dt>
            <dd style="margin-bottom: 25px; padding-left: 25px;">
              <a href="tel:<?= preg_replace('/[^0-9+]/', '', $row_center['phone'] ?? ''); ?>" 
                 style="color: #00a8cc; font-weight: 600; font-size: 1.2rem; text-decoration: none;">
                <?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
              </a>
            </dd>
            
            <dt style="color: #00a8cc; font-size: 1.1rem; margin-bottom: 8px;"><i class="fa fa-clock-o"></i> Horaires</dt>
            <dd style="margin-bottom: 25px; padding-left: 25px;"><?= htmlspecialchars($row_center['openhours'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
          </dl>

          <!-- Boutons d'action -->
          <div style="margin-top: 30px;">
            <a href="<?php 
                if (isset($row_center['id']) && $row_center['id'] == 179) {
                    echo 'https://member.resamania.com/aquavelonice';
                } else {
                    echo 'https://member.resamania.com/aquavelo/';
                }
            ?>" 
            title="Réservation en ligne pour adhérents" 
            target="_blank"
            rel="noopener"
            class="btn btn-default btn-block" 
            style="margin-bottom: 10px; padding: 12px; font-weight: 600;">
              <i class="fa fa-calendar"></i> Réserver en Ligne (Adhérents)
            </a>

            <a href="https://www.facebook.com/<?= htmlspecialchars($row_center['facebook'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
               title="Page Facebook Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
               target="_blank"
               rel="noopener nofollow"
               class="btn btn-default btn-block" 
               style="margin-bottom: 10px; padding: 12px; font-weight: 600;">
              <i class="fa fa-facebook"></i> Suivre sur Facebook
            </a>

            <button type="button" class="btn btn-default btn-block" 
               onclick="ouvre_popup('<?= BASE_PATH ?>nouveauResultat.html'); return false;" 
               style="margin-bottom: 10px; padding: 12px; font-weight: 600;">
              <i class="fa fa-image"></i> Voir les Résultats Minceur
            </button>

            <a href="<?= BASE_PATH ?>index.php?p=conseilminceur" class="btn btn-default btn-block" 
               style="margin-bottom: 10px; padding: 12px; font-weight: 600;">
              <i class="fa fa-cutlery"></i> Menu Perte de Poids
            </a>

            <?php if (in_array(strtolower($city ?? ''), ['cannes', 'mandelieu-la-napoule', 'mandelieu', 'vallauris', 'nice'])) : ?>
            <a href="<?php 
                // Pour Nice (ID 179), rediriger vers sudminceur.fr
                if (isset($row_center['id']) && $row_center['id'] == 179) {
                    echo 'https://sudminceur.fr/';
                } else {
                    echo BASE_PATH . 'index.php?p=cryolipolyse';
                }
            ?>" 
            class="btn btn-default btn-block" 
               style="margin-bottom: 10px; padding: 12px; font-weight: 600;">
              <i class="fa fa-snowflake-o"></i> Minceur Cryolipolyse
            </a>
            <?php endif; ?>
          </div>

          <?php if (!empty($row_center['description'])) : ?>
          <div style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #f0f0f0;">
            <h4 style="color: #00a8cc;"><i class="fa fa-home"></i> À Propos du Centre</h4>
            <p style="line-height: 1.8;"><?= $row_center['description'] ?></p>
          </div>
          <?php endif; ?>
        </div>
        
        <!-- Carte Google Maps -->
        <div style="margin-top: 30px;">
          <h3 style="color: #00a8cc; margin-bottom: 20px;"><i class="fa fa-map"></i> Comment Nous Trouver</h3>
          
          <?php 
          $map_address = urlencode($row_center['address'] ?? '');
          $map_city = urlencode($city ?? '');
          
          if (!empty($row_center['latitude']) && !empty($row_center['longitude'])) {
              $map_query = $row_center['latitude'] . ',' . $row_center['longitude'];
          } else {
              $map_query = $map_address . ',+' . $map_city . ',+France';
          }
          ?>
          
          <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <iframe 
              src="https://www.google.com/maps?q=<?= $map_query ?>&hl=fr&z=15&output=embed"
              style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade"
              title="Localisation du centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </iframe>
          </div>
          
          <div style="margin-top: 20px; padding: 20px; background: #f8f9fa; border-left: 4px solid #00d4ff; border-radius: 8px;">
            <h4 style="margin-top: 0; color: #00a8cc;"><i class="fa fa-car"></i> Accès et Parking</h4>
            <p><strong>En voiture :</strong> Parking gratuit disponible</p>
            <p><strong>En transports :</strong> Bus et tramway à proximité</p>
            
            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $map_query ?>" 
               target="_blank" 
               rel="noopener"
               class="btn btn-default"
               style="margin-top: 15px; padding: 12px 20px; background: #00a8cc; color: white; border: none; border-radius: 8px;">
              <i class="fa fa-location-arrow"></i> Calculer Mon Itinéraire
            </a>
          </div>
        </div>

        <!-- Autres centres -->
        <div style="margin-top: 40px; padding: 25px; background: #f0f8ff; border-radius: 10px;">
          <h3 style="color: #00a8cc; margin-top: 0;"><i class="fa fa-map-marker"></i> Nos Autres Centres</h3>
          <p>Retrouvez l'aquabiking dans toute la France :</p>
          <ul style="list-style: none; padding: 0; columns: 2; -webkit-columns: 2; -moz-columns: 2;">
            <?php if (isset($centers_list_d)) : ?>
              <?php foreach ($centers_list_d as $center) : ?>
                <?php if ($center['city'] != $city) : ?>
                  <li style="margin-bottom: 8px; break-inside: avoid;">
                    <a href="<?= BASE_PATH ?>centres/<?= htmlspecialchars(strtolower($center['city']), ENT_QUOTES, 'UTF-8'); ?>"
                       style="color: #00a8cc; text-decoration: none;"
                       title="Centre Aquavélo <?= htmlspecialchars($center['city'], ENT_QUOTES, 'UTF-8'); ?>">
                      <i class="fa fa-chevron-right" style="font-size: 0.8rem;"></i> <?= htmlspecialchars($center['city'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- Scripts -->
<script>
  function ouvre_popup(url) {
    const width = Math.max(window.innerWidth / 3, 300);
    const height = Math.max(window.innerHeight / 3, 200);
    const left = (window.innerWidth - width) / 2;
    const top = (window.innerHeight - height) / 2;
    window.open(
      url, 
      'popup', 
      `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
    );
    return false;
  }

  /* ⭐⭐⭐ FONCTION POUR OUVRIR LE FORMULAIRE SUR MOBILE ⭐⭐⭐ */
  function openFormMobile() {
    if (window.innerWidth <= 768) {
      const formSection = document.querySelector('.form-section');
      if (formSection) {
        // Ajouter la classe pour afficher le formulaire
        formSection.classList.add('form-opened');
        
        // Scroll vers le formulaire après un court délai
        setTimeout(function() {
          formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
          
          // Focus sur le premier champ après le scroll
          setTimeout(function() {
            const firstInput = document.getElementById('center');
            if (firstInput) {
              firstInput.focus();
            }
          }, 500);
        }, 100);
      }
    }
  }

  // Réinitialiser au redimensionnement de fenêtre
  window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
      const formSection = document.querySelector('.form-section');
      if (formSection) {
        formSection.classList.remove('form-opened');
      }
    }
  });

  // ⭐ Support clavier pour le bandeau promo (accessibilité)
  document.addEventListener('DOMContentLoaded', function() {
    const promoBanner = document.querySelector('.promo-banner');
    if (promoBanner) {
      promoBanner.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openFormMobile();
        }
      });
    }
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('contactForm');
    
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Toujours empêcher la soumission par défaut
        
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
        
        // Désactiver le bouton et soumettre
        var btn = document.getElementById('submitBtnPage');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Envoi...';
        
        // Track conversion
        var cityValue = "<?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>";
        if (typeof gtag !== 'undefined') {
            gtag('event', 'form_submission', {event_category: 'conversion', event_label: 'free_trial_center', value: cityValue});
        }
        
        form.submit();
        
        return false;
    });

    // Effacer les erreurs lors de la saisie
    var inputs = form.querySelectorAll('input, select');
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




