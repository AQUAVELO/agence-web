<?php
require '_settings.php';

try {
    $database->exec("ALTER TABLE am_free ADD COLUMN reminder_sent TINYINT(1) DEFAULT 0");
    echo "Colonne reminder_sent ajoutée avec succès !";
} catch (Exception $e) {
    echo "La colonne existe peut-être déjà ou erreur : " . $e->getMessage();
}
?>
