<?php require_once('Include/Sessions.php'); ?>
<?php require_once('Include/functions.php') ?>
<?php ConfirmLogin(); ?>
<?php AdminGle(); ?>
<?php
if ( isset( $_POST['post-delete'])) {
	$sql = "DELETE  FROM aquavelo_post WHERE post_id = '$_POST[deleteID]' ";
	$exec = Query($sql);
	if ($exec) {
		$_SESSION['successMessage'] = "Article supprimer avec succès";
		Redirect_To('Dashboard.php');
	}else {
		$_SESSION['errorMessage'] = "Une erreur est survenue veuillez réessayer ultérieurement";
		Redirect_To('Dashboard.php');
	}

}else if( isset($_GET['delete_post_id'])) {
	if (!empty($_GET['delete_post_id'])) {
		$sql = "SELECT * FROM aquavelo_post WHERE post_id = '$_GET[delete_post_id]'";
		$exec = Query($sql);
		if (mysqli_num_rows($exec) > 0 ) {
			if ($post = mysqli_fetch_assoc($exec)) {
				$post_id = $post['post_id'];
				$post_date = $post['post_date_time'];
				$post_title = $post['title'];
				$post_category = $post['category'];
				$post_author = $post['author'];
				$post_image = $post['image'];
				$post_content = $post['post'];
			}
		} 
	}
}else {
	Redirect_To('dashboard.php');
}

?>
<!DOCTYPE html>
<html>
<head>
     <meta charset="UTF-8" />
	<title>Supprimer un article</title>
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
				<div class="page-title"><h1>Supprimer l'article</h1></div>
					<?php echo Message(); ?>
					<?php echo SuccessMessage(); ?>
					<form action="deletepost.php" method="POST" enctype="multipart/form-data">
						<fieldset>
							<div class="form-group">
								<button name="post-delete" class="btn btn-danger form-control">SUPPRIMER</button>
							</div>
							<div class="form-group">
								<labal for="post-title">Title :</labal>
								<input disabled type="text" name="post-title" class="form-control" id="post-title" value="<?php echo $post_title ?>">
							</div>
							<div class="form-group">
								<label>Catégorie : <?php echo htmlentities($post_category); ?></label><br>
								<labal for="post-category">Modifier la Catégorie  :</labal>
								<select disabled class="form-control" name="post-category" id="post-category" value="<?php echo $post_category ?>">
									<?php
										$sql = "SELECT cat_name FROM aquavelo_category";
										$exec = Query($sql);
										$selected = "";
										while($row = mysqli_fetch_assoc($exec)){ 
											// if ( $row['cat_name'] == $post_category ) {
											// 	$select = 'selected';
											// }
											if($post_category === $row['cat_name']) {
												?>
												<option selected="selected" ><?php echo htmlentities($row['cat_name']) ?></option>
												<?php
											}else {
												?>
												<option><?php echo htmlentities($row['cat_name']) ?></option>
												<?php
											}
										}
									?>
								</select>
							</div>
							<label>Image : <img src="Upload/Image/<?php echo $post_image;  ?>" width='250' height='90'> </label>
							<div class="form-group">
								<labal for="post-image">Modifier l'Image :</labal>
								<input disabled type="File" name="post-image" class="form-control">
							</div>
							<div class="form-group">
								<labal for="post-content">Contenue :</labal>
								<textarea disabled rows="20" class="form-control" name="post-content" id="post-content"><?php echo htmlentities($post_content);  mysqli_close($con); ?></textarea>
							</div>
							<input type="hidden" name="deleteID" value="<?php echo $_GET['delete_post_id']; ?>">
							<input type="hidden" name="currentImage" value="<?php echo $post_image; ?>">
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