<?phpphp require_once('Include/Sessions.php'); ?>
<?phpphp require_once('Include/functions.php') ?>
<?phpphp
	if(isset($_POST['submit'])) {
		if(!empty($_POST['submit'])) {
			date_default_timezone_set('Europe/Paris');
			$time = time();
			$dateTime = strftime('%Y-%m-%d %H:%M:%S ',$time);
			$postID = $_POST['id'];
			$email = $_POST['email'];
			$comment = $_POST['comment'];
			$status = 'unapprove';
			$sql = "INSERT INTO comment (date_time, email, comment, status, post_id) VALUES('$dateTime', '$email', '$comment', '$status', '$postID' )";
			$exec = Query($sql);
			if ($exec) {
				$_SESSION['successMessage'] = "Votre commentaire a été envoyé avec succès. Une notification par mail vous sera envoyé lorsque il sera approuvé";
				mysqli_close($con);
				Redirect_To("Post.php?id=$postID");
			}else {
				$_SESSION['errorMessage'] = "Une erreur est survenue veuillez réessayer";
			}
		}
	}
?>