<?php
/**
 * Utilitaires maintenance planning Cannes.
 *
 * Fermeture :
 *   ?key=aquavelo123&dry=1
 *   ?key=aquavelo123
 *
 * Renvoi confirmation email :
 *   ?key=aquavelo123&action=resend&q=Hervieux&slot=17:15
 *   ?key=aquavelo123&action=resend&q=Hervieux&slot=17:15&send=1
 */
declare(strict_types=1);

require_once '_settings.php';
require_once __DIR__ . '/google_calendar_rdv_helpers.php';
require_once __DIR__ . '/load_env.php';

date_default_timezone_set('Europe/Paris');

$secret_key = 'aquavelo123';
if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    die('Accès non autorisé.');
}

header('Content-Type: text/plain; charset=utf-8');

if (isset($_GET['action']) && $_GET['action'] === 'resend') {
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }
    require_once __DIR__ . '/Include/rdv_confirmation_mail.php';

    $q = trim((string) ($_GET['q'] ?? ''));
    $slot = trim((string) ($_GET['slot'] ?? ''));
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $send = isset($_GET['send']) && $_GET['send'] === '1';

    if ($id > 0) {
        $stmt = $database->prepare(
            "SELECT id, name, email, phone, center_id, reference, segment_id, date
             FROM am_free WHERE id = ? AND name LIKE '%(RDV:%'"
        );
        $stmt->execute([$id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($q !== '') {
        $stmt = $database->prepare(
            "SELECT id, name, email, phone, center_id, reference, segment_id, date
             FROM am_free
             WHERE center_id IN (305, 347, 349)
               AND name LIKE ?
               AND name LIKE '%(RDV:%'
               AND name NOT LIKE '%BLOQUE%'
               AND COALESCE(segment_id, '') <> 'admin-lock'
             ORDER BY id DESC
             LIMIT 20"
        );
        $stmt->execute(['%' . $q . '%']);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die("Paramètre requis : q=Nom ou id=123\n");
    }

    if ($slot !== '') {
        $slotNorm = str_replace('h', ':', strtolower($slot));
        $rows = array_values(array_filter($rows, static function (array $row) use ($slotNorm): bool {
            $parsed = aquavelo_gc_resolve_rdv_datetime($row);

            return $parsed !== null && stripos($parsed['time'], $slotNorm) !== false;
        }));
    }

    if ($rows === []) {
        echo "Aucune réservation trouvée.\n";
        exit;
    }

    echo count($rows) . " réservation(s) trouvée(s).\n\n";

    foreach ($rows as $row) {
        $parsed = aquavelo_gc_resolve_rdv_datetime($row);
        $when = $parsed ? ($parsed['date'] . ' ' . $parsed['time']) : '?';
        echo "ID #{$row['id']}\n";
        echo "  Nom   : {$row['name']}\n";
        echo "  Email : {$row['email']}\n";
        echo "  Tél   : {$row['phone']}\n";
        echo "  RDV   : $when\n";
        echo "  Centre: {$row['center_id']}\n\n";

        if ($send) {
            $result = aquavelo_send_rdv_confirmation_to_client($database, $settings, $row);
            echo $result['ok'] ? "  => ENVOYÉ : {$result['message']}\n\n" : "  => ÉCHEC : {$result['message']}\n\n";
        }
    }

    if (!$send) {
        echo "Mode consultation. Ajoutez &send=1 pour renvoyer l'email.\n";
    }
    exit;
}

require_once __DIR__ . '/Include/cannes_group_closure.php';

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
