<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Aquabiking à <?php echo $city; ?>,
      <?= $row_center['postal_code']; ?>
      , <?php echo $department; ?>, <?php echo $region; ?></h1>
    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li><a href="/centres">Centres</a></li>
      <li class="active"><?= $city; ?>, <?= $department; ?></li>
    </ol>
  </div>
	
		  <?php if($row_center['id'] == 253) { ?>

          <!-- Facebook Pixel Code -->

<script>

!function(f,b,e,v,n,t,s)

{if(f.fbq)return;n=f.fbq=function(){n.callMethod?

n.callMethod.apply(n,arguments):n.queue.push(arguments)};

if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';

n.queue=[];t=b.createElement(e);t.async=!0;

t.src=v;s=b.getElementsByTagName(e)[0];

s.parentNode.insertBefore(t,s)}(window,document,'script',

'https://connect.facebook.net/en_US/fbevents.js');

 fbq('init', '259009481449831'); 

fbq('track', 'PageView');

</script>

<noscript>

 <img height="1" width="1" 

src="https://www.facebook.com/tr?id=259009481449831&ev=PageView

&noscript=1"/>

</noscript>

<!-- End Facebook Pixel Code -->

          <?php } ?>
	


	
</header>
<section class="content-area bg1">
  <div class="container">
    <div class="row">
      <div class="col-md-6"> <img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/1.jpg" alt=" ">
        <div class="row" style="margin-top:30px;">
          <div class="col-md-6"> <img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/2.jpg" alt=" "> </div>
          <div class="col-md-6"> <img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/3.jpg" alt=" "> </div>
        </div>
      </div>
	   
      <div class="col-md-6">
         <a href="/seance-decouverte/<?= $row_center['city']; ?>" class="btn btn-default">J'essaie un cours Gratuit</a>

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
           <dd> <a href="https://www.facebook.com/<?= $row_center['facebook']; ?>"" title="Facebook" target="_blank" class="btn btn-default">Facebook</a> </dd>

	 
          <dd>
		    <?php if($row_center['book_link']) { ?>
          <dt>Agenda pour les adhérents</dt>
          <dd> <a href="https://<?=$row_center['book_link'];?>/" title="Réservation en ligne" target="_blank" class="btn btn-default">Réserver en ligne</a> </dd>
          <?php } ?>

	   </dd>
		   
		   <dd>
		
          <dt>Résultats Minceurs Rapides</dt>
          <dd> </dd>
		   
		      		             

	   </dd>
         
          
          
          <?php if($row_center['id'] == 179) { ?>
          <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/z2hus3pm88qtvsl/PLANNING.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
          <?php } elseif ($row_center['id'] == 253) { ?>
          <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/yvmr1e9os5znlnc/PLANNINGANTIBES.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
           <?php } elseif ($row_center['id'] == 305) { ?>
			 <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/u584xqy1ptaay39/PLANNING%20CANNES.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
           <?php } elseif ($row_center['id'] == 308) { ?>
			 <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/lykwimvoey600r7/PLANNINGSTRAPHAEL.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
           <?php } elseif ($row_center['id'] == 338) { ?>
          <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/4t3epny43nyq60i/PLANNINGPUGET.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
 			<?php } ?>
          
        </dl>
		   

	<body>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
        LES RÉSULTATS MINCEURS<br>Cliquez ici
    </button>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Contact</title>
</head>
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Les résultats sur la silhouette</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">Voici quelques résultats obtenus avec une pratique réguliére de
                                    l'Aquavelo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img src="resultats/CHRISTINE.jpg" alt="Christine a minci" width=60% /></td>
                                <td><img src="resultats/MEDE.jpg" alt="Christine a minci" width=60% /></td>
                            </tr>
                            <tr>
                                <td><img src="resultats/SOPHIE.jpg" alt="Christine a minci" width=60% /></td>
                                <td><img src="resultats/AURELIE.jpg" alt="Christine a minci" width=60% /></td>
                            </tr>
                            <tr>
                                <td><img src="resultats/SANDRA.jpg" alt="Christine a minci" width=60% /></td>
                                <td><img src="resultats/PATY.jpg" alt="Christine a minci" width=60% /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
                    integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
                    crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
 </body>
		   
		   
		   
        <p>
          <?= $row_center['description']; ?>
        </p>
      </div>
    </div>
  </div>
      

</section>
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
