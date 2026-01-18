<?php
require '_settings.php';
try {
    $database->exec("ALTER TABLE am_free ADD COLUMN after_session_sent TINYINT(1) DEFAULT 0");
    echo "Colonne after_session_sent ajoutée avec succès !";
} catch (Exception $e) {
    echo "Erreur ou colonne déjà existante : " . $e->getMessage();
}
?>
