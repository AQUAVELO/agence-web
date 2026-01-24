<?php
/**
 * Script de RATTRAPAGE pour synchroniser tous les anciens RDV vers Google Calendar
 */

require '_settings.php';
date_default_timezone_set('Europe/Paris');

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

echo "<pre>";
echo "Démarrage du rattrapage Google Calendar...\n";

$keyFile = 'google_key.json';
if (!file_exists($keyFile)) { die("❌ Erreur : Fichier google_key.json manquant."); }

try {
    $client = new Client();
    $client->setAuthConfig($keyFile);
    $client->addScope(Calendar::CALENDAR);
    $service = new Calendar($client);
    $calendarId = 'aqua.cannes@gmail.com';

    // On récupère TOUS les RDV de Cannes, Mandelieu, Vallauris qui n'ont pas encore été synchronisés
    // On limite aux RDV à partir du 01/01/2026 pour ne pas trop charger l'agenda
    $stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND google_sync = 0 AND center_id IN (305, 347, 349, 343)");
    $stmt->execute();
    $bookings = $stmt->fetchAll();

    echo "Nombre de RDV à synchroniser : " . count($bookings) . "\n\n";

    $count = 0;
    foreach ($bookings as $booking) {
        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
        
        if (count($matches) === 3) {
            $rdv_start = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
            
            // On ne synchronise que si la date est valide
            if ($rdv_start) {
                $rdv_end = clone $rdv_start;
                $rdv_end->modify('+45 minutes');
                $client_name = trim(explode('(RDV:', $booking['name'])[0]);

                $event = new Event([
                    'summary' => '🏊 ' . $client_name,
                    'location' => '60 Avenue du Dr Raymond Picaud, 06150 Cannes',
                    'description' => "Client: " . $client_name . "\nEmail: " . $booking['email'] . "\nTél: " . $booking['phone'],
                    'start' => ['dateTime' => $rdv_start->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                    'end' => ['dateTime' => $rdv_end->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                ]);

                try {
                    $service->events->insert($calendarId, $event);
                    $database->prepare("UPDATE am_free SET google_sync = 1 WHERE id = ?")->execute([$booking['id']]);
                    $count++;
                    echo "✅ Synchro : $client_name (" . $rdv_start->format('d/m/Y H:i') . ")\n";
                    
                    // Petite pause pour ne pas saturer l'API Google (quota)
                    if ($count % 5 == 0) usleep(500000); 

                } catch (Exception $e) {
                    echo "❌ Erreur pour $client_name : " . $e->getMessage() . "\n";
                }
            }
        }
    }

    echo "\n\n✅ Rattrapage terminé ! Total synchronisés : $count";

} catch (Exception $e) {
    echo "❌ ERREUR GENERALE : " . $e->getMessage();
}
echo "</pre>";
?>
