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
        $photo = null; // Gestion du fichier ci-dessous
        $ville = $_POST['ville'] ?? null;
        $dept = $_POST['dept'] ?? '';
        $diplome = $_POST['diplome'] ?? null;
        $presentation = $_POST['presentation'] ?? null;
        $prix = $_POST['prix'] ?? null;
        $dispo = $_POST['dispo'] ?? null;
        $preference = $_POST['preference'] ?? null;

        // Gestion de l'upload de la photo
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $photo_name = uniqid() . '_' . basename($_FILES['photo']['name']);
            $photo_path = '/uploads/' . $photo_name; // Dossier uploads doit exister
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Crée le dossier si inexistant
            }
            move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_name);
            $photo = $photo_path;
        }

        // Préparer la requête d'insertion
        $stmt = $database->prepare('
            INSERT INTO nageur (nom, prenom, tel, photo, ville, dept, diplome, presentation, prix, dispo, preference, email)
            VALUES (:nom, :prenom, :tel, :photo, :ville, :dept, :diplome, :presentation, :prix, :dispo, :preference, :email)
        ');
        
        // Binder les paramètres
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
        $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
        $stmt->bindParam(':ville', $ville, PDO::PARAM_STR);
        $stmt->bindParam(':dept', $dept, PDO::PARAM_STR);
        $stmt->bindParam(':diplome', $diplome, PDO::PARAM_STR);
        $stmt->bindParam(':presentation', $presentation, PDO::PARAM_STR);
        $stmt->bindParam(':prix', $prix, PDO::PARAM_STR);
        $stmt->bindParam(':dispo', $dispo, PDO::PARAM_STR);
        $stmt->bindParam(':preference', $preference, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        // Exécuter la requête
        $stmt->execute();

        // Stocker un message de succès dans la session
        $_SESSION['message'] = "Merci, $prenom $nom, votre inscription a été enregistrée avec succès !";

        // Rediriger vers une page de confirmation
        header("Location: /merci_nageur.php"); // À créer si nécessaire
        exit;
    } catch (PDOException $e) {
        // En cas d'erreur, stocker un message d'erreur
        $_SESSION['message'] = "Une erreur est survenue : " . $e->getMessage();
        header("Location: /inscription_nageur");
        exit;
    }
} else {
    // Si accès direct sans POST, rediriger vers le formulaire
    header("Location: /inscription_nageur");
    exit;
}
?>
