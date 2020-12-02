<?phpphp require_once('Include/Sessions.php'); ?>
<?phpphp require_once('Include/functions.php') ?>
<?phpphp ConfirmLogin(); ?>
<?phpphp
if ( isset( $_POST['centre-update'])) {
	$centre_name = mysqli_real_escape_string($con, $_POST['centre_name']);
	$activites_annexes = mysqli_real_escape_string($con, $_POST['activites_annexes']);
	$horaires =  mysqli_real_escape_string($con, $_POST['horaires']);
	$page_fb = mysqli_real_escape_string($con, $_POST['page_fb']);
	$video_youtube = mysqli_real_escape_string($con, $_POST['video_youtube']);
	$planning = mysqli_real_escape_string($con, $_POST['planning']); 

	

	$sql = "UPDATE aquavelo_centre SET  planning = '$planning', activites_annexes = '$activites_annexes', horaires= '$horaires', page_fb = '$page_fb', video_youtube = '$video_youtube' WHERE centre_id = '$_POST[idFromUrl]' ";
	$exec = Query($sql);
	if($exec) {
		$_SESSION['successMessage'] = 'Centre mis à jour avec Succès';
			Redirect_To('Centre.php');
		
	}else {
		$_SESSION['errorMessage'] = 'Une erreur est survenue veuillez réessayer ultérieurement';

		Redirect_To('Centre.php');
	}

}else if( isset($_GET['centre_id'])) {
	if (!empty($_GET['centre_id'])) {
		$sql = "SELECT * FROM aquavelo_centre WHERE centre_id = '$_GET[centre_id]'";
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
		Redirect_To('Centre.php');
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
	
</div>
<div class="container-fluid">
	<div class="main">
		<div class="row">
			<div class="col-sm-2">
				<ul id="side-menu" class="nav nav-pills nav-stacked">

					<li><a href="Centres.php">
					<span = class="glyphicon glyphicon-th"></span>
					&nbsp;Centre</a></li>
					<li   class="active"><a href="NewCentre.php">
					<span = class="glyphicon glyphicon-list"></span>
					&nbsp;Modifier le centre</a></li>
					<li><br /></li>
					<li><a href="Lagout.php">
					<span = class="glyphicon glyphicon-log-out"></span>
					&nbsp;Se déconnecter</a></li>
				</ul>
			</div>
			<div class="col-xs-10">
				<div class="page-title"><h1>Mettre à jour le centre</h1></div>
					<?phpphp echo Message(); ?>
					<?phpphp echo SuccessMessage(); ?>
					<form action="editCentre.php" method="POST" enctype="multipart/form-data">
						<fieldset>		
							<div class="form-group">
								<label>Nom du centre : <?phpphp echo htmlentities($centre_name); ?></label>
								<input type="hidden" name="centre_name" value="<?phpphp echo $centre_name; ?>"><br>	      
							</div>
							<label>planning Actuelle: <a href="<?phpphp echo $planning;  ?>" target="_blank" > <?phpphp echo $planning;  ?></a></label>
							<br /><br />
							<div class="form-group">
								<labal for="planning">Modifier le planning</labal>
								<input type="text" name="planning" class="form-control" value="<?phpphp echo $planning ?>">
							</div>
							
							<div class="form-group">
								<labal for="centre-title">Activités annexes :</labal>
								<input type="text" name="activites_annexes" class="form-control" id="activites_annexes" value="<?phpphp echo $activites_annexes ?>">
							</div>
							
							<div class="form-group">
								<labal for="centre-title">Horaires :</labal>
								<input type="text" name="horaires" class="form-control" id="activites_annexes" value="<?phpphp echo $horaires ?>">
							</div>
	
							<div class="form-group">
								<labal for="centre-title">Page Facebook :</labal>
								<input type="text" name="page_fb" class="form-control" id="page_fb" value="<?phpphp echo $page_fb ?>">
							</div>
							
							<div class="form-group">
								<labal for="centre-title">Vidéo :</labal>
								<input type="text" name="video_youtube" class="form-control" id="video_youtube" value="<?phpphp echo $video_youtube ?>">
							</div>
							
							<input type="hidden" name="idFromUrl" value="<?phpphp echo $_GET['centre_id']; ?>">
							<input type="hidden" name="currentplanning" value="<?phpphp echo $planning; ?>">
							<div class="form-group">
								<button name="centre-update" class="btn btn-primary form-control">Mettre à jour</button>
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