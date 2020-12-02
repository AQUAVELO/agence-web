<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Contactez-nous</h1>

    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li class="active">Contact</li>
    </ol>
  </div>
</header>

<section class="content-area bg1">
  <div class="container">

    <header class="page-header text-center">
      <h1 class="page-title">Besoin d'aide      </h1>
      <p class="larger">Vous avez une question ? Utilisez le formulaire ci-dessous ou envoyez-nous un e-mail à <a href="mailto:claude@alesiaminceur.com">claude@alesiaminceur.com</a> </p>
      <p class="larger">ou contactez nous au 06 22 64 70 95.</p>
    </header>

    <div class="row">
      <div class="col-md-8">
        <div class="contactForm">
          <div class="successMessage alert alert-success alert-dismissable" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Merci! Nous vous répondrons dès que possible.
          </div>
          <div class="errorMessage alert alert-danger alert-dismissable" style="display: none">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Une erreur est survenue.
          </div>

          <div class="col-sm-6 col-sm-offset-3">
            <hr>
               
            <form action="#contact" method="post">
  <div class="form-group">
    <label for="exampleInputEmail1">Email</label>
    <input type="email" class="form-control" placeholder="Email" name="email" value="<?phpphp echo $_POST['email']; ?>" />
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Nom, prénom et ville concernée </label>
    <input type="text" class="form-control" placeholder="Nom, prénom et ville concernée" name="nom" value="<?phpphp echo $_POST['nom']; ?>" />
  </div>
  <div class="form-group">
    <label for="exampleInputFile">Message</label>
    <textarea class="form-control" placeholder="Message" name="message"><?phpphp echo $_POST['message']; ?></textarea>
  </div>
  <div class="checkbox">
   
  </div>
  <button type="submit" name="send" class="btn btn-default">Envoyer</button>
</form>


<?phpphp
if (isset($_POST['send']))
{
	
	
/**
 * This example shows sending a message using a local sendmail binary.
 */
//Import the PHPMailer class into the global namespace
require 'phpmailer/PHPMailerAutoload.php';


//Create a new PHPMailer instance
$mail = new PHPMailer;
$mail->IsSMTP();
$mail->Host="in-v3.mailjet.com";
$mail->ContentType = "text/html";
$mail->CharSet = 'UTF-8';
$mail->SMTPAuth=true;
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->Username="d876a5da2863648348c35d1bfa576ed2";
$mail->Password="0637b82d2f51ebedd0b4b80069641135" ;


	
	$nom=$_POST['nom'];
	$email=$_POST['email'];
	$message=$_POST['message'];
	$err=0;
	
	if (empty($nom) OR empty($email) OR empty($message))
	{
		$vide = '<br />Un des champs est vide.';
		$err++;
	}
	$errors=$vide;
	
	if ($err == 0)
	{
		
$mail->setFrom('contact@alesiaminceur.com', 'Aquavelo');
$mail->addReplyTo('contact@alesiaminceur.com', 'Aquavelo');

		$destinataire="Aquavelo <claude@alesiaminceur.com>";
		$mail->addAddress('claude@alesiaminceur.com', 'Claude Rodriguez');

		$object="Contact sur le site Aquavelo";
		$mail->Subject = $object;


		$message = 'La personne '.$nom.' avec le mail '.$email.' vous a envoy&eacute; ce message :
		<br /><br />'.$message;
		
		
		
		$mail->msgHTML($message);

		//send the message, check for errors
if (!$mail->send()) {
		echo '<div class="error">Votre message n\'a pas &eacute;t&eacute; envoy&eacute;.</div>';
} else {
		echo '<div class="error">Votre message a bien &eacute;t&eacute; envoy&eacute;.</div>';
}
		
		
		echo '<div class="error">Votre message a bien &eacute;t&eacute; envoy&eacute;.</div>';
	}
	else
	{
		echo '<div class="error"><span class="underline">Il y a '.$err.' erreur';if ($err > 1) { echo 's'; } echo ' dans le formulaire :</span>' . $errors.'</div>';
	}
}



?>


            </div>
        </div>
      </div>
      <div class="col-md-4">

        <dl>
        <dt>Recrutement</dt>
          <dd>Nous recherchons des maîtres nageurs pour notre développement national en France et international sur l'Espagne et le Maroc, envoyer CV + photo à claude@alesiaminceur.com </dd>
          <dd></dd>
            <dd></dd>
          <dt>Service clients</dt>
          <dd>1170 route de Nice</dd>
          <dd>06600 ANTIBES</dd>

          <dt>Téléphone</dt>
          <dd>+33 (0)6 22 64 70 95</dd>

          <dt>E-mail</dt>
          <dd><a href="mailto:claude@alesiaminceur.com">claude@alesiaminceur.com</a></dd>
        </dl>
        <dl>
          

        </dl>


      </div>
    
    </div>

  </div>
</section>

<section class="content-area bg2" data-topspace="0" data-btmspace="0">
  <div class="mapOuter">
    <div class="googleMap" data-location="1170 route de Nice 06600 ANTIBES" data-height="410" data-offset="0"></div>
  </div>
</section>
