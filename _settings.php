<?php

$settings = '';

$settings['dbhost'] = getenv("MYSQL_ADDON_HOST");
$settings['dbname'] = getenv("MYSQL_ADDON_DB");
$settings['dbusername'] = getenv("MYSQL_ADDON_USER");
$settings['dbpassword'] = getenv("MYSQL_ADDON_PASSWORD");

$settings['mjhost'] = "in.mailjet.com";
$settings['mjusername'] = getenv("MAILJET_USERNAME");
$settings['mjpassword'] = getenv("MAILJET_PASSWORD");
$settings['mjfrom'] = "info@aquavelo.com";


try
{
	$database = new PDO("mysql:host=".$settings['dbhost'].";dbname=".$settings['dbname'], $settings['dbusername'], $settings['dbpassword']);

               /*
               $database = new PDO("mysql:host=".$settings['dbhost'].";dbname=".$settings['dbname'].";charset=utf8", $settings['dbusername'], $settings['dbpassword'],
               array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
               */
}
catch(Exception $error)
{
        die('Error : '.$error->getMessage());
}

?>