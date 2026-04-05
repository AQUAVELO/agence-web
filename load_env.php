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
            
            // Définir la variable d'environnement uniquement si pas déjà définie par le système
            if (!empty($key)) {
                $existing = getenv($key);
                if ($existing === false || $existing === '') {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                } else {
                    $_ENV[$key] = $existing;
                }
            }
        }
    }
}

/**
 * Lit une variable d'environnement (Clever Cloud / PHP-FPM : souvent getenv, pas $_ENV).
 */
function aquavelo_env(string $key): string
{
    $v = getenv($key);
    if ($v !== false && $v !== '') {
        return $v;
    }
    if (isset($_SERVER[$key]) && (string) $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }
    if (isset($_ENV[$key]) && (string) $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }

    return '';
}

// 2. Générer dynamiquement google_key.json depuis les variables d'environnement
function generateGoogleKeyFile() {
    $keyFile = __DIR__ . '/google_key.json';
    
    // Option 1 : Si le JSON complet est stocké en base64 (Clever Cloud : GOOGLE_CALENDAR_KEY_JSON_BASE64)
    $b64 = aquavelo_env('GOOGLE_CALENDAR_KEY_JSON_BASE64');
    if ($b64 !== '') {
        $jsonContent = base64_decode($b64, true);
        if ($jsonContent !== false && json_decode($jsonContent) !== null) {
            file_put_contents($keyFile, $jsonContent);

            return true;
        }
        error_log('generateGoogleKeyFile: GOOGLE_CALENDAR_KEY_JSON_BASE64 invalide (base64 ou JSON)');
    }
    
    // Option 2 : Si les champs sont stockés individuellement
    $gEmail = aquavelo_env('GOOGLE_CLIENT_EMAIL');
    $gKey = aquavelo_env('GOOGLE_PRIVATE_KEY');
    if ($gEmail !== '' && $gKey !== '') {
        $keyData = [
            'type' => 'service_account',
            'project_id' => aquavelo_env('GOOGLE_PROJECT_ID') ?: 'aquavelo-calendar',
            'private_key_id' => aquavelo_env('GOOGLE_PRIVATE_KEY_ID'),
            'private_key' => str_replace('\\n', "\n", $gKey),
            'client_email' => $gEmail,
            'client_id' => aquavelo_env('GOOGLE_CLIENT_ID'),
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509/' . urlencode($gEmail),
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
