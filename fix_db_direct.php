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
    
    // 2. Vérifier si la colonne after_session_sent existe
    echo "Vérification de la colonne after_session_sent...\n";
    $check2 = $database->query("SHOW COLUMNS FROM am_free LIKE 'after_session_sent'");
    if ($check2->rowCount() == 0) {
        echo "Ajout de la colonne after_session_sent...\n";
        $database->exec("ALTER TABLE am_free ADD COLUMN after_session_sent TINYINT(1) DEFAULT 0");
    } else {
        echo "La colonne after_session_sent existe déjà.\n";
    }

    // 3. S'assurer que reminder_sent a une valeur par défaut 0 et n'est pas null
    echo "Correction de la colonne reminder_sent...\n";
    $database->exec("ALTER TABLE am_free MODIFY COLUMN reminder_sent TINYINT(1) NOT NULL DEFAULT 0");
    
    // 3. Mettre à jour les lignes existantes qui sont à NULL
    echo "Mise à jour des données existantes (NULL -> 0)...\n";
    $database->exec("UPDATE am_free SET reminder_sent = 0 WHERE reminder_sent IS NULL");
    $database->exec("UPDATE am_free SET reminder_3h_sent = 0 WHERE reminder_3h_sent IS NULL");
    $database->exec("UPDATE am_free SET after_session_sent = 0 WHERE after_session_sent IS NULL");

    // 4. Vérifier la colonne followup_2d_sent (Suivi J+2)
    echo "Vérification de la colonne followup_2d_sent...\n";
    $check2d = $database->query("SHOW COLUMNS FROM am_free LIKE 'followup_2d_sent'");
    if ($check2d->rowCount() == 0) {
        echo "Ajout de la colonne followup_2d_sent...\n";
        $database->exec("ALTER TABLE am_free ADD COLUMN followup_2d_sent TINYINT(1) DEFAULT 0");
    }
    $database->exec("UPDATE am_free SET followup_2d_sent = 0 WHERE followup_2d_sent IS NULL");

    // 5. Vérifier la colonne followup_7d_sent (Suivi J+7)
    echo "Vérification de la colonne followup_7d_sent...\n";
    $check7d = $database->query("SHOW COLUMNS FROM am_free LIKE 'followup_7d_sent'");
    if ($check7d->rowCount() == 0) {
        echo "Ajout de la colonne followup_7d_sent...\n";
        $database->exec("ALTER TABLE am_free ADD COLUMN followup_7d_sent TINYINT(1) DEFAULT 0");
    }
    $database->exec("UPDATE am_free SET followup_7d_sent = 0 WHERE followup_7d_sent IS NULL");

    // 6. Vérifier la colonne google_sync (Agenda)
    echo "Vérification de la colonne google_sync...\n";
    $checkSync = $database->query("SHOW COLUMNS FROM am_free LIKE 'google_sync'");
    if ($checkSync->rowCount() == 0) {
        echo "Ajout de la colonne google_sync...\n";
        $database->exec("ALTER TABLE am_free ADD COLUMN google_sync TINYINT(1) DEFAULT 0");
    }
    $database->exec("UPDATE am_free SET google_sync = 0 WHERE google_sync IS NULL");

    // 7. Vérifier la colonne google_event_id (ID de l'événement Google)
    echo "Vérification de la colonne google_event_id...\n";
    $checkEvent = $database->query("SHOW COLUMNS FROM am_free LIKE 'google_event_id'");
    if ($checkEvent->rowCount() == 0) {
        echo "Ajout de la colonne google_event_id...\n";
        $database->exec("ALTER TABLE am_free ADD COLUMN google_event_id VARCHAR(255) DEFAULT NULL");
    }

    // 6. Vérifier si la colonne google_sync existe
    echo "Vérification de la colonne google_sync...\n";
    $check4 = $database->query("SHOW COLUMNS FROM am_free LIKE 'google_sync'");
    if ($check4->rowCount() == 0) {
        echo "Ajout de la colonne google_sync...\n";
        $database->exec("ALTER TABLE am_free ADD COLUMN google_sync TINYINT(1) DEFAULT 0");
    } else {
        echo "La colonne google_sync existe déjà.\n";
    }
    $database->exec("UPDATE am_free SET google_sync = 0 WHERE google_sync IS NULL");

    // 4. TEST D'ENVOI MANUEL (si demandé)
    if (isset($_GET['force_email'])) {
        echo "\n--- TEST D'ENVOI MANUEL ---\n";
        require 'cron_rappel_24h.php';
        echo "\nRésultat : " . ($count > 0 ? "$count email(s) envoyé(s)." : "Aucun email envoyé (hors fenêtre ou déjà envoyé).");
    }

    echo "\n\n✅ Opération terminée !";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
echo "</pre>";
