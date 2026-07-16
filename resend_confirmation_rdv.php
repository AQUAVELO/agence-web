<?php
/**
 * Recherche et renvoi d'email de confirmation RDV séance d'essai.
 *
 * ?key=aquavelo123&q=Hervieux&slot=17:15
 * ?key=aquavelo123&id=123&send=1
 */
declare(strict_types=1);

require_once '_settings.php';
require_once __DIR__ . '/google_calendar_rdv_helpers.php';
require_once __DIR__ . '/Include/rdv_confirmation_mail.php';

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

date_default_timezone_set('Europe/Paris');

$secretKey = 'aquavelo123';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    die('Accès non autorisé.');
}

header('Content-Type: text/plain; charset=utf-8');

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
    $sql = "SELECT id, name, email, phone, center_id, reference, segment_id, date
            FROM am_free
            WHERE center_id IN (305, 347, 349)
              AND name LIKE ?
              AND name LIKE '%(RDV:%'
              AND name NOT LIKE '%BLOQUE%'
              AND COALESCE(segment_id, '') <> 'admin-lock'
            ORDER BY id DESC
            LIMIT 20";
    $stmt = $database->prepare($sql);
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
    echo "Mode consultation uniquement. Ajoutez &send=1 pour renvoyer l'email.\n";
}
