<?php require '_settings.php'; ?>
<?php

if (isset($_GET['p']) && is_file('_' . strip_tags($_GET['p']) . '.php')) $page = strip_tags($_GET['p']);
else $page = 'home';

if ($page == 'home') $title = 'Aquabiking collectif en piscine';
if ($page == 'aquabiking') $title = 'Aquabiking : les bienfaits du vélo dans l\'eau';
if ($page == 'centers') $title = 'Centres d\'aquabiking';
if ($page == 'concept') $title = 'Concept Aquavelo';
if ($page == 'free') $title = 'Séance découverte';
if ($page == 'Blog.php') $title = 'Blog';
if ($page == 'contact') $title = 'Contactez-nous';


#nav
$centers_list_d = $redis->get('centers_list_d');
$centers_list_d = json_decode($centers_list_d, true);
if (!$centers_list_d) {
  $centers_list = $database->prepare('SELECT * FROM am_centers WHERE online = ? AND aquavelo = ? ORDER BY city ASC');
  $centers_list->execute(array(1, 1));
  $centers_list_d = $centers_list->fetchAll(PDO::FETCH_ASSOC);
  $redis->set('centers_list_d', json_encode($centers_list_d));
  $redis->expire('centers_list_d', $settings['ttl']);
}

#home
if ($page == "home") {

  $centers_last_d = $redis->get('centers_last_d');
  $centers_last_d = json_decode($centers_last_d, true);
  if (!$centers_last_d) {
    $centers_last = $database->prepare('SELECT c.*, d.nom AS department_nom FROM am_centers c INNER JOIN departements d ON d.id = c.department WHERE c.online = ? AND c.aquavelo = ? ORDER BY c.id DESC');
    $centers_last->execute(array(1, 1));
    $centers_last_d = $centers_last->fetchAll(PDO::FETCH_ASSOC);
    $redis->set('centers_last_d', json_encode($centers_last_d));
    $redis->expire('centers_last_d', $settings['ttl']);
  }
}


#page
if (isset($_GET['city'])) {
  $city = strip_tags($_GET['city']);

  $row_center = $redis->get($city);
  $row_center = json_decode($row_center, true);
  if (!$row_center) {
    $center = $database->prepare('SELECT id FROM am_centers WHERE city = ? AND online = ? AND aquavelo = ?');
    $center->execute(array($city, 1, 1));
    $secure = $center->rowCount();
    if ($secure != 0) {
      $center = $database->prepare('SELECT c.*, d.nom AS department_nom, r.nom AS region_nom FROM am_centers c INNER JOIN departements d ON d.id = c.department INNER JOIN regions r ON r.id = c.region WHERE c.city = ? AND c.online = ? AND c.aquavelo = ?');
      $center->execute(array($city, 1, 1));
      $row_center = $center->fetch();
      $redis->set($city, json_encode($row_center));
      $redis->expire($city, $settings['ttl']);
    } else {
      return header('location: ./');
    }
  }
  $region = $row_center['region_nom'];
  $department = $row_center['department_nom'];
  $title = "Aquavelo $city : Aquabiking en $department ";
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
  <meta name="description" content="Cours d'aquabiking collectif en piscine animés par un coach sportif diplômé. Éliminez votre cellulite en pédalant dans l'eau et brûlez trois plus de calories en une séance d'aquabike.">
  <meta name="keywords" content="aquavelo, aquabiking, aquabike">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <title><?= $title; ?> | Aquavelo</title>
  <link rel="stylesheet" type="text/css" href="/css/animate.css">
  <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Courgette&display=swap" rel="stylesheet">
  <style type="text/css">
    body,
    td,
    th {
      font-family: 'Open Sans', sans-serif;
    }
  </style>
  <script src="/js/modernizr.custom.js"></script>

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="/bootstrap/js/html5shiv.js"></script>
  <script src="/bootstrap/js/respond.min.js"></script>
  <![endif]-->

</head>

<body class="withAnimation">
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
            <li<?php if ($p == 'home') echo ' class="active"'; ?>> <a href="http://aquavelo.com/">Accueil</a> </li>
              <li class="dropdown<?php if ($p == 'aquabiking') echo ' active'; ?>"> <a href="/aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Aquabiking</a>
                <ul class="dropdown-menu">
                  <li><a href="/aquabiking">Le vélo dans l'eau</a></li>
                  <li><a href="/aquabiking">Les bienfaits</a></li>
                </ul>
              </li>
              <li class="dropdown<?php if ($p == 'centres') echo ' active'; ?>"> <a href="/centres" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Centres</a>
                <ul class="dropdown-menu">

                  <?php foreach ($centers_list_d as &$row_centers_list) { ?>
                    <li><a href="/centres/<?= $row_centers_list['city']; ?>" title="Aquabiking à <?= $row_centers_list['city']; ?>"><?= $row_centers_list['city']; ?></a></li>
                  <?php } ?>

                </ul>
              </li>
              <li class="dropdown<?php if ($p == 'concept') echo ' active'; ?>"> <a href="/concept-aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Concept</a>
                <ul class="dropdown-menu">
                  <li><a href="/concept-aquabiking#ouvrir">Ouvrir un centre</a></li>
                </ul>
              </li>
              <li><a target="_blank" href="https://minceurprod.com">Minceur</a></li>
              <li <?php if ($p == 'contact') echo ' class="active"'; ?> class="dropdown<?php if ($p == 'contact') echo ' active'; ?>"> <a href="/contact" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Contact</a>
                <ul class="dropdown-menu">
                  <li><a href="/contact">Emploi</a></li>
                  <li><a href="/contact">Contactez-nous</a></li>

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
                  <h1 class="widget-title">Suivez-nous</h1>
                </div>
                <div class="table-cell">
                  <ul class="socialIcons bigIcons">
                    <li><a href="http://www.facebook.com/aquavelonice" data-toggle="tooltip" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="http://twitter.com/AquaveloNice" data-toggle="tooltip" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="http://plus.google.com/108184119422520190690" data-toggle="tooltip" title="Google+"><i class="fa fa-google-plus"></i></a></li>
                    <li><a href="#" data-toggle="tooltip" title="Pinterest"><i class="fa fa-pinterest"></i></a></li>
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
                        <h1 class="widget-title">Recevoir la newsletter</h1>
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
      <div class="container mainfooter">
        <div class="row">
          <aside class="col-md-3 widget"> <img src="/images/content/logo-footer.png" alt=" "> <br>
            <br>
            <p class="darker">Aquabiking collectif en piscine</p>
            <p class="darker">&copy; 2014</p>
          </aside>
          <aside class="col-md-3 widget">
            <h1 class="widget-title">Contactez-nous</h1>
            <a href="mailto:claude@alesiaminceur.com" class="larger">claude@alesiaminceur.com</a>
            <p> 60 avenue du Docteur Picaud<br>
              06150 Cannes</p>
            <p> T&eacute;l.: +33 (0)4 93 93 05 65</p>
          </aside>
          <aside class="col-md-3 widget">
            <h1 class="widget-title">&nbsp;</h1>
          </aside>
          <aside class="col-md-3 widget">
            <h1 class="widget-title">Flux de photo</h1>
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