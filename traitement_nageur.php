<?php
require '_settings.php';
session_start();

error_log("Début traitement_nageur.php - " . date('Y-m-d H:i:s'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("Dans POST - nageur");

        // Récupérer les données comme dans l'exemple
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $tel = $_POST['tel'] ?? '';
        $email = $_POST['email'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $dept = $_POST['dept'] ?? '';
        $diplome = $_POST['diplome'] ?? '';
        $presentation = $_POST['presentation'] ?? '';
        $prix = $_POST['prix'] ?? '';
        $dispo = $_POST['dispo'] ?? '';
        $preference = $_POST['preference'] ?? '';

        error_log("Données : nom=$nom, prenom=$prenom, email=$email");

        // Préparer et exécuter la requête
        $stmt = $database->prepare('
            INSERT INTO nageur (nom, prenom, tel, ville, dept, diplome, presentation, prix, dispo, preference, email)
            VALUES (:nom, :prenom, :tel, :ville, :dept, :diplome, :presentation, :prix, :dispo, :preference, :email)
        ');
        error_log("Requête préparée - nageur");

        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':tel' => $tel,
            ':ville' => $ville,
            ':dept' => $dept,
            ':diplome' => $diplome,
            ':presentation' => $presentation,
            ':prix' => $prix,
            ':dispo' => $dispo,
            ':preference' => $preference,
            ':email' => $email
        ]);
        error_log("Insertion réussie - nageur");

        $_SESSION['message'] = "Merci, $prenom $nom, votre inscription a été enregistrée avec succès !";
        $_SESSION['dept'] = $dept;
        header("Location: /merci_nageur.php");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur nageur : " . $e->getMessage();
        error_log($error);
        $_SESSION['message'] = $error;
        header("Location: /inscription_nageur");
        exit;
    }
} else {
    error_log("Redirection hors POST - nageur");
    header("Location: /inscription_nageur");
    exit;
}
?>
