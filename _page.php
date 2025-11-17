<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- ============================================
       BALISES SEO OPTIMISÉES
       ============================================ -->
  
  <!-- Title optimisé (50-60 caractères) -->
  <title>Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Gratuite 45min | Aquabiking</title>
  
  <!-- Meta Description (150-160 caractères) -->
  <meta name="description" content="Centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>. Essayez gratuitement l'aquabiking + aquagym 45min. Affinez votre silhouette rapidement. Réservez maintenant !">
  
  <!-- Meta Keywords -->
  <meta name="keywords" content="aquavélo, aquabiking, aquagym, <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>, sport aquatique, perte de poids, raffermissement, cellulite">
  
  <!-- Robots -->
  <meta name="robots" content="index, follow">
  <meta name="author" content="Aquavélo">
  
  <!-- Canonical URL -->
  <link rel="canonical" href="https://www.aquavelo.com/centres/<?= htmlspecialchars(strtolower($city ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://www.aquavelo.com/centres/<?= htmlspecialchars(strtolower($city ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:title" content="Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Découverte Gratuite">
  <meta property="og:description" content="Essayez gratuitement l'aquabiking et l'aquagym dans notre centre de <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>. 45 minutes pour découvrir tous les bienfaits.">
  <meta property="og:image" content="https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg">
  <meta property="og:locale" content="fr_FR">
  
  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> - Séance Gratuite">
  <meta name="twitter:description" content="Essayez l'aquabiking gratuitement pendant 45min à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>.">
  <meta name="twitter:image" content="https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg">
  
  <!-- Vos CSS existants -->
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/style.css">
  
  <!-- ============================================
       DONNÉES STRUCTURÉES SCHEMA.ORG
       ============================================ -->
  
  <!-- LocalBusiness Schema -->
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
  
  <!-- Breadcrumb Schema -->
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
  
  <!-- FAQ Schema -->
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
  
  <!-- CSS POUR LES MESSAGES D'ERREUR ET FORMULAIRE -->
  <style>
    .form-group {
      margin-bottom: 20px;
      position: relative;
    }
    
    /* Important pour iOS Safari */
    .form-control {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      border-radius: 4px;
    }
    
    select.form-control {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 20px;
      padding-right: 40px;
    }

    .error-message {
      display: none;
      color: #dc3545;
      font-size: 13px;
      margin-top: 5px;
      font-weight: 500;
    }

    .form-group.has-error .form-control {
      border: 2px solid #dc3545 !important;
      background-color: #fff5f5;
    }

    .form-group.has-error .error-message {
      display: block !important;
    }

    .form-control:focus {
      border-color: #00acdc;
      box-shadow: 0 0 0 0.2rem rgba(0, 172, 220, 0.25);
      outline: none;
    }

    .btn-default {
      background-color: #00acdc;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 4px;
      font-weight: 500;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      -webkit-appearance: none;
      transition: background-color 0.3s ease;
    }
    
    .btn-default:hover,
    .btn-default:active {
      background-color: #0096c7;
      color: white;
    }
    
    .btn-default:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
    
    /* Style pour le label requis */
    .form-group label span {
      color: #dc3545;
      font-weight: bold;
    }
    
    /* Images responsive */
    .img-same {
      width: 100%;
      height: auto;
      object-fit: cover;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">AQUAVELO = AQUABIKING + AQUAGYM</h1>
    <h2 class="page-title pull-left">Excellent pour affiner et raffermir la silhouette, et perdre du poids si besoin.</h2>
    <ol class="breadcrumb pull-right" itemscope itemtype="https://schema.org/BreadcrumbList">
      <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a href="./" itemprop="item">
          <span itemprop="name">Accueil</span>
        </a>
        <meta itemprop="position" content="1" />
      </li>
      <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a href="/centres" itemprop="item">
          <span itemprop="name">Centres</span>
        </a>
        <meta itemprop="position" content="2" />
      </li>
      <li class="active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <span itemprop="name"><?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
        <meta itemprop="position" content="3" />
      </li>
    </ol>
  </div>
  
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
           alt="Facebook Pixel"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
  <?php endif; ?>
</header>

<section class="content-area bg1">
  <div class="container">
    <div class="row mt-3">
      <!-- Image principale avec alt optimisé -->
      <div class="col-md-3 col-6 text-center">
        <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg" 
             alt="Salle d'aquabiking centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> avec vélos aquatiques" 
             class="img-fluid img-same" 
             width="300" 
             height="200"
             loading="eager">
      </div>
      
      <!-- Image secondaire avec alt optimisé -->
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
      
      <!-- Image supplémentaire avec alt optimisé -->
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
      
      <!-- Image promotionnelle avec alt optimisé -->
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

<!-- Formulaire et Informations supplémentaires -->
<div class="container">
  <div class="row">
    <!-- COLONNE FORMULAIRE -->
    <div class="col-md-6">
      <h2 class="form-group">Essayez une séance gratuite de 45 mn</h2>
      
      <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
        <p>en vous inscrivant sur notre <span style="color: #00acdc;"> <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank" rel="noopener"><strong>calendrier</strong> (cliquez ici)</a></span> ou en prenant rendez-vous ci-dessous.</p>
      <?php endif; ?>
      
      <?php if (isset($row_center['id']) && in_array($row_center['id'], [343])) : ?>
        <p>en vous inscrivant sur notre <span style="color: #00acdc;"> <a href="https://aquavelomerignac33.simplybook.it/v2/" target="_blank" rel="noopener"><strong>calendrier</strong> (cliquez ici)</a></span> ou en prenant rendez-vous ci-dessous.</p>
        
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17714430375"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', 'AW-17714430375');
        </script>
      <?php endif; ?>
    
      <!-- FORMULAIRE OPTIMISÉ POUR iOS SAFARI -->
      <form method="POST" action="_page.php" id="contactForm">
        
        <div class="form-group" id="centerGroup">
          <label for="center">Dans quel centre souhaitez-vous effectuer votre séance ? <span>*</span></label>
          <select class="form-control" id="center" name="center">
            <option value="">-- Sélectionnez un centre --</option>
            <?php if (isset($centers_list_d)) : ?>
              <?php foreach ($centers_list_d as $free_d) : ?>
                <option <?php if (isset($_GET['city']) && $_GET['city'] == $free_d['city']) echo 'selected'; ?> 
                        value="<?= htmlspecialchars($free_d['id'], ENT_QUOTES, 'UTF-8'); ?>">
                  <?= htmlspecialchars($free_d['city'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
          <span class="error-message">Veuillez sélectionner un centre</span>
        </div>
        
        <div class="form-group" id="nomGroup">
          <label for="nom">Nom et prénom <span>*</span></label>
          <input type="text" 
                 class="form-control" 
                 id="nom" 
                 name="nom" 
                 placeholder="Nom et prénom" 
                 value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <span class="error-message">Veuillez entrer votre nom et prénom</span>
        </div>
        
        <div class="form-group" id="emailGroup">
          <label for="email">Email <span>*</span></label>
          <input type="email" 
                 class="form-control" 
                 id="email" 
                 name="email" 
                 placeholder="exemple@email.com" 
                 value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <span class="error-message">Veuillez entrer un email valide</span>
        </div>
         
        <div class="form-group" id="phoneGroup">
          <label for="phone">Téléphone <span>*</span></label>
          <input type="tel" 
                 class="form-control" 
                 id="phone" 
                 name="phone" 
                 placeholder="06 12 34 56 78" 
                 value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <span class="error-message">Veuillez entrer votre numéro de téléphone</span>
        </div>
      
        <input type="hidden" name="reason" id="reason" value="">
        <input type="hidden" name="segment" id="segment" value="">
        
        <button type="button" id="submitBtn" class="btn btn-default">
          Recevoir mon bon par email
        </button>
      </form>
      <!-- FIN DU FORMULAIRE -->
      
      <!-- Planning des cours avec alt optimisé -->
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
      
      <!-- ============================================
           CONTENU SEO ENRICHI
           ============================================ -->
      
      <div style="margin-top: 40px;">
        <h3>Pourquoi choisir l'aquavélo à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?> ?</h3>
        
        <p>Notre <strong>centre d'aquabiking à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></strong> vous propose une méthode innovante pour <strong>affiner votre silhouette</strong> et <strong>perdre du poids</strong> efficacement. L'aquavélo combine les bienfaits du vélo et de l'aquagym dans une eau chauffée entre 28 et 32°C.</p>
        
        <h4>Les bienfaits de l'aquabiking</h4>
        <p>L'<strong>aquabiking</strong>, aussi appelé aquavélo ou aquacycling, offre de nombreux avantages pour votre corps :</p>
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
        <p>Chaque <strong>séance de 45 minutes</strong> comprend :</p>
        <ol>
          <li>Échauffement progressif sur le vélo aquatique (5 min)</li>
          <li>Exercices variés avec changements de rythme (30 min)</li>
          <li>Renforcement bras et buste avec accessoires (5 min)</li>
          <li>Étirements et relaxation dans l'eau (5 min)</li>
        </ol>
        
        <h4>Nos équipements à <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></h4>
        <p>Notre centre dispose de <strong>vélos aquatiques dernière génération</strong>, d'un <strong>bassin chauffé</strong>, de vestiaires spacieux avec douches, et de tout le matériel nécessaire. Ambiance musicale motivante et cours collectifs encadrés par des coachs diplômés.</p>
      </div>
      
      <!-- Section FAQ pour le SEO -->
      <div style="margin-top: 40px; background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h3>Questions fréquentes sur l'aquavélo</h3>
        
        <article itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h4 style="color: #00acdc; margin-top: 15px;" itemprop="name">Dois-je savoir nager ?</h4>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <p itemprop="text"><strong>Non</strong>, il n'est pas nécessaire de savoir nager. Le niveau d'eau arrive à la taille et vous êtes installé sur un vélo stable et sécurisé.</p>
          </div>
        </article>
        
        <article itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h4 style="color: #00acdc; margin-top: 15px;" itemprop="name">Que faut-il apporter ?</h4>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <p itemprop="text">Un <strong>maillot de bain</strong>, une <strong>serviette</strong> et optionnellement des <strong>chaussures d'eau</strong>. Nous mettons à disposition des casiers sécurisés pour vos affaires.</p>
          </div>
        </article>
        
        <article itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h4 style="color: #00acdc; margin-top: 15px;" itemprop="name">Combien de séances par semaine ?</h4>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <p itemprop="text">Pour des <strong>résultats optimaux</strong>, nous recommandons <strong>2 à 3 séances par semaine</strong>. Les premiers résultats sont visibles après 8 à 10 séances.</p>
          </div>
        </article>
        
        <article itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h4 style="color: #00acdc; margin-top: 15px;" itemprop="name">L'aquavélo fait-il maigrir ?</h4>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <p itemprop="text"><strong>Oui</strong>, l'aquabiking permet de brûler entre <strong>400 et 600 calories par séance</strong>. Associé à une alimentation équilibrée, c'est très efficace pour la perte de poids.</p>
          </div>
        </article>
        
        <article itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h4 style="color: #00acdc; margin-top: 15px;" itemprop="name">Y a-t-il des contre-indications ?</h4>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <p itemprop="text">L'aquavélo est une activité douce, mais en cas de <strong>problèmes cardiaques, grossesse à risque ou blessures récentes</strong>, consultez votre médecin avant de commencer.</p>
          </div>
        </article>
      </div>
    </div>
    
    <!-- COLONNE INFORMATIONS -->
    <div class="col-md-6">
      <dl style="margin-top:30px;">
        <!-- Adresse avec données structurées -->
        <dt>Adresse</dt>
        <dd itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
          <span itemprop="streetAddress"><?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
        </dd>
        
        <!-- Téléphone cliquable -->
        <dt>Téléphone</dt>
        <dd>
          <strong>
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $row_center['phone'] ?? ''); ?>" 
               style="color: inherit; text-decoration: none;"
               itemprop="telephone">
              <?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </a>
          </strong>
        </dd>
        
        <dt>Horaires</dt>
        <dd itemprop="openingHours"><?= htmlspecialchars($row_center['openhours'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
        
        <!-- Agenda avec rel noopener pour sécurité -->
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
          class="btn btn-default" style="width: auto; display: inline-block;">Réserver en ligne</a>
        </dd>
        
        <!-- Facebook avec rel noopener -->
        <dt>Découvrez la vie de votre centre</dt>
        <dd>
          <a href="https://www.facebook.com/<?= htmlspecialchars($row_center['facebook'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
             title="Page Facebook Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
             target="_blank"
             rel="noopener nofollow"
             class="btn btn-default" 
             style="width: auto; display: inline-block;"
             aria-label="Visitez notre page Facebook">
            Facebook
          </a>
        </dd>
        
        <!-- Résultats Minceurs -->
        <dt>Résultats Minceurs Rapides</dt>
        <dd>
          <button type="button" 
                  class="btn btn-default" 
                  style="width: auto; display: inline-block;"
                  onclick="ouvre_popup('/nouveauResultat.html'); return false;" 
                  title="Découvrez les témoignages et résultats minceur de nos clients" 
                  aria-label="Ouvrir les résultats minceurs">
            Résultats Minceurs
          </button>
        </dd>
        
        <!-- Calculateur avec button au lieu de link -->
        <dt>Calculateur calories avec conseils minceur</dt>
        <dd>
          <button type="button" 
                  class="btn btn-default" 
                  style="width: auto; display: inline-block;"
                  onclick="ouvre_popup('/resultatMinceur.php'); return false;" 
                  title="Calculateur de calories personnalisé avec conseils pour perdre du poids" 
                  aria-label="Calculateur calories & conseils minceur">
            Conseils pour perdre du poids
          </button>
        </dd>
        
        <!-- Menu Perte de Poids -->
        <dt>Menu perte de poids</dt>
        <dd>
          <a href="https://www.aquavelo.com/conseilminceur" 
             class="btn btn-default" 
             style="width: auto; display: inline-block;"
             title="Découvrez nos menus équilibrés pour perdre du poids" 
             aria-label="Menu Perte de Poids">
            Menu Perte de Poids
          </a>
        </dd>
        
        <!-- Description -->
        <?php if (!empty($row_center['description'])) : ?>
        <dt>Description</dt>
        <dd>
          <p><?= $row_center['description'] ?></p>
        </dd>
        <?php endif; ?>
      </dl>
      
      <!-- ============================================
           CARTE GOOGLE MAPS - LOCALISATION
           ============================================ -->
      
      <div style="margin-top: 40px;">
        <h3>📍 Comment nous trouver</h3>
        <p><strong>Adresse :</strong> <?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        
        <?php 
        // Préparer l'adresse pour Google Maps
        $map_address = urlencode($row_center['address'] ?? '');
        $map_city = urlencode($city ?? '');
        
        // Option 1 : Si vous avez les coordonnées GPS dans la base de données
        if (!empty($row_center['latitude']) && !empty($row_center['longitude'])) {
            $map_query = $row_center['latitude'] . ',' . $row_center['longitude'];
        } 
        // Option 2 : Utiliser l'adresse complète
        else {
            $map_query = $map_address . ',+' . $map_city . ',+France';
        }
        ?>
        
        <!-- Carte Google Maps en iframe -->
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
        
        <!-- Informations d'accès -->
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #00acdc; border-radius: 4px;">
          <h4 style="margin-top: 0; color: #00acdc;">🚗 Accès et parking</h4>
          <p><strong>En voiture :</strong> Parking gratuit disponible à proximité du centre.</p>
          <p><strong>En transports en commun :</strong> Consultez les horaires de bus et tramway locaux pour rejoindre facilement notre centre.</p>
          
          <!-- Bouton d'itinéraire Google Maps -->
          <p style="margin-top: 15px;">
            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $map_query ?>" 
               target="_blank" 
               rel="noopener"
               class="btn btn-default"
               style="display: inline-block; width: auto;"
               title="Obtenir l'itinéraire vers le centre Aquavélo <?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?>">
              🧭 Calculer mon itinéraire
            </a>
          </p>
        </div>
      </div>
      
      <!-- ============================================
           SECTION SEO SUPPLÉMENTAIRE : Maillage interne
           ============================================ -->
      
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

<!-- ============================================
     SCRIPTS JAVASCRIPT
     ============================================ -->

<script>
// ===================================
// FONCTION POPUP (Votre fonction existante)
// ===================================
function ouvre_popup(url) {
  var width = Math.max(window.innerWidth / 3, 300);
  var height = Math.max(window.innerHeight / 3, 200);
  var left = (window.innerWidth - width) / 2;
  var top = (window.innerHeight - height) / 2;
  window.open(
    url, 
    'popup', 
    'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',scrollbars=yes,resizable=yes'
  );
  return false;
}

// ===================================
// VALIDATION FORMULAIRE POUR iOS SAFARI
// ===================================
(function() {
  'use strict';
  
  var form = document.getElementById('contactForm');
  var submitBtn = document.getElementById('submitBtn');
  
  if (!form || !submitBtn) return;
  
  // ===================================
  // FONCTIONS DE VALIDATION
  // ===================================
  
  // Validation email
  function isValidEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }
  
  // Validation téléphone
  function isValidPhone(phone) {
    var cleaned = phone.replace(/[\s\-\(\)\.]/g, '');
    return cleaned.length >= 10;
  }
  
  // Reset toutes les erreurs
  function resetErrors() {
    var groups = document.querySelectorAll('.form-group');
    for (var i = 0; i < groups.length; i++) {
      groups[i].classList.remove('has-error');
    }
  }
  
  // Valider le formulaire
  function validateForm() {
    var isValid = true;
    
    // Reset errors
    resetErrors();
    
    // Centre
    var center = document.getElementById('center');
    if (!center.value || center.value === '') {
      document.getElementById('centerGroup').classList.add('has-error');
      isValid = false;
    }
    
    // Nom
    var nom = document.getElementById('nom');
    if (!nom.value || nom.value.trim().length < 2) {
      document.getElementById('nomGroup').classList.add('has-error');
      isValid = false;
    }
    
    // Email
    var email = document.getElementById('email');
    if (!email.value || !isValidEmail(email.value)) {
      document.getElementById('emailGroup').classList.add('has-error');
      isValid = false;
    }
    
    // Phone
    var phone = document.getElementById('phone');
    if (!phone.value || !isValidPhone(phone.value)) {
      document.getElementById('phoneGroup').classList.add('has-error');
      isValid = false;
    }
    
    return isValid;
  }
  
  // ===================================
  // SOUMETTRE LE FORMULAIRE
  // ===================================
  
  submitBtn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    if (validateForm()) {
      // Désactiver le bouton pour éviter double soumission
      submitBtn.disabled = true;
      submitBtn.textContent = 'Envoi en cours...';
      
      // Soumettre le formulaire
      form.submit();
    } else {
      // Scroll vers la première erreur
      var firstError = document.querySelector('.has-error');
      if (firstError) {
        var y = firstError.getBoundingClientRect().top + window.pageYOffset - 100;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    }
  });
  
  // ===================================
  // ENLEVER L'ERREUR AU CHANGEMENT
  // ===================================
  
  var inputs = document.querySelectorAll('#center, #nom, #email, #phone');
  
  for (var i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener('input', function() {
      var group = this.closest('.form-group');
      if (group && group.classList.contains('has-error')) {
        group.classList.remove('has-error');
      }
    });
    
    inputs[i].addEventListener('change', function() {
      var group = this.closest('.form-group');
      if (group && group.classList.contains('has-error')) {
        group.classList.remove('has-error');
      }
    });
  }
  
  // ===================================
  // PRÉVENIR SUBMIT PAR ENTRÉE
  // ===================================
  
  form.addEventListener('keypress', function(e) {
    if (e.keyCode === 13 && e.target.tagName.toLowerCase() !== 'textarea') {
      e.preventDefault();
      return false;
    }
  });
  
})();
</script>

</body>
</html>
