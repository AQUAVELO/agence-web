<?php
/**
 * Script de synchronisation vers Google Calendar (cron Clever Cloud)
 */

require '_settings.php';
require 'load_env.php';
date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/google_calendar_rdv_helpers.php';

$out = aquavelo_gc_sync_pending_rdvs($database);

echo "<pre style='font-family:monospace; font-size:13px; padding:20px;'>";
echo "=== SYNCHRONISATION GOOGLE CALENDAR ===\n\n";
foreach ($out['lines'] as $line) {
    echo $line . "\n";
}
if (empty($out['lines'])) {
    echo "Aucun RDV à traiter (tous ont un google_event_id, ou hors centres 305/347/349).\n";
}
echo "\n=== RÉSULTAT ===\n";
echo '✅ Synchronisés / reliés : ' . $out['synced'] . "\n";
if ($out['errors'] > 0) {
    echo '❌ Erreurs : ' . $out['errors'] . "\n";
}
echo '</pre>';
