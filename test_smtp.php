<?php
require '_settings.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "TEST MAILJET CONNECTIVITY...<br>";
echo "Host: " . $settings['mjhost'] . "<br>";
echo "User: " . $settings['mjusername'] . "<br>";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $settings['mjhost'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['mjusername'];
    $mail->Password = $settings['mjpassword'];
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 2; // DEBUG SORTIE SMTP

    $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo');
    $mail->addAddress('claude@alesiaminceur.com');
    $mail->isHTML(true);
    $mail->Subject = "TEST SMTP DEBUG " . date('H:i:s');
    $mail->Body = "Ceci est un test avec debug SMTP actif.";

    echo "<pre>";
    $mail->send();
    echo "</pre>";
    echo "SUCCESS ✅";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "Mailer Error: " . $mail->ErrorInfo;
}
?>
