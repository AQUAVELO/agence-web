<?phpphp require_once('Include/Sessions.php') ?>
<?phpphp require_once('Include/functions.php') ?>
<?phpphp
$_SESSION['user_id'] = null;
session_destroy();
Redirect_To('Login-Aquavelo-Blog.php');

?>