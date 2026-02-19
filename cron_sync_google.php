<?php
/**
 * Script de synchronisation vers Google Calendar
 */

require '_settings.php';
require 'load_env.php'; // Charger les variables d'environnement
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

$keyFile = __DIR__ . '/google_key.json';
if (!file_exists($keyFile)) {
    generateGoogleKeyFile();
}
if (!file_exists($keyFile)) {
    die("Erreur : Fichier google_key.json manquant même après génération.");
}

// 1. Authentification Google
$client = new Client();
$client->setAuthConfig($keyFile);
$client->addScope(Calendar::CALENDAR);
$service = new Calendar($client);

// 2. Récupérer les RDV non synchronisés (centres Cannes, Mandelieu, Vallauris uniquement)
$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND google_sync = 0 AND center_id IN (305, 347, 349) ORDER BY id DESC");
$stmt->execute();
$bookings = $stmt->fetchAll();

echo "<pre style='font-family:monospace; font-size:13px; padding:20px;'>";
echo "=== SYNCHRONISATION GOOGLE CALENDAR ===\n";
echo count($bookings) . " RDV à synchroniser...\n\n";

$count = 0;
$errors = 0;

foreach ($bookings as $booking) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    $client_name = trim(explode('(RDV:', $booking['name'])[0]);

    if (count($matches) === 3) {
        $rdv_start = \DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2], new \DateTimeZone('Europe/Paris'));

        if ($rdv_start) {
            try {
                $rdv_end = clone $rdv_start;
                $rdv_end->modify('+45 minutes');

                // Récupérer les infos du centre
                $stmt_c = $database->prepare("SELECT address, email, city FROM am_centers WHERE id = ?");
                $stmt_c->execute([$booking['center_id']]);
                $c_info = $stmt_c->fetch();

                $location = $c_info['address'] ?? '60 Avenue du Dr Raymond Picaud, 06150 Cannes';
                $targetCalendarId = 'aqua.cannes@gmail.com';

                $event = new Event([
                    'summary'     => '🏊 ' . $client_name . ' - ' . ($c_info['city'] ?? 'Cannes'),
                    'location'    => $location,
                    'description' => "Client: $client_name\nEmail: {$booking['email']}\nTél: {$booking['phone']}\nID: {$booking['id']}",
                    'start' => ['dateTime' => $rdv_start->format(\DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                    'end'   => ['dateTime' => $rdv_end->format(\DateTime::RFC3339),   'timeZone' => 'Europe/Paris'],
                ]);

                $createdEvent = $service->events->insert($targetCalendarId, $event);
                $googleEventId = $createdEvent->getId();

                $database->prepare("UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?")
                         ->execute([$googleEventId, $booking['id']]);

                echo "✅ ID:{$booking['id']} | $client_name | {$matches[1]} à {$matches[2]} → $googleEventId\n";
                $count++;

            } catch (\Exception $e) {
                echo "❌ ID:{$booking['id']} | $client_name | Erreur: " . $e->getMessage() . "\n";
                $errors++;
            }
        } else {
            echo "⚠️ ID:{$booking['id']} | $client_name | Date non reconnue: {$matches[1]} à {$matches[2]}\n";
        }
    } else {
        echo "⚠️ ID:{$booking['id']} | $client_name | Format date non parsé\n";
    }
}

echo "\n=== RÉSULTAT ===\n";
echo "✅ Synchronisés : $count\n";
if ($errors) echo "❌ Erreurs     : $errors\n";
echo "</pre>";
?>
