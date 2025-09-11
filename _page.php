<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">AQUAVELO = AQUABIKING + AQUAGYM</h1>
    <h2 class="page-title pull-left">Excellent pour affiner et raffermir la silhouette, et perdre du poids si besoin.</h2>
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
      <?php if (isset($row_center['id']) && !in_array($row_center['id'], [305, 347, 349])) : ?>
        <div class="col-md-3 col-6 text-center">
          <img src="/cloud/thumbnail/center_<?= htmlspecialchars($row_center['id'], ENT_QUOTES, 'UTF-8'); ?>/3.jpg" 
               alt="Photo supplémentaire du centre Aquavélo" class="img-fluid img-same" width="300" height="200">
        </div>
      <?php endif; ?>

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

<!-- Formulaire et Informations supplémentaires -->
<div class="container">
  <div class="row">
    <!-- Formulaire -->
    <div class="col-md-6">
      <h2 class="form-group">Essayez une séance gratuite de 45 mn</h2>
	<?php if (isset($row_center['id']) && in_array($row_center['id'], [305, 347, 349])) : ?>
      	  	<p>en vous inscrivant sur notre <span style="color: #00acdc;"> <a href="https://calendly.com/aqua-cannes/rdv-aquavelo" target="_blank"><strong>calendrier</strong> (cliquez ici)</a></span> ou en prenant rendez-vous ci-dessous.</p>
	<?php endif; ?>
    
      <form role="form" class="contact-form" method="POST" 
      action="<?php echo ($row_center['id'] == 253) ? '_lead_webhook.php' : '_page.php'; ?>">

        <div class="form-group">
          <label for="center">Dans quel centre souhaitez-vous effectuer votre séance ?</label>
          <select class="form-control" id="center" name="center">
            <?php if (isset($centers_list_d)) : ?>
              <?php foreach ($centers_list_d as $free_d) : ?>
                <option <?php if (isset($_GET['city']) && $_GET['city'] == $free_d['city']) echo 'selected'; ?> value="<?= htmlspecialchars($free_d['id'], ENT_QUOTES, 'UTF-8'); ?>">
                  <?= htmlspecialchars($free_d['city'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
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
      <?php if (isset($row_center['id'])) : ?>
        <?php if ($row_center['id'] == 253) : ?>
          <div class="text-center mt-4">
            <img src="/images/planningAntibes.jpg" alt="Planning des cours Aquavelo Antibes" class="img-fluid" style="max-width: 100%; height: auto;">
          </div>
        <?php elseif (in_array($row_center['id'], [305, 347, 349])) : ?>
          <div class="text-center mt-4">
            <img src="/images/PLANNINGCANNES0125.jpg" alt="Planning des cours Aquavelo Cannes" class="img-fluid" style="max-width: 100%; height: auto;">
          </div>
        <?php elseif ($row_center['id'] == 179) : ?>
          <div class="text-center mt-4">
            <img src="/images/planningNice.jpg" alt="Planning des cours Aquavelo Nice" class="img-fluid" style="max-width: 100%; height: auto;">
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <!-- Informations supplémentaires -->
    <div class="col-md-6">
      <dl style="margin-top:30px;">
        <!-- Adresse, Téléphone, Horaires -->
        <dt>Adresse</dt>
        <dd><?= htmlspecialchars($row_center['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
        <dt>Téléphone</dt>
        <dd><?= htmlspecialchars($row_center['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
        <dt>Horaires</dt>
        <dd><?= htmlspecialchars($row_center['openhours'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>

	      
        <!-- Découvrez la vie de votre centre -->

    	
  
  
	
	<dt>Agenda pour les adhérents</dt>

	<dd>
	    <a href="<?php 
	        if (isset($row_center['id']) && $row_center['id'] == 179) {
	            echo 'https://member.resamania.com/aquavelonice';
	        } else {
	                echo 'https://member.resamania.com/aquavelo/'; // Sinon, on concatène correctement
	            }
	        
	    ?>" 
	    title="Réservation en ligne" 
	    aria-label="Cliquez pour réserver en ligne"
	    target="_blank" 
	    class="btn btn-default">Réserver en ligne</a>
	</dd>

	

   

	      

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

        <!-- Résultats Minceurs Rapides -->
        <dt>Résultats Minceurs Rapides</dt>
        <dd>
          <a href="#" class="btn btn-default" onclick="ouvre_popup('/nouveauResultat.html'); return false;" 
             title="Ouvrir les résultats minceurs" 
             aria-label="Ouvrir les résultats minceurs">
            Résultats Minceurs
          </a>
        </dd>

        <!-- Conseils pour perdre du poids -->
        <dt>Calculateur calories avec conseils minceur</dt>
        <dd>
          <a href="#" class="btn btn-default" onclick="ouvre_popup('/resultatMinceur.php'); return false;" 
             title="Calculateur de calories et conseils pour perdre du poids" 
             aria-label="Calculateur calories & conseils minceur">
            Conseils pour perdre du poids
          </a>
        </dd>
	<!-- Menu Perte de Poids -->
	<dt>Menu perte de poids</dt>
	<dd>
  	<a href="https://www.aquavelo.com/conseilminceur" class="btn btn-default" 
     	   title="Menu Perte de Poids" 
            aria-label="Menu Perte de Poids">
         Menu Perte de Poids
         </a>
	</dd>


        <!-- Description -->
        <dt>Description</dt>
        <dd>
          <p><?= $row_center['description'] ?? '' ?></p>

        </dd>
	
	      
      </dl>
    </div>
  </div>
</div>

<!-- Script JavaScript à la fin -->
<script>
  function ouvre_popup(url) {
    // Calculer 1/3 de la largeur et de la hauteur du viewport
    const width = Math.max(window.innerWidth / 3, 300); // Largeur minimale de 300 pixels
    const height = Math.max(window.innerHeight / 3, 200); // Hauteur minimale de 200 pixels

    // Centrer la pop-up
    const left = (window.innerWidth - width) / 2;
    const top = (window.innerHeight - height) / 2;

    // Ouvrir la fenêtre pop-up
    window.open(
      url, 
      'popup', 
      `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
    );
    return false;
  }
</script>
		   
		  
		   

<!-- / section --> 
