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
      width: 150px; /* Taille réduite de moitié */
      height: 100px;
      object-fit: cover;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin: 10px 0;
    }

    .article-image {
      width: 300px; /* Taille réduite de moitié */
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

      // Affichage du menu du jour
if (isset($menu_datam) && !empty($menu_datam)) {
    echo "<h1>Menu du jour " . htmlspecialchars($menu_datam["day_number"]) . " (Total " . htmlspecialchars($menu_datam["total_calories"]) . ")</h1>";

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
            echo "<img src='images/" . htmlspecialchars($menu_datam[$fields['photo']]) . "' alt='Photo $title' style='max-width: 150px; height: 100px; object-fit: cover; border-radius: 10px;'>";
        }
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p>Aucun menu trouvé pour aujourd'hui.</p>";
}

// Sélecteur de menus précédents
$menu_query = "SELECT day_number, petit_dejeuner_menu, repas_midi_menu, souper_menu, collation_menu, petit_dejeuner_recette, repas_midi_recette, souper_recette, collation_recette FROM menu ORDER BY day_number ASC";
$menu_result = $conn->query($menu_query);

if ($menu_result->num_rows > 0) {
    echo "<h2>Sélecteur de menus par jour</h2>";
    echo "<select id='menu_selector' onchange='afficherMenu()'>";
    echo "<option value=''>-- Sélectionner un jour --</option>";
    while ($menu = $menu_result->fetch_assoc()) {
        $value = htmlentities(json_encode($menu));
        echo "<option value='" . $value . "'>Jour " . htmlspecialchars($menu["day_number"]) . " - PD: " . htmlspecialchars($menu["petit_dejeuner_menu"]) . ", Déj: " . htmlspecialchars($menu["repas_midi_menu"]) . ", Dîner: " . htmlspecialchars($menu["souper_menu"]) . ", Collation: " . htmlspecialchars($menu["collation_menu"]) . "</option>";
    }
    echo "</select>";
    echo "<div id='recette_affichee' style='margin-top: 20px;'></div>";
}

$conn->close();
?>

<script>
function afficherMenu() {
    const selectedValue = document.getElementById('menu_selector').value;
    if (selectedValue) {
        const menu = JSON.parse(selectedValue);
        const recetteHTML = `
            <h3>Jour ${menu.day_number}</h3>
            <div style='display: flex; justify-content: space-around; gap: 20px; flex-wrap: wrap;'>
                <div style='flex: 1; text-align: center; min-width: 250px;'>
                    <h4>Petit Déjeuner</h4>
                    <p><strong>Menu :</strong> ${menu.petit_dejeuner_menu}</p>
                    <p><strong>Recette :</strong> ${menu.petit_dejeuner_recette}</p>
                </div>
                <div style='flex: 1; text-align: center; min-width: 250px;'>
                    <h4>Déjeuner</h4>
                    <p><strong>Menu :</strong> ${menu.repas_midi_menu}</p>
                    <p><strong>Recette :</strong> ${menu.repas_midi_recette}</p>
                </div>
                <div style='flex: 1; text-align: center; min-width: 250px;'>
                    <h4>Dîner</h4>
                    <p><strong>Menu :</strong> ${menu.souper_menu}</p>
                    <p><strong>Recette :</strong> ${menu.souper_recette}</p>
                </div>
                <div style='flex: 1; text-align: center; min-width: 250px;'>
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





    
      echo "<hr style='margin: 20px 0;'>"; // Barre horizontale entre les menus et les articles

      if (isset($news_datas) && !empty($news_datas)) {
          foreach ($news_datas as $article) {
              echo '<div class="article" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd;">';

              if (!empty($article["photo"])) {
                  echo '<img src="' . htmlspecialchars($article["photo"]) . '" alt="Image de l\'article" class="article-image">';
              }

              echo '<div class="article-content" style="flex: 1;">';
              echo '<h2 style="margin-top: 0; color: #555;">' . htmlspecialchars($article["titre"]) . '</h2>';

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






