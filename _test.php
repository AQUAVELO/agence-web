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
            echo '<img src="' . htmlspecialchars($article["photo"]) . '" alt="Image de l\'article" style="width: 33.33%; height: auto; margin-right: 20px;">';
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
