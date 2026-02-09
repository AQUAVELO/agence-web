<?php
/**
 * Script de test pour vérifier l'accès au Google Calendar aqua.cannes@gmail.com
 */

require '_settings.php';
require 'load_env.php'; // Charger les variables d'environnement
date_default_timezone_set('Europe/Paris');

echo "<h1>🧪 Test Synchronisation Google Calendar</h1>";
echo "<h2>Calendrier cible : aqua.cannes@gmail.com</h2>";

// Charger l'autoloader de Composer pour Google API
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    die("❌ Erreur : Bibliothèque Google API non installée (composer require google/apiclient)");
}

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

$keyFile = 'google_key.json';
if (!file_exists($keyFile)) {
    die("❌ Erreur : Fichier google_key.json manquant.");
}

echo "<p>✅ Fichier google_key.json trouvé</p>";

// 1. Authentification Google
try {
    $client = new Client();
    $client->setAuthConfig($keyFile);
    $client->addScope(Calendar::CALENDAR);
    $service = new Calendar($client);
    
    echo "<p>✅ Authentification Google réussie</p>";
} catch (Exception $e) {
    die("<p>❌ Erreur d'authentification : " . $e->getMessage() . "</p>");
}

// 2. Test d'accès au calendrier aqua.cannes@gmail.com
$targetCalendarId = 'aqua.cannes@gmail.com';

try {
    // Essayer de lire les informations du calendrier
    $calendar = $service->calendars->get($targetCalendarId);
    echo "<p>✅ Accès au calendrier <b>{$calendar->getSummary()}</b> réussi</p>";
    echo "<p>📧 Email du calendrier : {$targetCalendarId}</p>";
    echo "<p>⏰ Fuseau horaire : {$calendar->getTimeZone()}</p>";
} catch (Exception $e) {
    echo "<p>❌ <b>ERREUR D'ACCÈS AU CALENDRIER</b> : " . $e->getMessage() . "</p>";
    echo "<p>⚠️ <b>Solution :</b> Le calendrier <b>aqua.cannes@gmail.com</b> doit partager son agenda avec le compte de service Google.</p>";
    echo "<hr>";
}

// 3. Vérifier les RDV Cannes/Mandelieu/Vallauris non synchronisés
echo "<hr><h2>📋 RDV en attente de synchronisation</h2>";

$stmt = $database->prepare("
    SELECT f.*, c.city 
    FROM am_free f
    LEFT JOIN am_centers c ON f.center_id = c.id
    WHERE f.name LIKE '%(RDV:%' 
    AND f.google_sync = 0 
    AND f.center_id IN (305, 347, 349)
    ORDER BY f.id DESC
    LIMIT 10
");
$stmt->execute();
$bookings = $stmt->fetchAll();

if (count($bookings) > 0) {
    echo "<p>📊 <b>" . count($bookings) . "</b> RDV non synchronisés trouvés (10 derniers) :</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #00d4ff; color: white;'>";
    echo "<th>ID</th><th>Centre</th><th>Client</th><th>Email</th><th>Téléphone</th><th>Date RDV</th>";
    echo "</tr>";
    
    foreach ($bookings as $booking) {
        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
        $client_name = trim(explode('(RDV:', $booking['name'])[0]);
        $rdv_date = isset($matches[1]) && isset($matches[2]) ? $matches[1] . ' à ' . $matches[2] : 'N/A';
        
        echo "<tr>";
        echo "<td>{$booking['id']}</td>";
        echo "<td>{$booking['city']}</td>";
        echo "<td>{$client_name}</td>";
        echo "<td>{$booking['email']}</td>";
        echo "<td>{$booking['phone']}</td>";
        echo "<td><b>{$rdv_date}</b></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>✅ Aucun RDV en attente de synchronisation</p>";
}

// 4. Créer un événement de test
echo "<hr><h2>🧪 Test de création d'événement</h2>";
echo "<p>Tentative de création d'un événement de test...</p>";

try {
    $rdv_start = new DateTime('now');
    $rdv_start->modify('+2 hours'); // Dans 2 heures
    $rdv_end = clone $rdv_start;
    $rdv_end->modify('+45 minutes');
    
    $event = new Event([
        'summary' => '🧪 TEST SYNC - Client Test',
        'location' => '60 Avenue du Dr Raymond Picaud, 06150 Cannes',
        'description' => "Événement de test pour vérifier la synchronisation Google Calendar\nScript: test_google_calendar_cannes.php",
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
    
    echo "<p>✅ <b>ÉVÉNEMENT DE TEST CRÉÉ AVEC SUCCÈS !</b></p>";
    echo "<p>📅 ID événement Google : <code>{$googleEventId}</code></p>";
    echo "<p>🕐 Date/Heure : <b>" . $rdv_start->format('d/m/Y à H:i') . "</b></p>";
    echo "<p>📧 Calendrier : <b>{$targetCalendarId}</b></p>";
    
    echo "<hr>";
    echo "<p>⚠️ <b>N'oubliez pas de supprimer cet événement de test dans Google Calendar</b></p>";
    
} catch (Exception $e) {
    echo "<p>❌ <b>ERREUR lors de la création de l'événement de test :</b></p>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    
    if (strpos($e->getMessage(), '403') !== false || strpos($e->getMessage(), 'Forbidden') !== false) {
        echo "<hr>";
        echo "<h3>🔧 SOLUTION : Partager le calendrier avec le compte de service</h3>";
        echo "<ol>";
        echo "<li>Ouvrez Google Calendar avec le compte <b>aqua.cannes@gmail.com</b></li>";
        echo "<li>Cliquez sur les 3 points à côté du calendrier → <b>Paramètres et partage</b></li>";
        echo "<li>Dans la section <b>Partager avec des personnes en particulier</b></li>";
        echo "<li>Ajoutez l'email du compte de service (trouvé dans <code>google_key.json</code> → <code>client_email</code>)</li>";
        echo "<li>Donnez-lui les droits <b>Apporter des modifications aux événements</b></li>";
        echo "</ol>";
        
        // Lire l'email du compte de service
        $keyContent = json_decode(file_get_contents($keyFile), true);
        if (isset($keyContent['client_email'])) {
            echo "<p>📧 <b>Email du compte de service à ajouter :</b> <code style='background: yellow; padding: 5px; font-size: 16px;'>{$keyContent['client_email']}</code></p>";
        }
    }
}

echo "<hr>";
echo "<p><a href='cron_sync_google.php' style='background: #00d4ff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>▶️ Lancer la synchronisation complète</a></p>";
?>
