<header class="main-header clearfix">
  <div class="container">
	  
   <h1 class="page-title pull-left">AQUAVELO = AQUABIKING + AQUAGYM</h1>
	  <h2 class="page-title pull-left">Excellent pour affiner la silhouette, la tonification et le bien-être.</h2>
   
    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li><a href="/centres">Centres</a></li>
	
	    
      <li class="active"><?= $city; ?></li> 
    </ol>
  </div>
	
		  <?php if($row_center['id'] == 253) { ?>

          <!-- Facebook Pixel Code -->

<script>
!function(f,b,e,v,n,t,s)
{
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

          <?php } ?>
	


	
</header>





<section class="content-area bg1">
  <div class="container">
    <div class="row mt-3">
      <!-- Image principale -->
      <div class="col-md-3 col-6 text-center">
        <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/1.jpg" 
             alt="Photo principale du centre Aquavélo" class="img-fluid img-same">
      </div>

      <!-- Image secondaire -->
      <div class="col-md-3 col-6 text-center">
        <?php if ($row_center['id'] != 305) { ?>
          <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/2.jpg" 
               alt="Photo secondaire du centre Aquavélo" class="img-fluid img-same">
        <?php } else { ?>
          <img src="/images/Cannes1.jpg" alt="Photo du centre de Cannes" class="img-fluid img-same">
        <?php } ?>
      </div>

      <!-- Image supplémentaire -->
      <div class="col-md-3 col-6 text-center">
        <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/3.jpg" 
             alt="Photo supplémentaire du centre Aquavélo" class="img-fluid img-same">
      </div>

      <!-- Image promotionnelle -->
      <?php 
      $promotions = [
          305 => "Cannes",
          253 => "Antibes",
          347 => "Mandelieu",
          349 => "Vallauris",
          343 => "Merignac"
      ];

      if (array_key_exists($row_center['id'], $promotions)) { ?>
        <div class="col-md-3 col-6 text-center">
          <a href="https://www.aquavelo.com/seance-decouverte/<?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>">
            <img src="/images/offre-av-mai-2026.png" 
                 alt="Promotion spéciale pour le centre <?= htmlspecialchars($promotions[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>" 
                 class="img-fluid" 
                 style="width: 100%; height: auto; max-width: 420px; object-fit: contain; display: block; margin: 0 auto;">
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
</section>




<?php


// Vérifier si des données ont été trouvées
if ($news_data) {
    // Afficher les données sur une page web
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>" . htmlspecialchars($news_data['titre']) . "</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #333; }
            img { max-width: 100%; height: auto; }
            .content { margin-top: 20px; }
        </style>
    </head>
    <body>
        <h1>" . htmlspecialchars($news_data['titre']) . "</h1>
        <div class='content'>
            <img src='" . htmlspecialchars($news_data['photo']) . "' alt='Image de l'article'>
            <p>" . nl2br(htmlspecialchars($news_data['news'])) . "</p>
        </div>
    </body>
    </html>";
} else {
    echo "<p>Aucune donnée trouvée pour cette condition.</p>";
}
?>





	      

<div class="col-md-6">
        <h2 class="form-group"> Essayez une séance gratuite de 45 mn </h2>
			
        <form role="form" class="contact-form" method="POST" action="_page.php">

		
          <div class="form-group">
            <label for="center">Dans quel centre souhaitez-vous effectuer votre séance ?</label>
            <select class="form-control" id="center" name="center">
              <?php foreach ($centers_list_d as &$free_d) { ?>
                <option <?php if (isset($_GET['city']) &&  $_GET['city'] == $free_d['city']) echo 'selected'; ?> value="<?= $free_d['id'] ?>"><?= $free_d['city'] ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label for="nom">Nom et prénom</label>
            <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom et prénom">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
          </div>
          <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="phone" class="form-control" id="phone" name="phone" placeholder="Téléphone">
          </div>
          <input type="hidden" name="reason" id="reason">
          <input type="hidden" name="segment" id="segment">
          <button type="submit" class="btn btn-default">Recevoir mon bon par email</button>
        </form>	
      </div>
     </div>

  <div class="col-md-6">
			
         <dl style="margin-top:30px;">
          <dt>Adresse </dt>
          <dd>
		  
            <?= $row_center['address']; ?>
          </dd>
          <dt>Téléphone </dt>
          <dd>
            <?= $row_center['phone']; ?>
          </dd>
          <dt>Horaires </dt>
          <dd>
            <?= $row_center['openhours']; ?>
          </dd>
          <dt>Découvrez la vie de votre centre </dt>
          <dd>
           <a href="https://www.facebook.com/<?= htmlspecialchars($row_center['facebook'], ENT_QUOTES, 'UTF-8'); ?>" 
  		 title="Facebook" 
  		 target="_blank" 
   		class="btn btn-default">Facebook</a>












		 
	

   
	   <?php	  
	   // Définition de la variable $date
		$date = date('d/m/Y');


            ?>


		  

<!-- Ajouter un espace entre les sections -->

	<div style="margin-top: 40px;"></div>

<dt><strong>Repas hypocalorique du <?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></strong></dt>
<dd>
    <p>Réalisé par Cyrielle Diététicienne pour perdre du poids rapidement :</p>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="width: 200px; padding: 10px; text-align: center;">Petit déjeuner</th>
                <th style="width: 200px; padding: 10px; text-align: center;">Repas du midi</th>
                <th style="width: 200px; padding: 10px; text-align: center;">Repas du soir</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">
                    <?php if (isset($menu_data['photo_pd'])): ?>
                        <img src="<?= htmlspecialchars($menu_data['photo_pd'], ENT_QUOTES, 'UTF-8'); ?>" alt="Photo du petit déjeuner" style="max-width: 100px;">
                    <?php else: ?>
                        <span>Image non disponible</span>
                    <?php endif; ?>
                </td>
                <td style="text-align: center;">
                    <?php if (isset($menu_data['photo_m'])): ?>
                        <img src="<?= htmlspecialchars($menu_data['photo_m'], ENT_QUOTES, 'UTF-8'); ?>" alt="Photo du repas du midi" style="max-width: 100px;">
                    <?php else: ?>
                        <span>Image non disponible</span>
                    <?php endif; ?>
                </td>
                <td style="text-align: center;">
                    <?php if (isset($menu_data['photo_s'])): ?>
                        <img src="<?= htmlspecialchars($menu_data['photo_s'], ENT_QUOTES, 'UTF-8'); ?>" alt="Photo du repas du soir" style="max-width: 100px;">
                    <?php else: ?>
                        <span>Image non disponible</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
        	<td style="padding: 10px;"><?= htmlspecialchars_decode($menu_data['petit_dejeuner'] ?? 'Non spécifié', ENT_QUOTES); ?></td>
		<td style="padding: 10px;"><?= htmlspecialchars_decode($menu_data['repas_midi'] ?? 'Non spécifié', ENT_QUOTES); ?></td>
		<td style="padding: 10px;"><?= htmlspecialchars_decode($menu_data['souper'] ?? 'Non spécifié', ENT_QUOTES); ?></td>
	   </tr>
        </tbody>
    </table>

    <div>
        <strong>Collation :</strong> <?= htmlspecialchars($menu_data['collation'] ?? 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?><br>
        <strong>Calories totales :</strong> <?= htmlspecialchars($menu_data['calories'] ?? 'Non spécifié', ENT_QUOTES, 'UTF-8'); ?>
        <p>Moyenne de la consommation calorique quotidienne d'une femme : 1500 calories. 
        Si vous maintenez un déficit calorique quotidien avec le menu ci-dessus, cela va entraîner <strong>une perte de poids.</strong> 👍</p>
    </div>

    <div>
        <?php if (isset($menu_envoye['photo_plat'])): ?>
            <img src="<?= htmlspecialchars($menu_envoye['photo_plat'], ENT_QUOTES, 'UTF-8'); ?>" alt="Photo du plat" style="max-width: 100px;">
        <?php else: ?>
            <span></span>
        <?php endif; ?>
    </div>
</dd>


<dl>
  <?php if (!empty($row_center['book_link'])) { ?>
    <dt>Agenda pour les adhérents</dt>
    <dd>
      <a href="https://<?= htmlspecialchars($row_center['book_link'], ENT_QUOTES, 'UTF-8'); ?>" 
         title="Réservation en ligne" 
         aria-label="Cliquez pour réserver en ligne"
         target="_blank" 
         class="btn btn-default">Réserver en ligne</a>
    </dd>
  <?php } ?>

<script>
function ouvre_popup(url) {
    window.open(url, '_blank', 'width=800,height=600,scrollbars=yes');
}
</script>


	

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


  <?php 
$plannings = [
    179 => 'https://www.dropbox.com/scl/fi/qbsl8jydinve58ouqscon/PLANNING.pdf?rlkey=1ugsl2mo7918q7af4f35jc6u5&st=c53qsrbc&dl=0',
    253 => 'https://www.dropbox.com/scl/fi/cdx3239ternr3lnrpk1gw/PLANNINGANTIBES.pdf?rlkey=ms9o5k3ithrillkl7g3n4tu3u&st=7uxywwwu&dl=0',
    305 => 'https://www.dropbox.com/scl/fi/ckpc872v3pelhw08ad3ei/PLANNING-CANNES.pdf?rlkey=b8lu043cu41bthgwm4allxonz&st=55k0x617&dl=0'
];

if (isset($row_center['id']) && isset($plannings[$row_center['id']])) { ?>
    <dt>Planning</dt>
    <dd>
        <a href="<?= htmlspecialchars($plannings[$row_center['id']], ENT_QUOTES, 'UTF-8'); ?>" 
           title="Télécharger le planning des cours" 
           aria-label="Télécharger le planning des cours" 
           target="_blank" 
           class="btn btn-default">
            Télécharger le planning des cours
        </a>
    </dd>
<?php } ?>

   <dt>Description</dt>
    <dd>
<p>
          <?= $row_center['description']; ?>
</p>
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
