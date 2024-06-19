<?php
include 'settings.php';

try {
    // Jointure pour obtenir le nom de la ville et l'activité à partir des tables ville et activite
    $stmt = $conn->prepare("
        SELECT p.*, v.Ville, a.Activity 
        FROM partenariats p
        JOIN ville v ON p.Ville = v.id
        JOIN activite a ON p.Activite = a.id
        ORDER BY v.Ville, a.Activity
    ");
    $stmt->execute();
    $partenariats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des partenariats : " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des fiches de partenariats</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card img {
            width: 1242px;
            height: 1232px;
            border-radius: 10px;
        }
        .card h3, .card h4 {
            margin: 10px 0;
            text-align: center;
        }
        .card p {
            margin: 5px 0;
            text-align: center;
        }
        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <a class="back-button" href="menus.php">Retour au Menu</a>
    <div class="container">
        <?php
        foreach ($partenariats as $partenariat) {
            echo '<div class="card">';
            echo '<img src="Images/' . htmlspecialchars($partenariat['Photo']) . '" alt="Photo">';
            echo '<h3>' . htmlspecialchars_decode($partenariat['Nom']) . ' ' . htmlspecialchars_decode($partenariat['Prenom']) . '</h3>';
            echo '<h4>' . htmlspecialchars_decode($partenariat['Enseigne']) . '</h4>';
            echo '<p><strong>Ville:</strong> ' . htmlspecialchars_decode($partenariat['Ville']) . '</p>';
            echo '<p><strong>Activité:</strong> ' . htmlspecialchars_decode($partenariat['Activity']) . '</p>';
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($partenariat['email']) . '</p>';
            echo '<p><strong>Téléphone:</strong> ' . htmlspecialchars_decode($partenariat['Phone']) . '</p>';
            echo '<p><strong>Promotion:</strong> ' . htmlspecialchars_decode($partenariat['Promotion']) . '</p>';
            echo '<p><strong>Détail:</strong> ' . htmlspecialchars_decode($partenariat['Detail']) . '</p>';
            echo '<p><strong>Adresse du Centre:</strong> ' . htmlspecialchars_decode($partenariat['AdresseCentre']) . '</p>';
            echo '<p><strong>Ville du Centre:</strong> ' . htmlspecialchars_decode($partenariat['VilleCentre']) . '</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>







