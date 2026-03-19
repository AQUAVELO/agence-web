<?php
/**
 * Script de correction one-shot : renommer "Aix en Provence" → "Aix-en-Provence" (ID 353)
 * À SUPPRIMER après utilisation
 */

require '_settings.php';

if (!isset($_GET['key']) || $_GET['key'] !== 'aquavelo-fix-353') {
    die("Accès non autorisé.");
}

echo "<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:20px;} .ok{color:green;font-weight:bold;} .err{color:red;font-weight:bold;} pre{background:#f5f5f5;padding:15px;border-radius:8px;}</style>";
echo "<h2>🔧 Correction nom ville centre ID 353</h2>";

// 1. Correction du nom de ville
$update = $database->prepare("UPDATE am_centers SET city = 'Aix-en-Provence' WHERE id = 353 AND city = 'Aix en Provence'");
$update->execute();
$rows = $update->rowCount();

if ($rows > 0) {
    echo "<p class='ok'>✅ Ville corrigée : \"Aix en Provence\" → \"Aix-en-Provence\"</p>";
} else {
    echo "<p style='color:orange;'>ℹ️ Ville déjà correcte ou ID introuvable.</p>";
}

// 2. Vider tous les caches liés
$keysToDelete = ['centers_list', 'centers_last', 'Aix en Provence', 'Aix-en-Provence'];
foreach ($keysToDelete as $key) {
    try {
        $redis->deleteItem($key);
        echo "<p class='ok'>✅ Cache supprimé : '$key'</p>";
    } catch (Exception $e) {
        echo "<p style='color:#999;'>ℹ️ Cache '$key' : " . $e->getMessage() . "</p>";
    }
}

// 3. Vérification finale
$stmt = $database->prepare("SELECT id, city, online, aquavelo, address, phone FROM am_centers WHERE id = 353");
$stmt->execute();
$final = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h3>État final :</h3><pre>";
print_r($final);
echo "</pre>";

if ($final['city'] === 'Aix-en-Provence') {
    echo "<p class='ok'>✅ Correction réussie. Le centre est maintenant accessible.</p>";
    echo "<p>👉 <a href='/centres/Aix-en-Provence'>Voir la page du centre</a> | <a href='/centres'>Voir tous les centres</a></p>";
} else {
    echo "<p class='err'>❌ La correction a échoué. Ville actuelle : " . htmlspecialchars($final['city']) . "</p>";
}

echo "<hr><p style='color:#d32f2f; font-weight:bold;'>⚠️ PENSEZ À SUPPRIMER CE FICHIER après utilisation !</p>";
?>
