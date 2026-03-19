<?php
/**
 * Script de correction one-shot : activer le centre Aix ID 353
 * Accès sécurisé par clé secrète
 * À SUPPRIMER après utilisation
 */

require '_settings.php';

if (!isset($_GET['key']) || $_GET['key'] !== 'aquavelo-fix-353') {
    die("Accès non autorisé.");
}

echo "<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:20px;} .ok{color:green;font-weight:bold;} .err{color:red;font-weight:bold;} pre{background:#f5f5f5;padding:15px;border-radius:8px;}</style>";
echo "<h2>🔧 Correction centre Aix-en-Provence (ID 353)</h2>";

// 1. Lire l'état actuel
$stmt = $database->prepare("SELECT id, city, online, aquavelo, address, phone, email FROM am_centers WHERE id = 353");
$stmt->execute();
$center = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$center) {
    echo "<p class='err'>❌ Centre ID 353 introuvable en base de données.</p>";
    exit;
}

echo "<h3>État actuel en BDD :</h3><pre>";
print_r($center);
echo "</pre>";

// 2. Mettre à jour online=1 et aquavelo=1
$update = $database->prepare("UPDATE am_centers SET online = 1, aquavelo = 1 WHERE id = 353");
$update->execute();
$rows = $update->rowCount();

echo "<p class='ok'>✅ Mise à jour BDD : $rows ligne(s) modifiée(s) — online=1, aquavelo=1</p>";

// 3. Vider le cache des centres (centers_list + centers_last + cache de la ville)
$keysToDelete = ['centers_list', 'centers_last', 'Aix-en-Provence'];
foreach ($keysToDelete as $key) {
    try {
        $item = $redis->getItem($key);
        if ($item->isHit()) {
            $redis->deleteItem($key);
            echo "<p class='ok'>✅ Cache supprimé : '$key'</p>";
        } else {
            echo "<p style='color:#999;'>ℹ️ Cache '$key' déjà vide ou inexistant</p>";
        }
    } catch (Exception $e) {
        echo "<p class='err'>⚠️ Erreur cache '$key' : " . $e->getMessage() . "</p>";
    }
}

// 4. Vérification finale
$stmt2 = $database->prepare("SELECT id, city, online, aquavelo FROM am_centers WHERE id = 353");
$stmt2->execute();
$final = $stmt2->fetch(PDO::FETCH_ASSOC);

echo "<h3>État après correction :</h3><pre>";
print_r($final);
echo "</pre>";

if ($final['online'] == 1 && $final['aquavelo'] == 1) {
    echo "<p class='ok'>✅ Tout est correct. Le centre Aix-en-Provence (ID 353) est maintenant visible.</p>";
    echo "<p>👉 <a href='/centres/Aix-en-Provence'>Voir la page du centre</a> | <a href='/centres'>Voir tous les centres</a></p>";
} else {
    echo "<p class='err'>❌ La correction a échoué. Vérifiez les droits BDD.</p>";
}

echo "<hr><p style='color:#d32f2f; font-weight:bold;'>⚠️ PENSEZ À SUPPRIMER CE FICHIER après utilisation !</p>";
?>
