<?php
// Connexion à la base de données
try {
    $database = new PDO('mysql:host=localhost;dbname=nom_de_votre_base', 'utilisateur', 'mot_de_passe');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

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
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #333; text-align: center; }
            .article { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
            .article img { max-width: 100%; height: auto; display: block; margin-bottom: 10px; }
            .article h2 { margin: 0 0 10px; color: #555; }
            .article p { margin: 0; color: #777; }
        </style>
    </head>
    <body>
        <h1>Liste des articles</h1>";

    // Parcourir chaque article et l'afficher
    foreach ($news_data as $article) {
        echo "<div class='article'>
                <img src='" . htmlspecialchars($article['photo']) . "' alt='Image de l'article'>
                <h2>" . htmlspecialchars($article['titre']) . "</h2>
                <p>" . nl2br(htmlspecialchars($article['news'])) . "</p>
              </div>";
    }

    echo "</body>
    </html>";
} else {
    echo "<p>Aucun article trouvé.</p>";
}
?>
