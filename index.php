<?php require '_settings.php'; ?>
<?php

if (isset($_GET['p']) && is_file('_' . strip_tags($_GET['p']) . '.php')) $page = strip_tags($_GET['p']);
else $page = 'home';

if ($page == 'home') $title = 'Aquabiking collectif en piscine';
if ($page == 'aquabiking') $title = 'Aquabiking : les bienfaits du vélo dans l\'eau';
if ($page == 'centers') $title = 'Centres d\'aquabiking';
if ($page == 'concept') $title = 'Concept Aquavelo';
if ($page == 'free') $title = 'Séance découverte';
if ($page == 'partenaires') $title = 'Partenaires';
if ($page == 'conseilminceur') $title = 'Articles Minceur';
if ($page == 'contact') $title = 'Contactez-nous';


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

// Préparer la requête pour récupérer les champs petit_dejeuner et repas_midi en fonction du jour du mois
$menu_query = $database->prepare('SELECT petit_dejeuner, repas_midi, souper, calories, collation, photo_pd, photo_m, photo_s FROM menus WHERE jour_du_mois = :jour_du_mois');
$menu_query->bindParam(':jour_du_mois', $jour_du_mois, PDO::PARAM_INT);
$menu_query->execute();
$menu_data = $menu_query->fetch(PDO::FETCH_ASSOC);

// Requête pour récupérer tous les articles
$news_querys = $database->prepare('SELECT news, photo, titre FROM article');
$news_querys->execute();
$news_datas = $news_querys->fetchAll(PDO::FETCH_ASSOC); // Récupère toutes les lignes




// NOUVEAU



// stop

// Préparer la requête pour récupérer les champs Nom et Prenom en fonction de l'email
$user_query = $database->prepare('SELECT Nom, Prenom FROM mensurations WHERE email = :email');
$user_query->bindParam(':email', $email, PDO::PARAM_STR);
$user_query->execute();
$user_data = $user_query->fetch(PDO::FETCH_ASSOC);


if (isset($_GET['p']) && is_file('_' . strip_tags($_GET['p']) . '.php')) {
    $page = strip_tags($_GET['p']);
} else {
    $page = 'home';
}


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
  $title = "Aquavelo $city";
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
  <meta name="description" content="Cours d'aquabiking collectif en piscine animé par un coach. Éliminez votre cellulite et affinez votre silhouette rapidement sur toutes les parties du corps.">
  <meta name="keywords" content="aquavelo, aquabiking, waterbike, aquabike, aquagym, anti cellulite, amincissement, perte de poids, kg en moins, affinement rapide de la silhouette">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <title><?= $title; ?> | Aquavelo</title>
  <link rel="stylesheet" type="text/css" href="/css/animate.css">
  <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link rel="icon" href="images/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Courgette&display=swap" rel="stylesheet">
  <style type="text/css">
    body,
    td,
    th {
      font-family: 'Open Sans', sans-serif;
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
  
  <script src="/js/modernizr.custom.js"></script>

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="/bootstrap/js/html5shiv.js"></script>
  <script src="/bootstrap/js/respond.min.js"></script>
  <![endif]-->
  <script>
  !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on","addSourceMiddleware","addIntegrationMiddleware","setAnonymousId","addDestinationMiddleware"];analytics.factory=function(e){return function(){var t=Array.prototype.slice.call(arguments);t.unshift(e);analytics.push(t);return analytics}};for(var e=0;e<analytics.methods.length;e++){var key=analytics.methods[e];analytics[key]=analytics.factory(key)}analytics.load=function(key,e){var t=document.createElement("script");t.type="text/javascript";t.async=!0;t.src="https://cdn.segment.com/analytics.js/v1/" + key + "/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);analytics._loadOptions=e};analytics.SNIPPET_VERSION="4.13.1";
  analytics.load("NbkSIsYp06eaohctPba8atbY90rGaxQs");
  analytics.page();
  }}();
</script>
  

 <!-- Card meta pour Twitter -->
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="https://www.aquavelo.com">
<meta name="twitter:title" content="accueil Aquavelo" />
<meta name="twitter:description" content="Cours d'aquabiking collectif en piscine animé par un coach ouvert 7 jours sur 7. Éliminez votre cellulite et affinez votre silhouette rapidement sur toutes les parties du corps." />
<!-- Twitter summary card avec image large de 280x150px -->
<meta name="twitter:image:src" content="/images/aquavelo.webp"/> 


  <!-- Open Graph meta pour Facebook -->
