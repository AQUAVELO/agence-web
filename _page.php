<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Meta Tags -->
  <title>Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Découverte Gratuite | Aquabiking + Aquagym</title>
  <meta name="description" content="Centre Aquavélo à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>. Essayez gratuitement l'aquabiking et l'aquagym pendant 45min. Affinez votre silhouette et perdez du poids efficacement. Réservez maintenant !">
  <meta name="keywords" content="aquavélo, aquabiking, aquagym, <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>, sport aquatique, perte de poids, raffermissement, fitness aquatique">
  <meta name="robots" content="index, follow">
  <meta name="author" content="Aquavélo">
  
  <!-- Canonical URL -->
  <link rel="canonical" href="https://www.aquavelo.com/centres/<?= htmlspecialchars(strtolower($city ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.aquavelo.com/centres/<?= htmlspecialchars(strtolower($city ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:title" content="Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Découverte Gratuite">
  <meta property="og:description" content="Essayez gratuitement l'aquabiking et l'aquagym dans notre centre de <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>. 45 minutes pour découvrir tous les bienfaits de l'aquavélo.">
  <meta property="og:image" content="https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg">
  <meta property="og:locale" content="fr_FR">
  
  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Découverte Gratuite">
  <meta name="twitter:description" content="Essayez gratuitement l'aquabiking et l'aquagym pendant 45min dans notre centre de <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>.">
  <meta name="twitter:image" content="https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg">
  
  <!-- Preconnect pour améliorer la performance -->
  <link rel="preconnect" href="https://connect.facebook.net">
  <link rel="preconnect" href="https://www.googletagmanager.com">
  
  <!-- CSS (à optimiser et minifier) -->
  <link rel="stylesheet" href="/css/styles.min.css">
  
  <!-- Schema.org structured data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SportsActivityLocation",
    "name": "Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>",
    "description": "Centre d'aquabiking et aquagym proposant des séances encadrées pour affiner et raffermir votre silhouette",
    "image": [
      "https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg",
      "https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/2.jpg"
    ],
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
      "addressLocality": "<?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>",
      "addressRegion": "<?= htmlspecialchars($row_center['region'] ?? 'Provence-Alpes-Côte d\'Azur', ENT_QUOTES, 'UTF-8'); ?>",
      "addressCountry": "FR"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": "<?= htmlspecialchars($row_center['latitude'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
      "longitude": "<?= htmlspecialchars($row_center['longitude'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
    },
    "telephone": "<?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
    "openingHoursSpecification": {
      "@type": "OpeningHoursSpecification",
      "description": "<?= htmlspecialchars($row_center['openhours'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
    },
    "priceRange": "$$",
    "sameAs": [
      "https://www.facebook.com/<?= htmlspecialchars($row_center['facebook'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
    ],
    "hasOfferCatalog": {
      "@type": "OfferCatalog",
      "name": "Services Aquavélo",
      "itemListElement": [
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Séance découverte gratuite",
            "description": "Essayez l'aquabiking et l'aquagym gratuitement pendant 45 minutes"
          },
          "price": "0",
          "priceCurrency": "EUR"
        }
      ]
    }
  }
  </script>
  
  <!-- LocalBusiness Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>",
    "url": "https://www.aquavelo.com/centres/<?= htmlspecialchars(strtolower($city ?? ''), ENT_QUOTES, 'UTF-8'); ?>",
    "logo": "https://www.aquavelo.com/images/logo.png",
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "<?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
      "contactType": "Customer Service",
      "areaServed": "FR",
      "availableLanguage": "French"
    }
  }
  </script>

  <?php if (isset($row_center['id']) && $row_center['id'] == 253) : ?>
    <!-- Facebook Pixel Code -->
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
    <noscript>
      <img height="1" width="1" style="display:none" 
           src="https://www.facebook.com/tr?id=259009481449831&ev=PageView&noscript=1" 
           alt="Facebook Pixel">
    </noscript>
    <!-- End Facebook Pixel Code -->
  <?php endif; ?>

  <?php if (isset($row_center['id']) && in_array($row_center['id'], [343])) : ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17714430375"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-17714430375');
    </script>
  <?php endif; ?>
