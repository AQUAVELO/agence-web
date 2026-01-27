<?php require '_settings.php'; ?>
<?php require 'Include/planning_functions.php'; ?>
<?php

// ===== ROUTEUR PHP POUR PRETTY URLs =====
// Gère les URLs comme /vente_formule, /centres/Cannes, etc.
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Retirer le préfixe /aquavelo/ si on est en local
if (strpos($request_uri, '/aquavelo/') === 0) {
    $request_uri = substr($request_uri, strlen('/aquavelo'));
}

// Retirer le slash initial
$request_uri = ltrim($request_uri, '/');

// Router les pretty URLs vers les paramètres GET
$routes = [
    'vente_formule' => ['p' => 'vente_formule'],
    'vente_cryo' => ['p' => 'vente_cryo'],
    'vente_prix' => ['p' => 'vente_prix'],
    'vente_prixprod' => ['p' => 'vente_prixprod'],
    'vente_cryoprod' => ['p' => 'vente_cryoprod'],
    'inscription' => ['p' => 'vente_formule'],
    'centres' => ['p' => 'centers'],
    'aquabiking' => ['p' => 'aquabiking'],
    'contact' => ['p' => 'contact'],
    'free' => ['p' => 'free'],
    'franchise' => ['p' => 'franchise'],
];

// Vérifier les routes exactes
if (isset($routes[$request_uri])) {
    foreach ($routes[$request_uri] as $key => $value) {
        $_GET[$key] = $value;
    }
}

// Vérifier les routes avec paramètres (centres/ville)
if (preg_match('#^centres/([^/]+)/?$#', $request_uri, $matches)) {
    $_GET['p'] = 'page';
    $_GET['city'] = urldecode($matches[1]);
}

// ===== FIN ROUTEUR =====

if (isset($_GET['p']) && is_file(__DIR__ . '/_' . strip_tags($_GET['p']) . '.php')) $page = strip_tags($_GET['p']);
else $page = 'home';

// ===== TITRES ET META DESCRIPTIONS OPTIMISÉS SEO =====

if ($page == 'home') {
    $title = 'Aquabiking Collectif en Piscine avec Coach | 1ère Séance Offerte';
    $meta_description = '🎁 PREMIÈRE SÉANCE OFFERTE ! Cours d\'aquabiking collectif avec coach professionnel. Perdez jusqu\'à 10kg en 3 mois. Brûlez 400-500 calories/séance. Centres ouverts 7j/7 partout en France. Réservez maintenant !';
}

if ($page == 'aquabiking') {
    $title = 'Aquabiking : Bienfaits du Vélo Aquatique pour Maigrir | Résultats Rapides';
    $meta_description = 'Découvrez les bienfaits de l\'aquabiking : perte de poids rapide (jusqu\'à 10kg en 3 mois), réduction de la cellulite, tonification musculaire sans douleur. Sport complet dans l\'eau avec coaching personnalisé. Première séance offerte !';
}

if ($page == 'centers') {
    $title = 'Centres Aquabiking en France : Trouvez le Plus Proche | Aquavelo';
    $meta_description = 'Trouvez votre centre d\'aquabiking Aquavelo près de chez vous. Plus de 17 centres en France avec piscines privées chauffées, coaching professionnel et cours collectifs 7j/7. Réservez votre séance découverte gratuite !';
}

if ($page == 'concept') {
    $title = 'Concept Aquavelo : Ouvrir un Centre d\'Aquabiking Clé en Main';
    $meta_description = 'Rejoignez le réseau Aquavelo et ouvrez votre centre d\'aquabiking. Concept clé en main, formation complète, accompagnement marketing et matériel professionnel inclus. CA : 300-600K€/an.';
}

// ⭐ NOUVEAU : Page Franchise dédiée
if ($page == 'franchise') {
    $title = 'Ouvrir un Centre Aquavelo | Devenez Franchisé - Investissement & Rentabilité';
    $meta_description = 'Ouvrez votre propre centre Aquavelo ! Investissement dès 50 000€, CA de 300-600K€/an, accompagnement complet, formation incluse, 490€/mois de redevances. Téléchargez la brochure gratuite. Contactez Claude Rodriguez : 06 22 64 70 95';
}

if ($page == 'free') {
    $title = 'Séance Découverte Gratuite d\'Aquabiking | Essayez Aquavelo';
    $meta_description = 'Profitez d\'une séance découverte gratuite d\'aquabiking dans votre centre Aquavelo. Testez les bienfaits du vélo aquatique avec un coach sans engagement. Réservez votre créneau dès maintenant !';
}

if ($page == 'partenaires') {
    $title = 'Nos Partenaires Bien-être et Minceur | Aquavelo';
    $meta_description = 'Découvrez nos partenaires spécialisés en bien-être, minceur et remise en forme. Des professionnels sélectionnés pour compléter votre programme aquabiking et maximiser vos résultats.';
}

if ($page == 'conseilminceur') {
    $title = 'Conseils Minceur & Nutrition | Menu du Jour | Aquavelo';
    $meta_description = 'Recettes minceur quotidiennes, menus équilibrés et conseils nutrition pour accompagner vos séances d\'aquabiking. Programme perte de poids complet avec coaching alimentaire gratuit.';
}

if ($page == 'vente_formule') {
    $title = 'Formules et Tarifs Aquabiking | Abonnements à partir de 8€/séance';
    $meta_description = 'Découvrez nos formules d\'abonnement aquabiking : séances à l\'unité, cartes 10 séances ou abonnements mensuels illimités. Tarifs dégressifs à partir de 8€/séance et offres promotionnelles.';
}

if ($page == 'vente_cryo') {
    $title = 'Cryothérapie Corps Entier : Complément Aquabiking | Aquavelo';
    $meta_description = 'Associez cryothérapie et aquabiking pour des résultats optimaux. Récupération musculaire accélérée, brûlage de calories amplifié et réduction de la cellulite maximale.';
}

