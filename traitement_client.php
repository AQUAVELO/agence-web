<?php
require '_settings.php'; // Inclut la connexion à la base de données ($database)
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupérer les données du formulaire
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $tel = $_POST['tel'] ?? null;
        $email = $_POST['email'] ?? null;
        $adresse = $_POST['adresse'] ?? null;
        $ville = $_POST['ville'] ?? null;
        $dept = $_POST['dept'] ?? '';
        $activites = $_POST['activites'] ?? null;
        $besoin = $_POST['besoin'] ?? null;

        // Préparer la requête d'insertion
        $stmt = $database->prepare('
            INSERT INTO client (nom, prenom, tel, email, adresse, ville, dept, activites, besoin)
            VALUES (:nom, :prenom, :tel, :email, :adresse, :ville, :dept, :activites, :besoin)
        ');
        
        // Binder les paramètres
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':adresse', $adresse, PDO::PARAM_STR);
        $stmt->bindParam(':ville', $ville, PDO::PARAM_STR);
        $stmt->bindParam(':dept', $dept, PDO::PARAM_STR);
        $stmt->bindParam(':activites', $activites, PDO::PARAM_STR);
        $stmt->bindParam(':besoin', $besoin, PDO::PARAM_STR);

        // Exécuter la requête
        $stmt->execute();

        // Stocker un message de succès dans la session
        $_SESSION['message'] = "Merci, $prenom $nom, votre inscription a été enregistrée avec succès !";

        // Rediriger vers merci_client.php
        header("Location: /merci_client.php");
        exit;
    } catch (PDOException $e) {
        // En cas d'erreur, stocker un message d'erreur
        $_SESSION['message'] = "Une erreur est survenue : " . $e->getMessage();
        header("Location: /inscription_client");
        exit;
    }
} else {
    // Si accès direct sans POST, rediriger vers le formulaire
    header("Location: /inscription_client");
    exit;
}
?>