</head>

<body>
  <!-- Header optimisé avec vraie structure H1 -->
  <header class="main-header clearfix">
    <div class="container">
      <h1 class="page-title">Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> : Aquabiking + Aquagym</h1>
      <p class="subtitle">Affinez et raffermissez votre silhouette efficacement avec l'aquavélo</p>
      
      <!-- Fil d'Ariane optimisé avec Schema.org -->
      <nav aria-label="Fil d'Ariane">
        <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
          <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="/">
              <span itemprop="name">Accueil</span>
            </a>
            <meta itemprop="position" content="1" />
          </li>
          <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="/centres">
              <span itemprop="name">Centres</span>
            </a>
            <meta itemprop="position" content="2" />
          </li>
          <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="active">
            <span itemprop="name"><?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            <meta itemprop="position" content="3" />
          </li>
        </ol>
      </nav>
    </div>
  </header>

  <!-- Section images avec lazy loading et alt optimisés -->
  <section class="content-area bg1">
    <div class="container">
      <h2 class="sr-only">Photos du centre Aquavélo de <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
      <div class="row mt-3">
        <!-- Image principale -->
        <div class="col-md-3 col-6 text-center">
          <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg" 
               alt="Salle d'aquabiking du centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> avec vélos aquatiques" 
               class="img-fluid img-same" 
               width="300" 
               height="200"
               loading="eager">
        </div>

        <!-- Image secondaire -->
        <div class="col-md-3 col-6 text-center">
          <?php if (isset($row_center['id']) && $row_center['id'] != 305) : ?>
            <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/2.jpg" 
                 alt="Espace aquagym du centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                 class="img-fluid img-same" 
                 width="300" 
                 height="200"
                 loading="lazy">
          <?php else : ?>
            <img src="/images/Cannes1.jpg" 
                 alt="Intérieur du centre Aquavélo de Cannes" 
                 class="img-fluid img-same" 
                 width="300" 
                 height="200"
                 loading="lazy">
          <?php endif; ?>
        </div>

        <!-- Image supplémentaire -->
        <?php if (isset($row_center['id']) && !in_array($row_center['id'], [305, 347, 349])) : ?>
          <div class="col-md-3 col-6 text-center">
            <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/3.jpg" 
                 alt="Vestiaires et équipements du centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                 class="img-fluid img-same" 
                 width="300" 
                 height="200"
                 loading="lazy">
          </div>
        <?php endif; ?>

        <!-- Image promotionnelle -->
        <?php 
        $promotions = [
            305 => "Cannes",
            253 => "Antibes",
            347 => "Nice",
            349 => "Vallauris"
        ];

        if (isset($row_center['id']) && array_key_exists($row_center['id'], $promotions)) : ?>
          <div class="col-md-3 col-6 text-center">
            <a href="https://www.aquavelo.com/seance-decouverte/<?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>" 
               title="Profitez de notre offre spéciale à <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>"
               aria-label="Promotion spéciale centre <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>">
              <img src="/images/promoJan24.webp" 
                   alt="Promotion séance découverte gratuite Aquavélo <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>" 
                   class="img-fluid img-same" 
                   width="300" 
                   height="200"
                   loading="lazy">
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Contenu principal enrichi pour le SEO -->
  <main class="container">
    <div class="row">
      <!-- Colonne formulaire -->
      <div class="col-md-6">
        <section>
          <h2>Essayez une séance découverte gratuite de 45 minutes</h2>
          
          <p>Découvrez les bienfaits de l'aquavélo dans notre centre de <strong><?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>. Cette séance d'essai gratuite vous permettra de tester nos équipements et de rencontrer nos coachs professionnels.</p>

          <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
            <p>Inscrivez-vous facilement sur notre <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank" rel="noopener" class="text-primary"><strong>calendrier en ligne</strong></a> ou remplissez le formulaire ci-dessous.</p>
          <?php endif; ?>

          <?php if (isset($row_center['id']) && in_array($row_center['id'], [343])) : ?>
            <p>Inscrivez-vous facilement sur notre <a href="https://aquavelomerignac33.simplybook.it/v2/" target="_blank" rel="noopener" class="text-primary"><strong>calendrier en ligne</strong></a> ou remplissez le formulaire ci-dessous.</p>
          <?php endif; ?>
        
          <!-- Formulaire de contact -->
          <form role="form" class="contact-form" method="POST" action="_page.php" aria-label="Formulaire de réservation séance découverte">
            <div class="form-group">
              <label for="center">Dans quel centre souhaitez-vous effectuer votre séance ?<span class="required" aria-label="Champ obligatoire">*</span></label>
              <select class="form-control" id="center" name="center" required aria-required="true">
                <?php if (isset($centers_list_d)) : ?>
                  <?php foreach ($centers_list_d as $free_d) : ?>
                    <option <?php if (isset($_GET['city']) && $_GET['city'] == $free_d['city']) echo 'selected'; ?> 
                            value="<?= htmlspecialchars($free_d['id'], ENT_QUOTES, 'UTF-8'); ?>">
                      <?= htmlspecialchars($free_d['city'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="nom">Nom et prénom<span class="required" aria-label="Champ obligatoire">*</span></label>
              <input type="text" 
                     class="form-control" 
                     id="nom" 
                     name="nom" 
                     placeholder="Nom et prénom" 
                     value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                     required
                     aria-required="true">
            </div>

            <div class="form-group">
              <label for="email">Email<span class="required" aria-label="Champ obligatoire">*</span></label>
              <input type="email" 
                     class="form-control" 
                     id="email" 
                     name="email" 
                     placeholder="votre.email@exemple.com" 
                     value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                     required
                     aria-required="true">
            </div>
            
            <div class="form-group">
              <label for="phone">Téléphone<span class="required" aria-label="Champ obligatoire">*</span></label>
              <input type="tel" 
                     class="form-control" 
                     id="phone" 
                     name="phone" 
                     placeholder="06 12 34 56 78" 
                     value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                     pattern="[0-9]{10}"
                     required
                     aria-required="true">
            </div>

            <input type="hidden" name="reason" id="reason" value="Séance découverte">
            <input type="hidden" name="segment" id="segment" value="Centre <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            
            <button type="submit" class="btn btn-primary btn-lg" aria-label="Recevoir mon bon de séance découverte par email">
              Recevoir mon bon par email
            </button>
          </form>

          <!-- Planning selon le centre -->
          <?php if (isset($row_center['id'])) : ?>
            <?php if ($row_center['id'] == 253) : ?>
              <div class="text-center mt-4">
                <h3>Planning des cours - Antibes</h3>
                <img src="/images/planningAntibes.jpg" 
                     alt="Planning hebdomadaire des cours d'aquabiking et aquagym à Antibes" 
                     class="img-fluid" 
                     style="max-width: 100%; height: auto;"
                     loading="lazy">
              </div>
            <?php elseif (in_array($row_center['id'], [305, 347, 349])) : ?>
              <div class="text-center mt-4">
                <h3>Planning des cours - Cannes</h3>
                <img src="/images/PLANNINGCANNES0125.jpg" 
                     alt="Planning hebdomadaire des cours d'aquabiking et aquagym à Cannes" 
                     class="img-fluid" 
                     style="max-width: 100%; height: auto;"
                     loading="lazy">
              </div>
            <?php elseif ($row_center['id'] == 179) : ?>
              <div class="text-center mt-4">
                <h3>Planning des cours - Nice</h3>
                <img src="/images/planningNice.jpg" 
                     alt="Planning hebdomadaire des cours d'aquabiking et aquagym à Nice" 
                     class="img-fluid" 
                     style="max-width: 100%; height: auto;"
                     loading="lazy">
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </section>
      </div>

      <!-- Colonne informations -->
      <div class="col-md-6">
        <section>
          <h2>Informations pratiques</h2>
          
          <dl style="margin-top:30px;">
            <dt>Adresse</dt>
            <dd itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
              <span itemprop="streetAddress"><?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            </dd>

            <dt>Téléphone</dt>
            <dd>
              <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $row_center['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" 
                 itemprop="telephone">
                <strong><?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
              </a>
            </dd>

            <dt>Horaires d'ouverture</dt>
            <dd itemprop="openingHours"><?= htmlspecialchars($row_center['openhours'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>

            <dt>Agenda pour les adhérents</dt>
            <dd>
              <a href="<?php 
                  if (isset($row_center['id']) && $row_center['id'] == 179) {
                      echo 'https://member.resamania.com/aquavelonice';
                  } else {
                      echo 'https://member.resamania.com/aquavelo/';
                  }
              ?>" 
              title="Réservation en ligne pour les adhérents" 
              aria-label="Accéder à l'espace de réservation en ligne"
              target="_blank" 
              rel="noopener"
              class="btn btn-default">Réserver en ligne</a>
            </dd>

            <dt>Suivez-nous sur Facebook</dt>
            <dd>
              <a href="https://www.facebook.com/<?= htmlspecialchars($row_center['facebook'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                 title="Page Facebook Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                 target="_blank" 
                 rel="noopener nofollow"
                 class="btn btn-default" 
                 aria-label="Visitez notre page Facebook">
                <i class="fab fa-facebook" aria-hidden="true"></i> Facebook
              </a>
            </dd>

            <dt>Résultats minceur de nos clients</dt>
            <dd>
              <button type="button" class="btn btn-default" onclick="ouvre_popup('/nouveauResultat.html'); return false;" 
                 title="Découvrir les résultats minceur de nos clients" 
                 aria-label="Voir les témoignages et résultats minceur">
                Voir les résultats
              </button>
            </dd>

            <dt>Calculateur de calories</dt>
            <dd>
              <button type="button" class="btn btn-default" onclick="ouvre_popup('/resultatMinceur.php'); return false;" 
                 title="Calculateur de calories avec conseils personnalisés" 
                 aria-label="Accéder au calculateur de calories">
                Calculateur & conseils
              </button>
            </dd>

            <dt>Menu perte de poids</dt>
            <dd>
              <a href="https://www.aquavelo.com/conseilminceur" 
                 class="btn btn-default" 
                 title="Découvrez nos menus pour perdre du poids" 
                 aria-label="Accéder aux menus perte de poids">
                Menus minceur
              </a>
            </dd>

            <dt>À propos du centre</dt>
            <dd>
              <?= $row_center['description'] ?? '' ?>
            </dd>
          </dl>
        </section>

        <!-- Section SEO : Contenu enrichi -->
        <section class="mt-5">
          <h2>Pourquoi choisir l'aquavélo à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> ?</h2>
          
          <p>L'<strong>aquabiking</strong>, également appelé aquavélo ou aquacycling, est une activité sportive aquatique qui combine les bienfaits du vélo et de l'aquagym. Dans l'eau, vos mouvements sont plus doux pour vos articulations tout en étant plus efficaces grâce à la résistance naturelle de l'eau.</p>

          <h3>Les bienfaits de l'aquavélo</h3>
          <p>Notre centre d'<strong>aquavélo à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></strong> vous propose des séances encadrées par des coachs diplômés. L'aquabiking permet de :</p>
          
          <ul>
            <li><strong>Affiner votre silhouette</strong> : perdez des centimètres de tour de taille, de cuisses et de hanches</li>
            <li><strong>Raffermir votre corps</strong> : tonifiez vos muscles sans risque de blessure</li>
            <li><strong>Réduire la cellulite</strong> : l'effet drainant de l'eau améliore visiblement l'aspect de votre peau</li>
            <li><strong>Améliorer votre circulation sanguine</strong> : l'hydrothérapie favorise le retour veineux</li>
            <li><strong>Brûler des calories</strong> : jusqu'à 500 calories par séance de 45 minutes</li>
            <li><strong>Vous détendre</strong> : l'eau a des vertus relaxantes et anti-stress</li>
          </ul>

          <h3>Pour qui ?</h3>
          <p>L'aquavélo convient à tous : débutants, sportifs confirmés, personnes en surpoids, femmes enceintes (avec accord médical), seniors. Nos bassins sont chauffés entre 28 et 32°C pour votre confort.</p>

          <h3>Nos équipements à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
          <p>Notre centre dispose de vélos aquatiques dernière génération, de vestiaires spacieux, de douches et de tout le matériel nécessaire pour vos séances d'aquagym. L'ambiance musicale dynamique et les cours collectifs rendent chaque séance motivante et conviviale.</p>

          <h3>Comment se déroule une séance ?</h3>
          <p>Une séance d'aquavélo dure 45 minutes et comprend :</p>
          <ol>
            <li>Échauffement progressif (5 minutes)</li>
            <li>Exercices variés sur le vélo aquatique (30 minutes)</li>
            <li>Exercices d'aquagym pour renforcer les bras et le buste (5 minutes)</li>
            <li>Étirements et retour au calme (5 minutes)</li>
          </ol>

          <p class="mt-4"><strong>Envie d'essayer ?</strong> Réservez votre séance découverte gratuite dès maintenant en remplissant le formulaire ci-dessus. Notre équipe sera ravie de vous accueillir dans notre centre d'aquavélo à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> !</p>
        </section>

        <!-- FAQ pour le SEO -->
        <section class="mt-5" itemscope itemtype="https://schema.org/FAQPage">
          <h2>Questions fréquentes</h2>
          
          <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
            <h3 itemprop="name">Dois-je savoir nager pour faire de l'aquavélo ?</h3>
            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
              <p itemprop="text">Non, il n'est pas nécessaire de savoir nager. Le niveau d'eau arrive à la taille et vous êtes installé sur un vélo stable.</p>
            </div>
          </div>

          <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
            <h3 itemprop="name">Que dois-je apporter pour ma première séance ?</h3>
            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
              <p itemprop="text">Prévoyez un maillot de bain, une serviette et des chaussures d'eau (non obligatoires). Nous mettons à disposition des casiers sécurisés.</p>
            </div>
          </div>

          <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
            <h3 itemprop="name">Combien de séances par semaine sont recommandées ?</h3>
            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
              <p itemprop="text">Pour des résultats optimaux, nous recommandons 2 à 3 séances par semaine. Des résultats visibles apparaissent généralement après 8 à 10 séances.</p>
            </div>
          </div>

          <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
            <h3 itemprop="name">L'aquavélo est-il efficace pour perdre du poids ?</h3>
            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
              <p itemprop="text">Oui, l'aquavélo permet de brûler entre 400 et 600 calories par séance. Associé à une alimentation équilibrée, c'est une méthode très efficace pour perdre du poids durablement.</p>
            </div>
          </div>

          <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
            <h3 itemprop="name">Y a-t-il des contre-indications ?</h3>
            <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
              <p itemprop="text">L'aquavélo est une activité douce mais en cas de doute (problèmes cardiaques, grossesse, blessures récentes), consultez votre médecin avant de commencer.</p>
            </div>
          </div>
        </section>
      </div>
    </div>

    <!-- Section avis clients (à ajouter si disponible) -->
    <?php if (isset($testimonials) && !empty($testimonials)) : ?>
    <section class="mt-5">
      <h2>Ce que nos clients disent de nous</h2>
      <div class="row">
        <?php foreach ($testimonials as $testimonial) : ?>
        <div class="col-md-4" itemscope itemtype="https://schema.org/Review">
          <div class="testimonial-card">
            <div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
              <meta itemprop="ratingValue" content="<?= $testimonial['rating'] ?>">
              <meta itemprop="bestRating" content="5">
            </div>
            <p itemprop="reviewBody">"<?= htmlspecialchars($testimonial['text']) ?>"</p>
            <p><strong itemprop="author"><?= htmlspecialchars($testimonial['name']) ?></strong></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Carte Google Maps -->
    <?php if (isset($row_center['latitude']) && isset($row_center['longitude'])) : ?>
    <section class="mt-5">
      <h2>Comment nous trouver</h2>
      <div class="map-container">
        <iframe 
          src="https://www.google.com/maps?q=<?= htmlspecialchars($row_center['latitude']) ?>,<?= htmlspecialchars($row_center['longitude']) ?>&hl=fr&z=15&output=embed"
          width="100%" 
          height="400" 
          style="border:0;" 
          allowfullscreen="" 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"
          title="Localisation du centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </iframe>
      </div>
      <p class="mt-3">
        <strong>Accès en transport :</strong> 
        <?php if (isset($row_center['transport_info'])) : ?>
          <?= htmlspecialchars($row_center['transport_info']) ?>
        <?php else : ?>
          Notre centre est facilement accessible en voiture avec parking à proximité. Transports en commun : consultez les horaires de bus locaux.
        <?php endif; ?>
      </p>
    </section>
    <?php endif; ?>

    <!-- Liens internes pour le maillage -->
    <section class="mt-5">
      <h2>Découvrez nos autres centres</h2>
      <nav aria-label="Autres centres Aquavélo">
        <ul class="centers-list">
          <?php if (isset($centers_list_d)) : ?>
            <?php foreach ($centers_list_d as $center) : ?>
              <?php if ($center['city'] != $city) : ?>
                <li>
                  <a href="/centres/<?= htmlspecialchars(strtolower($center['city']), ENT_QUOTES, 'UTF-8'); ?>" 
                     title="Centre Aquavélo <?= htmlspecialchars($center['city'], ENT_QUOTES, 'UTF-8'); ?>">
                    Aquavélo <?= htmlspecialchars($center['city'], ENT_QUOTES, 'UTF-8'); ?>
                  </a>
                </li>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </nav>
    </section>
  </main>

  <!-- Footer (à ajouter) -->
  <footer class="site-footer mt-5">
    <div class="container">
      <p>&copy; <?= date('Y'); ?> Aquavélo. Tous droits réservés.</p>
      <nav aria-label="Liens footer">
        <ul>
          <li><a href="/mentions-legales">Mentions légales</a></li>
          <li><a href="/politique-confidentialite">Politique de confidentialité</a></li>
          <li><a href="/cgv">CGV</a></li>
          <li><a href="/contact">Contact</a></li>
        </ul>
      </nav>
    </div>
  </footer>

  <!-- Scripts à la fin pour de meilleures performances -->
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

    // Validation du formulaire côté client
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('.contact-form');
      if (form) {
        form.addEventListener('submit', function(e) {
          const phone = document.getElementById('phone').value;
          const phoneRegex = /^[0-9]{10}$/;
          
          if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
            e.preventDefault();
            alert('Veuillez entrer un numéro de téléphone valide (10 chiffres)');
          }
        });
      }
    });
  </script>

  <!-- Script pour le lazy loading des images (si besoin de compatibilité avec anciens navigateurs) -->
  <script>
    if ('loading' in HTMLImageElement.prototype) {
      // Le navigateur supporte le lazy loading natif
    } else {
      // Fallback pour les anciens navigateurs
      const images = document.querySelectorAll('img[loading="lazy"]');
      images.forEach(img => {
        img.src = img.src;
      });
    }
  </script>
</body>
</html>
