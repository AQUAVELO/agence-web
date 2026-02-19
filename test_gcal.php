<?php
/**
 * Script de diagnostic Google Calendar
 * URL : https://www.aquavelo.com/test_gcal.php
 * SUPPRIMER après diagnostic !
 */

require '_settings.php';
require 'load_env.php';

echo "<h2>🔍 Diagnostic Google Calendar</h2>";
echo "<pre>";

// 1. Vérifier la variable d'environnement
$b64 = $_ENV['GOOGLE_CALENDAR_KEY_JSON_BASE64'] ?? getenv('GOOGLE_CALENDAR_KEY_JSON_BASE64') ?? null;
echo "1. GOOGLE_CALENDAR_KEY_JSON_BASE64 : " . ($b64 ? "✅ Définie (" . strlen($b64) . " chars)" : "❌ MANQUANTE") . "\n\n";

// 2. Vérifier google_key.json
$keyFile = __DIR__ . '/google_key.json';
echo "2. google_key.json : " . (file_exists($keyFile) ? "✅ Existe (" . filesize($keyFile) . " bytes)" : "❌ MANQUANT") . "\n\n";

if (file_exists($keyFile)) {
    $key = json_decode(file_get_contents($keyFile), true);
    echo "3. client_email : " . ($key['client_email'] ?? '❌ manquant') . "\n";
    echo "4. project_id   : " . ($key['project_id'] ?? '❌ manquant') . "\n\n";
}

// 3. Tester la connexion Google Calendar
if (file_exists($keyFile) && file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    try {
        $client = new Google\Client();
        $client->setAuthConfig($keyFile);
        $client->addScope(Google\Service\Calendar::CALENDAR);
        $service = new Google\Service\Calendar($client);

        // Tenter de lister les événements du calendrier
        $events = $service->events->listEvents('aqua.cannes@gmail.com', ['maxResults' => 1]);
        echo "5. Connexion Google Calendar : ✅ OK\n";
        echo "6. Accès aqua.cannes@gmail.com : ✅ OK\n";
        echo "   Dernier événement : " . ($events->getItems()[0]->getSummary() ?? 'Aucun') . "\n\n";
    } catch (Exception $e) {
        echo "5. Connexion Google Calendar : ❌ ERREUR\n";
        echo "   Message : " . $e->getMessage() . "\n\n";
    }
} else {
    echo "5. Test connexion impossible (fichiers manquants)\n\n";
}

// 4. Tester le parsing de date
$test_date = "Lundi 16/02/2026 à 10:15 (AQUAVELO)";
preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $test_date, $m);
echo "7. Parsing date '$test_date' :\n";
echo "   Résultat : " . (count($m) === 3 ? "✅ Date={$m[1]} Heure={$m[2]}" : "❌ Échec") . "\n\n";

echo "</pre>";
echo "<p style='color:red'><strong>⚠️ Supprimer ce fichier après diagnostic !</strong></p>";
?>
