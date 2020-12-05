<?php
require 'vendor/autoload.php';

use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\Predis\Config;
try {
  $redis = CacheManager::getInstance('redis', new Config([
          'host' => getenv("REDIS_HOST"),
          'port' => getenv("REDIS_PORT"),
          'password' => getenv("REDIS_PASSWORD"),
  ]));
} catch (Exception $e) {
  echo "Couldn't connected to Redis";
  echo $e->getMessage();
}

#nav
$centers_list_d_cache = $redis->getItem('centers_list_d');
if (!$centers_list_d_cache->isHit()) {
  $centers_list = $database->prepare('SELECT * FROM am_centers WHERE online = ? AND aquavelo = ? ORDER BY city ASC');
  $centers_list->execute(array(1, 1));
  $centers_list_d = $centers_list->fetchAll(PDO::FETCH_ASSOC);
  $centers_list_d_cache->set($centers_list_d)->expiresAfter(getenv("REDIS_TTL"));
  $redis->save($centers_list_d_cache);
} else {
  $centers_list_d = $centers_list_d_cache->get();
}
?>
<?php foreach ($centers_list_d as &$row_centers_list) { ?>
  <li><a href="/centres/<?= $row_centers_list['city']; ?>" title="Aquabiking à <?= $row_centers_list['city']; ?>"><?= $row_centers_list['city']; ?></a></li>
<?php } ?>