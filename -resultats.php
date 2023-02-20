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
        <p>
          <?= $row_center['description']; ?>
        </p>
      </div>
    </div>
  </div>
</section>
