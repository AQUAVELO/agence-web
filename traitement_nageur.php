<?php
require '_settings.php';
session_start();

// Ajouter des logs
$log_file = $_SERVER['DOCUMENT_ROOT'] . '/logs/traitement_nageur_log.txt';
file_put_contents($log_file, "Début - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        file_put_contents($log_file, "Dans POST\n", FILE_APPEND);

        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $tel = $_POST['tel'] ?? null;
        $email = $_POST['email'] ?? null;
        $ville = $_POST['ville'] ?? null;
        $dept = $_POST['dept'] ?? '';
        $diplome = $_POST['diplome'] ?? null;
        $presentation = $_POST['presentation'] ?? null;
        $prix = $_POST['prix'] ?? null;
        $dispo = $_POST['dispo'] ?? null;
        $preference = $_POST['preference'] ?? null;

        file_put_contents($log_file, "Données récupérées : $nom, $prenom\n", FILE_APPEND);

        $stmt = $database->prepare('
            INSERT INTO nageur (nom, prenom, tel, ville, dept, diplome, presentation, prix, dispo, preference, email)
            VALUES (:nom, :prenom, :tel, :ville, :dept, :diplome, :presentation, :prix, :dispo, :preference, :email)
        ');
        file_put_contents($log_file, "Requête préparée\n", FILE_APPEND);

        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
        $stmt->bindParam(':ville', $ville, PDO::PARAM_STR);
        $stmt->bindParam(':dept', $dept, PDO::PARAM_STR);
        $stmt->bindParam(':diplome', $diplome, PDO::PARAM_STR);
        $stmt->bindParam(':presentation', $presentation, PDO::PARAM_STR);
        $stmt->bindParam(':prix', $prix, PDO::PARAM_STR);
        $stmt->bindParam(':dispo', $dispo, PDO::PARAM_STR);
        $stmt->bindParam(':preference', $preference, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->execute();
        file_put_contents($log_file, "Insertion exécutée\n", FILE_APPEND);

        $_SESSION['message'] = "Merci, $prenom $nom, votre inscription a été enregistrée avec succès !";
        $_SESSION['dept'] = $dept;
        header("Location: /merci_nageur.php");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
        file_put_contents($log_file, "$error\n", FILE_APPEND);
        $_SESSION['message'] = $error;
        header("Location: /inscription_nageur");
        exit;
    }
} else {
    file_put_contents($log_file, "Redirection hors POST\n", FILE_APPEND);
    header("Location: /inscription_nageur");
    exit;
}
?>