<meta property="og:title" content="AQUAVELO" />
<meta property="og:url" content="https://www.aquavelo.com" />
<meta property="og:image" content="/images/aquavelo.webp"/>
<meta property="og:description" content="Cours d'aquabiking collectif en piscine animé par un coach ouvert 7 jours sur 7. Éliminez votre cellulite et affinez votre silhouette rapidement sur toutes les parties du corps." />
<meta property="og:site_name" content="AQUAVELO" />
<meta property="og:type" content="Site internet" />

  <!-- Epingle Pinterest -->
<meta property="og:type" content="Site internet" />
<meta property="og:title" content="accueil Aquavelo" />
<meta property="og:description" content="Cours d'aquabiking collectif en piscine animé par un coach ouvert 7 jours sur 7. Éliminez votre cellulite et affinez votre silhouette rapidement sur toutes les parties du corps." />
<meta property="og:url" content="https://www.aquavelo.com" />
<meta property="og:site_name" content="Aquavélo" />
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
          <a class="navbar-brand" href="http://aquavelo.com/"><img src="/images/content/logo.png" alt="Aquabiking collectif"></a> </div>
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
            <li<?php if ($page == 'home') echo ' class="active"'; ?>> <a href="http://aquavelo.com/">Accueil</a> </li>
              <li class="dropdown<?php if ($page == 'aquabiking') echo ' active'; ?>"> <a href="/aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Aquabiking</a>
                <ul class="dropdown-menu">
                  <li><a href="/aquabiking">Le vélo dans l'eau</a></li>
                  <li><a href="/aquabiking#bienfaits">Les bienfaits</a></li>
                  <li><a href="/aquabiking#questions">Vos questions</a></li>
                 
               
                  
                </ul>
              </li>
              <li class="dropdown<?php if ($page == 'centres') echo ' active'; ?>"> <a href="/centres" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Centres</a>
                <ul class="dropdown-menu">

                  <?php foreach ($centers_list_d as &$row_centers_list) { ?>
                    <li><a href="/centres/<?= $row_centers_list['city']; ?>" title="Aquabiking à <?= $row_centers_list['city']; ?>"><?= $row_centers_list['city']; ?></a></li>
                  <?php } ?>

                </ul>
              </li>
              <li class="dropdown<?php if ($page == 'concept') echo ' active'; ?>"> <a href="/concept-aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Concept</a>
               
                <ul class="dropdown-menu">
                  <li><a href="/concept-aquabiking#ouvrir">Ouvrir un centre</a></li>
                </ul>
              </li>


                <li <?php if ($page == 'conseilminceur') echo ' class="active"'; ?> class="dropdown<?php if ($page == 'Conseils minceurs') echo ' active'; ?>"> 
    <a href="/conseilminceur" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Articles Minceurs</a>
    <ul class="dropdown-menu">
        <li><a href="/conseilminceur">Articles Minceur</a></li> <!-- Nouveau lien -->
      
    </ul>
              </li>

                  <li <?php if ($page == 'partenaires') echo ' class="active"'; ?> class="dropdown<?php if ($page == 'partenaires') echo ' active'; ?>"> <a href="/partenaires" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Partenaires</a>
                <ul class="dropdown-menu">
                  <li><a href="/partenaires">Partenaires</a></li>
              

                </ul>
              </li>
          
    

             



              <li <?php if ($page == 'contact') echo ' class="active"'; ?> class="dropdown<?php if ($page == 'contact') echo ' active'; ?>"> <a href="/contact" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Contact</a>
                <ul class="dropdown-menu">
                  <li><a href="/contact#emploi">Emploi</a></li>
                  <li><a href="/contact#contact">Contactez-nous</a></li>
                 

                </ul>
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
                Abonnement confirmé. </div>
              <div class="errorMessage alert alert-danger alert-dismissable" style="display: none">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Une erreur est survenue. </div>
              <form class="liveForm" role="form" action="/form/send.php" method="post" data-email-subject="Newsletter Form" data-show-errors="true" data-hide-form="true">
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
    <!-- section -->

    <footer>
      <script>
