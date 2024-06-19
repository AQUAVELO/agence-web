<?php
include 'settings.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo "ID de partenariat invalide.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Traitement du formulaire de modification
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
    $prenom = filter_var($_POST['prenom'], FILTER_SANITIZE_STRING);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $enseigne = filter_var($_POST['enseigne'], FILTER_SANITIZE_STRING);
    $ville = filter_var($_POST['ville'], FILTER_SANITIZE_STRING);
    $activite = filter_var($_POST['activite'], FILTER_SANITIZE_STRING);
    $promotion = filter_var($_POST['promotion'], FILTER_SANITIZE_STRING);
    $detail = filter_var($_POST['detail'], FILTER_SANITIZE_STRING);
    $adresse_centre = filter_var($_POST['adresse_centre'], FILTER_SANITIZE_STRING);
    $ville_centre = filter_var($_POST['ville_centre'], FILTER_SANITIZE_STRING);

    $photoPath = $partenariat['Photo']; // Path to the existing photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $photo = $_FILES['photo'];
        $uploadDir = 'images/'; // Directory to save the uploaded photos
        $photoPath = $uploadDir . basename($photo['name']);

        // Move uploaded file to the specified directory
        if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
            echo "Erreur lors du téléchargement de la photo.";
            exit;
        }
    }

    // Prépare la requête SQL de mise à jour
    $sql = "UPDATE partenariats SET email = :email, Nom = :nom, Prenom = :prenom, Phone = :phone, Enseigne = :enseigne, Ville = :ville, Activite = :activite, Promotion = :promotion, Detail = :detail, Photo = :photo, AdresseCentre = :adresse_centre, VilleCentre = :ville_centre WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':enseigne', $enseigne);
    $stmt->bindParam(':ville', $ville);
    $stmt->bindParam(':activite', $activite);
    $stmt->bindParam(':promotion', $promotion);
    $stmt->bindParam(':detail', $detail);
    $stmt->bindParam(':photo', $photoPath);
    $stmt->bindParam(':adresse_centre', $adresse_centre);
    $stmt->bindParam(':ville_centre', $ville_centre);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "<script>alert('Fiche modifiée avec succès.'); window.location.href='affichage.php';</script>";
    } else {
        echo "Erreur lors de la modification de la fiche.";
    }
} else {
    // Récupère les données existantes
    try {
        $stmt = $conn->prepare("SELECT * FROM partenariats WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $partenariat = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$partenariat) {
            echo "fiche non trouvée.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération de la fiche: " . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Partenariat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            width: 80%;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
        }
        input[type="text"],
        input[type="email"],
        input[type="file"],
        input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        input[type="submit"] {
            width: auto;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .back-button {
            padding: 10px 20px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Modifier Partenariat</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($partenariat['email']) ?>" required>

            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($partenariat['Nom']) ?>">

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($partenariat['Prenom']) ?>">

            <label for="phone">Téléphone:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($partenariat['Phone']) ?>">

            <label for="enseigne">Enseigne:</label>
            <input type="text" id="enseigne" name="enseigne" value="<?= htmlspecialchars($partenariat['Enseigne']) ?>">

            <label for="ville">Ville:</label>
            <input type="text" id="ville" name="ville" value="<?= htmlspecialchars($partenariat['Ville']) ?>">

            <label for="activite">Activité:</label>
            <input type="text" id="activite" name="activite" value="<?= htmlspecialchars($partenariat['Activite']) ?>">

            <label for="promotion">Promotion:</label>
            <input type="text" id="promotion" name="promotion" value="<?= htmlspecialchars($partenariat['Promotion']) ?>">

            <label for="detail">Détail:</label>
            <input type="text" id="detail" name="detail" value="<?= htmlspecialchars($partenariat['Detail']) ?>">

            <label for="adresse_centre">Adresse du Centre:</label>
            <input type="text" id="adresse_centre" name="adresse_centre" value="<?= htmlspecialchars($partenariat['AdresseCentre']) ?>">

            <label for="ville_centre">Ville du Centre:</label>
            <input type="text" id="ville_centre" name="ville_centre" value="<?= htmlspecialchars($partenariat['VilleCentre']) ?>">

            <label for="photo">Photo:</label>
            <input type="file" id="photo" name="photo">
            <img src="<?= htmlspecialchars($partenariat['Photo']) ?>" alt="Photo actuelle" width="100">

            <input type="submit" value="Enregistrer les modifications">
        </form>
        <button class="back-button" onclick="window.location.href='affichage.php';">Retour à la liste</button>
    </div>
</body>
</html>


