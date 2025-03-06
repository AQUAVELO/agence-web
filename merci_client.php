<?php
require '_settings.php'; // Inclut les paramètres comme dans index.php (base de données, Redis, etc.)
$title = "Merci | Aquavelo"; // Titre de la page

// Démarrer la session pour récupérer les données si nécessaire
session_start();

// Récupérer le message ou les données depuis la session (optionnel, selon ton traitement)
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "Merci pour votre inscription ! Nous vous contacterons bientôt.";

// Nettoyer la session après usage
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> | Aquavelo</title>
    <!-- Inclusion des styles et scripts de index.php -->
    <link rel="stylesheet" type="text/css" href="/css/animate.css">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="icon" href="images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Courgette&display=swap" rel="stylesheet">
    <style type="text/css">
        body, td, th { font-family: 'Open Sans', sans-serif; }
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
        }
        #boxedWrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .message-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .message-container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #47c3e6;
        }
        p {
            font-size: 18px;
            color: #333;
        }
    </style>
    <script src="/js/modernizr.custom.js"></script>
</head>
<body class="withAnimation">
    <div id="boxedWrapper">
        <!-- Barre de navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="http://aquavelo.com/"><img src="/images/content/logo.png" alt="Aquabiking collectif"></a>
                </div>
                <div class="navbar-collapse collapse">
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
                            <a href="/conseilminceur" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Conseils Minceurs</a>
                            <ul class="dropdown-menu">
                                <li><a href="/conseilminceur">Conseils Minceurs</a></li>
                            </ul>
                        </li>
                        <li <?php if ($page == 'partenaires') echo ' class="active"'; ?> class="dropdown<?php if ($page == 'partenaires') echo ' active'; ?>">
                            <a href="/partenaires" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Partenaires</a>
                            <ul class="dropdown-menu">
                                <li><a href="/partenaires">Partenaires</a></li>
                            </ul>
                        </li>
                        <li <?php if ($page == 'contact') echo ' class="active"'; ?> class="dropdown<?php if ($page == 'contact') echo ' active'; ?>">
                            <a href="/contact" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Contact</a>
                            <ul class="dropdown-menu">
                                <li><a href="/contact#emploi">Emploi</a></li>
                                <li><a href="/contact#contact">Contactez-nous</a></li>
                            </ul>
                        </li>
                        <li <?php if ($page == 'natation') echo ' class="active"'; ?>>
                            <a href="/natation">Nat</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Contenu centré -->
        <div class="message-wrapper">
            <div class="message-container">
                <h2>Merci pour votre inscription !</h2>
                <p><?= htmlspecialchars($message); ?></p>
            </div>
        </div>

        <!-- Footer -->
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
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                Abonnement confirmé.
                            </div>
                            <div class="errorMessage alert alert-danger alert-dismissable" style="display: none">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                Une erreur est survenue.
                            </div>
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

        <footer>
            <div class="container mainfooter">
                <div class="row">
                    <aside class="col-md-3 widget"> <img src="/images/content/logo-footer.png" alt="centre Aquavélo"> <br>
                        <br>
                        <h1 style="font-size: 12px;">Aquabiking collectif en piscine</h1>
                        <h1 class="darker">Éliminez votre cellulite et affinez votre silhouette</h1>
                        <p class="darker">© 2014</p>
                    </aside>
                    <aside class="col-md-3 widget">
                        <h2 class="widget-title">Contactez-nous</h2>
                        <a href="mailto:claude@alesiaminceur.com" class="larger">claude@alesiaminceur.com</a>
                        <p> 60 avenue du Docteur Picaud<br>
                            06150 Cannes</p>
                        <p> Tél.: +33 (0)4 93 93 05 65</p>
                    </aside>
                    <aside class="col-md-3 widget">
                        <h2 class="widget-title"> </h2>
                    </aside>
                    <aside class="col-md-3 widget">
                        <h3 class="widget-title">Flux de photo</h3>
                        <div class="flickr_badge">
                            <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14199424047/" title="IMG_1877 de aquavelo, sur Flickr"><img src="https://farm3.staticflickr.com/2913/14199424047_ace3ffa999.jpg" width="80" alt="IMG_1877"></a></div>
                            <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14384877104/" title="IMG_1896 de aquavelo, sur Flickr"><img src="https://farm4.staticflickr.com/3894/14384877104_6f08cfe0d0.jpg" width="80" alt="IMG_1896"></a></div>
                            <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14199323080/" title="IMG_1921 de aquavelo, sur Flickr"><img src="https://farm3.staticflickr.com/2924/14199323080_07f86b8ee9.jpg" width="80" alt="IMG_1921"></a> </div>
                        </div>
                    </aside>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts de pied -->
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
    <script src="/js/main.js"></script>
</body>
</html>