window.axeptioSettings = {
  clientId: "63e92a4787869ce5703ea490",
};
 
(function(d, s) {
  var t = d.getElementsByTagName(s)[0], e = d.createElement(s);
  e.async = true; e.src = "//static.axept.io/sdk.js";
  t.parentNode.insertBefore(e, t);
})(document, "script");
</script>

      <div class="container mainfooter">
        <div class="row">
          <aside class="col-md-3 widget"> <img src="/images/content/logo-footer.png" alt="centre Aquavélo"> <br>
            <br>
            <h1 style="font-size: 12px;">Aquabiking collectif en piscine</h1>
            <h1 class="darker">Éliminez votre cellulite et affinez votre silhouette</h1>
            <p class="darker">&copy; 2014</p>
          </aside>
          <aside class="col-md-3 widget">
            <h2 class="widget-title">Contactez-nous</h2>
            <a href="mailto:claude@alesiaminceur.com" class="larger">claude@alesiaminceur.com</a>
            <p> 60 avenue du Docteur Picaud<br>
              06150 Cannes</p>
            <p> T&eacute;l.: +33 (0)4 93 93 05 65</p>
          </aside>
          <aside class="col-md-3 widget">
            <h2 class="widget-title">&nbsp;</h2>
          </aside>
          <aside class="col-md-3 widget">
            <h3 class="widget-title">Flux de photo</h3>
            <div class="flickr_badge">
              <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14199424047/" title="IMG_1877 de aquavelo, sur Flickr"><img src="https://farm3.staticflickr.com/2913/14199424047_ace3ffa999.jpg" width="80" alt="IMG_1877"></a></div>
              <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14384877104/" title="IMG_1896 de aquavelo, sur Flickr"><img src="https://farm4.staticflickr.com/3894/14384877104_6f08cfe0d0.jpg" width="80" alt="IMG_1896"></a>
              </div>
              <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14199323080/" title="IMG_1921 de aquavelo, sur Flickr"><img src="https://farm3.staticflickr.com/2924/14199323080_07f86b8ee9.jpg" width="80" alt="IMG_1921"></a> </div>
            </div>
          </aside>
        </div>
      </div>
    </footer>
  </div>
  <!-- boxedWrapper -->

  <a href="#" id="toTop"><i class="fa fa-angle-up"></i></a>
  <script src="/js/jquery.min.js"></script>
  <script src="/bootstrap/js/bootstrap.min.js"></script>
  <script src="/js/detectmobilebrowser.js"></script>
  <script src="/js/gmap3.min.js"></script>
  <script src="/js/jquery.appear.js"></script>
  <script src="/js/jquery.isotope.min.js"></script>
  <script src="/js/jquery.ba-bbq.min.js"></script>
  <script src="/js/jquery.countTo.js"></script>
  <script src="/js/jquery.fitvids.js"></script>
  <script src="/js/jquery.flexslider-min.js"></script>
  <script src="/js/jquery.magnific-popup.min.js"></script>
  <script src="/js/jquery.mb.YTPlayer.js"></script>
  <script src="/js/jquery.placeholder.min.js"></script>
  <script src="/js/retina-1.1.0.min.js"></script>
  <script src="/js/timeline/js/storyjs-embed.js"></script>
  <script src="/form/js/form.js"></script>
  <!--<script src="/twitter/js/jquery.tweet.js"></script> -->
  <script src="/js/main.js"></script>
  <script>
    /*
    (function(i, s, o, g, r, a, m) {
      i['GoogleAnalyticsObject'] = r;
      i[r] = i[r] || function() {
        (i[r].q = i[r].q || []).push(arguments)
      }, i[r].l = 1 * new Date();
      a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
      a.async = 1;
      a.src = g;
      m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-40045225-6', 'auto');
    ga('send', 'pageview');
    */

    document.addEventListener("DOMContentLoaded", function(event) {
      document.querySelectorAll('img').forEach(function(img) {
        img.onerror = function() {
          this.src = '/images/center_179/1.jpg';
        };
      })

    })
  </script>
</body>

</html>
