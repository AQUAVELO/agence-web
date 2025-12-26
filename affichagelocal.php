<?php
include 'settings.php';

// Vérifier la connexion à la base de données
try {
    $conn = new PDO(
        'mysql:host=' . $settings['dbhost'] . ';port=' . $settings['dbport'] . ';dbname=' . $settings['dbname'],
        $settings['dbusername'],
        $settings['dbpassword']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupérer les données de la table partenariats avec jointures pour obtenir les noms des villes et des activités
try {
    $stmt = $conn->prepare("
        SELECT p.*, v.Ville as VilleNom, a.Activity as ActiviteNom
        FROM partenariats p
        JOIN ville v ON p.Ville = v.id
        JOIN activite a ON p.Activite = a.id
        ORDER BY a.Activity
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
            width: 100%;
            height: auto;
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
    <a class="back-button">Profitez des remises chez nos partenaires</a>
    <div class="container">
        <?php
        foreach ($partenariats as $partenariat) {
            echo '<div class="card">';
            echo '<img src="' . htmlspecialchars($partenariat['Photo']) . '" alt="Photo">';
            echo '<h3>' . htmlspecialchars_decode($partenariat['Enseigne']) . '</h3>';
            echo '<p><strong>Adresse:</strong> ' . htmlspecialchars_decode($partenariat['AdresseCentre']) . '</p>';
            echo '<p><strong>Ville:</strong> ' . htmlspecialchars_decode($partenariat['VilleCentre']) . '</p>';
            echo '<p><strong>Activité:</strong> ' . htmlspecialchars_decode($partenariat['ActiviteNom']) . '</p>';
            echo '<p><strong>Email:</strong> <a href="mailto:' . htmlspecialchars($partenariat['email']) . '">' . htmlspecialchars($partenariat['email']) . '</a></p>';
            echo '<p><strong>Téléphone:</strong> <a href="tel:' . htmlspecialchars($partenariat['Phone']) . '">' . htmlspecialchars($partenariat['Phone']) . '</a></p>';
            echo '<p><strong>Promotion:</strong> ' . htmlspecialchars_decode($partenariat['Promotion']) . '</p>';
            echo '<p><strong>Détail:</strong> ' . htmlspecialchars_decode($partenariat['Detail']) . '</p>';
            echo '<p><strong>Contact:</strong> ' . htmlspecialchars_decode($partenariat['Nom']) . ' ' . htmlspecialchars_decode($partenariat['Prenom']) . '</p>';
            echo '<p><strong>Centre Aquavelo de:</strong> ' . htmlspecialchars_decode($partenariat['VilleNom']) . '</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>