if ($page == 'vente_cryoprod') {
    $title = 'Produits Cryothérapie et Récupération Sportive | Boutique Aquavelo';
    $meta_description = 'Boutique de produits cryothérapie professionnels : gels rafraîchissants, compléments alimentaires et accessoires pour optimiser votre récupération après l\'aquabiking.';
}

if ($page == 'vente_prix') {
    $title = 'Prix et Tarifs Détaillés Aquabiking | Grille Tarifaire Aquavelo';
    $meta_description = 'Consultez nos prix transparents pour l\'aquabiking : séances individuelles dès 15€, forfaits, abonnements mensuels dès 49€. Tarifs dégressifs et réductions pour étudiants et seniors.';
}

if ($page == 'confirmation') {
    $title = 'Réservation Confirmée | Votre Séance Aquabiking';
    $meta_description = 'Votre réservation de séance d\'aquabiking est confirmée. Retrouvez tous les détails de votre cours et préparez-vous à profiter des bienfaits du vélo aquatique.';
}

if ($page == 'natation') {
    $title = 'Cours de Natation avec Maître-Nageur | Aquavelo';
    $meta_description = 'Cours de natation individuels ou collectifs encadrés par des maîtres-nageurs diplômés. Apprentissage, perfectionnement et natation sportive pour tous âges et tous niveaux.';
}

if ($page == 'inscription_client') {
    $title = 'Inscription Client : Créez Votre Compte | Aquavelo';
    $meta_description = 'Inscrivez-vous sur Aquavelo pour réserver vos séances d\'aquabiking en ligne, suivre votre progression minceur et accéder à vos conseils nutrition personnalisés.';
}

if ($page == 'inscription_nageur') {
    $title = 'Recrutement Maître-Nageur : Rejoignez l\'Équipe | Aquavelo';
    $meta_description = 'Vous êtes maître-nageur diplômé ? Rejoignez notre équipe de coachs aquabiking. Déposez votre candidature et enseignez dans nos centres partout en France. CDI/CDD disponibles.';
}

if ($page == 'contact') {
    $title = 'Contact & Emploi : Contactez-nous | Aquavelo';
    $meta_description = 'Contactez-nous pour toute question sur l\'aquabiking ou consultez nos offres d\'emploi. Équipe disponible par téléphone au 06 22 64 70 95, email ou formulaire en ligne.';
}


#nav
$centers_list_d_cache = $redis->getItem('centers_list');
if (!$centers_list_d_cache->isHit()) {
  $centers_list = $database->prepare('SELECT id, city, TypeAQUAVELO FROM am_centers WHERE online = ? AND aquavelo = ? ORDER BY city ASC');
  $centers_list->execute(array(1, 1));
  $centers_list_d = $centers_list->fetchAll(PDO::FETCH_ASSOC);
  $centers_list_d_cache->set($centers_list_d)->expiresAfter($settings['ttl']);
  $redis->save($centers_list_d_cache);
} else {
  $centers_list_d = $centers_list_d_cache->get();
}

// Récupérer le jour du mois actuel
$jour_du_mois = date("d");

// Préparer la requête pour récupérer les champs spécifiques de la table menu
$menu_querym = $database->prepare('SELECT id, day_number, total_calories, petit_dejeuner_menu, petit_dejeuner_recette, photo_pet_dej, repas_midi_menu, repas_midi_recette, photo_repas_midi, souper_menu, souper_recette, photo_souper, collation_menu, collation_recette, photo_collation FROM menu WHERE day_number = :jour_du_mois');
$menu_querym->bindParam(':jour_du_mois', $jour_du_mois, PDO::PARAM_INT);
$menu_querym->execute();
$menu_datam = $menu_querym->fetch(PDO::FETCH_ASSOC);

// Requête pour récupérer tous les menus
$all_menus_query = $database->prepare('SELECT day_number, petit_dejeuner_menu, repas_midi_menu, souper_menu, collation_menu, petit_dejeuner_recette, repas_midi_recette, souper_recette, collation_recette FROM menu ORDER BY day_number ASC');
$all_menus_query->execute();
$all_menus = $all_menus_query->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer tous les articles
$news_querys = $database->prepare('SELECT news, photo, titre FROM article');
$news_querys->execute();
$news_datas = $news_querys->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tous les prospects (clients)
$clients_query = $database->prepare('SELECT id, nom, prenom, tel, email, adresse, ville, dept, activites, besoin FROM client ORDER BY id DESC');
$clients_query->execute();
$clients_data = $clients_query->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tous les maîtres-nageurs (nageurs)
$nageurs_query = $database->prepare('SELECT id, nom, prenom, tel, photo, ville, dept, diplome, presentation, prix, dispo, preference, email FROM nageur ORDER BY id DESC');
$nageurs_query->execute();
$nageurs_data = $nageurs_query->fetchAll(PDO::FETCH_ASSOC);

// Préparer la requête pour récupérer les champs Nom et Prenom en fonction de l'email
$user_query = $database->prepare('SELECT Nom, Prenom FROM mensurations WHERE email = :email');
$user_query->bindParam(':email', $email, PDO::PARAM_STR);
$user_query->execute();
$user_data = $user_query->fetch(PDO::FETCH_ASSOC);

#home
if ($page == "home") {
  $centers_last_d_cache = $redis->getItem('centers_last');
  if (!$centers_last_d_cache->isHit()) {
    $centers_last = $database->prepare('SELECT c.id, c.city, c.country, c.TypeAQUAVELO, d.nom AS department_nom FROM am_centers c INNER JOIN departements d ON d.id = c.department WHERE c.online = ? AND c.aquavelo = ? ORDER BY c.id DESC');
    $centers_last->execute(array(1, 1));
    $centers_last_d = $centers_last->fetchAll(PDO::FETCH_ASSOC);
    $centers_last_d_cache->set($centers_last_d)->expiresAfter($settings['ttl']);
  } else {
    $centers_last_d = $centers_last_d_cache->get();
  }
}

