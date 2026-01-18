<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// _settings.php
ini_set('display_errors', 0);
error_reporting(0);

require __DIR__ . '/vendor/autoload.php';
use Phpfastcache\CacheManager;

$settings = [];
$settings['ttl'] = 3600;

// Base URL pour les chemins
define('BASE_URL', '');
$settings['base_url'] = '';

// ⭐ Variables d'environnement Clever Cloud (avec fallback local)
$settings['dbhost'] = getenv('MYSQL_ADDON_HOST') ?: '127.0.0.1';
$settings['dbport'] = getenv('MYSQL_ADDON_PORT') ?: '8889';
$settings['dbname'] = getenv('MYSQL_ADDON_DB') ?: 'alesiaminceur';
$settings['dbusername'] = getenv('MYSQL_ADDON_USER') ?: 'root';
$settings['dbpassword'] = getenv('MYSQL_ADDON_PASSWORD') ?: 'root';

// ⭐ Configuration Mailjet pour l'envoi d'emails
$settings['mjhost'] = getenv('MAILJET_HOST') ?: 'in-v3.mailjet.com';
$settings['mjusername'] = getenv('MAILJET_USERNAME') ?: '';
$settings['mjpassword'] = getenv('MAILJET_PASSWORD') ?: '';


// ⭐ Configuration Telegram
$settings['tg_token'] = '8517515830:AAFWzlEOlxlrzo01l91836int0n5fTWVOZI';
$settings['tg_chat_id'] = '6535972843';

function sendTelegram($message) {
    global $settings;
    if (empty($settings['tg_token'])) return;
    $url = "https://api.telegram.org/bot" . $settings['tg_token'] . "/sendMessage?chat_id=" . $settings['tg_chat_id'] . "&text=" . urlencode($message) . "&parse_mode=html";
    @file_get_contents($url);
}

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
