<?phpphp require_once('Include/Sessions.php'); ?>
<?phpphp require_once('Include/functions.php') ?>
<?phpphp 
	if ( isset($_GET['id']) ) {
		$post_id = $_GET['id'];
		$post_title = "";
		$sql = "SELECT * FROM aquavelo_post WHERE post_id = '$post_id'";
		$exec = Query($sql);
		if ($title = mysqli_fetch_assoc($exec)) {
			$post_title = $title['title'];
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
	<title><?phpphp echo $post_title; ?></title>
	<meta name="keywords" content="aquavelo, aquabiking, aquabike">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Blog</title>
	<script src="jquery-3.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="Assets/styled-blog.css">
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<meta name="description" content="Cours d'aquabiking collectif en piscine animés par un coach sportif diplômé. Éliminez votre cellulite en pédalant dans l'eau et brûlez trois plus de calories en une séance d'aquabike.">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="/css/animate.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
<style type="text/css">
body, td, th {
	font-family: 'Open Sans', sans-serif;
}
</style>
<script src="/js/modernizr.custom.js"></script>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="/bootstrap/js/html5shiv.js"></script>
  <script src="/bootstrap/js/respond.min.js"></script>
  <![endif]-->

</head>
<body>
<div class="blog">
  <!-- navbar -->
  <nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
       <a class="navbar-brand" href="http://aquavelo.com/"><img src="/images/content/logo.png" alt="Aquabiking collectif"></a> </div>
      <div class="navbar-collapse collapse">
        <form class="pull-right header-search" role="form" style="display:none;">
          <fieldset>
            <div class="container">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Type to search...">
              </div>
              <button type="submit"><i class="fa fa-search"></i></button>
            </div>
          </fieldset>
        </form>
        <a href="#" id="showHeaderSearch" class="hidden-xs"><i class="fa fa-search"></i></a>
        <ul class="nav navbar-nav navbar-right">
          <li> <a href="http://aquavelo.com/">Accueil</a> </li>
          <li class="dropdown"> <a href="/aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Aquabiking</a>
            <ul class="dropdown-menu">
              <li><a href="/aquabiking">Le vélo dans l'eau</a></li>
              <li><a href="/aquabiking">Les bienfaits</a></li>
            </ul>
          </li>
          <li class="dropdown"> <a href="/centres" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Centres</a>
            <ul class="dropdown-menu">
            
                        <li><a href="/centres/Antibes" title="Aquabiking à Antibes">Antibes</a></li>
                        <li><a href="/centres/Bandol" title="Aquabiking à Bandol">Bandol</a></li>
                        <li><a href="/centres/Boulogne-Billancourt" title="Aquabiking à Boulogne-Billancourt">Boulogne-Billancourt</a></li>
                        <li><a href="/centres/Cannes" title="Aquabiking à Cannes">Cannes</a></li>
                        <li><a href="/centres/Collioure" title="Aquabiking à Collioure">Collioure</a></li>
                        <li><a href="/centres/Dijon" title="Aquabiking à Dijon">Dijon</a></li>
                        <li><a href="/centres/Draguignan" title="Aquabiking à Draguignan">Draguignan</a></li>
                        <li><a href="/centres/Fréjus" title="Aquabiking à Fréjus">Fréjus</a></li>
                        <li><a href="/centres/Grasse" title="Aquabiking à Grasse">Grasse</a></li>
                        <li><a href="/centres/Grigny" title="Aquabiking à Grigny">Grigny</a></li>
                        <li><a href="/centres/Guilherand-Granges" title="Aquabiking à Guilherand-Granges">Guilherand-Granges</a></li>
                        <li><a href="/centres/Hyères" title="Aquabiking à Hyères">Hyères</a></li>
                        <li><a href="/centres/Livry-Gargan" title="Aquabiking à Livry-Gargan">Livry-Gargan</a></li>
                        <li><a href="/centres/Menton" title="Aquabiking à Menton">Menton</a></li>
                        <li><a href="/centres/Montargis" title="Aquabiking à Montargis">Montargis</a></li>
                        <li><a href="/centres/Montauroux" title="Aquabiking à Montauroux">Montauroux</a></li>
                        <li><a href="/centres/Nantes" title="Aquabiking à Nantes">Nantes</a></li>
                        <li><a href="/centres/Nice" title="Aquabiking à Nice">Nice</a></li>
                        <li><a href="/centres/Puget-sur-Argens" title="Aquabiking à Puget-sur-Argens">Puget-sur-Argens</a></li>
                        <li><a href="/centres/Saint-Étienne" title="Aquabiking à Saint-Étienne">Saint-Étienne</a></li>
                        <li><a href="/centres/Saint-Raphaël" title="Aquabiking à Saint-Raphaël">Saint-Raphaël</a></li>
                        <li><a href="/centres/Sainte-Geneviève-des-Bois" title="Aquabiking à Sainte-Geneviève-des-Bois">Sainte-Geneviève-des-Bois</a></li>
                        <li><a href="/centres/Toulouse" title="Aquabiking à Toulouse">Toulouse</a></li>
                        <li><a href="/centres/Valbonne" title="Aquabiking à Valbonne">Valbonne</a></li>
                        <li><a href="/centres/Valence" title="Aquabiking à Valence">Valence</a></li>
                        <li><a href="/centres/Villebon-sur-Yvette" title="Aquabiking à Villebon-sur-Yvette">Villebon-sur-Yvette</a></li>
                          
            </ul>
          </li>
			<!--
		  <li> <a href="http://boutique.hyperminceur.com/" target="_blank">Boutique</a> </li>
			-->
          <li class="dropdown"> <a href="/concept-aquabiking" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Concept</a>
<ul class="dropdown-menu">
              <li><a href="/concept-aquabiking#ouvrir">Ouvrir un centre</a></li>
            </ul>
          </li>
		   <li><a href="/Blog.php">Blog</a></li>
           <li class="dropdown"> <a href="/contact" class="dropdown-toggle" data-toggle="dropdown" data-target="#">Contact</a>
<ul class="dropdown-menu">
              <li><a href="/contact">Emploi</a></li>
              <li><a href="/contact">Contactez-nous</a></li>

            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- / navbar -->
	<div class="container">
		<div class="blog-title">
		</div>
		<div class="row">
			<div class="col-md-8">
				<?phpphp echo SuccessMessage(); ?>
				<?phpphp echo Message(); ?>
				<?phpphp
					if( isset($_GET['id'])) {
						$query = "SELECT * FROM aquavelo_post WHERE post_id = '$_GET[id]'";
						$exec = Query($query);
						if (mysqli_num_rows($exec) > 0) {
							while ($post = mysqli_fetch_assoc($exec) ) {
								$post_id = $post['post_id'];
								$post_date = $post['post_date_time'];
								$post_title = $post['title'];
								$post_category = $post['category'];
								$post_author = $post['author'];
								$post_image = $post['image'];
								$post_content = $post['post']; 
							?>
							<div class="post">
								<div class="post-title"><h2><?phpphp echo htmlentities($post_title); ?></h2></div>
								<div class="thumbnail">
									<img class="img-responsive img-rounded" src="Upload/Image/<?phpphp echo $post_image; ?>">
								</div>
								<div class="post-info">
									<p class="">
									<?phpphp 
								     date_default_timezone_set('Europe/Paris');
									 setlocale (LC_TIME, 'fr_FR.utf8','fra');
									 $time = strtotime($post_date); 
                                     $date = getDate($time);
									 $date_pub = strftime("%d", $time). " " . strftime("%B", $time) . " " .strftime("%Y", $time) ;
									?>
									Publier le : <?phpphp echo htmlentities($date_pub); ?> | Catégorie: <?phpphp echo htmlentities($post_category);?> | Par: <?phpphp echo $post_author; ?>
									</p>
								</div>
								<div class="post-content">
								<p class=""><?phpphp echo nl2br($post_content); ?></p>
								</div>
							</div>
							<?phpphp
							}
						}
					}else {
						Redirect_To('Blog.php');
					}
				?>
				<div class="comment-section">
					<form method="POST" action="comment.php?>'"> 
						<legend>Ajouter un commentaire a cet article</legend>
						<div class="form-group">
							<label>Email :</label>
							<input type="email" name="email" placeholder="Votre Email" class="form-control">
						</div>
						<div class="form-group">
							<label>Commentaire :</label>
							<textarea name="comment" placeholder="Votre Commentaire ici" class="form-control" rows="10"></textarea>
						</div>
						<div class="form-group">
							<input type="submit" name="submit" class="btn btn-primary" value="Envoyer le Commentaire">
						</div>
						<input type="hidden" name="id" value="<?phpphp echo $_GET['id']; ?>">
					</form>
				</div>
				<div class="page-header"><h4>Commentaires</h4></div>
				<?phpphp
					date_default_timezone_set('Europe/Paris');
    				 setlocale (LC_TIME, 'fr_FR.utf8','fra');
					$sql = "SELECT * FROM comment WHERE post_id = '$_GET[id]' AND status = 'approved'";
					$exec = Query($sql);
					if (mysqli_num_rows($exec) > 0) {
						while ($comments = mysqli_fetch_assoc($exec)) {
							$c_email = $comments['email'];
							$c_dateTime = $comments['date_time'];
									 $time = strtotime($c_dateTime); 
									 $date_pub_cmt = strftime("%d", $time). " " . strftime("%B", $time) . " " . strftime("%Y", $time) . " à " . strftime("%H", $time). ":" . strftime("%M", $time) ;
									
							
							$c_comment = $comments['comment'];
							?>
							
							<div class="comment-block" style="margin-bottom: 20px; ">
								<div class="row">
									<div class="col-sm-2" style="height: 70px;width: 100px; padding:0; margin:0; background-color: transparent">
									<img src="Assets/Images/man.svg" height="70px" width="100px">
									</div>
									<div class="col-sm-10">
										<div><span class="lead text-info"><?phpphp echo $c_email; ?></span></div>
										<div><span><?phpphp echo $date_pub_cmt; ?></span></div>
										<div><span class=""> a ecrit </span>: <span class="lead"> <?phpphp echo $c_comment; ?></span></div>
									</div>
								</div>
							</div>

							<?phpphp
						}
					}else {
							echo "Aucun commentaire ";
							echo "<br />";echo "<br />";echo "<br />";
						}
				?>
				
			</div><!--END OF COL-MD-8  -->
			
			<div class="col-md-3 post-side-menu col-md-offset-1">
<div class="panel panel-primary" style="border: 0px !important">

					<div class="panel-body" style="border: 0px !important">
									<form action="Blog.php" method="GET" >
					<div class="input-group">
						<input type="text" name="search" class="form-control" placeholder="chercher un article">
						<span class="input-group-btn">
							<button class="btn btn-default"><span class="glyphicon glyphicon-search"></button>
						</span>
					</div>
				</form></div>
				</div>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h2 class="panel-title">Articles récents</h2>
					</div>
					<div class="panel-body">
						<?phpphp
							$sql = "SELECT * FROM aquavelo_post ORDER BY post_date_time LIMIT 5";
							$exec = Query($sql);
							while ($recentPost = mysqli_fetch_assoc($exec)) {
								$postID = $recentPost['post_id'];
								?>
								<nav>
									<ul>
										<li><a href="Post.php?id=<?phpphp echo $postID; ?>"><?phpphp echo $recentPost['title'] ?></a></li>
									</ul>
								</nav>
								<?phpphp
							}
						?>
					</div>
				</div>

				<div class="panel panel-primary">
					<div class="panel-heading">
						<h2 class="panel-title">Catégories</h2>
					</div>
					<div class="panel-body">
						<nav>
							<ul>
						<?phpphp 
							$sql = "SELECT cat_name FROM aquavelo_category ";
							$exec = Query($sql);
							if (mysqli_num_rows($exec) > 0) {
								while ($category = mysqli_fetch_assoc($exec)) {
									?>
									<li><a href="Blog.php?category=<?phpphp echo $category['cat_name'] ?>"><?phpphp echo $category['cat_name'] ?></a></li>
									<?phpphp
								}
							}	
						?>
							
								
							</ul>
						</nav>
					</div>
				</div>
			</div> <!--END OF COL-MD-4  -->
		</div> <!--END OF ROW  -->
	</div>
</div>
  <section class="content-area prefooter">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <article class="table-content animated" data-fx="flipInY">
            <section class="table-row">
              <div class="table-cell">
                <h1 class="widget-title">Suivez-nous</h1>
              </div>
              <div class="table-cell">
                <ul class="socialIcons bigIcons">
                  <li><a href="http://www.facebook.com/aquavelonice" data-toggle="tooltip" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                  <li><a href="http://twitter.com/AquaveloNice" data-toggle="tooltip" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                  <li><a href="http://plus.google.com/108184119422520190690" data-toggle="tooltip" title="Google+"><i class="fa fa-google-plus"></i></a></li>
                  <li><a href="#" data-toggle="tooltip" title="Pinterest"><i class="fa fa-pinterest"></i></a></li>
                </ul>
              </div>
            </section>
          </article>
        </div>
        <div class="col-md-6">
          <div class="newsletterForm">
            <div class="successMessage alert alert-success alert-dismissable" style="display: none">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              Abonnement confirmé. </div>
            <div class="errorMessage alert alert-danger alert-dismissable" style="display: none">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              Une erreur est survenue. </div>
            <form class="liveForm" role="form" action="/form/send.php" method="post" data-email-subject="Newsletter Form" data-show-errors="true" data-hide-form="true">
              <fieldset>
                <article class="table-content animated" data-fx="flipInY">
                  <section class="table-row">
                    <div class="table-cell">
                      <h1 class="widget-title">Recevoir la newsletter</h1>
                    </div>
                    <div class="table-cell">
                      <label class="sr-only">Adresse e-mail</label>
                      <input type="email" name="field[]" class="form-control" placeholder="Adresse e-mail">
                    </div>
                    <div class="table-cell">
                      <input type="submit" class="btn btn-primary" value="S'abonner">
                    </div>
                  </section>
                </article>
              </fieldset>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- section -->

  <footer>
    <div class="container mainfooter">
      <div class="row">
        <aside class="col-md-3 widget"> <img src="/images/content/logo-footer.png" alt=" "> <br>
          <br>
          <p class="darker">Aquabiking collectif en piscine</p>
          <p class="darker">&copy; 2014</p>
        </aside>
        <aside class="col-md-3 widget">
          <h1 class="widget-title">Contactez-nous</h1>
          <a href="mailto:claude@alesiaminceur.com" class="larger">claude@alesiaminceur.com</a>
          <p> 60 avenue du Docteur Picaud<br>
            06150 Cannes</p>
          <p> T&eacute;l.: +33 (0)4 93 93 05 65</p>
        </aside>
        <aside class="col-md-3 widget">
          <h1 class="widget-title">Tweets r&eacute;cents</h1>
          <a class="twitter-timeline" href="https://twitter.com/AquaveloNice" data-widget-id="476110394226798593">Tweets de @AquaveloNice</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>



        </aside>
        <aside class="col-md-3 widget">
          <h1 class="widget-title">Flux de photo</h1>
          <div class="flickr_badge">
            <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14199424047/" title="IMG_1877 de aquavelo, sur Flickr"><img src="https://farm3.staticflickr.com/2913/14199424047_ace3ffa999.jpg" width="80" alt="IMG_1877"></a></div>
            <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14384877104/" title="IMG_1896 de aquavelo, sur Flickr"><img src="https://farm4.staticflickr.com/3894/14384877104_6f08cfe0d0.jpg" width="80" alt="IMG_1896"></a> 
            </div>
            <div class="flickr_badge_image"> <a href="https://www.flickr.com/photos/124590983@N08/14199323080/" title="IMG_1921 de aquavelo, sur Flickr"><img src="https://farm3.staticflickr.com/2924/14199323080_07f86b8ee9.jpg" width="80" alt="IMG_1921"></a> </div>
          </div>
        </aside>
      </div>
    </div>
  </footer>
</body>
<!-- boxedWrapper --> 

<a href="#" id="toTop"><i class="fa fa-angle-up"></i></a> 
<script src="/js/jquery.min.js"></script> 
<script src="/bootstrap/js/bootstrap.min.js"></script> 
<script src="/js/detectmobilebrowser.js"></script> 
<script src="/js/gmap3.min.js"></script> 
<script src="/js/jquery.appear.js"></script> 
<script src="/js/jquery.isotope.min.js"></script> 
<script src="/js/jquery.ba-bbq.min.js"></script> 
<script src="/js/jquery.countTo.js"></script> 
<script src="/js/jquery.fitvids.js"></script> 
<script src="/js/jquery.flexslider-min.js"></script> 
<script src="/js/jquery.magnific-popup.min.js"></script> 
<script src="/js/jquery.mb.YTPlayer.js"></script> 
<script src="/js/jquery.placeholder.min.js"></script> 
<script src="/js/retina-1.1.0.min.js"></script> 
<script src="/js/timeline/js/storyjs-embed.js"></script> 
<script src="/form/js/form.js"></script> 
<!--<script src="/twitter/js/jquery.tweet.js"></script> -->
<script src="/js/main.js"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40045225-6', 'auto');
  ga('send', 'pageview');

</script>
</html>