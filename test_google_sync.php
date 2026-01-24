<?php
/**
 * Script de TEST pour la synchronisation Google Calendar
 * Force la synchro du dernier RDV trouvé
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
echo "Démarrage du test Google Calendar...\n";

$keyFile = 'google_key.json';
if (!file_exists($keyFile)) { die("❌ Erreur : Fichier google_key.json manquant."); }

try {
    $client = new Client();
    $client->setAuthConfig($keyFile);
    $client->addScope(Calendar::CALENDAR);
    $service = new Calendar($client);
    $calendarId = 'aqua.cannes@gmail.com';

    echo "Connexion Google établie ✅\n";

    // On récupère le dernier RDV de Cannes (305) pour tester
    $stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND center_id = 305 ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $booking = $stmt->fetch();

    if ($booking) {
        echo "RDV trouvé pour le test : " . $booking['name'] . " (ID: " . $booking['id'] . ")\n";
        
        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
        if (count($matches) === 3) {
            $rdv_start = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
            $rdv_end = clone $rdv_start;
            $rdv_end->modify('+45 minutes');
            $client_name = trim(explode('(RDV:', $booking['name'])[0]);

            $event = new Event([
                'summary' => '🏊 TEST - ' . $client_name,
                'location' => '60 Avenue du Dr Raymond Picaud, 06150 Cannes',
                'description' => "TEST DE SYNCHRO\nClient: " . $client_name . "\nEmail: " . $booking['email'],
                'start' => ['dateTime' => $rdv_start->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                'end' => ['dateTime' => $rdv_end->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
            ]);

            echo "Tentative d'insertion dans l'agenda $calendarId...\n";
            $service->events->insert($calendarId, $event);
            echo "✅ SUCCÈS : L'événement de test a été créé dans votre Google Agenda !";
        } else {
            echo "❌ Erreur : Format de date non reconnu dans le nom.";
        }
    } else {
        echo "❌ Erreur : Aucun RDV trouvé dans la base de données pour le centre 305.";
    }

} catch (Exception $e) {
    echo "❌ ERREUR GOOGLE : " . $e->getMessage();
}
echo "</pre>";
?>