#page
if (isset($_GET['city'])) {
  $city = strip_tags($_GET['city']);

  $row_center_cache = $redis->getItem($city);
  if (!$row_center_cache->isHit()) {
    $center = $database->prepare('SELECT id FROM am_centers WHERE city = ? AND online = ? AND aquavelo = ?');
    $center->execute(array($city, 1, 1));
    $secure = $center->rowCount();
    if ($secure != 0) {
      $center = $database->prepare('SELECT c.*, d.nom AS department_nom, r.nom AS region_nom FROM am_centers c INNER JOIN departements d ON d.id = c.department INNER JOIN regions r ON r.id = c.region WHERE c.city = ? AND c.online = ? AND c.aquavelo = ?');
      $center->execute(array($city, 1, 1));
      $row_center = $center->fetch();
      $row_center_cache->set($row_center)->expiresAfter($settings['ttl']);
    } else {
      return header('location: ./');
    }
  } else {
    $row_center = $row_center_cache->get();
  }
  $region = $row_center['region_nom'];
  $department = $row_center['department_nom'];
  $title = "Aquabiking $city | 1ère Séance OFFERTE - Centre Aquavelo $department";
  
  $meta_description = "🎁 Première séance OFFERTE au centre Aquavelo de $city ($department) ! Cours d'aquabiking avec coach, piscine privée chauffée. Perdez du poids rapidement. Réservez en ligne !";
  $meta_keywords = "aquavelo, aquabiking, waterbike, aquabike, aquagym, anti cellulite, amincissement, perte de poids, aquabiking $city, centre aquabiking $city, cours aquabike $city, piscine $city, coach aquabiking $city";
}

?>
<!DOCTYPE html>
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="fr"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="fr">
<!--<![endif]-->

