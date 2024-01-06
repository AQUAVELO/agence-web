<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Je réserve ma séance Gratuite et mon bilan</h1>
    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li class="active">Séance découverte</li>
    </ol>
  </div>
</header>
<section class="content-area bg1">
  <div class="container">
    <div class="row">
     <div class="col-md-6"> <img src="/images/center_179/1.jpg" alt="Aquavélo intérieur">
        <div class="row" style="margin-top:30px;">
          <div class="col-md-6"> <img src="/images/center_179/2.jpg" alt="Aquavélo"> </div>
          <div class="col-md-6"> <img src="/images/center_179/3.jpg" alt="Aquavélo"> </div>
          
        

        </div>
      </div>
      <div class="col-md-6">
        <form role="form" class="contact-form" method="POST" action="?">
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
    <div class="row" style="margin-top:30px;">
      <div class="col-md-12">
        <dl>
          <dt>Adresse</dt>
          <dd>
            <?= $row_center['address']; ?>
          </dd>
          <dt>Téléphone</dt>
          <dd>
            <?= $row_center['phone']; ?>
          </dd>
          <dt>Horaires</dt>
          <dd>
            <?= $row_center['openhours']; ?>
          </dd>
          
           
           <?php if($row_center['book_link']) { ?>
          <dt>Agenda</dt>
          <dd> <a href="https://<?=$row_center['book_link'];?>/" title="Réservation en ligne" target="_blank" class="btn btn-default">Réserver en ligne</a> </dd>
          <?php } ?>
          
          
          
          
         

          
          
          
          
          
          
          
          

          <dt>Facebook</dt>
         
          
          <dd> <a href="https://www.facebook.com/aquavelo<?= $row_center['city']; ?>"" title="Facebook" target="_blank" class="btn btn-default">Facebook</a> </dd>

        </dl>
        <p>
          <?= $row_center['description']; ?>
        </p>
      </div>
    </div>
  </div>
  </div>
            
            
            
</section>
<!-- Google Code for S&eacute;ance gratuite Conversion Page -->
<script type="text/javascript">
  /* <![CDATA[ */
  var google_conversion_id = 966428486;
  var google_conversion_language = "fr";
  var google_conversion_format = "3";
  var google_conversion_color = "ffffff";
  var google_conversion_label = "tYZcCPLYqgkQxo7qzAM";
  var google_remarketing_only = false;
  /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
  <div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt="image" src="//www.googleadservices.com/pagead/conversion/966428486/?label=tYZcCPLYqgkQxo7qzAM&amp;guid=ON&amp;script=0" />
  </div>
</noscript>
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
