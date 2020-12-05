<?php require '_settings.php'; ?>
<?php


#nav
$centers_list_d = $redis->get('centers_list_d');
$centers_list = $database->prepare('SELECT * FROM am_centers WHERE online = ? AND aquavelo = ? ORDER BY city ASC');
$centers_list->execute(array(1, 1));
$centers_list_d = $centers_list->fetchAll(PDO::FETCH_ASSOC);
$redis->set('centers_list_d', $centers_list_d);
$redis->expire('centers_list_d', 5);
