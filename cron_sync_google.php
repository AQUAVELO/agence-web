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

// 2. Récupérer les RDV non synchronisés
// Ajout du centre 253 (Antibes)
$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND google_sync = 0 AND center_id IN (305, 347, 349, 343, 253)");
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
                
                // Récupérer les infos du centre (adresse et email pour l'agenda)
                $stmt_c = $database->prepare("SELECT address, email FROM am_centers WHERE id = ?");
                $stmt_c->execute([$booking['center_id'] ?: 305]);
                $c_info = $stmt_c->fetch();
                
                $location = $c_info['address'] ?? '60 Avenue du Dr Raymond Picaud, 06150 Cannes';
                
                // Déterminer l'agenda de destination
                // Par défaut aqua.cannes@gmail.com, sauf si le centre a un email spécifique
                $targetCalendarId = 'aqua.cannes@gmail.com';
                if (!empty($c_info['email'])) {
                    $targetCalendarId = $c_info['email'];
                }

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

                $createdEvent = $service->events->insert($targetCalendarId, $event);
                $googleEventId = $createdEvent->getId();

                // Marquer comme synchronisé et stocker l'ID Google
                $database->prepare("UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?")->execute([$googleEventId, $booking['id']]);
                $count++;
                
            } catch (Exception $e) {
                error_log("Erreur Google Calendar Sync (ID: {$booking['id']}, Target: $targetCalendarId): " . $e->getMessage());
            }
        }
    }
}

echo "Nombre de RDV synchronisés avec Google Calendar : $count";
?>
