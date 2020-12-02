<?phpphp
/**
 * Setup mail server config
 */

//where we would like to send email
$recipientEmail = 'claude@aquavelo.com';
$recipientName = 'Aquavelo';

//Address which will be visible in "From" field
$fromEmail = 'franchise@aquavelo.com';
$fromName = 'Aquavelo';

//Validation error messages
$requiredMessage = 'Champ requis';
$invalidEmail = 'E-mail invalide';

/**
 * Advanced configuration - no need to modify
 */

require_once(dirname(__FILE__) . '/vendor/ctPHPMailer.php');
$mail = new ctPHPMailer();

//set your email address
$mail->AddAddress($recipientEmail, $recipientName);
$mail->SetFrom($fromEmail, $fromName);

$debug = false; //if problems occur, set to true to view debug messages

/**
 * For GMAIL configuration please use this values:
 *
 * $mail->Host = "smtp.gmail.com"; // SMTP server
 * $mail->Username = "mail@gmail.com"; // SMTP account username
 * $mail->Password = "yourpassword"; // SMTP account password
 * $mail->Port = 465; // set the SMTP port for the GMAIL server
 * $mail->SMTPSecure = "ssl";
 *
 * More configuration options available here: https://code.google.com/a/apache-extras.org/p/phpmailer/wiki/ExamplesPage
 */

/**
 * SERVER CONFIG
 */

/**
 * Config for SMTP server - uncomment if you don't want to use PHP mail() function
 **/


require'../_settings.php';


$mail->IsSMTP();
$mail->Host=$settings['mjhost'];
$mail->ContentType = "text/html";
$mail->CharSet = 'UTF-8';
$mail->SMTPAuth=true;
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->Username=$settings['mjusername'];
$mail->Password=$settings['mjpassword'];