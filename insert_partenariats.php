<?php
include 'settings.php';

// Récupérer les données de la table ville
try {
    $stmtVille = $conn->prepare("SELECT id, Ville FROM ville");
    $stmtVille->execute();
    $villes = $stmtVille->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des villes : " . $e->getMessage();
    die();
}

// Récupérer les données de la table activite
try {
    $stmtActivite = $conn->prepare("SELECT id, Activity FROM activite");
    $stmtActivite->execute();
    $activites = $stmtActivite->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des activités : " . $e->getMessage();
    die();
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Vérification si l'email existe déjà
    $stmt = $conn->prepare("SELECT COUNT(*) FROM partenariats WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "L'email $email existe déjà dans la base de données.<br>";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nom = htmlspecialchars_decode(filter_var($_POST['nom'], FILTER_SANITIZE_STRING));
        $prenom = htmlspecialchars_decode(filter_var($_POST['prenom'], FILTER_SANITIZE_STRING));
        $phone = htmlspecialchars_decode(filter_var($_POST['phone'], FILTER_SANITIZE_STRING));
        $enseigne = htmlspecialchars_decode(filter_var($_POST['enseigne'], FILTER_SANITIZE_STRING));
        $ville_id = filter_var($_POST['ville'], FILTER_SANITIZE_NUMBER_INT);
        $activite = htmlspecialchars_decode(filter_var($_POST['activite'], FILTER_SANITIZE_STRING));
        $promotion = htmlspecialchars_decode(filter_var($_POST['promotion'], FILTER_SANITIZE_STRING));
        $detail = htmlspecialchars_decode(filter_var($_POST['detail'], FILTER_SANITIZE_STRING));
        $adresse_centre = htmlspecialchars_decode(filter_var($_POST['adresse_centre'], FILTER_SANITIZE_STRING));
        $ville_centre = htmlspecialchars_decode(filter_var($_POST['ville_centre'], FILTER_SANITIZE_STRING));

        $photo = "";
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $photo = $uploadDir . basename($_FILES['photo']['name']);
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo)) {
                echo "Photo téléchargée avec succès.<br>";
            } else {
                echo "Erreur lors du téléchargement de la photo.<br>";
            }
        } else {
            echo "Aucune photo téléchargée ou erreur de téléchargement.<br>";
        }

        // Prépare la requête SQL
        $sql = "INSERT INTO partenariats (email, password, Nom, Prenom, Phone, Enseigne, Ville, Activite, Promotion, Detail, Photo, AdresseCentre, VilleCentre) 
                VALUES (:email, :password, :nom, :prenom, :phone, :enseigne, :ville_id, :activite, :promotion, :detail, :photo, :adresse_centre, :ville_centre)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':enseigne', $enseigne);
        $stmt->bindParam(':ville_id', $ville_id);
        $stmt->bindParam(':activite', $activite);
        $stmt->bindParam(':promotion', $promotion);
        $stmt->bindParam(':detail', $detail);
        $stmt->bindParam(':photo', $photo);
        $stmt->bindParam(':adresse_centre', $adresse_centre);
        $stmt->bindParam(':ville_centre', $ville_centre);

        // Exécute la requête
        if ($stmt->execute()) {
            header("Location: menus.php"); // Redirige vers menu.php
            echo "Enregistrement réussi.<br>";
            sendNotificationEmail($email);  // Envoie un email de notification
          
            exit(); // Assure que le script s'arrête après la redirection
        } else {
            echo "Erreur lors de l'enregistrement.<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Partenariats</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select, textarea {
            width: 100%; /* Réduit la largeur des champs à 100% du conteneur */
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Formulaire de Partenariats</h2>
        
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom">
            </div>
            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom">
            </div>
            <div class="form-group">
                <label for="phone">Téléphone:</label>
                <input type="text" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="enseigne">Enseigne:</label>
                <input type="text" id="enseigne" name="enseigne">
            </div>
            <div class="form-group">
                <label for="ville">Ville:</label>
                <select id="ville" name="ville">
                <?php
                foreach ($villes as $ville) {
                    echo "<option value=\"{$ville['id']}\">{$ville['Ville']}</option>";
                }
                ?>
                </select>
            </div>
            <div class="form-group">
                <label for="activite">Activité:</label>
                <select id="activite" name="activite">
                <?php
                foreach ($activites as $activite) {
                    echo "<option value=\"{$activite['id']}\">{$activite['Activity']}</option>";
                }
                ?>
                </select>
            </div>
            <div class="form-group">
                <label for="promotion">Promotion:</label>
                <input type="text" id="promotion" name="promotion">
            </div>
            <div class="form-group">
                <label for="detail">Détail:</label>
                <textarea id="detail" name="detail"></textarea>
            </div>
            <div class="form-group">
                <label for="adresse_centre">Adresse du Centre:</label>
                <input type="text" id="adresse_centre" name="adresse_centre">
            </div>
            <div class="form-group">
                <label for="ville_centre">Ville du Centre:</label>
                <input type="text" id="ville_centre" name="ville_centre">
            </div>
            <div class="form-group">
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo">
            </div>
            <button type="submit">Soumettre</button>
            <button type="button" onclick="window.location.href='menus.php';">Retour au Menu</button>
        </form>
    </div>
</body>
</html>



