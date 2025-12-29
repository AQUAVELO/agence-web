<?php
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
$settings['dbport'] = getenv('MYSQL_ADDON_PORT') ?: '3306';
$settings['dbname'] = getenv('MYSQL_ADDON_DB') ?: 'alesiaminceur';
$settings['dbusername'] = getenv('MYSQL_ADDON_USER') ?: 'root';
$settings['dbpassword'] = getenv('MYSQL_ADDON_PASSWORD') ?: 'root';

// ⭐ Configuration Mailjet pour l'envoi d'emails
$settings['mjhost'] = getenv('MAILJET_HOST') ?: 'in-v3.mailjet.com';
$settings['mjusername'] = getenv('MAILJET_USERNAME') ?: '';
$settings['mjpassword'] = getenv('MAILJET_PASSWORD') ?: '';

// ⭐ Configuration reCAPTCHA v3 (anti-spam)
$settings['recaptcha_site_key'] = getenv('RECAPTCHA_SITE_KEY') ?: '6LfKRjosAAAANiQmJ5BxYnMFVnYU_r-tyP0bMhz';
$settings['recaptcha_secret_key'] = getenv('RECAPTCHA_SECRET_KEY') ?: '6LfKRjosAAAABUFV-a02SvA2ciHEMS5T-Xnlw3z';
$settings['recaptcha_score_threshold'] = 0.5; // Seuil de score (0.0 = bot, 1.0 = humain)

// ⭐ Détection environnement local (désactive reCAPTCHA en local)
$is_local = (
    strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false ||
    strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false
);
$settings['recaptcha_enabled'] = !$is_local;

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
