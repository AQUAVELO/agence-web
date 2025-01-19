<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">AQUAVELO = AQUABIKING + AQUAGYM</h1>
    <h2 class="page-title pull-left">Excellent pour affiner la silhouette, la tonification et le bien-être.</h2>
    <ol class="breadcrumb pull-right">
      <!-- Liens de navigation ici -->
    </ol>
  </div>
</header>

<div class="container" style="background-color: white; padding: 20px;">
  <?php
  // Vérifier si des articles ont été trouvés
  if (!empty($news_datas)) {
      // Afficher un message indiquant qu'il y a des résultats
      echo "<p>Nombre d'articles trouvés : " . count($news_datas) . "</p><br>";

      // Afficher les données de chaque ligne
      foreach ($news_datas as $article) {
          echo '<div class="article" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd;">';
          
          // Afficher l'image si elle existe
          if (!empty($article["photo"])) {
              echo '<img src="' . htmlspecialchars($article["photo"]) . '" alt="Image de l\'article" style="width: 33.33%; height: auto; margin-right: 20px;">';
          }
          
          // Contenu (titre et article) à droite de l'image
          echo '<div class="article-content" style="flex: 1;">';
          echo '<h2 style="margin-top: 0; color: #555;">' . htmlspecialchars($article["titre"]) . '</h2>';
          
           // Transformation du texte
          $formattedText = $article["contenu"]; // Récupérer le texte brut
          $formattedText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formattedText); // Mettre en gras
          $formattedText = nl2br($formattedText); // Convertir les sauts de ligne en <br>

          // Affichage avec le style souhaité
          echo '<p style="line-height: 1.6; margin: 0; color: #777;">' . $formattedText . '</p

          
          echo '</div>'; // Fermeture de .article-content
          echo '</div>'; // Fermeture de .article
      }
  } else {
      echo "Aucun article trouvé.";
  }
  ?>
</div>
