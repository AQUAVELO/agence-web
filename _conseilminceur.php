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
  try {
      date_default_timezone_set('Europe/Paris'); // Assurez-vous du bon fuseau horaire

     

      $jour_du_mois = date('j');


      // Vérifier si des menus sont trouvés
      if ($menu_datam) {
          echo "<h1>Menu du jour " . htmlspecialchars($menu_datam['day_number']) . " (Total " . htmlspecialchars($menu_datam['total_calories']) . ")</h1>";

          // Affichage des sections (Petit Déjeuner, Déjeuner, Dîner, Collation)
          $sections = [
              "Petit Déjeuner" => ["menu" => "petit_dejeuner_menu", "recette" => "petit_dejeuner_recette", "photo" => "photo_pet_dej"],
              "Déjeuner" => ["menu" => "repas_midi_menu", "recette" => "repas_midi_recette", "photo" => "photo_repas_midi"],
              "Dîner" => ["menu" => "souper_menu", "recette" => "souper_recette", "photo" => "photo_souper"],
              "Collation" => ["menu" => "collation_menu", "recette" => "collation_recette", "photo" => "photo_collation"]
          ];

          echo "<div style='display: flex; justify-content: space-around; gap: 20px;'>";
          foreach ($sections as $title => $fields) {
              echo "<div style='flex: 1; text-align: center;'>";
              echo "<h2>$title</h2>";
              echo "<p><strong>Menu :</strong> " . htmlspecialchars($menu_datam[$fields['menu']]) . "</p>";
              echo "<p><strong>Recette :</strong> " . htmlspecialchars($menu_datam[$fields['recette']]) . "</p>";

              if (!empty($menu_datam[$fields['photo']])) {
                  echo "<img src='images/" . htmlspecialchars($menu_datam[$fields['photo']]) . "' alt='Photo $title' style='max-width: 100%; height: auto;'>";
              }
              echo "</div>";
          }
          echo "</div>";
      } else {
          echo "<p>Aucun menu trouvé pour aujourd'hui (jour $jour_du_mois).</p>";
      }

      // Vérifier si des articles ont été trouvés
      if (isset($news_datas) && !empty($news_datas)) {
          echo "<p>Nombre d'articles trouvés : " . count($news_datas) . "</p><br>";

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
              $formattedText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $article["news"]);
              $formattedText = nl2br($formattedText);

              echo '<p style="line-height: 1.6; margin: 0; color: #777;">' . $formattedText . '</p>';
              echo '</div>'; // Fermeture de .article-content
              echo '</div>'; // Fermeture de .article
          }
      } else {
          echo "Aucun article trouvé.";
      }
  } catch (PDOException $e) {
      echo "Erreur de connexion : " . $e->getMessage();
  }
  ?>
</div>



