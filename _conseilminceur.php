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


  date_default_timezone_set('Europe/Paris'); // Assurez-vous du bon fuseau horaire

  $day_number = date('j'); 
  echo "<p>Jour du mois actuel : $day_number</p>";

  $sql = "SELECT * FROM menu WHERE day_number = $day_number";

  echo "<p>Requête SQL : $sql</p>";

  $menu_datam = $conn->query($sql);

  if (!$menu_datam) {
    die("Erreur SQL : " . $conn->error);
  }

  if ($menu_datam->num_rows > 0) {
    echo "<p>Menus trouvés : " . $menu_datam->num_rows . "</p>";
  } else {
    echo "<p>Aucun menu trouvé pour le jour $day_number.</p>";
  }


  // Vérifier si des menus sont trouvés
  if ($menu_datam && $menu_datam->num_rows > 0) {
      echo "<p>Nombre de menus trouvés : " . $menu_datam->num_rows . "</p><br>";

      

      // Afficher les données de chaque ligne
      while ($row = $menu_datam->fetch_assoc()) {
          echo "<h1>Menu du jour " . $row["day_number"] . " (Total " . $row["total_calories"] . ")</h1>";
          echo "<div style='display: flex; justify-content: space-around; gap: 20px;'>";

          // Petit Déjeuner
          echo "<div style='flex: 1; text-align: center;'>";
          echo "<h2>Petit Déjeuner (≈300 kcal)</h2>";
          echo "<p><strong>Menu :</strong> " . $row["petit_dejeuner_menu"] . "</p>";
          echo "<p><strong>Recette :</strong> " . $row["petit_dejeuner_recette"] . "</p>";
          if (!empty($row["photo_pet_dej"])) {
              echo "<img src='images/" . $row["photo_pet_dej"] . "' alt='Photo petit déjeuner' style='max-width: 100%; height: auto;'>";
          }
          echo "</div>";

          // Déjeuner
          echo "<div style='flex: 1; text-align: center;'>";
          echo "<h2>Déjeuner</h2>";
          echo "<p><strong>Menu :</strong> " . $row["repas_midi_menu"] . "</p>";
          echo "<p><strong>Recette :</strong> " . $row["repas_midi_recette"] . "</p>";
          if (!empty($row["photo_repas_midi"])) {
              echo "<img src='images/" . $row["photo_repas_midi"] . "' alt='Photo déjeuner' style='max-width: 100%; height: auto;'>";
          }
          echo "</div>";

          // Dîner
          echo "<div style='flex: 1; text-align: center;'>";
          echo "<h2>Dîner</h2>";
          echo "<p><strong>Menu :</strong> " . $row["souper_menu"] . "</p>";
          echo "<p><strong>Recette :</strong> " . $row["souper_recette"] . "</p>";
          if (!empty($row["photo_souper"])) {
              echo "<img src='images/" . $row["photo_souper"] . "' alt='Photo dîner' style='max-width: 100%; height: auto;'>";
          }
          echo "</div>";

          echo "</div>"; // Fin de la disposition horizontale

          // Collation
          echo "<h2>Collation</h2>";
          echo "<p><strong>Menu :</strong> " . $row["collation_menu"] . "</p>";
          echo "<p><strong>Recette :</strong> " . $row["collation_recette"] . "</p>";
          if (!empty($row["photo_collation"])) {
              echo "<img src='images/" . $row["photo_collation"] . "' alt='Photo collation'>";
          }
      }
  } else {
      echo "<p>Aucun menu trouvé pour aujourd'hui.</p>";
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
          $formattedText = $article["news"];
          $formattedText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formattedText);
          $formattedText = nl2br($formattedText);

          echo '<p style="line-height: 1.6; margin: 0; color: #777;">' . $formattedText . '</p>';
          echo '</div>'; // Fermeture de .article-content
          echo '</div>'; // Fermeture de .article
      }
  } else {
      echo "Aucun article trouvé.";
  }
  ?>
</div>


