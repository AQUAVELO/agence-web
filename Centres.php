<?phpphp require_once('Include/Sessions.php') ?>
<?phpphp require_once('Include/functions.php') ?>
<?phpphp ConfirmLogin(); ?>
<?phpphp AdminGle(); ?>
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
	<div class="main" id="dashboard">
		<div class="row">
			<div class="col-sm-2">
				<ul id="side-menu" class="nav nav-pills nav-stacked">
					<li><a href="Dashboard.php">
					<span = class="glyphicon glyphicon-th"></span>
					 &nbsp;Dashboard</a></li>
					<li><a href="Newcentre.php">
					<span = class="glyphicon glyphicon-list"></span>
					&nbsp;Nouveau post</a></li>
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
					<li  class="active"><a href="Centres.php">
					<span = class="glyphicon glyphicon-th"></span>
					&nbsp;Centres</a></li>
					<li><a href="NewCentre.php">
					<span = class="glyphicon glyphicon-list"></span>
					&nbsp;Informations d'un centres</a></li>
					<li><br /></li>
					<li><a href="Lagout.php">
					<span = class="glyphicon glyphicon-log-out"></span>
					&nbsp;Se déconnecter</a></li>
				</ul>
			</div>
			<div class="col-xs-10">
				<div>
					<h1>Dashboard</h1>
					<?phpphp echo SuccessMessage(); ?>
					<?phpphp echo Message(); ?>
					<div class="table-responsive">
						
							<?phpphp
							$sql = "SELECT * FROM aquavelo_centre";
							$exec = Query($sql);
							$centreNo = 1;
							if(mysqli_num_rows($exec) < 1	) {
								?>
									<p class="lead">Vous Avez Aucune information pour le Moment</p>
									<a href="NewCentre.php"><button class="btn btn-info">Ajouter les information d'un centre</button></a>
								<?phpphp
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
							<?phpphp
								while ($centre = mysqli_fetch_assoc($exec)) {
									$centre_id = $centre['centre_id'];
									$centre_name = $centre['centre_name'];
									$planning = $centre['planning'];
									$horaire = $centre['horaires'];
									$activites_annexes = $centre['activites_annexes'];
									$page_fb = $centre['page_fb'];
									$video_youtube = $centre['video_youtube'];
									?>
									<tr>
									<td><?phpphp echo $centreNo; ?></td>
									<td><?phpphp echo $centre_name; ?></td>
									<td><?phpphp echo "<a href='".$planning."'>". substr($planning,0,20) ." </a>"; ?></td>
									<td><?phpphp echo substr($horaire,0, 20); ?></td>
									<td><?phpphp echo $activites_annexes; ?></td>
									<td><a href="<?phpphp echo $page_fb; ?>"><?phpphp echo substr($page_fb,0, 20); ?></a></td>
									<td><a href="<?phpphp echo $video_youtube; ?>"><?phpphp echo substr($video_youtube,0,20); ?></a></td>
									<td><?phpphp echo ''; ?></td>
									<td><?phpphp echo "<a href='editCentre.php?centre_id=$centre_id'>Modifier</a> | <a href='deleteCentre.php?delete_centre_id=$centre_id'>Supprimer</a>"; ?></td>
									</tr>
									<?phpphp
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