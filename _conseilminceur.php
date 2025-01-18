<header class="main-header clearfix">
  <div class="container">
	  
   <h1 class="page-title pull-left">AQUAVELO = AQUABIKING + AQUAGYM</h1>
	  <h2 class="page-title pull-left">Excellent pour affiner la silhouette, la tonification et le bien-être.</h2>
   
    <ol class="breadcrumb pull-right">
   
	
	
	










 <div class="container">
        <?php
        // Vérifier si des articles ont été trouvés
        if (!empty($news_datas)) {
            // Afficher un message indiquant qu'il y a des résultats
            echo "<p>Nombre d'articles trouvés : " . count($news_datas) . "</p><br>";

            // Afficher les données de chaque ligne
            foreach ($news_datas as $article) {
                echo '<div class="article">';
                
                // Afficher l'image si elle existe
                if (!empty($article["photo"])) {
                    echo '<img src="' . htmlspecialchars($article["photo"]) . '" alt="Image de l\'article">';
                }
                
                // Contenu (titre et article) à droite de l'image
                echo '<div class="article-content">';
                echo '<h2>' . htmlspecialchars($article["titre"]) . '</h2>';
                echo '<p>' . nl2br(htmlspecialchars($article["news"])) . '</p>';
                echo '</div>';
                
                echo '</div>';
            }
        } else {
            echo "Aucun article trouvé.";
        }
        ?>
    </div>



	    








		 
 

	    
	
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