<head>
  <meta charset="UTF-8">
  <meta name="description" content="<?= $meta_description ?? 'Cours d\'aquabiking collectif en piscine animé par un coach. Éliminez votre cellulite et affinez votre silhouette rapidement sur toutes les parties du corps.'; ?>">
  <meta name="keywords" content="aquavelo, aquabiking, waterbike, aquabike, aquagym, anti cellulite, amincissement, perte de poids, kg en moins, affinement rapide de la silhouette<?php if(isset($city)) echo ", aquabiking $city, centre aquabiking $city"; ?>">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <meta name="author" content="Aquavelo">
  
  <!-- Balise Canonical SEO -->
  <?php 
  $canonical_url = 'https://www.aquavelo.com';
  if (isset($city)) {
      $canonical_url .= '/centres/' . urlencode($city);
  } elseif ($page != 'home') {
      $canonical_url .= '/?p=' . urlencode($page);
  }
  ?>
  <link rel="canonical" href="<?= $canonical_url; ?>">
  
  <link rel="shortcut icon" href="<?= BASE_PATH ?>favicon.ico" type="image/x-icon">
  
  <!-- Hreflang pour SEO international -->
  <link rel="alternate" hreflang="fr-FR" href="<?= $canonical_url; ?>">
  <link rel="alternate" hreflang="x-default" href="<?= $canonical_url; ?>">
  
  <!-- Preconnect pour optimiser les performances -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://www.googletagmanager.com">
  
  <title><?= $title; ?> | Aquavelo</title>
  <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>css/animate.css?v=1">
  <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>css/bootstrap.css?v=1">
  <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>css/style.css?v=1">
  <link rel="stylesheet" type="text/css" href="<?= BASE_PATH ?>css/planning.css?v=1">
  <link rel="icon" href="<?= BASE_PATH ?>images/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Courgette&display=swap" rel="stylesheet">
  <style type="text/css">
    body,
    td,
    th {
      font-family: 'Open Sans', sans-serif;
    }
    
    /* ⭐ NOUVEAU : Styles pour les CTA améliorés */
    
    /* Bouton Réserver dans le menu */
    .cta-nav .btn-reserve-nav {
      background: linear-gradient(135deg, #ff6b35, #f7931e) !important;
      color: white !important;
      animation: pulse-glow 2s ease-in-out infinite;
      padding: 10px 20px !important;
      border-radius: 25px !important;
      margin-left: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0, 168, 204, 0.3);
    }

    .cta-nav .btn-reserve-nav:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 168, 204, 0.5);
    }

    /* Bouton Ouvrir un Centre dans le menu */
    .cta-franchise .btn-franchise-nav {
      background: linear-gradient(135deg, #ff9800, #f57c00) !important;
      color: white !important;
      padding: 10px 20px !important;
      border-radius: 25px !important;
      margin-left: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(255, 152, 0, 0.3);
    }

    .cta-franchise .btn-franchise-nav:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 152, 0, 0.5);
    }

    /* Animation pulse pour CTA */
    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4); }
      50% { box-shadow: 0 4px 25px rgba(255, 107, 53, 0.7); }
    }

    @keyframes pulse-ring {
      0% { transform: scale(0.8); opacity: 1; }
      100% { transform: scale(2); opacity: 0; }
    }

    /* Point pulsant sur bouton flottant */
    .pulse-dot {
      position: absolute;
      top: -5px;
      right: -5px;
      width: 15px;
      height: 15px;
      background: #ff3b30;
      border-radius: 50%;
      animation: pulse-ring 1.5s ease-out infinite;
    }
    .pulse-dot::after {
      content: '';
      position: absolute;
      top: 3px;
      left: 3px;
      width: 9px;
      height: 9px;
      background: #ff3b30;
      border-radius: 50%;
    }

    /* Bouton flottant Réserver */
    #floating-booking-btn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 999;
    }

    #floating-booking-btn a {
      position: relative;
      display: flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(135deg, #00d4ff, #00a8cc);
      color: white;
      padding: 15px 25px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      box-shadow: 0 5px 20px rgba(0, 168, 204, 0.4);
      transition: all 0.3s ease;
    }

    #floating-booking-btn a:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(0, 168, 204, 0.6);
    }

    #floating-booking-btn i {
      font-size: 1.2rem;
    }

    /* Bouton téléphone flottant (mobile) */
    #floating-phone-btn {
      position: fixed;
      bottom: 100px;
      right: 30px;
      z-index: 999;
    }

    #floating-phone-btn a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 60px;
      height: 60px;
      background: #4CAF50;
      color: white;
      border-radius: 50%;
      box-shadow: 0 5px 20px rgba(76, 175, 80, 0.4);
      animation: pulse 2s infinite;
      text-decoration: none;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.1);
      }
    }

    #floating-phone-btn i {
      font-size: 1.5rem;
    }

    /* Pop-up Franchise */
    .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      z-index: 9998;
    }

    .popup-content {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 40px;
      border-radius: 15px;
      max-width: 500px;
      width: 90%;
      z-index: 9999;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .popup-close {
      position: absolute;
      top: 15px;
      right: 15px;
      background: none;
      border: none;
      font-size: 2rem;
      cursor: pointer;
      color: #999;
      line-height: 1;
    }

    .popup-close:hover {
      color: #333;
    }

    .popup-content h2 {
      color: #00a8cc;
      margin-bottom: 15px;
      font-size: 1.8rem;
    }

    .popup-content p {
      color: #666;
      margin-bottom: 20px;
      font-size: 1.1rem;
    }

    .popup-content ul {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }

    .popup-content ul li {
      padding: 8px 0;
      color: #666;
      font-size: 1rem;
    }

    .btn-franchise-popup {
      display: block;
      background: linear-gradient(135deg, #ff9800, #f57c00);
      color: white;
      padding: 15px 30px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      text-align: center;
      margin: 20px 0 10px;
      transition: all 0.3s ease;
    }

    .btn-franchise-popup:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 152, 0, 0.4);
      color: white;
      text-decoration: none;
    }

    .btn-later {
      display: block;
      width: 100%;
      background: none;
      border: none;
      color: #999;
      cursor: pointer;
      padding: 10px;
      font-size: 0.9rem;
    }

    .btn-later:hover {
      color: #666;
    }

    /* Mobile responsive */
    @media (max-width: 991px) {
      .cta-nav .btn-reserve-nav,
      .cta-franchise .btn-franchise-nav {
        display: block;
        margin: 10px 15px;
        text-align: center;
      }
    }

    @media (max-width: 768px) {
      #floating-booking-btn {
        bottom: 20px;
        right: 20px;
      }
      
      #floating-booking-btn a {
        padding: 12px 20px;
        font-size: 0.9rem;
      }

      #floating-phone-btn {
        bottom: 90px;
        right: 20px;
      }

      .popup-content {
        padding: 30px 20px;
      }

      .popup-content h2 {
        font-size: 1.5rem;
      }
    }
  </style>
  
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TS8N7KG');</script>
<!-- End Google Tag Manager -->
  
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-26LRGBE9X2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-26LRGBE9X2');
</script>
  
  <script src="<?= BASE_PATH ?>js/modernizr.custom.js"></script>

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="<?= BASE_PATH ?>bootstrap/js/html5shiv.js"></script>
  <script src="<?= BASE_PATH ?>bootstrap/js/respond.min.js"></script>
  <![endif]-->
  
  <script>
  !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on","addSourceMiddleware","addIntegrationMiddleware","setAnonymousId","addDestinationMiddleware"];analytics.factory=function(e){return function(){var t=Array.prototype.slice.call(arguments);t.unshift(e);analytics.push(t);return analytics}};for(var e=0;e<analytics.methods.length;e++){var key=analytics.methods[e];analytics[key]=analytics.factory(key)}analytics.load=function(key,e){var t=document.createElement("script");t.type="text/javascript";t.async=!0;t.src="https://cdn.segment.com/analytics.js/v1/" + key + "/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);analytics._loadOptions=e};analytics.SNIPPET_VERSION="4.13.1";
  analytics.load("NbkSIsYp06eaohctPba8atbY90rGaxQs");
  analytics.page();
  }}();
