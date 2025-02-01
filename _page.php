<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">AQUAVELO = AQUABIKING + AQUAGYM</h1>
    <h2 class="page-title pull-left">Excellent pour affiner la silhouette, la tonification et le bien-être.</h2>
    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li><a href="/centres">Centres</a></li>
      <li class="active"><?= htmlspecialchars($city ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
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
    <!-- End Facebook Pixel Code -->
  <?php endif; ?>
</header>

<section class="content-area bg1">
  <div class="container">
    <div class="row mt-3">
      <!-- Image principale -->
      <div class="col-md-3 col-6 text-center">
        <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/1.jpg" 
             alt="Photo principale du centre Aquavélo" class="img-fluid img-same" width="300" height="200">
      </div>

      <!-- Image secondaire -->
      <div class="col-md-3 col-6 text-center">
        <?php if (isset($row_center['id']) && $row_center['id'] != 305) : ?>
          <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/2.jpg" 
               alt="Photo secondaire du centre Aquavélo" class="img-fluid img-same" width="300" height="200">
        <?php else : ?>
          <img src="/images/Cannes1.jpg" alt="Photo du centre de Cannes" class="img-fluid img-same" width="300" height="200">
        <?php endif; ?>
      </div>

      <!-- Image supplémentaire -->
      <div class="col-md-3 col-6 text-center">
        <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>/3.jpg" 
             alt="Photo supplémentaire du centre Aquavélo" class="img-fluid img-same" width="300" height="200">
      </div>

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
          <a href="https://www.aquavelo.com/seance-decouverte/<?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>">
            <img src="/images/promoJan24.webp" 
                 alt="Promotion spéciale pour le centre <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>" 
                 class="img-fluid img-same" width="300" height="200">
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>


<!-- Formulaire -->
<div class="col-md-6">
  <h2 class="form-group">Essayez une séance gratuite de 45 mn</h2>
  <?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
    <p>en vous inscrivant sur notre <strong>calendrier</strong> <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank">(cliquez ici)</a> ou en prenant rendez-vous ci-dessous.</p>
  <?php endif; ?>
  <form role="form" class="contact-form" method="POST" action="_page.php">
    <div class="form-group">
      <label for="center">Dans quel centre souhaitez-vous effectuer votre séance ?</label>
      <select class="form-control" id="center" name="center">
        <?php if (isset($centers_list_d)) { ?>
          <?php foreach ($centers_list_d as $free_d) { ?>
            <option <?php if (isset($_GET['city']) && $_GET['city'] == $free_d['city']) echo 'selected'; ?> value="<?= htmlspecialchars($free_d['id'], ENT_QUOTES, 'UTF-8'); ?>">
              <?= htmlspecialchars($free_d['city'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php } ?>
        <?php } ?>
      </select>
    </div>
    <div class="form-group">
      <label for="nom">Nom et prénom</label>
      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom et prénom" value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="form-group">
      <label for="phone">Téléphone</label>
      <input type="tel" class="form-control" id="phone" name="phone" placeholder="Téléphone" value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <input type="hidden" name="reason" id="reason">
    <input type="hidden" name="segment" id="segment">
    <button type="submit" class="btn btn-default" aria-label="Recevoir mon bon par email">Recevoir mon bon par email</button>
  </form>

  <!-- Ajout de l'image avec bouton pour agrandir -->
	<?php 
	if (in_array($row_center['id'], [305, 347, 349])) { ?>
	  <div class="text-center mt-4">
	      <img src="/images/PLANNINGCANNES0125.jpg" alt="Planning des cours Aquavelo Cannes" class="img-fluid" style="max-width: 100%; height: auto;">
	      <br>
	      <a href="/images/PLANNINGCANNES0125.jpg" target="_blank" class="btn btn-default mt-2">Agrandir l'image</a>
	  </div>
	<?php } ?>
	
	</div>

<!-- Informations du centre -->
<div class="col-md-6">
  <dl style="margin-top:30px;">
    <dt>Adresse</dt>
    <dd><?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
    <dt>Téléphone</dt>
    <dd><?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
    <dt>Horaires</dt>
    <dd><?= htmlspecialchars($row_center['openhours'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
    <dt>Découvrez la vie de votre centre</dt>
    <dd>
      <a href="https://www.facebook.com/<?= htmlspecialchars($row_center['facebook'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
         title="Facebook" 
         target="_blank" 
         class="btn btn-default" 
         aria-label="Visitez notre page Facebook">
        Facebook
      </a>
    </dd>
  </dl>
</div>

<!-- Espace entre les sections -->
<div style="margin-top: 40px;"></div>




<!-- Reste du contenu (agenda, résultats minceurs, etc.) -->
<dl>

  <dt>Résultats Minceurs Rapides</dt>
  <dd>
    <a class="btn btn-default" 
       href="javascript:ouvre_popup('/nouveauResultat.html')" 
       title="Ouvrir les résultats minceurs" 
       aria-label="Ouvrir les résultats minceurs">Résultats Minceurs</a>
  </dd>

  <dt>Conseils pour perdre du poids</dt>
  <dd>
    <a class="btn btn-default" 
       href="#"
       onclick="ouvre_popup('/resultatMinceur.php'); return false;" 
       title="Conseils pour perdre du poids" 
       aria-label="Conseils pour perdre du poids">Conseils pour perdre du poids</a>
  </dd>


  <dt>Description</dt>
  <dd>
    <p><?= $row_center['description']; ?></p>
  </dd>    
</dl>
		 
 

	    
	
<!--
<section class="content-area bg2">
  <div class="container">
    <header class="page-header text-center">
      <h1 class="page-title">Centres à proximité</h1>
    </header>
    <div id="galleryContainer" class="clearfix withSpaces col-4">
      <div class="galleryItem identity">
        <article class="portfolio-item">
          <div class="portfolio-thumbnail"> <a href="04-pluto-portfolio-single.html"><img src="/images/content/related-01.jpg" alt=" "></a> <a href="04-pluto-portfolio-single.html" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
          <div class="entry-meta"> <span class="cat-links"><a href="#">Identity</a>, <a href="#">Web</a></span> </div>
          <h4 class="entry-title"><a href="04-pluto-portfolio-single.html">Project name goes here</a></h4>
        </article>
      </div>
      <div class="galleryItem web">
        <article class="portfolio-item">
          <div class="portfolio-thumbnail"> <a href="04-pluto-portfolio-single.html"><img src="/images/content/related-02.jpg" alt=" "></a> <a href="04-pluto-portfolio-single.html" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
          <div class="entry-meta"> <span class="cat-links"><a href="#">Identity</a>, <a href="#">Web</a></span> </div>
          <h4 class="entry-title"><a href="04-pluto-portfolio-single.html">Project name goes here</a></h4>
        </article>
      </div>
      <div class="galleryItem print">
        <article class="portfolio-item">
          <div class="portfolio-thumbnail"> <a href="04-pluto-portfolio-single.html"><img src="/images/content/related-03.jpg" alt=" "></a> <a href="04-pluto-portfolio-single.html" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
          <div class="entry-meta"> <span class="cat-links"><a href="#">Identity</a>, <a href="#">Web</a></span> </div>
          <h4 class="entry-title"><a href="04-pluto-portfolio-single.html">Project name goes here</a></h4>
        </article>
      </div>
      <div class="galleryItem identity web">
        <article class="portfolio-item">
          <div class="portfolio-thumbnail"> <a href="04-pluto-portfolio-single.html"><img src="/images/content/related-04.jpg" alt=" "></a> <a href="04-pluto-portfolio-single.html" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a> </div>
          <div class="entry-meta"> <span class="cat-links"><a href="#">Identity</a>, <a href="#">Web</a></span> </div>
          <h4 class="entry-title"><a href="04-pluto-portfolio-single.html">Project name goes here</a></h4>
        </article>
      </div>
    </div>
    
  </div>


</section>
		   
		  
		   
-->
<!-- / section --> 
