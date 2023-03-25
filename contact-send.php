<?php

require '_settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Segment;
Segment::init("CvtZvzpEIJ0UHZuZCwSqQuq5F6o2FGsB");


$mail = new PHPMailer(true);
$mail->IsSMTP();
$mail->Host = $settings['mjhost'];
$mail->isHTML(true);                                  // Set email format to HTML

$mail->SMTPAuth = true;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
$mail->Port = 587;
$mail->Username = $settings['mjusername'];
$mail->Password = $settings['mjpassword'];

$mail2 = new PHPMailer();
$mail2->IsSMTP();
$mail2->Host = $settings['mjhost'];
$mail2->isHTML(true);                                  // Set email format to HTML

$mail2->SMTPAuth = true;
$mail2->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
$mail2->Port = 587;
$mail2->Username = $settings['mjusername'];
$mail2->Password = $settings['mjpassword'];


// Ensures no one loads page and does simple spam check
if (isset($_POST['nom']) && empty($_POST['reason'])) {

	// Declare our $errors variable we will be using later to store any errors
	$error = '';

	// Setup our basic variables
	$input_nom = strip_tags(utf8_decode($_POST['nom']));
	$input_prenom = strip_tags(utf8_decode($_POST['prenom']));
	$input_name = $input_nom . ' ' . $input_prenom;
	$input_email = strip_tags($_POST['email']);
	$input_tel = strip_tags($_POST['phone']);
	$center = strip_tags($_POST['center']);
	$segment = strip_tags($_POST['segment']);

	// We'll check and see if any of the required fields are empty
	if (strlen($input_name) < 2) $error['name'] = 'Veuillez nous indiquer votre nom.';
	//if( strlen($input_message) < 5 ) $error['message'] = 'Veuillez inscrire votre message.';

	// Make sure the email is valid
	if (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) $error['email'] = 'Veuillez nous indiquer une adresse email correcte.';

	if ($_COOKIE["secure"] == true) $error['spam'] = 'Veuillez patienter 60 secondes pour renouveler votre demande.';

	// Set a subject & check if custom subject exist
	// $subject = "Message from $input_name";
	// if( $input_subject ) $subject .= ": $input_subject";
	$name = $input_name;
	$email = $input_email;
	$tel = $input_tel;

	$message = "$input_message\n";
	// $message .= "\n---\nThis email was sent by contact form.";

	$center_contact = $database->prepare('SELECT * FROM am_centers WHERE id = ? AND online = ? AND aquavelo = ?');
	$center_contact->execute(array($center, 1, 1));
	$row_center_contact = $center_contact->fetch();

	$count_center_contact = $center_contact->rowCount();
	if ($count_center_contact != 1) $error['center'] = 'Une erreur est survenue avec le centre selectionné.';

	$city = $row_center_contact['city'];
	$email_center = $row_center_contact['email'];
	$address = $row_center_contact['address'];
	$hours = $row_center_contact['openhours'];
	$phone = $row_center_contact['phone'];



	// Now check to see if there are any errors 
	if (!$error) {
		$reference = 'AQ' . date('dmhis');
		$add_free = $database->prepare("INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$add_free->execute(array($reference, $center, 3, $name, $email, $tel, $segment));

		Segment::track(array(
			"anonymousId" => $segment,
			"event" => "Demo Requested",
			"properties" => array(
				"reference" => $reference,
				"center" => $center,
				"firstname" => $name,
				"email" => $email,
				"phone" => $tel
			)
		));
	
		setcookie('secure', 'true', (time() + 15));

		//PHPMAILER
		$mail->setFrom('service.clients@aquavelo.com', 'Service clients Aquavelo');
		$mail->addAddress($email_center, 'Aquavelo ' . $city);
		//$mail->addBCC('contact@aquavelo.com', 'Rodriguez Alexandre');
		$mail->addReplyTo($email, $name);
		$mail->Subject = 'Aquavelo, Un contact pour votre centre de ' . $city . ' !';
		$mail->Body = '<p>Bonjour, </p><p>' . $name . ' <br/> Adresse &eacute;lectronique : <strong>' . $email . ' </strong> <br/> T&eacute;l&eacute;phone : <strong> ' . $tel . '</strong></p><p>La personne ci-dessus a command&eacute;e une s&eacute;ance d&eacute;couverte gratuite ainsi qu\'un bilan minceur dans votre centre. <br/><em>Nous vous invitons &agrave; la contacter pour prendre rendez-vous.</em></p><p>Cordialement,<br/>L\'&eacute;quipe Aquavelo</p><p><em>(Demande effectu&eacute;e &agrave; partir du site aquavelo.com, le ' . date("d-m-Y   H:i:s") . ')</em></p>';
		$mail->AltBody = 'Bonjour, ' . $name . ' ' . $email . ' ' . $tel . '. La personne ci-dessus a command&eacute;e une s&eacute;ance d&eacute;couverte gratuite ainsi qu\'un bilan minceur dans votre centre. <br/><em>Nous vous invitons &agrave; la contacter pour prendre rendez-vous..</em></p><p>Cordialement,<br/>L\'&eacute;quipe Aquavelo</p><p><em>(Demande effectu&eacute;e &agrave; partir du site www.aquavelo.com, le ' . date("d-m-Y   H:i:s") . ')</em></p>';

		if (!$mail->send()) {
			echo '<div class="alert alert-danger">Nous avons rencontr&eacute; un probl&egrave;me lors de l\'envoi de votre message.</div>';
		} else {
			// echo '<p class="success">Votre message a bien &eacute;t&eacute; envoy&eacute;!</p>';
		}





		$msg = '
	
	<p>Bonjour ' . $name . ', </p><p>Nous avons bien re&ccedil;u votre demande pour une s&eacute;ance d&eacute;couverte gratuite.</p> <p>Le centre <strong>Aquavelo de ' . $city . '</strong> est heureux de vous accueillir pour vous faire essayer un <strong>cours d\'aquabiking coach&eacute;</strong> par des professeurs de sport diplom&eacute;s &agrave; la suite d\'un <strong>bilan</strong> durant lequel nous d&eacute;finirons vos besoins et vos objectifs.</p>
	
	<p>Prenez vite rendez vous avec le centre <strong>Aquavelo de ' . $city . '</strong> en appelant au <strong>' . $phone . '</strong>. Pour effectuer votre s&eacute;ance, pensez à prendre un maillot de bain, une serviette de bain, un gel douche, une bouteille d\’eau et des chaussures d\’aquav&eacute;lo.</p>
	<p>
	Horaires d\'ouverture: <strong> ' . $hours . ' </strong><br>
	Adresse: <strong> ' . $address . ' </strong><br>
	
	
	<p><em>*Offre non cumulable.</em></p>
	<p>Cordialement,<br>L\'&eacute;quipe Aquavelo<br>http://aquavelo.com/</p>';
		//PHPMAILER
		$mail2->setFrom('service.clients@aquavelo.com', 'Service clients Aquavelo');
		$mail2->addAddress($email, $name);
		//$mail2->addBCC('contact@aquavelo.com', 'Rodriguez Alexandre');
		$mail2->addReplyTo('service.clients@aquavelo.com', 'Service clients Aquavelo');
		$mail2->Subject = 'Aquavelo - Votre seance decouverte gratuite';
		$mail2->Body = $msg;
		$mail2->AltBody = strip_tags($msg);
		if (!$mail2->send()) {
			echo '<div class="alert alert-danger">Nous avons rencontr&eacute; un probl&egrave;me lors de l\'envoi de votre message.</div>';
		} else {
			echo '<div class="alert alert-success">Appelez vite le centre AQUAVELO pour prendre rendez-vous !</div>';
		}
	} else {

		// Errors were found, output all errors to the user
		$response = (isset($error['name'])) ? $error['name'] . "<br /> \n" : null;
		$response .= (isset($error['email'])) ? $error['email'] . "<br /> \n" : null;
		$response .= (isset($error['spam'])) ? $error['spam'] . "<br /> \n" : null;
		$response .= (isset($error['center'])) ? $error['center'] . "<br /> \n" : null;

		echo "<div class=\"alert alert-danger\">$response</div>";
	}
}
