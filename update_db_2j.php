<?php
require '_settings.php';
try {
    $database->exec("ALTER TABLE am_free ADD COLUMN followup_2d_sent TINYINT(1) DEFAULT 0");
    echo "Colonne followup_2d_sent ajoutée avec succès !";
} catch (Exception $e) {
    echo "Erreur ou colonne déjà existante : " . $e->getMessage();
}
?>
