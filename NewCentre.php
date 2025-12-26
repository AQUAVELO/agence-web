	<?php require_once('Include/Sessions.php'); ?>
<?php require_once('Include/functions.php') ?>
<?php ConfirmLogin(); ?>
<?php AdminGle(); ?>

<?php

date_default_timezone_set('Europe/Paris');
$time = time();
if ( isset( $_POST['post-submit'])) {
	$centre_name = mysqli_real_escape_string($con, $_POST['centre_name']);
    $planning =  mysqli_real_escape_string($con, $_POST['centre_planning']);
	$horaires =  mysqli_real_escape_string($con, $_POST['horaires']);
	$activites_annexes = mysqli_real_escape_string($con, $_POST['activites_annexes']);
	$page_fb = mysqli_real_escape_string($con, $_POST['page_fb']);
    $video_youtube = mysqli_real_escape_string($con, $_POST['video_youtube']);
	
		$query = "INSERT INTO aquavelo_centre (centre_name, planning, horaires, activites_annexes, page_fb, video_youtube) 
		VALUES ('$centre_name', '$planning', '$horaires', '$activites_annexes', '$page_fb', '$video_youtube')";
		$exec = Query($query);
		if ($exec) {
						
			move_uploaded_file($_FILES['centre_planning']['tmp_name'], $imageDirectory);
			$_SESSION['successMessage'] = "Informations du Centre ajouter avec succeès";
			echo "<script>setTimeout(\"location.href = 'NewCentre.php';\",1500);</script>";
		}else {
			$_SESSION['errorMessage'] = "Une erreur est survenue veuillez réessayer ";

		}

	
}
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="UTF-8">
	<title>Dashboard</title>
	<script src="jquery-3.2.1.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="Assets/styled-blog-admin.css">
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<div class="heading">
	<a href="Blog.php"><p>Visiter le Blog</p></a>
</div>
<div class="container-fluid">
	<div class="main">
		<div class="row">
			<div class="col-sm-2">
				<ul id="side-menu" class="nav nav-pills nav-stacked">
					<li ><a href="Dashboard.php">
					<span = class="glyphicon glyphicon-th"></span>
					 &nbsp;Dashboard</a></li>
					<li class="active"><a href="NewPost.php">
					<span = class="glyphicon glyphicon-list"></span>
					&nbsp;Nouveau Post</a></li>
					<li><a href="Categories.php">
					<span = class="glyphicon glyphicon-tags"></span>
					&nbsp;Catégories</a></li>
					<li><a href="Admin.php">
					<span = class="glyphicon glyphicon-user"></span>
					&nbsp;Gérer les Admin</a></li>
					<li><a href="Comments.php">
					<span = class="glyphicon glyphicon-comment"></span>
					&nbsp;Commentaires</a></li>
					<li><a href="Blog.php">
					<span = class="glyphicon glyphicon-equalizer"></span>
					&nbsp;Live Blog</a></li>
					<li><br /></li>
					<li><a href="Centres.php">
					<span = class="glyphicon glyphicon-th"></span>
					&nbsp;Centres</a></li>
					<li   class="active"><a href="NewCentre.php">
					<span = class="glyphicon glyphicon-list"></span>
					&nbsp;Informations d'un centres</a></li>
					<li><br /></li>
					<li><a href="Lagout.php">
					<span = class="glyphicon glyphicon-log-out"></span>
					&nbsp;Se déconnecter</a></li>
				</ul>
			</div>
			<div class="col-xs-10">
				<div class="page-title"><h1>Ajouter les info d'un centre</h1></div>
					<?php echo Message(); ?>
					<?php echo SuccessMessage(); ?>
					<form action="NewCentre.php" method="POST" enctype="multipart/form-data">
						<fieldset>
							<div class="form-group">
								<labal for="post-category">Nom du centre :</labal>
								<select class="form-control" name="centre_name" id="post-category" required >
								    <option desabled value="">Choisissez un centre</option>
									<option>Antibes</option>
									<option>Bandol</option>
									<option>Boulogne-Billancourt</option>
									<option>Cannes</option>
									<option>Collioure</option>
									<option>Dijon</option>
									<option>Draguignan</option>
									<option>Fréjus</option>
									<option>Grasse</option>
									<option>Grigny</option>
									<option>Guilherand-Granges</option>
									<option>Hyères</option>
									<option>Livry-Gargan</option>
									<option>Menton</option>
									<option>Montargis</option>
									<option>Montauroux</option>
									<option>Nantes</option>
									<option>Nice</option>
									<option>Puget-sur-Argens</option>
									<option>Saint-Étienne</option>
									<option>Saint-Raphaël</option>
									<option>Sainte-Geneviève-des-Bois</option>
									<option>Toulouse</option>
									<option>Valbonne</option>
									<option>Valence</option>
									<option>Villebon-sur-Yvette</option>
								</select>
							</div>
							
						    <div class="form-group">
								<labal for="post-planning">planning :</labal>
								<input type="text" name="centre_planning" class="form-control" placeholder="https://www.dropbox.com/s/ippgfpky29n0vqg/PLANNING%20CANNES.pdf?dl=0" required>
							</div>
                             
							     <div class="form-group">
								<labal for="post-planning">Horaires :</labal>
								<input type="text" name="horaires" class="form-control" placeholder="Du Lundi au Vendredi de 8h à 21H, Samedi 9h à 17h et le Dimanche 9h à 13h" required>
							</div>
							  
							<div class="form-group">
								<labal for="post-title">Activités annexes :</labal>
								<input type="text" name="activites_annexes" class="form-control" id="post-title" placeholder="Activités annexes" required>
							</div> 
							 
							<div class="form-group">
								<labal for="post-title">Page Facebook :</labal>
								<input type="text" name="page_fb" class="form-control" id="post-title" required placeholder="https://web.facebook.com/aquaveloCannes/?ref=bookmarks&_rdc=1&_rdr">
							</div>
							
							<div class="form-group">
								<labal for="post-title">Vidéo youtube:</labal>
								<input type="text" name="video_youtube" class="form-control" id="video_youtube" placeholder="ID de la video Exemple : Zh2bZiaosOg " required >
							</div>

							<div class="form-group">
								<button name="post-submit" class="btn btn-primary form-control">Ajouter</button>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="row" id="blog-footer">
		<div class="col-sm-12">
		<hr>
			<p>Aquabiking collectif en piscine © 2019</p>
		<hr>
		</div>
	</div>
</div>
<script type="text/javascript" src="jquery.js"></script>
</body>
</html>