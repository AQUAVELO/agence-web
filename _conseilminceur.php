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
    .btn-default {
      display: inline-block;
      padding: 10px 20px;
      background-color: #47c3e6;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s;
    }
    .btn-default:hover {
      background-color: #0056b3;
    }
  </style>

  <?php
  try {
      date_default_timezone_set('Europe/Paris');
      $jour_du_mois = date('j');
      $date_cache_buster = date('YmdHis');
  ?>

  <!-- ✅ Conseils pour perdre du poids avec calculateur AVANT le menu du jour -->
  <h3>Voici nos conseils pour perdre du poids :</h3>
  <p>1) Calculez votre besoin calorique avec ce calculateur</p>
  <dd>
    <a href="#" class="btn btn-default" onclick="ouvre_popup('/resultatMinceur.php'); return false;" 
       title="Calculateur de calories et conseils pour perdre du poids" 
       aria-label="Calculateur calories & conseils minceur">
      Cliquez ici
    </a>
  </dd>
  <p style="margin-top: 10px;">2) Respectez les conseils préconisés pour perdre du poids rapidement en respectant l'alimentation à moins de 1500 calories ci dessous :</p>
  <hr style='margin: 20px 0;'>

  <?php
      if (isset($menu_datam)) {
          $date_du_jour = date('d/m/y'); 
          echo "<h4>Menu du jour - Date : " . $date_du_jour . " - Total : " . htmlspecialchars($menu_datam['total_calories']) . " kcal</h4>";

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
              echo "<p><strong>Menu :</strong> " . htmlspecialchars($menu_datam[$fields['menu']] ?? 'Non disponible') . "</p>";
              echo "<p><strong>Recette :</strong> " . htmlspecialchars($menu_datam[$fields['recette']] ?? 'Non disponible') . "</p>";

              if (!empty($menu_datam[$fields['photo']])) {
                  echo "<img src='images/" . htmlspecialchars($menu_datam[$fields['photo']]) . "?v=$date_cache_buster' alt='Photo $title' class='menu-image'>";
              }
              echo "</div>";
          }
          echo "</div>";
      } else {
          echo "<p>Aucun menu trouvé pour aujourd'hui (jour $jour_du_mois).</p>";
      }
  ?>

  <!-- ✅ Sélecteur de menus -->
  <h4>Sélectionner d'autres menus à moins de 1500 calories</h4>
  <select id="menu_selector" style="padding: 5px; min-width: 400px; width: auto;">
    <option value="">-- Sélectionner un jour --</option>
    <?php foreach ($all_menus as $menu): ?>
        <?php
            $pd = explode(',', $menu['petit_dejeuner_menu'])[0];
            $dejeuner = explode(',', $menu['repas_midi_menu'])[0];
            $diner = explode(',', $menu['souper_menu'])[0];
        ?>
        <option value='<?php echo json_encode($menu); ?>'>
            Jour <?php echo htmlspecialchars($menu['day_number']); ?> - 
            PD: <?php echo htmlspecialchars($pd); ?> - 
            Déjeuner: <?php echo htmlspecialchars($dejeuner); ?> - 
            Dîner: <?php echo htmlspecialchars($diner); ?>
        </option>
    <?php endforeach; ?>
  </select>

  <!-- ✅ Bouton pour afficher le menu sélectionné -->
  <button onclick="afficherMenu()" style="padding: 5px 15px; margin-left: 10px;">Afficher le Menu</button>

  <div id="recette_affichee" style="margin-top: 20px;"></div>

  <hr style='margin: 20px 0;'>

  <?php
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
function ouvre_popup(url) {
    window.open(url, 'Calculateur', 'width=600,height=400,scrollbars=yes');
    return false; // Assure que le lien ne suit pas le href="#"
}

function afficherMenu() {
    const selectedValue = document.getElementById('menu_selector').value;
    const recetteContainer = document.getElementById('recette_affichee');

    if (selectedValue) {
        const menu = JSON.parse(selectedValue);
        const recetteHTML = `
            <h3>Jour ${menu.day_number}</h3>
            <p><strong>Petit Déjeuner :</strong> ${menu.petit_dejeuner_menu}<br><strong>Recette :</strong> ${menu.petit_dejeuner_recette}</p>
            <p><strong>Déjeuner :</strong> ${menu.repas_midi_menu}<br><strong>Recette :</strong> ${menu.repas_midi_recette}</p>
            <p><strong>Dîner :</strong> ${menu.souper_menu}<br><strong>Recette :</strong> ${menu.souper_recette}</p>
            <p><strong>Collation :</strong> ${menu.collation_menu}<br><strong>Recette :</strong> ${menu.collation_recette}</p>
        `;
        recetteContainer.innerHTML = recetteHTML;
    } else {
        recetteContainer.innerHTML = '<p>Veuillez sélectionner un menu.</p>';
    }
}
</script>










