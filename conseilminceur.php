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
        .article {
            display: flex; /* Utilisation de Flexbox */
            align-items: flex-start; /* Aligner les éléments en haut */
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .article img {
            width: 33.33%; /* Largeur de l'image réduite à 1/3 */
            height: auto;
            margin-right: 20px; /* Espace entre l'image et le texte */
        }
        .article-content {
            flex: 1; /* Le contenu prend le reste de l'espace */
        }
        .article h2 {
            margin-top: 0; /* Supprimer la marge supérieure du titre */
        }
        .article p {
            line-height: 1.6;
            margin: 0; /* Supprimer la marge par défaut */
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
        require '_settings.php';
        

        // Requête SQL pour sélectionner les articles
        $sql = "SELECT id, titre, news, photo FROM article"; // Assurez-vous que les noms des champs sont corrects
        $result = $conn->query($sql); // Exécuter la requête

        // Vérifier s'il y a des résultats
        if ($result && $result->num_rows > 0) {
            // Afficher les données de chaque ligne
            while($row = $result->fetch_assoc()) {
                echo '<div class="article">';
                
                // Afficher l'image si elle existe
                if (!empty($row["photo"])) {
                    echo '<img src="' . htmlspecialchars($row["photo"]) . '" alt="Image de l\'article">';
                }
                
                // Contenu (titre et article) à droite de l'image
                echo '<div class="article-content">';
                echo '<h2>' . htmlspecialchars($row["titre"]) . '</h2>';
                echo '<p>' . nl2br(htmlspecialchars($row["news"])) . '</p>';
                echo '</div>';
                
                echo '</div>';
            }
        } else {
            echo "Aucun article trouvé.";
        }

        // Fermer la connexion
        $conn->close();
        ?>
    </div>
</body>
</html>
