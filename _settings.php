<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// _settings.php
ini_set('display_errors', 0);
error_reporting(0);

require __DIR__ . '/vendor/autoload.php';
use Phpfastcache\CacheManager;

$settings = [];
$settings['ttl'] = 3600;

// Base URL pour les chemins locaux (MAMP)
$base_url = "/";
if (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
    // Uniquement en local, on détecte si on est dans le sous-dossier /aquavelo/
    if (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/aquavelo/') !== false) {
        $base_url = "/aquavelo/";
    }
}
define('BASE_PATH', $base_url);

// ⭐ Variables d'environnement Clever Cloud (avec fallback local)
$settings['dbhost'] = getenv('MYSQL_ADDON_HOST') ?: '127.0.0.1';
$settings['dbport'] = getenv('MYSQL_ADDON_PORT') ?: '8889';
$settings['dbname'] = getenv('MYSQL_ADDON_DB') ?: 'alesiaminceur';
$settings['dbusername'] = getenv('MYSQL_ADDON_USER') ?: 'root';
$settings['dbpassword'] = getenv('MYSQL_ADDON_PASSWORD') ?: 'root';

// ⭐ Configuration Mailjet pour l'envoi d'emails
$settings['mjhost'] = getenv('MAILJET_HOST') ?: 'in-v3.mailjet.com';
$settings['mjusername'] = getenv('MAILJET_USERNAME') ?: 'adf33e0c77039ed69396e3a8a07400cb';
$settings['mjpassword'] = getenv('MAILJET_PASSWORD') ?: '05906e966c8e2933b1dc8b0f8bb1e18b';


// ⭐ Configuration Telegram
$settings['tg_token'] = '8517515830:AAFWzlEOlxlrzo01l91836int0n5fTWVOZI';
$settings['tg_chat_id'] = '6535972843';

// ⭐ Configuration SMSFactor
$settings['smsfactor_token'] = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMzIzOSIsImlhdCI6MTc2ODkyMjIzOS4wMjUwMjZ9.CXCqk_FX_6WzK6Hk3MosVXWTgsDtntI2t7j6W5Q5o4Y';

if (!function_exists('sendTelegram')) {
    function sendTelegram($message) {
        global $settings;
        if (empty($settings['tg_token'])) return;
        
        $url = "https://api.telegram.org/bot" . $settings['tg_token'] . "/sendMessage";
        $data = [
            'chat_id' => $settings['tg_chat_id'],
            'text' => $message,
            'parse_mode' => 'html'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('sendSMS')) {
    function sendSMS($phone, $message) {
        global $settings;
        if (empty($settings['smsfactor_token'])) return false;

        // Nettoyage du numéro (on garde que les chiffres)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        // Si ça commence par 06 ou 07, on ajoute 33
        if (strpos($phone, '0') === 0 && (strpos($phone, '06') === 0 || strpos($phone, '07') === 0)) {
            $phone = '33' . substr($phone, 1);
        }

        $postdata = [
            'sms' => [
                'message' => [
                    'text' => $message,
                    'sender' => 'Aquavelo'
                ],
                'recipients' => [
                    'gsm' => [['value' => $phone]]
                ]
            ]
        ];

        $ch = curl_init('https://api.smsfactor.com/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $settings['smsfactor_token']
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
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

$cachePath = __DIR__ . '/cache';
if (!is_dir($cachePath)) { mkdir($cachePath, 0777, true); }
CacheManager::setDefaultConfig(new \Phpfastcache\Drivers\Files\Config(['path' => $cachePath]));
$redis = CacheManager::getInstance('files');
?>
 
