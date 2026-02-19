<?php
/**
 * Vérification des événements Google Calendar - aqua.cannes@gmail.com
 * À supprimer après diagnostic !
 */
require '_settings.php';
require 'load_env.php';

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Google\Client;
use Google\Service\Calendar;

$keyFile = __DIR__ . '/google_key.json';
if (!file_exists($keyFile)) {
    generateGoogleKeyFile();
}
if (!file_exists($keyFile)) {
    die("❌ google_key.json manquant");
}

$client = new Client();
$client->setAuthConfig($keyFile);
$client->addScope(Calendar::CALENDAR);
$service = new Calendar($client);

$calendarId = 'aqua.cannes@gmail.com';

// Lister les événements du 1er février au 31 mars 2026
$timeMin = (new \DateTime('2026-02-01', new \DateTimeZone('Europe/Paris')))->format(\DateTime::RFC3339);
$timeMax = (new \DateTime('2026-03-31', new \DateTimeZone('Europe/Paris')))->format(\DateTime::RFC3339);

echo "<pre style='font-family:monospace; font-size:13px; padding:20px;'>";
echo "=== ÉVÉNEMENTS Google Calendar : $calendarId ===\n";
echo "Période : 01/02/2026 → 31/03/2026\n\n";

try {
    $events = $service->events->listEvents($calendarId, [
        'timeMin'      => $timeMin,
        'timeMax'      => $timeMax,
        'singleEvents' => true,
        'orderBy'      => 'startTime',
        'maxResults'   => 50,
    ]);

    $items = $events->getItems();
    if (empty($items)) {
        echo "⚠️ Aucun événement trouvé dans cette période.\n";
        echo "→ Soit le calendrier est vide, soit le service account n'y a pas accès en lecture.\n";
    } else {
        echo count($items) . " événements trouvés :\n\n";
        foreach ($items as $event) {
            $start = $event->start->dateTime ?? $event->start->date;
            $dt = new \DateTime($start, new \DateTimeZone('Europe/Paris'));
            echo "📅 " . $dt->format('d/m/Y H:i') . " | " . $event->getSummary() . "\n";
            echo "   ID: " . $event->getId() . "\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Erreur API: " . $e->getMessage() . "\n";
    echo "\n→ Si erreur 403 : le service account n'a pas accès au calendrier aqua.cannes@gmail.com\n";
    echo "→ Solution : dans Google Calendar, partager le calendrier avec l'email du service account\n";
    echo "  et lui donner l'autorisation 'Apporter des modifications aux événements'\n";
}

echo "</pre>";
?>
