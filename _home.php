<?php include '_slider.php'; ?>
<section class="content-area bg1">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <div class="iconBox">
          <div class="media"> <a class="pull-left" href="#"> <span class="octagon"> <span class="svg-load"></span> <i class="fa fa-fire"></i> </span> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">500 calories brûlées</a></h4>
              <p>Une séance de 45 min d'aquabiking brûle autant qu'1h30 de course à pied. 
                <strong>Perdez jusqu'à 10 kg en 3 mois</strong> avec 2 séances par semaine !</p>
            </div>
          </div>
        </div>
        <!-- / iconBox -->
      </div>
      <div class="col-md-4">
        <div class="iconBox">
          <div class="media"> <a class="pull-left" href="#"> <span class="octagon"> <span class="svg-load"></span> <i class="fa fa-heart"></i> </span> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Adieu cellulite</a></h4>
              <p>L'effet drainant de l'eau combiné au pédalage <strong>réduit visiblement la cellulite</strong>. Vos jambes retrouvent fermeté et légèreté dès les premières séances.</p>
            </div>
          </div>
        </div>
        <!-- / iconBox -->
      </div>
      <div class="col-md-4">
        <div class="iconBox">
          <div class="media"> <a class="pull-left" href="#"> <span class="octagon"> <span class="svg-load"></span> <i class="fa fa-smile-o"></i> </span> </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="#">Zéro douleur</a></h4>
              <p><strong>Sport doux pour les articulations</strong> : l'eau porte votre corps. Idéal après une grossesse, une blessure ou pour les seniors. Résultats visibles, zéro courbature !</p>
            </div>
          </div>
        </div>
        <!-- / iconBox -->
      </div>
    </div>
  </div>
</section>
<!-- / section -->

<section class="content-area bg2">
  <div class="container">
    <header class="page-header text-center">
      <h1 class="page-title">17 centres en France</h1>
      <h2>Trouvez le centre Aquavelo le plus proche de chez vous</h2>
      <p style="color: #666;">Ouverts 7j/7 · Coachs diplômés · 1ère séance gratuite</p>
    </header>
    <div class="flexslider carousel-slider" data-slideshow="false" data-speed="7000" data-animspeed="600" data-loop="true" data-min="1" data-max="3" data-move="1" data-controls="true" data-dircontrols="true">
      <ul class="slides">
        <?php foreach ($centers_last_d as &$row_centers_last) {
          $department = $row_centers_last['department_nom'];
        ?>
          <li>
            <article class="portfolio-item animated" data-fx="fadeInUp">
              <div class="portfolio-thumbnail"> <a href="<?= BASE_PATH ?>centres/<?= $row_centers_last['city'] ?>"><img src="<?= BASE_PATH ?>cloud/thumbnail/center_<?= $row_centers_last['id']; ?>/1.jpg" alt="Aquavelo <?= $row_centers_last['city'] ?>"></a> <a href="<?= BASE_PATH ?>centres/<?= $row_centers_last['city'] ?>" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
              <div class="entry-meta"> <span class="cat-links"><a href="#"><?= $department; ?></a>, <a href="#"><?= $row_centers_last['country'] ?></a></span> </div>
              <h4 class="entry-title"><a href="#"><?= $row_centers_last['city'] ?>, <?= $row_centers_last['TypeAQUAVELO'] ?></a></h4>
            </article>
          </li>
        <?php } ?>
        <li>
          <article class="portfolio-item animated" data-fx="fadeInDown">
            <div class="portfolio-thumbnail"> <a href="#"><img src="<?= BASE_PATH ?>images/content/works-02.jpg" alt=" "></a> <a href="#" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
            <div class="entry-meta"> <span class="cat-links"><a href="#">&Icirc;le de France</a>, <a href="#">France</a></span> </div>
            <h4 class="entry-title"><a href="#">Paris, 75013</a></h4>
          </article>
          <!-- / portfolio-item -->
        </li>
        <li>
          <article class="portfolio-item animated" data-fx="fadeInUp">
            <div class="portfolio-thumbnail"> <a href="#"><img src="<?= BASE_PATH ?>images/content/works-03.jpg" alt=" "></a> <a href="#" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
            <div class="entry-meta"> <span class="cat-links"><a href="#">Maroc</a>, <a href="#">Afrique</a></span> </div>
            <h4 class="entry-title"><a href="#">Casablanca, Racine</a></h4>
          </article>
          <!-- / portfolio-item -->
        </li>
      </ul>
    </div>
    <div class="text-center"> 
      <a href="<?= BASE_PATH ?>?p=free" class="btn btn-primary btn-lg animated" data-fx="fadeInUp" style="margin-right: 10px;"><i class="fa fa-gift"></i> Réserver ma séance gratuite</a>
      <a href="<?= BASE_PATH ?>centres" class="btn btn-default animated" data-fx="fadeInUp">Voir tous les centres</a> 
    </div>
  </div>
</section>
<!-- / section -->

<section class="content-area bg1" data-btmspace="0">
  <div class="container">
    <div class="promoBox">
      <div class="row">
        <div class="col-md-6 col-md-push-6">
          <div class="inner animated" data-fx="fadeInLeft">
            <h2>Un cadre unique pour vos séances</h2>
            <p class="larger">Nos centres allient design contemporain et confort : eau chauffée à 28°C, vestiaires privatifs, produits de douche fournis.<br><br>
              <strong>Immergé jusqu'à la taille, vos cheveux restent secs.</strong> Venez comme vous êtes, repartez transformé(e) !</p>
            <a href="<?= BASE_PATH ?>?p=free" class="btn btn-primary" style="margin-right: 10px;"><i class="fa fa-gift"></i> Essayer gratuitement</a>
            <a href="<?= BASE_PATH ?>concept-aquabiking" class="btn btn-default">Découvrir le concept</a>
          </div>
        </div>
        <div class="col-md-6 col-md-pull-6"> <img src="<?= BASE_PATH ?>images/content/home-v1-promo.jpg" alt=" " class="animated" data-fx="fadeInLeft"> </div>
      </div>
    </div>
    <!-- / promoBox -->
  </div>
</section>
<!-- / section -->
