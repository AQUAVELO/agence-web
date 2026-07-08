<?php
/**
 * Suppression des réservations séance d'essai Cannes / Mandelieu / Vallauris
 * pour la fermeture du 27/07/2026 au 01/08/2026.
 *
 * Usage :
 *   ?key=aquavelo123&dry=1   → aperçu sans suppression
 *   ?key=aquavelo123         → suppression BDD + Google Calendar
 */
declare(strict_types=1);

require_once '_settings.php';
require_once __DIR__ . '/Include/cannes_group_closure.php';
require_once __DIR__ . '/google_calendar_rdv_helpers.php';
require_once __DIR__ . '/load_env.php';

date_default_timezone_set('Europe/Paris');

$secret_key = 'aquavelo123';
if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    die('Accès non autorisé.');
}

$dry_run = isset($_GET['dry']) && $_GET['dry'] === '1';

header('Content-Type: text/plain; charset=utf-8');

echo $dry_run ? "=== MODE APERÇU (aucune suppression) ===\n\n" : "=== SUPPRESSION DES RÉSERVATIONS ===\n\n";

$stmt = $database->query(
    "SELECT id, name, email, phone, center_id, google_event_id
     FROM am_free
     WHERE center_id IN (305, 347, 349)
       AND name LIKE '%(RDV:%'
       AND name NOT LIKE '%BLOQUE%'
       AND name NOT LIKE '%VERROUILLÉ%'
       AND COALESCE(segment_id, '') <> 'admin-lock'"
);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$to_delete = [];
foreach ($rows as $row) {
    $parsed = aquavelo_gc_resolve_rdv_datetime($row);
    if ($parsed === null) {
        continue;
    }
    if (is_cannes_group_closed((int) $row['center_id'], $parsed['date'])) {
        $to_delete[] = $row;
    }
}

echo count($to_delete) . " réservation(s) trouvée(s) sur la période de fermeture.\n\n";

if ($to_delete === []) {
    echo "Rien à faire.\n";
    exit;
}

$gc_service = null;
if (!$dry_run) {
    $gc_service = aquavelo_gc_bootstrap();
    if (!$gc_service) {
        echo "Attention : Google Calendar indisponible, suppression BDD uniquement.\n\n";
    }
}

$deleted = 0;
$gc_deleted = 0;

foreach ($to_delete as $row) {
    $parsed = aquavelo_gc_resolve_rdv_datetime($row);
    $when = $parsed ? ($parsed['date'] . ' ' . $parsed['time']) : '?';
    echo sprintf(
        "#%d | centre %d | %s | %s | %s\n",
        (int) $row['id'],
        (int) $row['center_id'],
        $when,
        $row['email'],
        $row['name']
    );

    if ($dry_run) {
        continue;
    }

    if ($gc_service) {
        if (aquavelo_gc_delete_booking_event($gc_service, $database, $row)) {
            $gc_deleted++;
        }
    }

    $database->prepare('DELETE FROM am_free WHERE id = ?')->execute([(int) $row['id']]);
    $deleted++;
}

echo "\n";
if ($dry_run) {
    echo "Aperçu terminé. Relancez sans &dry=1 pour supprimer.\n";
} else {
    echo "Terminé : {$deleted} réservation(s) supprimée(s), {$gc_deleted} événement(s) Google Calendar retiré(s).\n";
}
