<?php
/**
 * Déclencheur de Cron sécurisé par URL
 * Usage: https://www.aquavelo.com/cron_trigger.php?key=aquavelo123
 */

// Clé de sécurité (vous pouvez la changer)
$secret_key = "aquavelo123";

if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    die("Accès non autorisé.");
}

echo "<pre>";
echo "Démarrage des tâches planifiées...\n\n";

echo "1. Rappels 24h :\n";
include 'cron_rappel_24h.php';
echo "\n\n";

echo "2. Rappels 3h :\n";
include 'cron_rappel_3h.php';
echo "\n\n";

echo "3. Suivi après séance :\n";
include 'cron_apres_seance.php';
echo "\n\n";

echo "4. Suivi J+2 :\n";
include 'cron_suivi_2j.php';

echo "\n\n✅ Toutes les tâches ont été exécutées.";
echo "</pre>";
