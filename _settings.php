<?
$settings = '';
$settings['dbhost'] = "localhost";
$settings['dbname'] = "alesiaminceur";
$settings['dbusername'] = "web_aquavelo";
$settings['dbpassword'] = "Amh4d0%5";

$settings['mjhost'] = "in.mailjet.com";
$settings['mjusername'] = "af3d279995741286f01141125f990ec0";
$settings['mjpassword'] = "cd92d1991f2e5e125abcfe94dcebf14a";
$settings['mjfrom'] = "info@aquavelo.com";
?>
<?
try
{
	$database = new PDO("mysql:host=".$settings['dbhost'].";dbname=".$settings['dbname'].";charset=utf8", $settings['dbusername'], $settings['dbpassword'],
               array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch(Exception $error)
{
        die('Error : '.$error->getMessage());
}
?>