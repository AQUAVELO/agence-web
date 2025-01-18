<?php


// Requête pour récupérer tous les articles
$news_query = $database->prepare('SELECT news, photo, titre FROM article');
$news_query->execute();
$news_data = $news_query->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si des articles ont été trouvés
if ($news_data) {
    // Afficher les articles sur une page HTML
    echo "<!DOCTYPE html>
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des articles</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; text-align: center; }
        .article { 
            display: flex; 
            align-items: flex-start; 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
        }
        .article img { 
            width: 33.33%; 
            height: auto; 
            margin-right: 20px; 
        }
        .article-content { 
            flex: 1; 
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
    <h1>Liste des articles</h1>
    <div class="article">
        <img src="images/tech.jpg" alt="Image de l'article">
        <div class="article-content">
            <h2>Nouvelle technologie</h2>
            <p>Une nouvelle technologie révolutionnaire.</p>
        </div>
    </div>
    <div class="article">
        <img src="images/sci.jpg" alt="Image de l'article">
        <div class="article-content">
            <h2>Découverte scientifique</h2>
            <p>Une découverte majeure en biologie.</p>
        </div>
    </div>
</body>
</html>
} else {
    echo "<p>Aucun article trouvé.</p>";
}
?>
