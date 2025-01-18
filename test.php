<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Blog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .activity {
            display: flex; /* Utilisation de Flexbox */
            align-items: flex-start; /* Aligner les éléments en haut */
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .activity-content {
            flex: 1; /* Le contenu prend le reste de l'espace */
        }
        .activity h2 {
            margin-top: 0; /* Supprimer la marge supérieure du titre */
            color: #555;
        }
        .activity p {
            line-height: 1.6;
            margin: 0; /* Supprimer la marge par défaut */
            color: #777;
        }
    </style>
</head>
<body>
    <header>
        <h1>Mon blog Minceur</h1>
    </header>

    <div class="container">
        <?php
        // Inclure les paramètres de connexion à la base de données
        //require '_settings.php';

        // Tester si la connexion à la base de données est réussie
        if ($conn->connect_error) {
            die("Erreur de connexion à la base de données : " . $conn->connect_error);
        } else {
            echo "Connexion à la base de données réussie !<br><br>";
        }

        // Requête SQL pour sélectionner les activités
        $sql = "SELECT Date, Activity FROM activite"; // Assurez-vous que les noms des champs sont corrects
        $result = $conn->query($sql); // Exécuter la requête

        // Vérifier si la requête a réussi
        if ($result === false) {
            // Afficher un message d'erreur détaillé
            die("Erreur lors de l'exécution de la requête : " . $conn->error);
        }

        // Vérifier s'il y a des résultats
        if ($result->num_rows > 0) {
            // Afficher un message indiquant qu'il y a des résultats
            echo "<p>Nombre d'activités trouvées : " . $result->num_rows . "</p><br>";

            // Afficher les données de chaque ligne
            while($row = $result->fetch_assoc()) {
                echo '<div class="activity">';
                
                // Contenu (date et activité)
                echo '<div class="activity-content">';
                echo '<h2>' . htmlspecialchars($row["Date"]) . '</h2>';
                echo '<p>' . nl2br(htmlspecialchars($row["Activity"])) . '</p>';
                echo '</div>';
                
                echo '</div>';
            }
        } else {
            echo "Aucune activité trouvée.";
        }

        // Fermer la connexion
        $conn->close();
        ?>
    </div>
</body>
</html>
