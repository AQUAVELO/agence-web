<?phpphp
include('Include/Sessions.php');
include('Include/functions.php');
if ( isset($_POST['submit'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	if(empty($username) || empty($password)) {
		$_SESSION['errorMessage'] = 'Tous les champs sont obligatoires';
	}else {
		$foundAccount = LoginAttempt($username, $password);
		if ($foundAccount) {
			$_SESSION['successMessage'] = 'Login Successfully Bienvenue ' . $foundAccount['username'];
			$_SESSION['user_id'] = $foundAccount['id'];
			$_SESSION['username'] = $foundAccount['username'];
			$_SESSION['user_type'] = $foundAccount['type'];
			$_SESSION['user_centre'] = $foundAccount['centre'];
			
			
			if($_SESSION['user_type'] === "Administrateure_Gle") {
			Redirect_To('Dashboard.php');
			}
			else {
				Redirect_To('Centre.php');
			}
			
			
		}else {
			$_SESSION['errorMessage'] = 'Username et/ou mot de passe Incorrects';
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script src="jquery-3.2.1.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="Assets/login-blog.css">
</head>
<body>
  
	<div class="col-md-4 col-md-offset-4 login-area">
		<?phpphp echo Message(); ?>
				
		<div class="">
			<form method="POST" action="Login-Aquavelo-Blog.php">
				<legend class="lead"><h1>Athentification</h1></legend>
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
						<input type="text" name="username" class="form-control input-lg" placeholder="Nom d'utitilisateur">
					</div>
				</div>
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-lock"></span>
						</span>
						<input type="password" name="password" class="form-control input-lg" placeholder="Mot de passe">
					</div>
				</div>
				<div class="form-group">
					<input type="submit" name="submit" class="form-control input-lg btn btn-info" value="Se connecter">
				</div>
			</form>
		</div>
		</div>


</body>
</html>