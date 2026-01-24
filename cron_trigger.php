<?php
/**
 * Déclencheur de Cron sécurisé par URL
 * Version robuste pour éviter les Erreurs 500
 */
require '_settings.php';
date_default_timezone_set('Europe/Paris');

$secret_key = "aquavelo123";

if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    die("Accès non autorisé.");
}

// Désactiver l'affichage des erreurs pour éviter de casser le flux de sortie
ini_set('display_errors', 0);
error_reporting(E_ALL);

echo "Démarrage des tâches planifiées...\n\n";

$tasks = [
    'Rappels 24h' => 'cron_rappel_24h.php',
    'Rappels 3h' => 'cron_rappel_3h.php',
    'Suivi après séance' => 'cron_apres_seance.php',
    'Suivi J+2' => 'cron_suivi_2j.php',
    'Suivi J+7' => 'cron_suivi_7j.php',
    'Sync Google Calendar' => 'cron_sync_google.php'
];

foreach ($tasks as $name => $file) {
    echo "Exécution : $name... ";
    try {
        if (file_exists($file)) {
            include $file;
            echo " ✅ TERMINÉ\n";
        } else {
            echo " ❌ FICHIER MANQUANT\n";
        }
    } catch (Exception $e) {
        echo " ❌ ERREUR : " . $e->getMessage() . "\n";
    }
    echo "--------------------------\n";
}

echo "\n✅ Toutes les tâches ont été traitées.";
?>
