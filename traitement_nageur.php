<?php
require '_settings.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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

        $stmt = $database->prepare('
            INSERT INTO nageur (nom, prenom, tel, ville, dept, diplome, presentation, prix, dispo, preference, email)
            VALUES (:nom, :prenom, :tel, :ville, :dept, :diplome, :presentation, :prix, :dispo, :preference, :email)
        ');
        
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

        $_SESSION['message'] = "Merci, $prenom $nom, votre inscription a été enregistrée avec succès !";
        header("Location: /merci_nageur.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = "Une erreur est survenue : " . $e->getMessage();
        header("Location: /inscription_nageur");
        exit;
    }
} else {
    header("Location: /inscription_nageur");
    exit;
}
?>
