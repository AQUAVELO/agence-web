<?php
// Désactiver le cache navigateur
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>

<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">AQUAVELO = AQUABIKING + AQUAGYM</h1>
    <h2 class="page-title pull-left">Excellent pour affiner la silhouette, la tonification et le bien-être.</h2>
  </div>
</header>

<div class="container" style="background-color: white; padding: 20px;">
  <style>
    .menu-image {
      width: 150px;
      height: 100px;
      object-fit: cover;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin: 10px 0;
    }

    .article-image {
      width: 300px;
      height: 200px;
      object-fit: cover;
      margin-right: 20px;
      border-radius: 8px;
    }
  </style>

  <?php
  try {
      date_default_timezone_set('Europe/Paris');
      $jour_du_mois = date('j');
      $date_cache_buster = date('YmdHis');

      if ($menu_datam) {
          // Format de la date JJ/MM/AA
          $date_du_jour = date('d/m/y'); 

          echo "<h3>Menu du jour (Jour " . htmlspecialchars($menu_datam['day_number']) . " - Date : " . $date_du_jour . ") - Total : " . htmlspecialchars($menu_datam['total_calories']) . " kcal</h3>";

          $sections = [
              "Petit Déjeuner" => ["menu" => "petit_dejeuner_menu", "recette" => "petit_dejeuner_recette", "photo" => "photo_pet_dej"],
              "Déjeuner" => ["menu" => "repas_midi_menu", "recette" => "repas_midi_recette", "photo" => "photo_repas_midi"],
              "Dîner" => ["menu" => "souper_menu", "recette" => "souper_recette", "photo" => "photo_souper"],
              "Collation" => ["menu" => "collation_menu", "recette" => "collation_recette", "photo" => "photo_collation"]
          ];

          echo "<div style='display: flex; justify-content: space-around; gap: 13px; flex-wrap: wrap;'>";
          foreach ($sections as $title => $fields) {
              echo "<div style='flex: 1; text-align: center; min-width: 250px;'>";
              echo "<h4>$title</h4>";
              echo "<p><strong>Menu :</strong> " . htmlspecialchars($menu_datam[$fields['menu']]) . "</p>";
              echo "<p><strong>Recette :</strong> " . htmlspecialchars($menu_datam[$fields['recette']]) . "</p>";

              if (!empty($menu_datam[$fields['photo']])) {
                  echo "<img src='images/" . htmlspecialchars($menu_datam[$fields['photo']]) . "?v=$date_cache_buster' alt='Photo $title' class='menu-image'>";
              }
              echo "</div>";
          }
          echo "</div>";
      } else {
          echo "<p>Aucun menu trouvé pour aujourd'hui (jour $jour_du_mois).</p>";
      }

      echo "<hr style='margin: 20px 0;'>"; // Séparation

      // Affichage des articles
      if (isset($news_datas) && !empty($news_datas)) {
          echo "<h2>Articles récents</h2>";
          foreach ($news_datas as $article) {
              echo '<div class="article" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd;">';

              if (!empty($article["photo"])) {
                  echo '<img src="' . htmlspecialchars($article["photo"]) . '?v=' . $date_cache_buster . '" alt="Image de l\'article" class="article-image">';
              }

              echo '<div class="article-content" style="flex: 1;">';
              echo '<h2 style="margin-top: 0; color: #555;">' . htmlspecialchars($article["titre"]) . '</h2>';

              $formattedText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $article["news"]);
              $formattedText = nl2br($formattedText);

              echo '<p style="line-height: 1.6; margin: 0; color: #777;">' . $formattedText . '</p>';
              echo '</div>';
              echo '</div>';
          }
      } else {
          echo "<p>Aucun article trouvé.</p>";
      }
  } catch (PDOException $e) {
      echo "Erreur de connexion : " . $e->getMessage();
  }
  ?>
</div>

<script>
  // Rafraîchissement automatique toutes les 24 heures
  setTimeout(function() {
    console.log("Rafraîchissement de la page pour mettre à jour le menu du jour.");
    window.location.reload(true);
  }, 86400000); // 24 heures (en millisecondes)
</script>








