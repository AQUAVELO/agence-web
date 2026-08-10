<?php
/**
 * Cron : ingère les RDV Google Agenda (site + Calendly) dans am_free
 * pour aujourd'hui et les 2 prochains jours.
 */
require __DIR__ . '/_settings.php';
require __DIR__ . '/load_env.php';
date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/Include/trial_attendance.php';

aquavelo_trial_ensure_schema($database);

$tz = new DateTimeZone('Europe/Paris');
$days = [];
for ($i = 0; $i < 3; $i++) {
    $d = new DateTime('now', $tz);
    $d->setTime(0, 0, 0);
    if ($i > 0) {
        $d->modify('+' . $i . ' day');
    }
    $days[] = $d;
}

echo "<pre style='font-family:monospace;font-size:13px;padding:20px;'>";
echo "=== INGESTION AGENDA → am_free ===\n\n";

$totalImported = 0;
$totalLinked = 0;
$totalSkipped = 0;

foreach ($days as $day) {
    echo '--- ' . $day->format('Y-m-d') . " ---\n";
    $out = aquavelo_trial_ingest_day($database, $day);
    foreach ($out['lines'] as $line) {
        echo $line . "\n";
    }
    $totalImported += $out['imported'];
    $totalLinked += $out['linked'];
    $totalSkipped += $out['skipped'];
    echo "\n";
}

echo "=== TOTAL ===\n";
echo "Créés : $totalImported\n";
echo "Reliés : $totalLinked\n";
echo "Ignorés : $totalSkipped\n";
echo '</pre>';
