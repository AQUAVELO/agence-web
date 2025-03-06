<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ajouter des logs pour diagnostiquer
file_put_contents('/tmp/nageur_log.txt', "Début de traitement_nageur.php - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

require '_settings.php'; // Inclut la connexion à la base de données ($database)
file_put_contents('/tmp/nageur_log.txt', "Après require _settings.php\n", FILE_APPEND);

session_start();
file_put_contents('/tmp/nageur_log.txt', "Après session_start\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        file_put_contents('/tmp/nageur_log.txt', "Dans le bloc POST\n", FILE_APPEND);

        $nom = $_POST['nom'] ?? 'Inconnu';
        $prenom = $_POST['prenom'] ?? 'Inconnu';

        // Test minimal avec seulement nom et prenom
        $stmt = $database->prepare('INSERT INTO nageur (nom, prenom) VALUES (:nom, :prenom)');
        file_put_contents('/tmp/nageur_log.txt', "Requête préparée\n", FILE_APPEND);

        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);

        $stmt->execute();
        file_put_contents('/tmp/nageur_log.txt', "Requête exécutée\n", FILE_APPEND);

        $_SESSION['message'] = "Inscription réussie pour $prenom $nom !";
        header("Location: /merci_nageur.php");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
        file_put_contents('/tmp/nageur_log.txt', "$error\n", FILE_APPEND);
        $_SESSION['message'] = $error;
        header("Location: /inscription_nageur");
        exit;
    }
} else {
    file_put_contents('/tmp/nageur_log.txt', "Redirection vers inscription_nageur\n", FILE_APPEND);
    header("Location: /inscription_nageur");
    exit;
}
?>
