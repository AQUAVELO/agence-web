<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Centres d'aquabiking</h1>

    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li class="active">Centres</li>
    </ol>
  </div>
</header>


<section class="content-area bg1">
  <div class="container">

    <ul id="galleryFilters" class="option-set clearfix list-unstyled list-inline">
      <li><a href="#filter=*" class="btn btn-default btn-primary">Tous les centres</a></li>
      <li><a href="#filter=.france" class="btn btn-default">France</a></li>
      <li><a href="#filter=.maroc" class="btn btn-default">Maroc</a></li>
    </ul>

    <div id="galleryContainer" class="clearfix withSpaces col-3">
      <?php foreach ($centers_list_d as $row_centers_list) { ?>
      <div class="galleryItem france">
        <article class="portfolio-item">
          <div class="portfolio-thumbnail">
            <a href="/centres/<?=strtolower($row_centers_list['city']);?>"><img src="/crop.php?src=https://www.alesiaminceur.com/cloud/center_<?=$row_centers_list['id'];?>/1.jpg&w=300" alt=" "></a>
            <a href="/centres/<?=strtolower($row_centers_list['city']);?>" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a>
			 
			  
          </div>
          <div class="entry-meta">
            <span class="cat-links"><a href="#">France</a>, <a href="#">Europe</a></span>
          </div>
          <h4 class="entry-title"><a href="/centres/<?=strtolower($row_centers_list['city']);?>"><?=$row_centers_list['city'];?></span> centre avec <?=$row_centers_list['TypeAQUAVELO'];?></a></h4>
        </article>
        <!-- / portfolio-item -->
      </div>
      <?php } ?>

      <div class="galleryItem maroc">
        <article class="portfolio-item">
          <div class="portfolio-thumbnail">
            <a href="#"><img src="images/content/works-03.jpg" alt=" "></a>
            <a href="#" class="overlay-img"><span class="overlay-ico"><i class="fa fa-plus"></i></span></a>
          </div>
          <div class="entry-meta">
            <span class="cat-links"><a href="#">Maroc</a>, <a href="#">Afrique</a></span>
          </div>
          <h4 class="entry-title"><a href="#">Casablanca, Racine</a></h4>
        </article>
        <!-- / portfolio-item -->
      </div>
  
   
    </div>
    <!-- / galleryContainer -->


  </div>
</section>
<!-- / section -->


