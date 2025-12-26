<?php
$server = 'localhost';
$username = 'aquaveloblog';
$password = 'e017D&xg';
$db_name = 'aquaveloblog';
$con;

try{
	$con = mysqli_connect($server, $username, $password, $db_name) or die(mysqli_connect_errno());
	
}catch(Exception $e){
	echo $e->getMessage();
}
?>