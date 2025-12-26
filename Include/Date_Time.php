<?php
date_default_timezone_set('Europe/Paris');

$time = time();
$dateTime = strftime('%Y-%m-%d',$time);
echo $dateTime;
?>