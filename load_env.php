<?php
/**
 * Chargement des variables d'environnement depuis .env
 * et génération dynamique du fichier google_key.json
 */

// 1. Charger le fichier .env si il existe
$envFile = __DIR__ . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parser la ligne KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Retirer les guillemets si présents
            $value = trim($value, '"\'');
            
            // Définir la variable d'environnement
            if (!empty($key) && !isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// 2. Générer dynamiquement google_key.json depuis les variables d'environnement
function generateGoogleKeyFile() {
    $keyFile = __DIR__ . '/google_key.json';
    
    // Option 1 : Si le JSON complet est stocké en base64
    if (!empty($_ENV['GOOGLE_CALENDAR_KEY_JSON_BASE64'])) {
        $jsonContent = base64_decode($_ENV['GOOGLE_CALENDAR_KEY_JSON_BASE64']);
        file_put_contents($keyFile, $jsonContent);
        return true;
    }
    
    // Option 2 : Si les champs sont stockés individuellement
    if (!empty($_ENV['GOOGLE_CLIENT_EMAIL']) && !empty($_ENV['GOOGLE_PRIVATE_KEY'])) {
        $keyData = [
            'type' => 'service_account',
            'project_id' => $_ENV['GOOGLE_PROJECT_ID'] ?? 'aquavelo-calendar',
            'private_key_id' => $_ENV['GOOGLE_PRIVATE_KEY_ID'] ?? '',
            'private_key' => str_replace('\\n', "\n", $_ENV['GOOGLE_PRIVATE_KEY']),
            'client_email' => $_ENV['GOOGLE_CLIENT_EMAIL'],
            'client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/' . urlencode($_ENV['GOOGLE_CLIENT_EMAIL']),
            'universe_domain' => 'googleapis.com'
        ];
        
        file_put_contents($keyFile, json_encode($keyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return true;
    }
    
    // Si le fichier existe déjà (local), on le garde
    if (file_exists($keyFile)) {
        return true;
    }
    
    return false;
}

// Générer le fichier si nécessaire
generateGoogleKeyFile();
?>
