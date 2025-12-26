<?php require_once('Include/Sessions.php'); ?>
<?php require_once('Include/functions.php') ?>
<?php ConfirmLogin(); ?>
<?php AdminGle(); ?>
<?php

if( isset($_POST['submit_category'])){
	date_default_timezone_set('Europe/Paris');
	$time = time();
	$dateTime = strftime('%Y-%m-%d ',$time);
	$categoryName = ($_POST['cat_name']);
	$category = mysqli_real_escape_string($con,$categoryName);
	$cat_name_length = strlen($category);
	$admin = $_SESSION['username']; 
	if( empty($category)) {
		$_SESSION['errorMessage'] = 'Tous les champs sont obligatoire' . $category;
		Redirect_To('Categories.php');
		exit;
	}else if($cat_name_length > 50) {
		$_SESSION['errorMessage'] = 'Le nom de la catégorie de doit pas dépasser 50 carractères.';
		Redirect_To('Categories.php');
	}else {
		global $con;
		$query = "INSERT INTO aquavelo_category (cat_datetime, cat_name,	cat_creator) 
		VALUES('$dateTime', '$category', '$admin')";
		$exec = Query($query);
		if ($exec) {
			$_SESSION['successMessage'] = 'Categorie Ajouter avec succès.';
			Redirect_To('Categories.php');
			mysqli_close($con);
		}else {
			$_SESSION['errorMessage'] = 'Une erreur est survenue veuillez réessayer';
			Redirect_To('Categories.php');

		}
	}
}

if (isset($_GET['delete_attempt'])) {
	if (!empty($_GET['delete_attempt'])) {
		$_SESSION['del_id'] = $_GET['delete_attempt'];
		$_SESSION['optDeleteCategory'] = "";
		$_SESSION['categoryName'] = $_GET['name'];
	}else {
		Redirect_To('Categories.php');
	}
}

if (isset($_GET['CategoryID'])) {
	if (!empty($_GET['CategoryID'])) {
		$sql = "DELETE FROM aquavelo_category WHERE cat_id = $_GET[CategoryID]";
		$exec = Query($sql);
		if ($exec) {
			$_SESSION['successMessage'] = "Category Delete Successfully";
			Redirect_To('Categories.php');
		}else {
			Redirect_To('Categories.php');
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
	<title>Catégories</title>
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
					<li class="active"><a href="Categories.php">
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
					<li><a href="Lagout.php">
					<span = class="glyphicon glyphicon-log-out"></span>
					&nbsp;Se déconnecter</a></li>
				</ul>
			</div>
			<div class="col-xs-10">
				<div class="page-title"><h1>Gérer les Catégories</h1></div>
				<?php echo Message(); ?>
				<?php echo SuccessMessage(); ?>
				<div>
					<div class="row">
						<div class="col-md-12 ">
							<h3>Nouvelle Catégorie</h3>
							<form method="POST" action="Categories.php">
								<fieldset>
									<div class="form-group">
										<label for="cat_name">Nom :</label>
										<input class="form-control input-md" type="text" name="cat_name" placeholder="Nom de la catégories">
									</div>
									<div class="form-group">
										<input class="form-control btn btn-primary" type="submit" name="submit_category" value="Ajouter">
									</div>
								</fieldset>
							</form>
						</div>
					</div>
					<div id="cat_table">
						<?php echo deleteCategory(); ?>
						<h3>Liste des Catégories</h3>
						<table class="table table-striped table-hover">
							<tr>
								<th>Numéro</th>
								<th>Nom</th>
								<th>Date d'ajout</th>
								<th>Ajouter par</th>
								<th>Mettre à jour</th>
								<th>Supprimer</th>
							</tr>
							<?php
								$num = 1;
								$viewSql = "SELECT * FROM aquavelo_category ORDER BY cat_id DESC";
								$exec = Query($viewSql);
								while($data = mysqli_fetch_assoc($exec)) {
									$cat_id = $data['cat_id'];
									$cat_dateTime = $data['cat_datetime'];
									$cat_name = $data['cat_name'];
									$cat_creator = $data['cat_creator'];
									echo "<tr>
										<td>$num</td>
											<td>$cat_name</td>
											<td>$cat_dateTime</td>
											<td>$cat_creator</td>
											<td><input class='btn btn-success' type='button' name='update' value='Editer'></td>
											<td><a href='Categories.php?delete_attempt=$cat_id&name=$cat_name'><button class='btn btn-danger'>Supprimer</button></a></td>
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