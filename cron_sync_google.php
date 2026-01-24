<?php
/**
 * Script de synchronisation vers Google Calendar
 */

require '_settings.php';
date_default_timezone_set('Europe/Paris');

// Charger l'autoloader de Composer pour Google API
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    die("Erreur : Bibliothèque Google API non installée (composer require google/apiclient)");
}

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

$keyFile = 'google_key.json';
if (!file_exists($keyFile)) {
    die("Erreur : Fichier google_key.json manquant.");
}

// 1. Authentification Google
$client = new Client();
$client->setAuthConfig($keyFile);
$client->addScope(Calendar::CALENDAR);
$service = new Calendar($client);

$calendarId = 'aqua.cannes@gmail.com';

// 2. Récupérer les RDV non synchronisés (sur les 7 derniers jours et le futur)
$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND google_sync = 0 AND center_id IN (305, 347, 349, 343)");
$stmt->execute();
$bookings = $stmt->fetchAll();

$count = 0;
foreach ($bookings as $booking) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    
    if (count($matches) === 3) {
        $rdv_start = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
        
        if ($rdv_start) {
            try {
                // Durée estimée 45 min
                $rdv_end = clone $rdv_start;
                $rdv_end->modify('+45 minutes');

                $client_name = trim(explode('(RDV:', $booking['name'])[0]);
                
                // Récupérer le lieu du centre
                $stmt_c = $database->prepare("SELECT address FROM am_centers WHERE id = ?");
                $stmt_c->execute([$booking['center_id'] ?: 305]);
                $c_info = $stmt_c->fetch();
                $location = $c_info['address'] ?? '60 Avenue du Dr Raymond Picaud, 06150 Cannes';

                // Création de l'événement
                $event = new Event([
                    'summary' => '🏊 ' . $client_name,
                    'location' => $location,
                    'description' => "Client: " . $client_name . "\nEmail: " . $booking['email'] . "\nTél: " . $booking['phone'] . "\nID: " . $booking['id'],
                    'start' => [
                        'dateTime' => $rdv_start->format(DateTime::RFC3339),
                        'timeZone' => 'Europe/Paris',
                    ],
                    'end' => [
                        'dateTime' => $rdv_end->format(DateTime::RFC3339),
                        'timeZone' => 'Europe/Paris',
                    ],
                ]);

                $createdEvent = $service->events->insert($calendarId, $event);
                $googleEventId = $createdEvent->getId();

                // Marquer comme synchronisé et stocker l'ID Google
                $database->prepare("UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?")->execute([$googleEventId, $booking['id']]);
                $count++;
                
            } catch (Exception $e) {
                error_log("Erreur Google Calendar Sync: " . $e->getMessage());
            }
        }
    }
}

echo "Nombre de RDV synchronisés avec Google Calendar : $count";
?>
