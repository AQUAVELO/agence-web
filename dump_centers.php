<?php
require '_settings.php';
$stmt = $database->prepare("SELECT id, city FROM am_centers WHERE city LIKE '%Cannes%' OR city LIKE '%Mandelieu%' OR city LIKE '%Vallauris%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
