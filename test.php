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
            color: #555;
        }
        .article p {
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
        // Vérifier si des articles ont été trouvés
        if (!empty($news_datas)) {
            // Afficher un message indiquant qu'il y a des résultats
            echo "<p>Nombre d'articles trouvés : " . count($news_datas) . "</p><br>";

            // Afficher les données de chaque ligne
            foreach ($news_datas as $article) {
                echo '<div class="article">';
                
                // Afficher l'image si elle existe
                if (!empty($article["photo"])) {
                    echo '<img src="' . htmlspecialchars($article["photo"]) . '" alt="Image de l\'article">';
                }
                
                // Contenu (titre et article) à droite de l'image
                echo '<div class="article-content">';
                echo '<h2>' . htmlspecialchars($article["titre"]) . '</h2>';
                echo '<p>' . nl2br(htmlspecialchars($article["news"])) . '</p>';
                echo '</div>';
                
                echo '</div>';
            }
        } else {
            echo "Aucun article trouvé.";
        }
        ?>
    </div>
</body>
</html>
