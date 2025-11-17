<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Gratuite 45min | Aquabiking</title>
  
  <meta name="description" content="Centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>. Essayez gratuitement l'aquabiking + aquagym 45min. Affinez votre silhouette rapidement. Réservez maintenant !">
  <meta name="keywords" content="aquavélo, aquabiking, aquagym, <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>, sport aquatique, perte de poids, raffermissement, cellulite">
  <meta name="robots" content="index, follow">
  <meta name="author" content="Aquavélo">
  
  <link rel="canonical" href="https://www.aquavelo.com/centres/<?= htmlspecialchars(strtolower($city ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.aquavelo.com/centres/<?= htmlspecialchars(strtolower($city ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:title" content="Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Découverte Gratuite">
  <meta property="og:description" content="Essayez gratuitement l'aquabiking et l'aquagym dans notre centre de <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>. 45 minutes pour découvrir tous les bienfaits.">
  <meta property="og:image" content="https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg">
  <meta property="og:locale" content="fr_FR">
  
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Gratuite">
  <meta name="twitter:description" content="Essayez l'aquabiking gratuitement pendant 45min à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>.">
  <meta name="twitter:image" content="https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg">
  
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/style.css">
  
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
</head>
<body>

<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">AQUAVELO = AQUABIKING + AQUAGYM</h1>
    <h2 class="page-title pull-left">Excellent pour affiner et raffermir la silhouette, et perdre du poids si besoin.</h2>
    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li><a href="/centres">Centres</a></li>
      <li class="active"><?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
    </ol>
  </div>

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
</header>

<section class="content-area bg1">
  <div class="container">
    <div class="row mt-3">
      <div class="col-md-3 col-6 text-center">
        <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg" 
             alt="Salle d'aquabiking centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> avec vélos aquatiques" 
             class="img-fluid img-same" 
             width="300" 
             height="200"
             loading="eager">
      </div>

      <div class="col-md-3 col-6 text-center">
        <?php if (isset($row_center['id']) && $row_center['id'] != 305) : ?>
          <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/2.jpg" 
               alt="Espace aquagym centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
               class="img-fluid img-same" 
               width="300" 
               height="200"
               loading="lazy">
        <?php else : ?>
          <img src="/images/Cannes1.jpg" 
               alt="Intérieur centre Aquavélo Cannes" 
               class="img-fluid img-same" 
               width="300" 
               height="200"
               loading="lazy">
        <?php endif; ?>
      </div>

      <?php if (isset($row_center['id']) && !in_array($row_center['id'], [305, 347, 349])) : ?>
        <div class="col-md-3 col-6 text-center">
          <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/3.jpg" 
               alt="Équipements vestiaires centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
               class="img-fluid img-same" 
               width="300" 
               height="200"
               loading="lazy">
        </div>
      <?php endif; ?>

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
             title="Offre séance découverte gratuite Aquavélo <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>">
            <img src="/images/promoJan24.webp" 
                 alt="Promotion séance découverte gratuite 45min Aquavélo <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>" 
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