</script>

  <!-- ========== DONNÉES STRUCTURÉES JSON-LD POUR SEO ========== -->
  
  <!-- Organization Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Aquavelo",
    "alternateName": "Aquavélo",
    "url": "https://www.aquavelo.com",
    "logo": "https://www.aquavelo.com/images/content/logo.png",
    "description": "Réseau de centres d'aquabiking en France proposant des cours collectifs en piscine avec coach professionnel pour perdre du poids et tonifier votre silhouette.",
    "foundingDate": "2014",
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+33-6-22-64-70-95",
      "contactType": "customer service",
      "areaServed": "FR",
      "availableLanguage": "French"
    },
    "sameAs": [
      "https://www.facebook.com/aquaveloCannes",
      "https://www.instagram.com/aquaveloCannes",
      "https://twitter.com/AquaveloNice"
    ],
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "60 avenue du Docteur Picaud",
      "addressLocality": "Cannes",
      "postalCode": "06150",
      "addressCountry": "FR"
    }
  }
  </script>

  <!-- WebSite Schema avec SearchAction -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Aquavelo",
    "url": "https://www.aquavelo.com",
    "potentialAction": {
      "@type": "SearchAction",
      "target": {
        "@type": "EntryPoint",
        "urlTemplate": "https://www.aquavelo.com/centres/{search_term}"
      },
      "query-input": "required name=search_term"
    }
  }
  </script>

  <?php if ($page == 'home') : ?>
  <!-- FAQ Schema pour la page d'accueil -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "Qu'est-ce que l'aquabiking ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "L'aquabiking est un sport aquatique qui consiste à pédaler sur un vélo immergé dans l'eau. Cette activité permet de brûler 400 à 600 calories par séance, de réduire la cellulite et de tonifier les muscles sans impact sur les articulations."
        }
      },
      {
        "@type": "Question",
        "name": "Combien coûte une séance d'aquabiking ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Les séances d'aquabiking chez Aquavelo débutent à partir de 8€ la séance avec nos formules d'abonnement. Nous proposons également une première séance découverte gratuite."
        }
      },
      {
        "@type": "Question",
        "name": "Faut-il savoir nager pour faire de l'aquabiking ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Non, il n'est pas nécessaire de savoir nager pour pratiquer l'aquabiking. Le vélo est immergé dans un bassin où l'eau arrive à la taille. Vous restez assis sur le vélo en toute sécurité."
        }
      },
      {
        "@type": "Question",
        "name": "L'aquabiking est-il efficace pour maigrir ?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Oui, l'aquabiking est très efficace pour la perte de poids. Une séance de 45 minutes permet de brûler entre 400 et 600 calories. Combiné à une alimentation équilibrée, vous pouvez perdre jusqu'à 10kg en 3 mois avec 2 à 3 séances par semaine."
        }
      }
    ]
  }
  </script>
  <?php endif; ?>

  <?php if (isset($city) && isset($row_center)) : ?>
  <!-- LocalBusiness Schema pour les pages de centres -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SportsActivityLocation",
    "name": "Aquavelo <?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>",
    "description": "Centre d'aquabiking à <?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?> proposant des cours collectifs avec coach professionnel. Première séance découverte gratuite.",
    "url": "<?= $canonical_url; ?>",
    "image": "https://www.aquavelo.com/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/1.jpg",
    "telephone": "<?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
    "priceRange": "€€",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
      "addressLocality": "<?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>",
      "addressRegion": "<?= htmlspecialchars($row_center['region_nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>",
      "addressCountry": "FR"
    },
    <?php if (!empty($row_center['latitude']) && !empty($row_center['longitude'])) : ?>
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": "<?= htmlspecialchars($row_center['latitude'], ENT_QUOTES, 'UTF-8'); ?>",
      "longitude": "<?= htmlspecialchars($row_center['longitude'], ENT_QUOTES, 'UTF-8'); ?>"
    },
    <?php endif; ?>
    "openingHoursSpecification": {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
      "opens": "08:00",
      "closes": "20:00"
    },
    "hasOfferCatalog": {
      "@type": "OfferCatalog",
      "name": "Services Aquavelo",
      "itemListElement": [
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Séance découverte gratuite",
            "description": "Essayez l'aquabiking gratuitement pendant 45 minutes avec coaching"
          },
          "price": "0",
          "priceCurrency": "EUR"
        },
        {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Cours d'aquabiking",
            "description": "Séance d'aquabiking de 30 ou 45 minutes avec coach professionnel"
          },
          "priceSpecification": {
            "@type": "PriceSpecification",
            "price": "8",
            "priceCurrency": "EUR",
            "description": "À partir de 8€ la séance avec abonnement"
          }
        }
      ]
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.8",
      "reviewCount": "127",
      "bestRating": "5",
      "worstRating": "1"
    }
  }
  </script>
  
  <!-- BreadcrumbList Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Accueil",
        "item": "https://www.aquavelo.com/"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Centres",
        "item": "https://www.aquavelo.com/centres"
      },
      {
        "@type": "ListItem",
        "position": 3,
        "name": "<?= htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>",
        "item": "<?= $canonical_url; ?>"
      }
    ]
  }
  </script>
  <?php endif; ?>
  
  <!-- ========== FIN DONNÉES STRUCTURÉES ========== -->

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?= $canonical_url; ?>" />
  <meta property="og:title" content="<?= htmlspecialchars($title ?? 'Aquabiking Collectif en Piscine | Aquavelo', ENT_QUOTES, 'UTF-8'); ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($meta_description ?? 'Cours d\'aquabiking collectif en piscine animé par un coach ouvert 7 jours sur 7. Éliminez votre cellulite et affinez votre silhouette.', ENT_QUOTES, 'UTF-8'); ?>" />
  <meta property="og:image" content="https://www.aquavelo.com/images/aquavelo.webp" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:site_name" content="Aquavelo" />
  <meta property="og:locale" content="fr_FR" />
  
  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:site" content="@AquaveloNice" />
  <meta name="twitter:title" content="<?= htmlspecialchars($title ?? 'Aquabiking Collectif en Piscine | Aquavelo', ENT_QUOTES, 'UTF-8'); ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($meta_description ?? 'Cours d\'aquabiking collectif en piscine animé par un coach. Éliminez votre cellulite et affinez votre silhouette.', ENT_QUOTES, 'UTF-8'); ?>" />
  <meta name="twitter:image" content="https://www.aquavelo.com/images/aquavelo.webp" />
  
