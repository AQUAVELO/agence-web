<?php
require '_settings.php';
$stmt = $database->prepare('SELECT id, city FROM am_centers WHERE online = 1 AND aquavelo = 1 ORDER BY city ASC');
$stmt->execute();
$centers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($centers);
echo "</pre>";
?>
