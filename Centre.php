<?php require_once('Include/Sessions.php') ?>
<?php require_once('Include/functions.php') ?>
<?php ConfirmLogin(); ?>
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
	<div class="main" id="dashboard">
		<div class="row">
			<div class="col-sm-2">
				<ul id="side-menu" class="nav nav-pills nav-stacked">
					<li  class="active"><a href="Centre.php">
					<span = class="glyphicon glyphicon-th"></span>
					&nbsp;Centre</a></li>
					<li><a href="Lagout.php">
					<span = class="glyphicon glyphicon-log-out"></span>
					&nbsp;Se déconnecter</a></li>
				</ul>
			</div>
			<div class="col-xs-10">
				<div>
					<h1>Gérer les informations du centre</h1>
					<?php echo SuccessMessage(); ?>
					<?php echo Message(); ?>
					<div class="table-responsive">
						
							<?php
							$centre_name= $_SESSION['user_centre'];
							$sql = "SELECT * FROM aquavelo_centre WHERE centre_name = '$centre_name'";
							$exec = Query($sql);
							$centreNo = 1;
							if(mysqli_num_rows($exec) < 1	) {
								?>
									<p class="lead">Votre centre n'est pas encore inscrit veuillez contacter l'administrateur</p>
									<?php echo $_SESSION['user_type']; ?>
								<?php
							}else{ ?>
							<table class="table table-hover">
							<tr>
								<th>Centre N°.</th>
								<th>Nom du centre</th>
								<th>Planning </th>
								<th>Horaires </th>
								<th>Activités annexes</th>
								<th>page facebook</th>
								<th>video youtube</th>
							</tr>
							<?php
								while ($centre = mysqli_fetch_assoc($exec)) {
									$centre_id = $centre['centre_id'];
									$centre_name = $centre['centre_name'];
									$planning = $centre['planning'];
									$activites_annexes = $centre['activites_annexes'];
									$page_fb = $centre['page_fb'];
									$video_youtube = $centre['video_youtube'];
									?>
									<tr>
									<td><?php echo $centreNo; ?></td>
									<td><?php echo $centre_name; ?></td>
									<td><?php echo "<a href='".$planning."'>". substr($planning,0,20) ." </a>"; ?></td>
									<td><?php echo  substr($horaires,0,20); ?></td>
									<td><?php echo $activites_annexes; ?></td>
									<td><a href="<?php echo $page_fb; ?>"><?php echo substr($page_fb,0, 20); ?></a></td>
									<td><a href="<?php echo $video_youtube; ?>"><?php echo substr($video_youtube,0,20); ?></a></td>
									<td><?php echo ''; ?></td>
									<td><?php echo "<a href='editCentre.php?centre_id=$centre_id'>Modifier</a>"; ?></td>
									</tr>
									<?php
									$centreNo++;
								}
							}
							?>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
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