<?php require_once('Include/Sessions.php'); ?>
<?php require_once('Include/functions.php') ?>
<?php ConfirmLogin(); ?>
<?php AdminGle(); ?>
<?php
if ( isset( $_POST['centre-delete'])) {
	$sql = "DELETE  FROM aquavelo_centre WHERE centre_id = '$_POST[deleteID]' ";
	$exec = Query($sql);
	if ($exec) {
		$_SESSION['successMessage'] = "Centre supprimer avec succès";
		Redirect_To('Centres.php');
	}else {
		$_SESSION['errorMessage'] = "Une erreur est survenue veuillez réessayer ultérieurement";
		Redirect_To('Centres.php');
	}

}else if( isset($_GET['delete_centre_id'])) {
	if (!empty($_GET['delete_centre_id'])) {
	      $sql = "SELECT * FROM aquavelo_centre WHERE centre_id = '$_GET[delete_centre_id]'";
		   $exec = Query($sql);
		if (mysqli_num_rows($exec) > 0 ) {
			if ($centre = mysqli_fetch_assoc($exec)) {
				$centre_id = $centre['centre_id'];
				$centre_name = $centre['centre_name'];
				$planning = $centre['planning'];
				$activites_annexes = $centre['activites_annexes'];
				$horaires =  $centre['horaires'];
				$page_fb = $centre['page_fb'];
				$video_youtube = $centre['video_youtube'];
			}
		}  
	}
}else {
	Redirect_To('Centres.php');
}

?>
<!DOCTYPE html>
<html>
<head>
     <meta charset="UTF-8" />
	<title>Supprimer un centre</title>
	<script src="jquery-3.2.1.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="Assets/styled-blog-admin.css">
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<div class="heading">

</div>
<div class="container-fluid">
	<div class="main">
		<div class="row">
			<div class="col-sm-2">
				<ul id="side-menu" class="nav nav-pills nav-stacked">
					<li ><a href="Centres.php">
					<span = class="glyphicon glyphicon-th"></span>
					 &nbsp;Centres</a></li>
					<li><a href="NewPost.php">
					<span = class="glyphicon glyphicon-list"></span>
					&nbsp;Nouveau Post</a></li>
					<li><a href="Categories.php">
					<span = class="glyphicon glyphicon-tags"></span>
					&nbsp;Catégories</a></li>
					<li class=""><a href="Admin.php">
					<span = class="glyphicon glyphicon-user"></span>
					&nbsp;Gérer les Admin</a></li>
					<li><a href="Comments.php">
					<span = class="glyphicon glyphicon-comment"></span>
					&nbsp;Commentaires</a></li>
					<li><a href="Blog.php">
					<span = class="glyphicon glyphicon-equalizer"></span>
					&nbsp;Live Blog</a></li>
					<li><a href="Lagout.php">
					<span = class="glyphicon glyphicon-log-out"></span>
					&nbsp;Se déconnecter</a></li>
				</ul>
			</div>
			<div class="col-xs-10">
				<div class="page-title"><h1>Supprimer le centre</h1></div>
					<?php echo Message(); ?>
					<?php echo SuccessMessage(); ?>
					<form action="deleteCentre.php" method="POST" enctype="multipart/form-data">
						<fieldset>
							<div class="form-group">
								<button name="centre-delete" class="btn btn-danger form-control">SUPPRIMER</button>
							</div>
							<fieldset>		
							<div class="form-group">
								<label>Nom du centre : <?php echo htmlentities($centre_name); ?></label>
								<input disabled type="hidden" name="centre_name" value="<?php echo $centre_name; ?>"><br>	      
							</div>

							<div class="form-group">
								<labal for="planning">Planning</labal>
								<input disabled type="text" name="planning" class="form-control" value="<?php echo $planning ?>">
							</div>
							
							<div class="form-group">
								<labal for="centre-title">Activités annexes :</labal>
								<input disabled type="text" name="activites_annexes" class="form-control" id="activites_annexes" value="<?php echo $activites_annexes ?>">
							</div>
							
							<div class="form-group">
								<labal for="centre-title">Horaires :</labal>
								<input disabled type="text" name="horaires" class="form-control" id="activites_annexes" value="<?php echo $horaires ?>">
							</div>
	
							<div class="form-group">
								<labal for="centre-title">Page Facebook :</labal>
								<input disabled type="text" name="page_fb" class="form-control" id="page_fb" value="<?php echo $page_fb ?>">
							</div>
							
							<div class="form-group">
								<labal for="centre-title">Vidéo youtub:</labal>
								<input disabled type="text" name="video_youtube" class="form-control" id="video_youtube" value="<?php echo $video_youtube ?>">
							</div>
							
							
							
							<input type="hidden" name="deleteID" value="<?php echo $_GET['delete_centre_id']; ?>">
				
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