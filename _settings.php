<?php
require 'vendor/autoload.php';

use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\Redis\Config;

$settings = [];

$settings['ttl'] = getenv("REDIS_TTL");
$settings['dbhost'] = getenv("MYSQL_ADDON_HOST");
$settings['dbport'] = getenv("MYSQL_ADDON_PORT");

$settings['dbname'] = getenv("MYSQL_ADDON_DB");
$settings['dbusername'] = getenv("MYSQL_ADDON_USER");
$settings['dbpassword'] = getenv("MYSQL_ADDON_PASSWORD");

$settings['mjhost'] = "in.mailjet.com";
$settings['mjusername'] = getenv("MAILJET_USERNAME");
$settings['mjpassword'] = getenv("MAILJET_PASSWORD");
$settings['mjfrom'] = "info@aquavelo.com";


try {
        $database = new PDO(
                'mysql:host=' . getenv("MYSQL_ADDON_HOST") . ';port=' . getenv('MYSQL_ADDON_PORT') . ';dbname=' . getenv("MYSQL_ADDON_DB"),
                getenv("MYSQL_ADDON_USER"),
                getenv("MYSQL_ADDON_PASSWORD"),
        );
} catch (PDOException $e) {
        echo "Couldn't connected to MySQL";
        echo $e->getMessage();
}

try {
        $redis = CacheManager::getInstance('redis', new Config([
                'host' => getenv("REDIS_HOST"),
                'port' => intval(getenv("REDIS_PORT")),
                'password' => getenv("REDIS_PASSWORD"),
        ]));
} catch (Exception $e) {
        echo "Couldn't connected to Redis";
        echo $e->getMessage();
}
