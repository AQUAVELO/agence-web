<?php
// settings.php

// Charger les variables d'environnement depuis .env
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Phpfastcache\CacheManager;

// Paramètres de configuration
$settings = [];

$settings['ttl'] = intval(getenv("REDIS_TTL")) ?: 3600;
$settings['dbhost'] = getenv("MYSQL_ADDON_HOST") ?: '127.0.0.1';
$settings['dbport'] = getenv("MYSQL_ADDON_PORT") ?: '3306';
$settings['dbname'] = getenv("MYSQL_ADDON_DB") ?: 'alesiaminceur';
$settings['dbusername'] = getenv("MYSQL_ADDON_USER") ?: 'root';
$settings['dbpassword'] = getenv("MYSQL_ADDON_PASSWORD") ?: 'root';

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

// Configuration Files cache (pour développement local)
CacheManager::setDefaultConfig(new \Phpfastcache\Drivers\Files\Config([
    'path' => __DIR__ . '/cache'
]));

// Instance Files cache
try {
    if (!isset($GLOBALS['redis'])) {
        $GLOBALS['redis'] = CacheManager::getInstance('files');
    }
    $redis = $GLOBALS['redis'];
} catch (Exception $e) {
    die("Couldn't connect to Cache: " . $e->getMessage());
}
```

**Sauvegardez** : `Ctrl+O`, `Entrée`, `Ctrl+X`

## 3. **Testez maintenant**
```
http://localhost:8888/aquavelo/
