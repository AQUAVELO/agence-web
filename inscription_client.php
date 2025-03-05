<?php
require '_settings.php'; // Inclut les paramètres comme dans index.php (base de données, Redis, etc.)
$title = "Inscription Client | Aquavelo"; // Titre de la page
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
        .form-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #47c3e6;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
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
                        <li class="dropdown<?php if ($page == 'concept') echo ' active'; ?>"> <a href="/concept-aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Concept</ bandera </a>
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
        <div class="form-wrapper">
            <div class="form-container">
                <h2>Inscription Client</h2>
                <form action="traitement_client.php" method="POST">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required>

                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" required>

                    <label for="tel">Téléphone</label>
                    <input type="text" id="tel" name="tel">

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">

                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse">

                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville">

                    <label for="dept">Département *</label>
                    <select id="dept" name="dept" required>
                        <option value="">Sélectionnez un département</option>
                        <option value="01">01 - Ain</option>
                        <option value="02">02 - Aisne</option>
                        <option value="03">03 - Allier</option>
                        <option value="04">04 - Alpes-de-Haute-Provence</option>
                        <option value="05">05 - Hautes-Alpes</option>
                        <option value="06">06 - Alpes-Maritimes</option>
                        <option value="07">07 - Ardèche</option>
                        <option value="08">08 - Ardennes</option>
                        <option value="09">09 - Ariège</option>
                        <option value="10">10 - Aube</option>
                        <option value="11">11 - Aude</option>
                        <option value="12">12 - Aveyron</option>
                        <option value="13">13 - Bouches-du-Rhône</option>
                        <option value="14">14 - Calvados</option>
                        <option value="15">15 - Cantal</option>
                        <option value="16">16 - Charente</option>
                        <option value="17">17 - Charente-Maritime</option>
                        <option value="18">18 - Cher</option>
                        <option value="19">19 - Corrèze</option>
                        <option value="2A">2A - Corse-du-Sud</option>
                        <option value="2B">2B - Haute-Corse</option>
                        <option value="21">21 - Côte-d'Or</option>
                        <option value="22">22 - Côtes-d'Armor</option>
                        <option value="23">23 - Creuse</option>
                        <option value="24">24 - Dordogne</option>
                        <option value="25">25 - Doubs</option>
                        <option value="26">26 - Drôme</option>
                        <option value="27">27 - Eure</option>
                        <option value="28">28 - Eure-et-Loir</option>
                        <option value="29">29 - Finistère</option>
                        <option value="30">30 - Gard</option>
                        <option value="31">31 - Haute-Garonne</option>
                        <option value="32">32 - Gers</option>
                        <option value="33">33 - Gironde</option>
                        <option value="34">34 - Hérault</option>
                        <option value="35">35 - Ille-et-Vilaine</option>
                        <option value="36">36 - Indre</option>
                        <option value="37">37 - Indre-et-Loire</option>
                        <option value="38">38 - Isère</option>
                        <option value="39">39 - Jura</option>
                        <option value="40">40 - Landes</option>
                        <option value="41">41 - Loir-et-Cher</option>
                        <option value="42">42 - Loire</option>
                        <option value="43">43 - Haute-Loire</option>
                        <option value="44">44 - Loire-Atlantique</option>
                        <option value="45">45 - Loiret</option>
                        <option value="46">46 - Lot</option>
                        <option value="47">47 - Lot-et-Garonne</option>
                        <option value="48">48 - Lozère</option>
                        <option value="49">49 - Maine-et-Loire</option>
                        <option value="50">50 - Manche</option>
                        <option value="51">51 - Marne</option>
                        <option value="52">52 - Haute-Marne</option>
                        <option value="53">53 - Mayenne</option>
                        <option value="54">54 - Meurthe-et-Moselle</option>
                        <option value="55">55 - Meuse</option>
                        <option value="56">56 - Morbihan</option>
                        <option value="57">57 - Moselle</option>
                        <option value="58">58 - Nièvre</option>
                        <option value="59">59 - Nord</option>
                        <option value="60">60 - Oise</option>
                        <option value="61">61 - Orne</option>
                        <option value="62">62 - Pas-de-Calais</option>
                        <option value="63">63 - Puy-de-Dôme</option>
                        <option value="64">64 - Pyrénées-Atlantiques</option>
                        <option value="65">65 - Hautes-Pyrénées</option>
                        <option value="66">66 - Pyrénées-Orientales</option>
                        <option value="67">67 - Bas-Rhin</option>
                        <option value="68">68 - Haut-Rhin</option>
                        <option value="69">69 - Rhône</option>
                        <option value="70">70 - Haute-Saône</option>
                        <option value="71">71 - Saône-et-Loire</option>
                        <option value="72">72 - Sarthe</option>
                        <option value="73">73 - Savoie</option>
                        <option value="74">74 - Haute-Savoie</option>
                        <option value="75">75 - Paris</option>
                        <option value="76">76 - Seine-Maritime</option>
                        <option value="77">77 - Seine-et-Marne</option>
                        <option value="78">78 - Yvelines</option>
                        <option value="79">79 - Deux-Sèvres</option>
                        <option value="80">80 - Somme</option>
                        <option value="81">81 - Tarn</option>
                        <option value="82">82 - Tarn-et-Garonne</option>
                        <option value="83">83 - Var</option>
                        <option value="84">84 - Vaucluse</option>
                        <option value="85">85 - Vendée</option>
                        <option value="86">86 - Vienne</option>
                        <option value="87">87 - Haute-Vienne</option>
                        <option value="88">88 - Vosges</option>
                        <option value="89">89 - Yonne</option>
                        <option value="90">90 - Territoire de Belfort</option>
                        <option value="91">91 - Essonne</option>
                        <option value="92">92 - Hauts-de-Seine</option>
                        <option value="93">93 - Seine-Saint-Denis</option>
                        <option value="94">94 - Val-de-Marne</option>
                        <option value="95">95 - Val-d'Oise</option>
                        <option value="971">971 - Guadeloupe</option>
                        <option value="972">972 - Martinique</option>
                        <option value="973">973 - Guyane</option>
                        <option value="974">974 - La Réunion</option>
                        <option value="976">976 - Mayotte</option>
                    </select>

                    <label for="activites">Activités souhaitées (natation, phobie de l'eau, cours collectifs, individuel..etc)</label>
                    <textarea id="activites" name="activites" rows="4"></textarea>

                    <label for="besoin">Spécificités (dans piscine à domicile, en mer, handicap...etc)</label>
                    <textarea id="besoin" name="besoin" rows="4"></textarea>

                    <button type="submit">S'inscrire</button>
                </form>
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
                        <h2 class="widget-title"> </h2>
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