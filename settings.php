<?php
// settings.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\Redis\Config;

// Paramètres de configuration
$settings = [];

$settings['ttl'] = intval(getenv("REDIS_TTL"));
$settings['dbhost'] = getenv("MYSQL_ADDON_HOST");
$settings['dbport'] = getenv("MYSQL_ADDON_PORT");
$settings['dbname'] = getenv("MYSQL_ADDON_DB");
$settings['dbusername'] = getenv("MYSQL_ADDON_USER");
$settings['dbpassword'] = getenv("MYSQL_ADDON_PASSWORD");

// Connexion à la base de données
try {
    $conn = new PDO(
        'mysql:host=' . $settings['dbhost'] . ';port=' . $settings['dbport'] . ';dbname=' . $settings['dbname'],
        $settings['dbusername'],
        $settings['dbpassword']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Couldn't connect to MySQL: " . $e->getMessage());
}

// Configuration Redis (une seule fois pour tout le projet)
CacheManager::setDefaultConfig(new Config([
    'host' => getenv("REDIS_HOST"),
    'port' => intval(getenv("REDIS_PORT")),
    'password' => getenv("REDIS_PASSWORD"),
]));

// Instance Redis unique
try {
    if (!isset($GLOBALS['redis'])) {
        $GLOBALS['redis'] = CacheManager::getInstance('redis');
    }
    $redis = $GLOBALS['redis'];
} catch (Exception $e) {
    die("Couldn't connect to Redis: " . $e->getMessage());
}