<?php if (isset($row_center['id'])) : ?>
  <?php if ($row_center['id'] == 305) : // Cannes ?>
    <link rel="manifest" href="<?= BASE_PATH ?>manifest-cannes.json">
    <link rel="icon" type="image/png" sizes="92x92" href="<?= BASE_PATH ?>images/Aquavelo_Icon_C.png">
    <link rel="apple-touch-icon" href="<?= BASE_PATH ?>images/Aquavelo_Icon_192_C.png">
  <?php elseif ($row_center['id'] == 253) : // Antibes ?>
    <link rel="manifest" href="<?= BASE_PATH ?>manifest-antibes.json">
    <link rel="icon" type="image/png" sizes="92x92" href="<?= BASE_PATH ?>images/Aquavelo_Icon_A.png">
    <link rel="apple-touch-icon" href="<?= BASE_PATH ?>images/Aquavelo_Icon_192_A.png">
  <?php elseif ($_SERVER['REQUEST_URI'] === '/conseilminceur') : // Conseil Minceur ?>
    <link rel="manifest" href="<?= BASE_PATH ?>manifest-conseilminceur.json">
    <link rel="icon" type="image/png" sizes="92x92" href="<?= BASE_PATH ?>images/Aquavelo_Icon_M.png">
    <link rel="apple-touch-icon" href="<?= BASE_PATH ?>images/Aquavelo_Icon_192_M.png">
  <?php endif; ?>
  <meta name="theme-color" content="#00ACDC">
<?php endif; ?>

<!-- Styles pour la bannière RGPD -->
<style>
  #cookie-banner {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background: rgba(0, 0, 0, 0.85);
      color: white;
      text-align: center;
      padding: 15px;
      font-size: 14px;
      display: none;
      z-index: 1000;
  }
  #cookie-banner button {
      margin: 5px;
      padding: 10px 15px;
      border: none;
      cursor: pointer;
      font-weight: bold;
  }
  #accept-cookies {
      background: #4CAF50;
      color: white;
  }
  #reject-cookies {
      background: #D32F2F;
      color: white;
  }
</style>

</head>

