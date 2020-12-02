	<?php require_once('Include/Sessions.php'); ?>
<?php require_once('Include/functions.php') ?>
<?php ConfirmLogin(); ?>
<?php AdminGle(); ?>
<?php

date_default_timezone_set('Europe/Paris');
$time = time();
if ( isset( $_POST['post-submit'])) {
	$title = mysqli_real_escape_string($con, $_POST['post-title']);
	$category = mysqli_real_escape_string($con, $_POST['post-category']);
	$content = mysqli_real_escape_string($con, $_POST['post-content']);
	$image = $_FILES['post-image']['name'];
	$author = $_SESSION['username'];
	$dateTime = strftime('%Y-%m-%d',$time);
	$title_length = strlen($title);
	$content_lenght = strlen($content);
	$imageDirectory = "Upload/Image/" . basename($_FILES['post-image']['name']);
	if ( empty($title)) {
		$_SESSION['errorMessage'] = "Title Is Emtpy";
		Redirect_To('NewPost.php');
	}else if ( $title_length > 50) {
		$_SESSION['errorMessage'] = "Title Is Too Long";
		Redirect_To('NewPost.php');
	}else if ( empty($content)) {
		$_SESSION['errorMessage'] = "Content Is Empty";
		Redirect_To('NewPost.php');
	}else if ( $content_lenght > 4000) {
		$_SESSION['errorMessage'] = "Content Is Too Long";
		Redirect_To('NewPost.php');
	}else {
		$query = "INSERT INTO aquavelo_post (post_date_time, title, category, author, image, post) 
		VALUES ('$dateTime', '$title', '$category', '$author', '$image', '$content')";
		$exec = Query($query);
		if ($exec) {
			move_uploaded_file($_FILES['post-image']['tmp_name'], $imageDirectory);
			$_SESSION['successMessage'] = "Aticle ajouter avec succeès";
		}else {
			$_SESSION['errorMessage'] = "Une erreur est survenue veuillez réessayer ";

		}

	}
}
?>
<!DOCTYPE html>
<html>
<head>
     <meta charset="utf-8" />
	<title>Nouveau article</title>
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
					<li><a href="Lagout.php">
					<span = class="glyphicon glyphicon-log-out"></span>
					&nbsp;Se déconnecter</a></li>
				</ul>
			</div>
			<div class="col-xs-10">
				<div class="page-title"><h1>Ajouter un nouveau article</h1></div>
					<?php echo Message(); ?>
					<?php echo SuccessMessage(); ?>
					<form action="NewPost.php" method="POST" enctype="multipart/form-data">
						<fieldset>
							<div class="form-group">
								<labal for="post-title">Titre :</labal>
								<input type="text" name="post-title" class="form-control" id="post-title">
							</div>
							<div class="form-group">
								<labal for="post-category">Categorie :</labal>
								<select class="form-control" name="post-category" id="post-category">
									<?php
										$sql = "SELECT * FROM aquavelo_category";
										$exec = Query($sql);
										while($row = mysqli_fetch_assoc($exec)){
											echo "<option>$row[cat_name]</option>";
										}
									?>
								</select>
							</div>
							<div class="form-group">
								<labal for="post-image">Image :</labal>
								<input type="File" name="post-image" class="form-control">
							</div>
							<div class="form-group">
								<labal for="post-content">Contenue :</labal>
								<textarea rows="10" class="form-control" name="post-content" id="post-content">
									
								</textarea>
							</div>
							<div class="form-group">
								<button name="post-submit" class="btn btn-primary form-control">Publier</button>
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