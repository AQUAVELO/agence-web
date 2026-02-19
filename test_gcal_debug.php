<?php
/**
 * Diagnostic annulation Google Calendar
 * https://www.aquavelo.com/test_gcal_debug.php
 * SUPPRIMER après diagnostic !
 */
require '_settings.php';
echo "<h2>🔍 Diagnostic Google Calendar - Annulations</h2><pre>";

// 1. Vérifier les colonnes de am_free
echo "=== COLONNES TABLE am_free ===\n";
$cols = $database->query("SHOW COLUMNS FROM am_free")->fetchAll(PDO::FETCH_ASSOC);
$col_names = array_column($cols, 'Field');
echo "Colonnes : " . implode(', ', $col_names) . "\n\n";

$has_google_event_id = in_array('google_event_id', $col_names);
$has_google_sync     = in_array('google_sync', $col_names);
echo "google_event_id : " . ($has_google_event_id ? "✅ Existe" : "❌ MANQUANTE") . "\n";
echo "google_sync     : " . ($has_google_sync     ? "✅ Existe" : "❌ MANQUANTE") . "\n\n";

// 2. Ajouter les colonnes si manquantes
if (!$has_google_event_id) {
    $database->exec("ALTER TABLE am_free ADD COLUMN google_event_id VARCHAR(255) DEFAULT NULL");
    echo "✅ Colonne google_event_id AJOUTÉE\n";
}
if (!$has_google_sync) {
    $database->exec("ALTER TABLE am_free ADD COLUMN google_sync TINYINT(1) DEFAULT 0");
    echo "✅ Colonne google_sync AJOUTÉE\n";
}

// 3. Afficher les 10 derniers RDV avec leur statut Google
echo "\n=== 10 DERNIERS RDV (Cannes/Mandelieu/Vallauris) ===\n";
$stmt = $database->query("SELECT id, name, email, center_id, google_sync, google_event_id, created_at 
                           FROM am_free 
                           WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%'
                           ORDER BY id DESC LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    $client = trim(explode('(RDV:', $r['name'])[0]);
    $rdv_part = isset(explode('(RDV:', $r['name'])[1]) ? explode('(RDV:', $r['name'])[1] : '';
    echo "ID:{$r['id']} | {$client} | sync:{$r['google_sync']} | event_id:" . ($r['google_event_id'] ?: '❌ VIDE') . "\n";
}

echo "\n=== FIN DIAGNOSTIC ===\n";
echo "</pre><p style='color:red'><strong>⚠️ Supprimer ce fichier après diagnostic !</strong></p>";
?>