<body class="withAnimation">

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TS8N7KG"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  
  <div id="boxedWrapper">

    <!-- navbar -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
          <a class="navbar-brand" href="<?= BASE_PATH ?>"><img src="<?= BASE_PATH ?>images/content/logo.png" alt="Aquabiking collectif en piscine - Aquavelo" width="180" height="60"></a> 
        </div>
        <div class="navbar-collapse collapse">
          <form class="pull-right header-search" role="form" style="display:none;">
            <fieldset>
              <div class="container">
                <div class="form-group">
                  <input type="text" class="form-control" placeholder="Type to search...">
                </div>
                <button type="submit"><i class="fa fa-search"></i></button>
              </div>
            </fieldset>
          </form>
          <a href="#" id="showHeaderSearch" class="hidden-xs"><i class="fa fa-search"></i></a>
            <ul class="nav navbar-nav navbar-right">
              <li<?php if ($page == 'home') echo ' class="active"'; ?>> 
                <a href="<?= BASE_PATH ?>">Accueil</a> 
              </li>
              
              <li class="dropdown<?php if ($page == 'aquabiking') echo ' active'; ?>"> 
                <a href="<?= BASE_PATH ?>?p=aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Aquabiking</a>
                <ul class="dropdown-menu">
                  <li><a href="<?= BASE_PATH ?>?p=aquabiking">Le vélo dans l'eau</a></li>
                  <li><a href="<?= BASE_PATH ?>?p=aquabiking#bienfaits">Les bienfaits</a></li>
                  <li><a href="<?= BASE_PATH ?>?p=aquabiking#questions">Vos questions</a></li>
                </ul>
              </li>
              
              <li class="dropdown<?php if ($page == 'centres') echo ' active'; ?>"> 
                <a href="<?= BASE_PATH ?>?p=centers" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Centres</a>
                <ul class="dropdown-menu">
                  <?php foreach ($centers_list_d as &$row_centers_list) { ?>
                    <li><a href="<?= BASE_PATH ?>?p=page&city=<?= $row_centers_list['city']; ?>" title="Aquabiking à <?= $row_centers_list['city']; ?>"><?= $row_centers_list['city']; ?></a></li>
                  <?php } ?>
                </ul>
              </li>
              
              <li class="dropdown<?php if ($page == 'concept' || $page == 'franchise') echo ' active'; ?>"> 
                <a href="<?= BASE_PATH ?>?p=franchise" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Concept</a>
                <ul class="dropdown-menu">
                  <li><a href="<?= BASE_PATH ?>?p=franchise">Notre Concept</a></li>
                  <li class="divider"></li>
                  <li><a href="<?= BASE_PATH ?>?p=franchise" style="color: #ff9800; font-weight: 600;">
                    <i class="fa fa-briefcase"></i> Ouvrir un Centre
                  </a></li>
                </ul>
              </li>

              <li> 
                <a href="https://aquacoach.fr" target="_blank" rel="noopener">Natation</a>
              </li>

              <li <?php if ($page == 'conseilminceur') echo ' class="active"'; ?> class="dropdown"> 
                <a href="<?= BASE_PATH ?>?p=conseilminceur" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Amincissement</a>
                <ul class="dropdown-menu">
                  <li><a href="<?= BASE_PATH ?>?p=conseilminceur">Amincissement</a></li>
                </ul>
              </li>

              <li <?php if ($page == 'contact') echo ' class="active"'; ?> class="dropdown"> 
                <a href="<?= BASE_PATH ?>?p=contact" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Contact</a>
                <ul class="dropdown-menu">
                  <li><a href="<?= BASE_PATH ?>?p=contact#emploi">Emploi</a></li>
                  <li><a href="<?= BASE_PATH ?>?p=contact#contact">Contactez-nous</a></li>
                </ul>
              </li>
              
              <!-- ⭐ NOUVEAU : Bouton Réserver visible -->
                
              <!-- CTA Séance gratuite optimisé -->
              <li class="cta-nav"> 
                  <a href="<?= BASE_PATH ?>?p=free" class="btn-reserve-nav">
                     <i class="fa fa-gift"></i> 🎁 SÉANCE GRATUITE
                  </a>
              </li>
              
              <!-- ⭐ NOUVEAU : Bouton Ouvrir un Centre visible -->
              <li class="cta-franchise hidden-sm"> 
                <a href="<?= BASE_PATH ?>?p=franchise" class="btn-franchise-nav">
                  <i class="fa fa-briefcase"></i> Ouvrir un Centre
                </a> 
              </li>
            </ul>
        </div>
      </div>
    </nav>
    <!-- / navbar -->
    
    <?php include '_' . $page . '.php'; ?>

    <section class="content-area prefooter">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <article class="table-content animated" data-fx="flipInY">
              <section class="table-row">
                <div class="table-cell">
                  <h2 class="widget-title">Suivez-nous</h2>
                </div>
                <div class="table-cell">
                  <ul class="socialIcons bigIcons">
                    <li><a href="https://www.facebook.com/aquaveloCannes/?locale=fr_FR" data-toggle="tooltip" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="https://www.instagram.com/aquaveloCannes/?locale=fr_FR" data-toggle="tooltip" title="Instagram"><i class="fa fa-instagram"></i></a></li>
                    <li><a href="http://twitter.com/AquaveloNice" data-toggle="tooltip" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                  </ul>
                </div>
              </section>
            </article>
          </div>
          <div class="col-md-6">
            <div class="newsletterForm">
              <div class="successMessage alert alert-success alert-dismissable" style="display: none">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Abonnement confirmé. 
              </div>
              <div class="errorMessage alert alert-danger alert-dismissable" style="display: none">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Une erreur est survenue. 
              </div>
              <form class="liveForm" role="form" action="<?= BASE_PATH ?>form/send.php" method="post" data-email-subject="Newsletter Form" data-show-errors="true" data-hide-form="true">
                <fieldset>
                  <article class="table-content animated" data-fx="flipInY">
                    <section class="table-row">
                      <div class="table-cell">
                        <h2 class="widget-title">Recevoir la newsletter</h2>
                      </div>
                      <div class="table-cell">
                        <label class="sr-only">Adresse e-mail</label>
                        <input type="email" name="field[]" class="form-control" placeholder="Adresse e-mail">
                      </div>
                      <div class="table-cell">
                        <input type="submit" class="btn btn-primary" value="S'abonner">
                      </div>
                    </section>
                  </article>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer>
      <div class="container mainfooter">
        <div class="row">
          <aside class="col-md-3 widget"> 
            <img src="<?= BASE_PATH ?>images/content/logo-footer.png" alt="centre Aquavélo"> <br><br>
            <h1 style="font-size: 12px;">Aquabiking collectif en piscine</h1>
            <h1 class="darker">Éliminez votre cellulite et affinez votre silhouette</h1>
            <p class="darker">&copy; 2014-<?= date('Y'); ?></p>
          </aside>
          <aside class="col-md-3 widget">
            <h2 class="widget-title">Contactez-nous</h2>
            <a href="mailto:claude@alesiaminceur.com" class="larger">claude@alesiaminceur.com</a>
            <p>60 avenue du Docteur Picaud<br>06150 Cannes</p>
          
          </aside>
          <aside class="col-md-3 widget">
            <h2 class="widget-title">Liens Rapides</h2>
            <ul>
              <li><a href="<?= BASE_PATH ?>?p=free">Séance Découverte Gratuite</a></li>
              <li><a href="<?= BASE_PATH ?>?p=centers">Trouver un Centre</a></li>
              <li><a href="<?= BASE_PATH ?>?p=franchise" style="color: #ff9800; font-weight: 600;">Ouvrir un Centre</a></li>
              <li><a href="<?= BASE_PATH ?>?p=contact">Contact</a></li>
            </ul>
          </aside>
          <aside class="col-md-3 widget">
            <h3 class="widget-title">Nos Centres</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 5px;">
              <a href="<?= BASE_PATH ?>?p=page&city=cannes" title="Centre Aquavelo Cannes" style="display: block; width: 80px; height: 60px; overflow: hidden; border-radius: 5px;">
                <img src="<?= BASE_PATH ?>images/Cannes1.jpg" alt="Centre Aquavelo Cannes" style="width: 100%; height: 100%; object-fit: cover;">
              </a>
              <a href="<?= BASE_PATH ?>?p=page&city=antibes" title="Centre Aquavelo Antibes" style="display: block; width: 80px; height: 60px; overflow: hidden; border-radius: 5px;">
                <img src="<?= BASE_PATH ?>images/content/home-v1-slider-03.webp" alt="Centre Aquavelo Antibes" style="width: 100%; height: 100%; object-fit: cover;">
              </a>
              <a href="<?= BASE_PATH ?>?p=page&city=nice" title="Centre Aquavelo Nice" style="display: block; width: 80px; height: 60px; overflow: hidden; border-radius: 5px;">
                <img src="<?= BASE_PATH ?>images/center_179/1.jpg" alt="Centre Aquavelo Nice" style="width: 100%; height: 100%; object-fit: cover;">
              </a>
              <a href="<?= BASE_PATH ?>?p=free" title="Séance découverte gratuite" style="display: flex; align-items: center; justify-content: center; width: 80px; height: 60px; background: linear-gradient(135deg, #ff6b35, #f7931e); border-radius: 5px; color: #fff; font-size: 11px; text-align: center; text-decoration: none; font-weight: 600;">
                Essai<br>Gratuit
              </a>
            </div>
          </aside>
        </div>
      </div>
    </footer>
  </div>
  <!-- boxedWrapper -->

  <!-- ⭐ Bouton réservation flottant -->
    <div id="floating-booking-btn">
      <a href="<?= BASE_PATH ?>?p=free">
        <span class="pulse-dot"></span>
        <i class="fa fa-gift"></i> 1ère SÉANCE OFFERTE
        <span>🎁 SÉANCE GRATUITE</span>
      </a>
    </div>

 

  <!-- ⭐ NOUVEAU : Pop-up Franchise -->
  <div id="franchise-popup" style="display: none;">
    <div class="popup-overlay"></div>
    <div class="popup-content">
      <button class="popup-close">&times;</button>
      <h2>💼 Entrepreneur ?</h2>
      <p>Découvrez comment ouvrir votre propre centre Aquavelo</p>
      <ul>
        <li>✓ Investissement dès 50 000€</li>
        <li>✓ CA : 300-600K€ par an</li>
        <li>✓ Formation complète incluse</li>
        <li>✓ Accompagnement marketing</li>
        <li>✓ Redevances : 490€/mois seulement</li>
      </ul>
      <a href="<?= BASE_PATH ?>franchise" class="btn-franchise-popup">En savoir plus</a>
      <button class="btn-later">Plus tard</button>
    </div>
  </div>

  <a href="#" id="toTop"><i class="fa fa-angle-up"></i></a>
  
  <script src="<?= BASE_PATH ?>js/jquery.min.js"></script>
  <script src="<?= BASE_PATH ?>bootstrap/js/bootstrap.min.js"></script>
  <script src="<?= BASE_PATH ?>js/detectmobilebrowser.js"></script>
  <script src="<?= BASE_PATH ?>js/gmap3.min.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.appear.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.isotope.min.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.ba-bbq.min.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.countTo.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.fitvids.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.flexslider-min.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.magnific-popup.min.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.mb.YTPlayer.js"></script>
  <script src="<?= BASE_PATH ?>js/jquery.placeholder.min.js"></script>
  <script src="<?= BASE_PATH ?>js/retina-1.1.0.min.js"></script>
  <script src="<?= BASE_PATH ?>js/timeline/js/storyjs-embed.js"></script>
  <script src="<?= BASE_PATH ?>form/js/form.js"></script>
  <script src="<?= BASE_PATH ?>js/main.js"></script>
  
  <script>
    document.addEventListener("DOMContentLoaded", function(event) {
      document.querySelectorAll('img').forEach(function(img) {
        img.onerror = function() {
          this.src = '<?= BASE_PATH ?>images/center_179/1.jpg';
        };
      })
    })
  </script>
  
  <script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('<?= BASE_PATH ?>service-worker.js')
    .then(function(registration) {
      console.log('Service Worker enregistré avec succès:', registration);
    })
    .catch(function(error) {
      console.log("Échec de l'enregistrement du Service Worker:", error);
    });
  }
  </script>

  <!-- ⭐ NOUVEAU : Script Pop-up Franchise -->
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    // Tracking des pages vues
    let pageViews = parseInt(localStorage.getItem('pageViews') || '0');
    pageViews++;
    localStorage.setItem('pageViews', pageViews);

    const popupShown = localStorage.getItem('franchisePopupShown');
    const popup = document.getElementById('franchise-popup');

    // Afficher le pop-up si :
    // - Pas encore montré
    // - ET (3+ pages vues OU vient de Google OU plus de 45 secondes sur le site)
    if (!popupShown) {
      const fromGoogle = document.referrer.includes('google');
      
      if (pageViews >= 3 || fromGoogle) {
        setTimeout(function() {
          popup.style.display = 'block';
          
          // Analytics event
          if (typeof gtag !== 'undefined') {
            gtag('event', 'popup_franchise_shown', {
              'event_category': 'engagement',
              'event_label': 'franchise_popup'
            });
          }
        }, fromGoogle ? 20000 : 45000); // 20s si Google, 45s sinon
      }
    }

    // Fermer le pop-up
    document.querySelector('.popup-close').addEventListener('click', function() {
      popup.style.display = 'none';
      localStorage.setItem('franchisePopupShown', 'true');
    });

    document.querySelector('.popup-overlay').addEventListener('click', function() {
      popup.style.display = 'none';
      localStorage.setItem('franchisePopupShown', 'true');
    });

    document.querySelector('.btn-later').addEventListener('click', function() {
      popup.style.display = 'none';
      // Ne pas marquer comme "shown" pour qu'il puisse réapparaître plus tard
    });

    // Track clic sur bouton franchise
    document.querySelector('.btn-franchise-popup').addEventListener('click', function() {
      if (typeof gtag !== 'undefined') {
        gtag('event', 'click_franchise_popup', {
          'event_category': 'conversion',
          'event_label': 'franchise_interest'
        });
      }
    });

    // Track clics sur boutons flottants
    document.querySelector('#floating-booking-btn a').addEventListener('click', function() {
      if (typeof gtag !== 'undefined') {
        gtag('event', 'click_floating_booking', {
          'event_category': 'conversion',
          'event_label': 'floating_button'
        });
      }
    });
  });
  </script>

  <!-- Script RGPD de gestion des cookies -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        const banner = document.createElement("div");
        banner.id = "cookie-banner";
        banner.innerHTML = `
            <p>Nous utilisons des cookies pour améliorer votre expérience. En acceptant, vous nous autorisez à collecter des statistiques de navigation.</p>
            <button id="accept-cookies">Accepter</button>
            <button id="reject-cookies">Refuser</button>
        `;

        document.body.appendChild(banner);

        const acceptBtn = document.getElementById("accept-cookies");
        const rejectBtn = document.getElementById("reject-cookies");

        if (!localStorage.getItem("cookieConsent")) {
            banner.style.display = "block";
        }

        acceptBtn.addEventListener("click", function () {
            localStorage.setItem("cookieConsent", "accepted");
            banner.style.display = "none";
            loadAnalytics();
        });

        rejectBtn.addEventListener("click", function () {
            localStorage.setItem("cookieConsent", "rejected");
            banner.style.display = "none";
        });

        if (localStorage.getItem("cookieConsent") === "accepted") {
            loadAnalytics();
        }
    });

    function loadAnalytics() {
        // Analytics déjà chargé dans le head
        console.log('Analytics accepté');
    }
  </script>

</body>
</html>
