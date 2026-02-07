<?php
/**
 * Script pour vérifier et activer le centre Paris 12 (ID 352)
 */

require '_settings.php';

echo "<h2>Vérification Centre Paris 12 (ID 352)</h2>";

// 1. Vérifier si le centre existe
$stmt = $database->prepare('SELECT * FROM am_centers WHERE id = 352');
$stmt->execute();
$center = $stmt->fetch(PDO::FETCH_ASSOC);

if ($center) {
    echo "<h3>✅ Centre trouvé :</h3>";
    echo "<pre>";
    print_r($center);
    echo "</pre>";
    
    // 2. Vérifier les paramètres online et aquavelo
    if ($center['online'] == 1 && $center['aquavelo'] == 1) {
        echo "<p style='color: green;'><strong>✅ Le centre est déjà activé (online=1, aquavelo=1)</strong></p>";
        echo "<p>Il devrait apparaître dans la liste. <strong>Vider le cache Redis peut être nécessaire.</strong></p>";
    } else {
        echo "<p style='color: orange;'><strong>⚠️ Le centre existe mais n'est pas activé</strong></p>";
        echo "<p>online = " . $center['online'] . " (devrait être 1)</p>";
        echo "<p>aquavelo = " . $center['aquavelo'] . " (devrait être 1)</p>";
        
        // 3. Proposer de l'activer
        echo "<hr>";
        echo "<h3>Voulez-vous activer le centre ?</h3>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='activate' style='padding: 15px 30px; background: #00a8cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>🚀 ACTIVER LE CENTRE PARIS 12</button>";
        echo "</form>";
    }
} else {
    echo "<p style='color: red;'><strong>❌ Centre ID 352 (Paris 12) non trouvé dans la base de données</strong></p>";
    echo "<p>Le centre doit d'abord être créé dans la table am_centers.</p>";
}

// 4. Traitement activation
if (isset($_POST['activate'])) {
    $update = $database->prepare('UPDATE am_centers SET online = 1, aquavelo = 1 WHERE id = 352');
    if ($update->execute()) {
        echo "<hr>";
        echo "<h3 style='color: green;'>✅ CENTRE ACTIVÉ !</h3>";
        echo "<p>Le centre Paris 12 est maintenant online=1 et aquavelo=1</p>";
        echo "<p><strong>Important :</strong> Videz le cache Redis pour voir les changements immédiatement.</p>";
        
        // Essayer de vider le cache
        try {
            $redis->clear();
            echo "<p style='color: green;'>✅ Cache Redis vidé avec succès !</p>";
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Impossible de vider automatiquement le cache : " . $e->getMessage() . "</p>";
        }
        
        echo "<p><a href='/' style='padding: 10px 20px; background: #00a8cc; color: white; text-decoration: none; border-radius: 5px;'>→ Voir la liste des centres</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Erreur lors de l'activation</p>";
    }
}

// 5. Liste de tous les centres
echo "<hr>";
echo "<h3>📋 Liste de tous les centres actifs (online=1, aquavelo=1) :</h3>";
$all = $database->prepare('SELECT id, city, online, aquavelo FROM am_centers WHERE online = 1 AND aquavelo = 1 ORDER BY city ASC');
$all->execute();
$centers = $all->fetchAll(PDO::FETCH_ASSOC);

echo "<ul>";
foreach ($centers as $c) {
    echo "<li><strong>" . $c['city'] . "</strong> (ID: " . $c['id'] . ")</li>";
}
echo "</ul>";
?>