<div class="container">
  <div class="row">
    <div class="col-md-6">
      <h2 class="form-group">Essayez une séance gratuite de 45 mn</h2>
      
      <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
        <p>en vous inscrivant sur notre <span style="color: #00acdc;"> <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank"><strong>calendrier</strong> (cliquez ici)</a></span> ou en prenant rendez-vous ci-dessous.</p>
      <?php endif; ?>
      
      <?php if (isset($row_center['id']) && in_array($row_center['id'], [343])) : ?>
        <p>en vous inscrivant sur notre <span style="color: #00acdc;"> <a href="https://aquavelomerignac33.simplybook.it/v2/" target="_blank"><strong>calendrier</strong> (cliquez ici)</a></span> ou en prenant rendez-vous ci-dessous.</p>

        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17714430375"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', 'AW-17714430375');
        </script>
      <?php endif; ?>
    
      <form role="form" id="contactForm" class="contact-form" method="POST" action="_page.php" novalidate>
        <div class="form-group">
          <label for="center">Dans quel centre souhaitez-vous effectuer votre séance ? <span style="color: red;">*</span></label>
          <select class="form-control" id="center" name="center">
            <?php if (isset($centers_list_d)) : ?>
              <option value="">-- Sélectionnez un centre --</option>
              <?php foreach ($centers_list_d as $free_d) : ?>
                <option <?php if (isset($_GET['city']) && $_GET['city'] == $free_d['city']) echo 'selected'; ?> value="<?= htmlspecialchars($free_d['id'], ENT_QUOTES, 'UTF-8'); ?>">
                  <?= htmlspecialchars($free_d['city'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
          <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez sélectionner un centre</span>
        </div>
        
        <div class="form-group">
          <label for="nom">Nom et prénom <span style="color: red;">*</span></label>
          <input type="text" 
                 class="form-control" 
                 id="nom" 
                 name="nom" 
                 placeholder="Nom et prénom" 
                 value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez entrer votre nom</span>
        </div>
        
        <div class="form-group">
          <label for="email">Email <span style="color: red;">*</span></label>
          <input type="email" 
                 class="form-control" 
                 id="email" 
                 name="email" 
                 placeholder="exemple@email.com" 
                 value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <span class="error-message" style="color: red; font-size: 12px; display: none;">Veuillez entrer un email valide</span>
        </div>
          
        <div class="form-group">
          <label for="phone">Téléphone <span style="color: red;">*</span></label>
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
        
        <button type="submit" class="btn btn-default" aria-label="Recevoir mon bon par email">
          Recevoir mon bon par email
        </button>
      </form>

      <?php if (isset($row_center['id'])) : ?>
        <?php if ($row_center['id'] == 253) : ?>
          <div class="text-center mt-4">
            <img src="/images/planningAntibes.jpg" 
                 alt="Planning hebdomadaire cours aquabiking aquagym Antibes" 
                 class="img-fluid" 
                 style="max-width: 100%; height: auto;"
                 loading="lazy">
          </div>
        <?php elseif (in_array($row_center['id'], [305, 347, 349])) : ?>
          <div class="text-center mt-4">
            <img src="/images/PLANNINGCANNES0125.jpg" 
                 alt="Planning hebdomadaire cours aquabiking aquagym Cannes janvier 2025" 
                 class="img-fluid" 
                 style="max-width: 100%; height: auto;"
                 loading="lazy">
          </div>
        <?php elseif ($row_center['id'] == 179) : ?>
          <div class="text-center mt-4">
            <img src="/images/planningNice.jpg" 
                 alt="Planning hebdomadaire cours aquabiking aquagym Nice" 
                 class="img-fluid" 
                 style="max-width: 100%; height: auto;"
                 loading="lazy">
          </div>
        <?php endif; ?>
      <?php endif; ?>
      
      <div style="margin-top: 40px;">
        <h3>Pourquoi choisir l'aquavélo à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> ?</h3>
        <p>Notre <strong>centre d'aquabiking à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></strong> vous propose une méthode innovante pour <strong>affiner votre silhouette</strong> et <strong>perdre du poids</strong> efficacement. L'aquavélo combine les bienfaits du vélo et de l'aquagym dans une eau chauffée entre 28 et 32°C.</p>

        <h4>Les bienfaits de l'aquabiking</h4>
        <ul>
          <li><strong>Brûlez jusqu'à 600 calories</strong> par séance de 45 minutes</li>
          <li><strong>Réduisez la cellulite</strong> grâce à l'effet drainant de l'eau</li>
          <li><strong>Tonifiez vos muscles</strong> sans impact sur les articulations</li>
          <li><strong>Améliorez votre circulation sanguine</strong> et votre retour veineux</li>
          <li><strong>Sculptez votre silhouette</strong> : cuisses, fessiers, abdos</li>
          <li><strong>Détendez-vous</strong> : l'eau a des vertus relaxantes naturelles</li>
        </ul>

        <h4>Pour qui est fait l'aquavélo ?</h4>
        <p>L'aquabiking convient à <strong>tous les âges et tous les niveaux</strong> : débutants, sportifs confirmés, personnes en surpoids, seniors, femmes enceintes (avec accord médical). <strong>Pas besoin de savoir nager</strong> - l'eau arrive à la taille et vous êtes installé sur un vélo stable.</p>

        <h4>Une séance d'aquavélo, comment ça se passe ?</h4>
        <ol>
          <li>Échauffement progressif sur le vélo aquatique (5 min)</li>
          <li>Exercices variés avec changements de rythme (30 min)</li>
          <li>Renforcement bras et buste avec accessoires (5 min)</li>
          <li>Étirements et relaxation dans l'eau (5 min)</li>
        </ol>
      </div>

      <div style="margin-top: 40px; background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h3>Questions fréquentes sur l'aquavélo</h3>
        <h4 style="color: #00acdc; margin-top: 15px;">Dois-je savoir nager ?</h4>
        <p><strong>Non</strong>, il n'est pas nécessaire de savoir nager. Le niveau d'eau arrive à la taille et vous êtes installé sur un vélo stable et sécurisé.</p>

        <h4 style="color: #00acdc; margin-top: 15px;">Que faut-il apporter ?</h4>
        <p>Un <strong>maillot de bain</strong>, une <strong>serviette</strong> et optionnellement des <strong>chaussures d'eau</strong>. Nous mettons à disposition des casiers sécurisés pour vos affaires.</p>

        <h4 style="color: #00acdc; margin-top: 15px;">Combien de séances par semaine ?</h4>
        <p>Pour des <strong>résultats optimaux</strong>, nous recommandons <strong>2 à 3 séances par semaine</strong>. Les premiers résultats sont visibles après 8 à 10 séances.</p>

        <h4 style="color: #00acdc; margin-top: 15px;">L'aquavélo fait-il maigrir ?</h4>
        <p><strong>Oui</strong>, l'aquabiking permet de brûler entre <strong>400 et 600 calories par séance</strong>. Associé à une alimentation équilibrée, c'est très efficace pour la perte de poids.</p>

        <h4 style="color: #00acdc; margin-top: 15px;">Y a-t-il des contre-indications ?</h4>
        <p>L'aquavélo est une activité douce, mais en cas de <strong>problèmes cardiaques, grossesse à risque ou blessures récentes</strong>, consultez votre médecin avant de commencer.</p>
      </div>
    </div>

    <div class="col-md-6">
      <dl style="margin-top:30px;">
        <dt>Adresse</dt>
        <dd itemprop="address"><?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
        
        <dt>Téléphone</dt>
        <dd>
          <strong>
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $row_center['phone'] ?? ''); ?>" style="color: inherit; text-decoration: none;">
              <?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </a>
          </strong>
        </dd>
        
        <dt>Horaires</dt>
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
          title="Réservation en ligne pour adhérents Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
          aria-label="Cliquez pour réserver en ligne"
          target="_blank"
          rel="noopener"
          class="btn btn-default">Réserver en ligne</a>
        </dd>

        <dt>Découvrez la vie de votre centre</dt>
        <dd>
          <a href="https://www.facebook.com/<?= htmlspecialchars($row_center['facebook'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
             title="Page Facebook Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
             target="_blank"
             rel="noopener nofollow"
             class="btn btn-default" 
             aria-label="Visitez notre page Facebook">
            Facebook
          </a>
        </dd>

        <dt>Résultats Minceurs Rapides</dt>
        <dd>
          <button type="button" class="btn btn-default" onclick="ouvre_popup('/nouveauResultat.html'); return false;" 
             title="Découvrez les témoignages et résultats minceur de nos clients" 
             aria-label="Ouvrir les résultats minceurs">
            Résultats Minceurs
          </button>
        </dd>

        <dt>Calculateur calories avec conseils minceur</dt>
        <dd>
          <button type="button" class="btn btn-default" onclick="ouvre_popup('/resultatMinceur.php'); return false;" 
             title="Calculateur de calories personnalisé avec conseils pour perdre du poids" 
             aria-label="Calculateur calories & conseils minceur">
            Conseils pour perdre du poids
          </button>
        </dd>
        
        <dt>Menu perte de poids</dt>
        <dd>
          <a href="https://www.aquavelo.com/conseilminceur" class="btn btn-default" 
             title="Découvrez nos menus équilibrés pour perdre du poids" 
             aria-label="Menu Perte de Poids">
           Menu Perte de Poids
           </a>
        </dd>

        <dt>Description</dt>
        <dd>
          <p><?= $row_center['description'] ?? '' ?></p>
        </dd>
      </dl>
      
      <div style="margin-top: 40px;">
        <h3>📍 Comment nous trouver</h3>
        <p><strong>Adresse :</strong> <?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        
        <?php 
        $map_address = urlencode($row_center['address'] ?? '');
        $map_city = urlencode($city ?? '');
        
        if (!empty($row_center['latitude']) && !empty($row_center['longitude'])) {
            $map_query = $row_center['latitude'] . ',' . $row_center['longitude'];
        } else {
            $map_query = $map_address . ',+' . $map_city . ',+France';
        }
        ?>
        
        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; margin-top: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          <iframe 
            src="https://www.google.com/maps?q=<?= $map_query ?>&hl=fr&z=15&output=embed"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade"
            title="Localisation du centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> sur Google Maps"
            aria-label="Carte interactive montrant l'emplacement du centre Aquavélo">
          </iframe>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #00acdc; border-radius: 4px;">
          <h4 style="margin-top: 0; color: #00acdc;">🚗 Accès et parking</h4>
          <p><strong>En voiture :</strong> Parking gratuit disponible à proximité du centre.</p>
          <p><strong>En transports en commun :</strong> Consultez les horaires de bus et tramway locaux pour rejoindre facilement notre centre.</p>
          
          <p style="margin-top: 15px;">
            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $map_query ?>" 
               target="_blank" 
               rel="noopener"
               class="btn btn-default"
               style="display: inline-block; padding: 10px 20px; background: #00acdc; color: white; text-decoration: none; border-radius: 4px;"
               title="Obtenir l'itinéraire vers le centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>">
              🧭 Calculer mon itinéraire
            </a>
          </p>
        </div>
      </div>
      
      <div style="margin-top: 40px; padding: 20px; background: #f0f8ff; border-radius: 8px;">
        <h3>Découvrez nos autres centres Aquavélo</h3>
        <p>Retrouvez l'aquabiking et l'aquagym dans toute la France :</p>
        <ul style="list-style: none; padding: 0;">
          <?php if (isset($centers_list_d)) : ?>
            <?php foreach ($centers_list_d as $center) : ?>
              <?php if ($center['city'] != $city) : ?>
                <li style="margin-bottom: 8px;">
                  <a href="/centres/<?= htmlspecialchars(strtolower($center['city']), ENT_QUOTES, 'UTF-8'); ?>" 
                     title="Centre Aquavélo <?= htmlspecialchars($center['city'], ENT_QUOTES, 'UTF-8'); ?> - Aquabiking">
                    ➤ Aquavélo <?= htmlspecialchars($center['city'], ENT_QUOTES, 'UTF-8'); ?>
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
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('contactForm');
    
    if (!form) return;

    form.addEventListener('submit', function(e) {
        var isValid = true;
        var firstError = null;

        // 1. Validation du CENTRE
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

        // 2. Validation du NOM
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

        // 3. Validation EMAIL
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

        // 4. Validation TÉLÉPHONE
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

        // SI INVALIDE : Bloquer la soumission
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
        
        // SI VALIDE : Le formulaire se soumet normalement
        return true;
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

</body>
</html>



