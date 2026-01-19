<?php
// On utilise require_once pour éviter les conflits si appelé via index.php
require_once '_settings.php';

echo "<pre>";
try {
    // 1. Vérifier si la colonne reminder_3h_sent existe
    echo "Vérification de la colonne reminder_3h_sent...\n";
    $check = $database->query("SHOW COLUMNS FROM am_free LIKE 'reminder_3h_sent'");
    if ($check->rowCount() == 0) {
        echo "Ajout de la colonne reminder_3h_sent...\n";
        $database->exec("ALTER TABLE am_free ADD COLUMN reminder_3h_sent TINYINT(1) DEFAULT 0");
    } else {
        echo "La colonne reminder_3h_sent existe déjà.\n";
    }
    
    // 2. S'assurer que reminder_sent a une valeur par défaut 0 et n'est pas null
    echo "Correction de la colonne reminder_sent...\n";
    $database->exec("ALTER TABLE am_free MODIFY COLUMN reminder_sent TINYINT(1) NOT NULL DEFAULT 0");
    
    // 3. Mettre à jour les lignes existantes qui sont à NULL
    echo "Mise à jour des données existantes (NULL -> 0)...\n";
    $database->exec("UPDATE am_free SET reminder_sent = 0 WHERE reminder_sent IS NULL");
    $database->exec("UPDATE am_free SET reminder_3h_sent = 0 WHERE reminder_3h_sent IS NULL");

    echo "\n✅ Base de données mise à jour avec succès !";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
echo "</pre>";
