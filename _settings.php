<?php
// _settings.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
use Phpfastcache\CacheManager;

$settings = [];
$settings['ttl'] = 3600;
// Base URL pour les chemins
define('BASE_URL', '/aquavelo');
$settings['base_url'] = '/aquavelo';
$settings['dbhost'] = '127.0.0.1';
$settings['dbport'] = '3306';
$settings['dbname'] = 'alesiaminceur';
$settings['dbusername'] = 'root';
$settings['dbpassword'] = 'root';

try {
    $conn = new PDO(
        "mysql:host={$settings['dbhost']};port={$settings['dbport']};dbname={$settings['dbname']}",
        $settings['dbusername'],
        $settings['dbpassword']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database = $conn;
} catch (PDOException $e) {
    die("MySQL Error: " . $e->getMessage());
}

CacheManager::setDefaultConfig(new \Phpfastcache\Drivers\Files\Config(['path' => __DIR__ . '/cache']));
$redis = CacheManager::getInstance('files');
?>
