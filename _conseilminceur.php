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

      if (isset($menu_datam) && !empty($menu_datam)) {
          echo "<h1>Menu du jour " . htmlspecialchars($menu_datam["day_number"]) . " (Total " . htmlspecialchars($menu_datam["total_calories"]) . ")</h1>";
      } else {
          echo "<p>Aucun menu trouvé pour aujourd'hui.</p>";
      }

      echo "<div style='display: flex; justify-content: space-around; gap: 20px; flex-wrap: wrap;'>";

      $sections = [
          "Petit Déjeuner" => ["menu" => "petit_dejeuner_menu", "recette" => "petit_dejeuner_recette", "photo" => "photo_pet_dej"],
          "Déjeuner" => ["menu" => "repas_midi_menu", "recette" => "repas_midi_recette", "photo" => "photo_repas_midi"],
          "Dîner" => ["menu" => "souper_menu", "recette" => "souper_recette", "photo" => "photo_souper"],
          "Collation" => ["menu" => "collation_menu", "recette" => "collation_recette", "photo" => "photo_collation"]
      ];

      foreach ($sections as $title => $fields) {
          echo "<div style='flex: 1; text-align: center; min-width: 250px;'>";
          echo "<h4>$title</h4>";
          echo "<p><strong>Menu :</strong> " . htmlspecialchars($menu_datam[$fields['menu']]) . "</p>";
          echo "<p><strong>Recette :</strong> " . htmlspecialchars($menu_datam[$fields['recette']]) . "</p>";

          if (!empty($menu_datam[$fields['photo']])) {
              echo "<img src='images/" . htmlspecialchars($menu_datam[$fields['photo']]) . "' alt='Photo $title' class='menu-image'>";
          }
          echo "</div>";
      }
      echo "</div>";

      // Barre horizontale entre les menus et les articles
      echo "<hr style='margin: 20px 0;'>";

      if (isset($news_datas) && !empty($news_datas)) {
          foreach ($news_datas as $article) {
              echo '<div class="article" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd;">';

              if (!empty($article["photo"])) {
                  echo '<img src="' . htmlspecialchars($article["photo"]) . '" alt="Image de l\'article" class="article-image">';
              }

              echo '<div class="article-content" style="flex: 1;">';
              echo '<h2>' . htmlspecialchars($article["titre"]) . '</h2>';
              $formattedText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $article["news"]);
              echo '<p>' . nl2br($formattedText) . '</p>';
              echo '</div>';
              echo '</div>';
          }
      } else {
          echo "Aucun article trouvé.";
      }
  } catch (PDOException $e) {
      echo "Erreur de connexion : " . $e->getMessage();
  }
  ?>
</div>

<script>
function afficherMenu() {
    const selectedValue = document.getElementById('menu_selector').value;
    if (selectedValue) {
        const menu = JSON.parse(selectedValue);
        const recetteHTML = `
            <h3>Jour ${menu.day_number}</h3>
            <div style='display: flex; justify-content: space-around; gap: 20px; flex-wrap: wrap;'>
                <div style='flex: 1; text-align: center;'>
                    <h4>Petit Déjeuner</h4>
                    <p><strong>Menu :</strong> ${menu.petit_dejeuner_menu}</p>
                    <p><strong>Recette :</strong> ${menu.petit_dejeuner_recette}</p>
                </div>
                <div style='flex: 1; text-align: center;'>
                    <h4>Déjeuner</h4>
                    <p><strong>Menu :</strong> ${menu.repas_midi_menu}</p>
                    <p><strong>Recette :</strong> ${menu.repas_midi_recette}</p>
                </div>
                <div style='flex: 1; text-align: center;'>
                    <h4>Dîner</h4>
                    <p><strong>Menu :</strong> ${menu.souper_menu}</p>
                    <p><strong>Recette :</strong> ${menu.souper_recette}</p>
                </div>
                <div style='flex: 1; text-align: center;'>
                    <h4>Collation</h4>
                    <p><strong>Menu :</strong> ${menu.collation_menu}</p>
                    <p><strong>Recette :</strong> ${menu.collation_recette}</p>
                </div>
            </div>
        `;
        document.getElementById('recette_affichee').innerHTML = recetteHTML;
    } else {
        document.getElementById('recette_affichee').innerHTML = '';
    }
}
</script>







