<?php include'_slider.php'; ?>
 <section class="content-area bg1">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="iconBox">
            <div class="media"> <a class="pull-left" href="#"> <span class="octagon"> <span class="svg-load"></span> <i class="fa fa-calendar"></i> </span> </a>
              <div class="media-body">
                <h4 class="media-heading"><a href="#">R&eacute;servation en ligne</a></h4>
                <p>Avec 70 cours par semaine à votre disposition, réservez en ligne où que vous soyez, 
votre vélo vous attend selon votre emploi du temps. Pas de stress! </p>
              </div>
            </div>
          </div>
          <!-- / iconBox --> 
        </div>
        <div class="col-md-4">
          <div class="iconBox">
            <div class="media"> <a class="pull-left" href="#"> <span class="octagon"> <span class="svg-load"></span> <i class="fa fa-male"></i> </span> </a>
              <div class="media-body">
                <h4 class="media-heading"><a href="#">Coach sportif</a></h4>
                <p> Sélectionnés avec le plus grand soin, nos coachs diplômés vous motiveront à travers des chorégraphies rythmées pour atteindre rapidement vos objectifs.</p>
              </div>
            </div>
          </div>
          <!-- / iconBox --> 
        </div>
        <div class="col-md-4">
          <div class="iconBox">
            <div class="media"> <a class="pull-left" href="#"> <span class="octagon"> <span class="svg-load"></span> <i class="fa fa-users"></i> </span> </a>
              <div class="media-body">
                <h4 class="media-heading"><a href="#">Cours collectifs</a></h4>
                <p>Votre cours d'aquabiking dure 30 ou 45 minutes et se déroule en groupe restreint. Pourquoi ne pas proposer à un(e) ami(e) de vous accompagner ?</p>
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
        <h1 class="page-title">Nos centres</h1>
        <h2>Localisez le centre le plus proche de chez vous</h2>
      </header>
      <div class="flexslider carousel-slider" data-slideshow="false" data-speed="7000" data-animspeed="600" data-loop="true" data-min="1" data-max="3" data-move="1" data-controls="true" data-dircontrols="true">
        <ul class="slides">
            <?php foreach ($centers_last_d as $row_centers_last) {
		$department = $database->prepare('SELECT nom FROM departements WHERE id = ?');
	$department->execute(array($row_centers_last['department']));
	$row_department = $department->fetch();
	$department = $row_department['nom'];
		?>
           <li>
            <article class="portfolio-item animated" data-fx="fadeInUp">
              <div class="portfolio-thumbnail"> <a href="/centres/<?= $row_centers_last['city'] ?>"><img src="/images/content/works-01.jpg" alt=" "></a> <a href="/centres/<?= $row_centers_last['city'] ?>" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
              <div class="entry-meta"> <span class="cat-links"><a href="#"><?= $department; ?></a>, <a href="#"><?= $row_centers_last['country'] ?></a></span> </div>
              <h4 class="entry-title"><a href="#"><?= $row_centers_last['city'] ?>, <?= $row_centers_last['TypeAQUAVELO'] ?></a></h4>
            </article>
          </li>
          <?php } ?>
          <li>
            <article class="portfolio-item animated" data-fx="fadeInDown">
              <div class="portfolio-thumbnail"> <a href="#"><img src="/images/content/works-02.jpg" alt=" "></a> <a href="#" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
              <div class="entry-meta"> <span class="cat-links"><a href="#">&Icirc;le de France</a>, <a href="#">France</a></span> </div>
              <h4 class="entry-title"><a href="#">Paris, 75013</a></h4>
            </article>
            <!-- / portfolio-item --> 
          </li>
          <li>
            <article class="portfolio-item animated" data-fx="fadeInUp">
              <div class="portfolio-thumbnail"> <a href="#"><img src="/images/content/works-03.jpg" alt=" "></a> <a href="#" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
              <div class="entry-meta"> <span class="cat-links"><a href="#">Maroc</a>, <a href="#">Afrique</a></span> </div>
              <h4 class="entry-title"><a href="#">Casablanca, Racine</a></h4>
            </article>
            <!-- / portfolio-item --> 
          </li>
        </ul>
      </div>
      <div class="text-center"> <a href="/centres" class="btn btn-default animated" data-fx="fadeInUp">Tous les centres</a> </div>
    </div>
  </section>
  <!-- / section -->
  
  <section class="content-area bg1" data-btmspace="0">
    <div class="container">
      <div class="promoBox">
        <div class="row">
          <div class="col-md-6 col-md-push-6">
            <div class="inner animated" data-fx="fadeInLeft">
              <h2>Un design contemporain</h2>
              <p class="larger">Une alliance parfaite entre les matières minérales, l'eau, la pierre et le bois pour vous assurer un véritable hâvre de paix.<br> Qu’attendez-vous pour vous jeter à l'eau ?<br>
En pratiquant l'aquabiking, vous êtes immergé seulement jusqu’à la taille, vos cheveux restent secs. 
Une serviette et des produits de douche sont mis à votre disposition après chaque séance pour vous assurer confort et détente. </p>
              <a href="/concept-aquabiking" class="btn btn-default">Le concept</a> </div>
          </div>
          <div class="col-md-6 col-md-pull-6"> <img src="/images/content/home-v1-promo.jpg" alt=" " class="animated" data-fx="fadeInLeft"> </div>
        </div>
      </div>
      <!-- / promoBox --> 
    </div>
  </section>
  <!-- / section -->
  

  
 