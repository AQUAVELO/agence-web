<?php
require_once '_settings.php';

try {
    $check = $database->query("SHOW COLUMNS FROM am_free LIKE 'followup_48h_sent'");
    if ($check->rowCount() == 0) {
        $database->exec("ALTER TABLE am_free ADD COLUMN followup_48h_sent TINYINT(1) DEFAULT 0");
        echo "Colonne followup_48h_sent ajoutée avec succès !\n";
    } else {
        echo "La colonne followup_48h_sent existe déjà.\n";
    }
    $database->exec("UPDATE am_free SET followup_48h_sent = 0 WHERE followup_48h_sent IS NULL");
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
