<?php require_once('Include/Sessions.php') ?>
<?php require_once('Include/functions.php') ?>
<?php ConfirmLogin(); ?>
<?php AdminGle(); ?>
<?php
if ( isset($_GET['Approve_ID'])) {
	if ( !empty($_GET['Approve_ID'])) {
		$sql = "UPDATE comment SET status ='approved', approve_by = '$_SESSION[username]' WHERE id = '$_GET[Approve_ID]'";
		$exec = Query($sql);
		if ( $exec) {
			$_SESSION['SuccessMessage'] = 'Post Has Been Approved';
			mysqli_close($con);
			Redirect_To('Comments.php');
		}else {
			$_SESSION['errorMessage'] = 'Something Went Wrong Please Try Again';
			Redirect_To('Comments.php');
			mysqli_close($con);
		}
	}else {
		Redirect_To('Comments.php');
	}
}

if ( isset($_GET['Unapprove_ID'])) {
	if ( !empty($_GET['Unapprove_ID'])) {
		$sql = "UPDATE comment SET status ='unapprove'  WHERE id = '$_GET[Unapprove_ID]'";
		$exec = Query($sql);
		if ( $exec) {
			$_SESSION['SuccessMessage'] = 'Post Has Been Unapproved';
			mysqli_close($con);
			Redirect_To('Comments.php');
		}else {
			$_SESSION['errorMessage'] = 'Something Went Wrong Please Try Again';
			Redirect_To('Comments.php');
			mysqli_close($con);
		}
	}else {
		Redirect_To('Comments.php');
	}
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
	<title>Comments</title>
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
					<li ><a href="Dashboard.php">
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
					<li class="active"><a href="Comments.php">
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
				<div>
					<h1>Commentaires</h1>
					<?php echo SuccessMessage(); ?>
					<?php echo Message(); ?>
					<div class="table-responsive">
							<?php
							$sql = "SELECT * FROM comment WHERE status ='approved' ORDER BY date_time";
							$exec = Query($sql);
							$postNo = 1;
							if(mysqli_num_rows($exec) < 1	) {
								?>
									<p class="lead">Aucun Commentaire Approuver</p>
								<?php
							}else{ ?>
							<span class="lead">Commentaires approuver</span>
							<table class="table table-hover">
							<tr>
								<th>Commentaire N°.</th>
								<th>Date du Commentaire</th>
								<th>Email de l'utilisateur</th>
								<th>Contenue du Comment</th>
								<th>Approuver par</th>
								<th>Action</th>
								
							</tr>
							<?php
								while ($post = mysqli_fetch_assoc($exec)) {
									$comment_id = $post['id'];
									$comment_dateTime = $post['date_time'];
									$comment_email = $post['email'];
									$comment_content = $post['comment'];
									$comment_status = $post['status'];
									$approved_by = $post['approve_by'];
									?>
									<tr>
									<td><?php echo $postNo; ?></td>
									<td><?php echo $comment_dateTime; ?></td>
									<td><?php echo $comment_email; ?></td>
									<td><?php echo $comment_content; ?></td>
									<td><?php echo $approved_by; ?></td>
									<td><a href="Comments.php?Unapprove_ID=<?php echo $comment_id; ?>"><button class="btn btn-danger">Inapprover</button></a></td>
									<?php
									$postNo++;
								}
							}
							?>
						</table>
					</div>
					<div class="table-responsive">
							<?php
							$sql = "SELECT * FROM comment WHERE status ='unapprove' ORDER BY date_time";
							$exec = Query($sql);
							$postNo = 1;
							if(mysqli_num_rows($exec) < 1	) {
								?>
									<p class="lead">Aucun commentaire inapprouver</p>
								<?php
							}else{ ?>
							<span class="lead">Commentaires inapprouver</span>
							<table class="table table-hover">
							<tr>
								<th>Commentaire N°.</th>
								<th>Date du Commentaire</th>
								<th>Email de l'utilisateur</th>
								<th>Contenue du Comment</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
							<?php
								while ($post = mysqli_fetch_assoc($exec)) {
									$comment_id = $post['id'];
									$comment_dateTime = $post['date_time'];
									$comment_email = $post['email'];
									$comment_content = $post['comment'];
									$comment_status = $post['status'];
									?>
									<tr>
									<td><?php echo $postNo; ?></td>
									<td><?php echo $comment_dateTime; ?></td>
									<td><?php echo $comment_email; ?></td>
									<td><?php echo $comment_content; ?></td>
									<td><?php echo $comment_status; ?></td>
									<td><a href="Comments.php?Approve_ID=<?php echo $comment_id; ?>"><button class="btn btn-success">Approuver</button></a></td>
									<?php
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