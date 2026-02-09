<?php
/**
 * Script pour ajouter le champ Instagram à la table am_centers
 */

require '_settings.php';

echo "<h1>🔧 Ajout du champ Instagram à la table am_centers</h1>";

// 1. Vérifier si le champ existe déjà
try {
    $check = $database->query("SHOW COLUMNS FROM am_centers LIKE 'instagram'");
    $fieldExists = $check->fetch();
    
    if ($fieldExists) {
        echo "<p>✅ Le champ <b>instagram</b> existe déjà dans la table <b>am_centers</b></p>";
    } else {
        echo "<p>❌ Le champ <b>instagram</b> n'existe pas encore</p>";
        echo "<p>🔧 Ajout du champ en cours...</p>";
        
        // 2. Ajouter le champ
        $database->exec("ALTER TABLE am_centers ADD COLUMN instagram VARCHAR(255) NULL AFTER facebook");
        
        echo "<p>✅ <b>Champ instagram ajouté avec succès !</b></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
    die();
}

// 3. Afficher la structure de la table
echo "<hr><h2>📋 Structure de la table am_centers</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #00d4ff; color: white;'>";
echo "<th>Champ</th><th>Type</th><th>Null</th><th>Défaut</th>";
echo "</tr>";

$columns = $database->query("SHOW COLUMNS FROM am_centers");
while ($col = $columns->fetch()) {
    echo "<tr>";
    echo "<td><b>" . $col['Field'] . "</b></td>";
    echo "<td>" . $col['Type'] . "</td>";
    echo "<td>" . $col['Null'] . "</td>";
    echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

// 4. Afficher les centres avec leur Facebook et Instagram
echo "<hr><h2>📱 Centres avec Facebook/Instagram</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #00d4ff; color: white;'>";
echo "<th>ID</th><th>Ville</th><th>Facebook</th><th>Instagram</th><th>Actions</th>";
echo "</tr>";

$centers = $database->query("SELECT id, city, facebook, instagram FROM am_centers WHERE online = 1 AND aquavelo = 1 ORDER BY city");
while ($center = $centers->fetch()) {
    $fb_icon = !empty($center['facebook']) ? '✅' : '❌';
    $ig_icon = !empty($center['instagram']) ? '✅' : '❌';
    
    echo "<tr>";
    echo "<td>{$center['id']}</td>";
    echo "<td><b>{$center['city']}</b></td>";
    echo "<td>{$fb_icon} " . ($center['facebook'] ?: '<i>Non renseigné</i>') . "</td>";
    echo "<td>{$ig_icon} " . ($center['instagram'] ?: '<i>Non renseigné</i>') . "</td>";
    echo "<td><a href='https://aquavelo.com/centres/" . strtolower($center['city']) . "' target='_blank'>Voir la page</a></td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<h3>📝 Pour ajouter un compte Instagram à un centre :</h3>";
echo "<p>Utilisez cette requête SQL (remplacez les valeurs) :</p>";
echo "<pre style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "UPDATE am_centers SET instagram = 'nom_du_compte' WHERE id = ID_DU_CENTRE;\n\n";
echo "Exemple pour Cannes (ID 305) :\n";
echo "UPDATE am_centers SET instagram = 'aquavelocannes' WHERE id = 305;";
echo "</pre>";

echo "<p><b>Note :</b> N'incluez pas le @ ni l'URL complète, juste le nom du compte Instagram</p>";
?>
