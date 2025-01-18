<?php
// Requête pour récupérer tous les articles
$news_query = $database->prepare('SELECT news, photo, titre FROM article');
$news_query->execute();
$news_data = $news_query->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si des articles ont été trouvés
if ($news_data) {
    // Afficher les articles sur une page HTML
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Liste des articles</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
            }
            h1 { 
                color: #333; 
                text-align: center; 
            }
            .article { 
                display: flex; /* Utilisation de Flexbox */
                align-items: flex-start; /* Aligner les éléments en haut */
                border: 1px solid #ddd; 
                padding: 15px; 
                margin-bottom: 20px; 
                border-radius: 5px; 
            }
            .article img { 
                width: 33.33%; /* Largeur de l'image à 1/3 */
                height: auto; 
                margin-right: 20px; /* Espace entre l'image et le texte */
            }
            .article-content { 
                flex: 1; /* Le contenu prend le reste de l'espace */
            }
            .article h2 { 
                margin: 0 0 10px; 
                color: #555; 
            }
            .article p { 
                margin: 0; 
                color: #777; 
            }
        </style>
    </head>
    <body>
        <h1>Liste des articles</h1>";

    // Parcourir chaque article et l'afficher
    foreach ($news_data as $article) {
        echo "<div class='article'>
                <img src='" . htmlspecialchars($article['photo']) . "' alt='Image de l\'article'>
                <div class='article-content'>
                    <h2>" . htmlspecialchars($article['titre']) . "</h2>
                    <p>" . nl2br(htmlspecialchars($article['news'])) . "</p>
                </div>
              </div>";
    }

    echo "</body>
    </html>";
} else {
    echo "<p>Aucun article trouvé.</p>";
}
?>
