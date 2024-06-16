<header class="main-header clearfix">
  <div class="container">
   <h1 class="page-title pull-left" align="center">L'aquabiking est efficace pour l'affinement de la silhouette, la tonification et le bien-être.</h1>
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
	

});

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
      <div class="col-md-6"> <img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/1.jpg" alt="Aquavélo">
        <div class="row" style="margin-top:30px;">
		
          <?php if ($row_center['id'] != 305) { ?>
    <div class="col-md-6"> <img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/2.jpg" alt="Aquavélo">   </div>
<?php } else { ?>
    <div class="col-md-6"> <img src="/images/Cannes1.jpg" alt="Aquavélo">
    </div>
<?php } ?>

          <div class="col-md-6"> <img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/3.jpg" alt="Aquavélo"> </div>
</div>	
      
	
<?php if($row_center['id'] == 305)  { ?>
    <div class="animated activate fadeInLeft">
        <a href="https://www.aquavelo.com/seance-decouverte/Cannes">
            <img src="/images/promoJan24.webp" alt="Promotion Aquavelo">
        </a>
    </div>
<?php } elseif ($row_center['id'] == 253) { ?>
    <div class="animated activate fadeInLeft">
        <a href="https://www.aquavelo.com/seance-decouverte/Cannes">
            <img src="/images/promoJan24.webp" alt="Promotion Aquavelo">
        </a>
    </div>
<?php } elseif ($row_center['id'] == 347) { ?>
    <div class="animated activate fadeInLeft">
        <a href="https://www.aquavelo.com/seance-decouverte/Cannes">
            <img src="/images/promoJan24.webp" alt="Promotion Aquavelo">
        </a>
    </div>
<?php } ?>




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
           <dd> <a href="https://www.facebook.com/<?= $row_center['facebook']; ?>"" title="Facebook" target="_blank" class="btn btn-default">Facebook</a> </dd>

	


	

          <dd>
	   <?php	  
	   // Définition de la variable $date
		$date = date('d/m/Y');
            ?>

	 <dt><strong>Repas hypocalorique du <?php echo $date; ?></strong> réalisé par Cyrielle Diététicienne pour perdre du poids rapidement :</dt>
          <dd>			  
          <table>
            <tr>
              <th style="width: 200px; padding: 10px;">Petit déjeuner</th>
              <th style="width: 200px; padding: 10px;">Repas du midi</th>
              <th style="width: 200px; padding: 10px;">Repas du soir</th>
	  </tr>
	    <tr>
                <!-- Photo du petit déjeuner -->
		<td style="width: 200px; padding: 10px;"><img src="<?php echo $menu_data['photo_pd'];  ?>" alt="Photo du petit déjeuner" style="max-width: 100px;"></td>
		<!-- Photo du repas du midi -->
        	<td style="width: 200px; padding: 10px;"><img src="<?php echo $menu_data['photo_m'];  ?>" alt="Photo du repas du midi" style="max-width: 100px;"></td>
       		 <!-- Photo du repas du soir -->
		 <td style="width: 200px; padding: 10px;"><img src="<?php echo $menu_data['photo_s']; ?>" alt="Photo du repas du soir" style="max-width: 100px;"></td>
            </tr>
            <tr>
              <td style="width: 200px; padding: 10px;"><?php echo $menu_data['petit_dejeuner']; ?></td>
              <td style="width: 200px; padding: 10px;"><?php echo $menu_data['repas_midi']; ?></td>
              <td style="width: 200px; padding: 10px;"><?php echo $menu_data['souper']; ?></td>
            </tr>
          </table>

		  
		  
		 
	 <div>
 	  <strong>Collation:</strong> <?php echo $menu_data['collation']; ?>
	<br>
	  <strong>Calories totales:</strong> <?php echo $menu_data['calories']; ?>
	  <p>Moyenne de la consommation calorique quotidienne d'une femme : 1500 calories. 
        Si vous maintenez un déficit calorique quotidien avec le menu ci-dessus, cela va entraîner <strong>une perte de poids.</strong> 👍</p>
		 
	 </div>

	 <div>
	

 	
 	
	<!-- Photo du plat: <?php echo '<img src="' . $menu_envoye['photo_plat'] . '" alt="Photo du plat">'; ?>-->

	 </div>

          </dd>
	 <?php if($row_center['book_link']) { ?>
          <dt>Agenda pour les adhérents</dt>
          <dd> <a href="https://<?=$row_center['book_link'];?>/" title="Réservation en ligne" target="_blank" class="btn btn-default">Réserver en ligne</a> </dd>
		 <?php } ?>

	   </dd>
		   
	  <dd>
		 

		   
		   
          <dt>Résultats Minceurs Rapides</dt>
          <dd><a class= "btn btn-default" href="javascript:ouvre_popup('/nouveauResultat.html')">Résultats Minceurs</a>  </dd>
         <script type="text/javascript">
        function ouvre_popup(page) {
            window.open(page, "nom_popup", "menubar=no, status=no, scrollbars=no, menubar=no, width=700, height=700");
        }
	</script>
		    <dt>Mesurez vos résultats Minceurs ? </dt>
          <dd><a class= "btn btn-default" href="javascript:ouvre_popup('/_analyse.php')">Mesurez vos résultats Minceurs</a>  </dd>
         <script type="text/javascript">
        function ouvre_popup(page) {
            window.open(page, "nom_popup", "menubar=no, status=no, scrollbars=no, menubar=no, width=700, height=700");
        }
	</script>
		  
          
          <?php if($row_center['id'] == 179) { ?>
          <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/9ek3ipg53blseow/PLANNING.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
          <?php } elseif ($row_center['id'] == 253) { ?>
          <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/yvmr1e9os5znlnc/PLANNINGANTIBES.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
           <?php } elseif ($row_center['id'] == 305) { ?>
			 <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/u584xqy1ptaay39/PLANNING%20CANNES.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
           <?php } elseif ($row_center['id'] == 308) { ?>
			 <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/bp1og5armdvom7e/PLANNINGSTRAPHAEL.pdf?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
           <?php } elseif ($row_center['id'] == 338) { ?>
          <dt>Planning</dt>
          <dd> <a href="https://www.dropbox.com/s/x82imnnohskzosx/PLANNINGPUGET?dl=0" title="Réservation Resamania" target="_blank" class="btn btn-default">Télécharger le planning des cours</a> </dd>
 			<?php } ?>
          
        </dl>
		   
   
		   
		   
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
