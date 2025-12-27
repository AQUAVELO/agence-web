<?php require_once('Include/Sessions.php'); ?>
<?php require_once('Include/functions.php') ?>
<?php ConfirmLogin(); ?>
<?php AdminGle(); ?>
<?php
if ( isset($_POST['submit'])) {
	date_default_timezone_set('Europe/Paris');
	$time = time();
	$dateTime = strftime('%Y-%m-%d %H:%M:%S ',$time);
	$username = mysqli_real_escape_string($con,$_POST['username']);
	$admin_type = mysqli_real_escape_string($con,$_POST['admin_type']);
	$centre_name = mysqli_real_escape_string($con,$_POST['centre_name']);
	$password = mysqli_real_escape_string($con,$_POST['password']);
	$confirmPassword = mysqli_real_escape_string($con,$_POST['confirm_password']);
	
	
	$creator = $_SESSION['username'];
	if ( empty($username) || empty($password) || empty($confirmPassword)) {
		$_SESSION['errorMessage'] = 'Tous les champs sont obligatoire';
		Redirect_To('Admin.php');
	}else if (strlen($password) < 7) {
		$_SESSION['errorMessage'] = 'Le mot de passe doit avoir 7 charactères au minimum';
		Redirect_To('Admin.php');
	}else if ($password !== $confirmPassword) {
		$_SESSION['errorMessage'] = 'Les deux mots de passe ne sont pas identique';
		Redirect_To('Admin.php');
	}else {
		$sql = "INSERT INTO aquavelo_admin (date_time, username, type, centre, password, added_by) VALUES('$dateTime', '$username', '$admin_type', '$centre_name', '$password', '$creator')";
		$exec = Query($sql);
		if ($exec) {
			$_SESSION['successMessage'] = 'Le nouveau administrateur est ajouter avec succès';
			mysqli_close($con);
			Redirect_To('Admin.php');
		} else {
			$_SESSION['errorMessage'] = 'Une erreur est survenue veuillez réessayer';
			Redirect_To('Admin.php');
		}
	}
}

if ( isset($_GET['del_admin'])) {
	if ( !empty($_GET['del_admin'])) {
		$sql = "DELETE FROM aquavelo_admin WHERE id = '$_GET[del_admin]'";
		$exec = Query($sql);
		if ($exec) {
			$_SESSION['successMessage'] = 'Administrateur supprimer avec succès';
			mysqli_close($con);
			Redirect_To('Admin.php');
			
		}else {
			$_SESSION['errorMessage'] = 'Une erreur est survenue veuillez réessayer';
			mysqli_close($con);
			Redirect_To('Admin.php');

		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
	<title>Gérer les Admin</title>
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
					<li><a href="NewPost.php">
					<span = class="glyphicon glyphicon-list"></span>
					&nbsp;Nouveau Post</a></li>
					<li><a href="Categories.php">
					<span = class="glyphicon glyphicon-tags"></span>
					&nbsp;Catégories</a></li>
					<li class="active"><a href="Admin.php">
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
				<div class="page-title"><h1>Gérer les Admins</h1></div>
				<?php echo Message(); ?>
				<?php echo SuccessMessage(); ?>
				<div>
					<div class="row">
						<div class="col-md-12 ">
							<form method="POST" action="Admin.php">
								<fieldset>
									<div class="form-group">
										<label for="username">Nom de l'admin:</label>
										<input class="form-control input-md" type="text" name="username" placeholder="Nom d'utilisateur">
									</div>
									
									<div class="form-group">
										<label for="username">Type de l'admin:</label>
											<select class="form-control" name="admin_type" id="admin_type" required >
								    <option desabled value="">Choisissez un type</option>
									<option>Administrateure_Gle</option>
									<option>Administrateur_Centre</option>
									</select>
									</div>
									
																		<div class="form-group">
										<label for="username">Nom du centre:</label>
									<select class="form-control" name="centre_name" id="centre_name" required >
								    <option desabled value="">Choisissez un centre</option>
									<option >Global</option>
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
										<label for="password">Mot de Passe :</label>
										<input class="form-control input-md" type="Password" name="password" placeholder="Mot de passe">
									</div>
									<div class="form-group">
										<label for="confirm_password">Re-taper le Mot de Passe :</label>
										<input class="form-control input-md" type="Password" name="confirm_password" placeholder="Confirmer le mot de passe">
									</div>
									<div class="form-group">
										<input class="form-control btn btn-primary" type="submit" name="submit" value="Ajouter le Nouveau Administrateur">
									</div>
								</fieldset>
							</form>
						</div>
					</div>
					<div id="cat_table">
						<?php echo deleteCategory(); ?>
						<h3>Liste des Administrateurs</h3>
						<table class="table table-striped table-hover">
							<tr>
								<th>Numéro</th>
								<th>Date A'ajout</th>
								<th>Nom</th>
								<th>Type</th>
								<th>Centre</th>
								<th>Ajouter par</th>
								<th>Action</th>
							</tr>
							<?php
								$num = 1;
								$viewSql = "SELECT * FROM aquavelo_admin ORDER BY date_time DESC";
								$exec = Query($viewSql);
								while($data = mysqli_fetch_assoc($exec)) {
									$id = $data['id'];
									$dateAdded = $data['date_time'];
									$username = $data['username'];
									$type = $data['type'];
									$centre = $data['centre'];
									$creator = $data['added_by'];
									echo "<tr>
										<td>$num</td>
											<td>$dateAdded</td>
											<td>$username</td>
											<td>$type</td>
											<td>$centre</td>
											<td>$creator</td>
											<td><a href='Admin.php?del_admin=$id'><button class='btn btn-danger'>Supprimer</button></a></td>
										</tr>
									";
									$num++;
								} 
								mysqli_close($con);
							?>
						</table>
					</div>
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