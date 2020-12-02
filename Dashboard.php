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
					<li class="active"><a href="Dashboard.php">
					<span = class="glyphicon glyphicon-th"></span>
					 &nbsp;Dashboard</a></li>
					<li><a href="NewPost.php">
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
							$sql = "SELECT * FROM aquavelo_post ORDER BY post_date_time";
							$exec = Query($sql);
							$postNo = 1;
							if(mysqli_num_rows($exec) < 1	) {
								?>
									<p class="lead">Vous Avez 0 Post Pour le Moment</p>
									<a href="NewPost.php"><button class="btn btn-info">Ajouter un Post</button></a>
								<?phpphp
							}else{ ?>
							<table class="table table-hover">
							<tr>
								<th>Post N°.</th>
								<th>Date du Post</th>
								<th>Titre</th>
								<th>Auteur</th>
								<th>Categorie</th>
								<th>Image</th>
								<th>Comments</th>
								<th>Action</th>
								<th>Details</th>
							</tr>
							<?phpphp
								while ($post = mysqli_fetch_assoc($exec)) {
									$post_id = $post['post_id'];
									$post_date = $post['post_date_time'];
									$post_title = $post['title'];
									$category = $post['category'];
									$author = "Admin";
									$image = $post['image'];
									?>
									<tr>
									<td><?phpphp echo $postNo; ?></td>
									<td><?phpphp echo $post_date; ?></td>
									<td><?phpphp 
									if(strlen($post_title) > 20 ) {
										echo substr($post_title,0,20) . '...';
									}else {
										echo $post_title;
									}
					
									?></td>
									<td><?phpphp echo $author; ?></td>
									<td><?phpphp echo $category; ?></td>
									<td><?phpphp echo "<img class='img-responsive' src='Upload/Image/$image' width='100px' height='150px'>"; ?></td>
									<td><?phpphp echo 'Ongoing'; ?></td>
									<td><?phpphp echo "<a href='editpost.php?post_id=$post_id'>Edit</a> | <a href='deletepost.php?delete_post_id=$post_id'>Delete</a>"; ?></td>
									<td><a href="Post.php?id=<?phpphp echo $post_id; ?>"><button class="btn btn-primary">Live Preview</button></a></td>
									</tr>
									<?phpphp
									$postNo++;
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