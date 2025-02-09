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

    
    // Affichage du sélecteur
    if ($all_menus && count($all_menus) > 0) {
        echo "<h2>Sélecteur de menus par jour</h2>";
        echo "<select id='menu_selector' onchange='afficherMenu()'>";
        echo "<option value=''>-- Sélectionner un jour --</option>";
    
        foreach ($all_menus as $menu) {
            $value = htmlentities(json_encode($menu));
            echo "<option value='" . $value . "'>Jour " . htmlspecialchars($menu["day_number"]) . " - PD: " . htmlspecialchars($menu["petit_dejeuner_menu"]) . ", Déj: " . htmlspecialchars($menu["repas_midi_menu"]) . ", Dîner: " . htmlspecialchars($menu["souper_menu"]) . ", Collation: " . htmlspecialchars($menu["collation_menu"]) . "</option>";
        }
    
        echo "</select>";
        echo "<div id='recette_affichee' style='margin-top: 20px;'></div>";
    } else {
        echo "<p>Aucun menu précédent trouvé.</p>";
    }
  // Affichage du menu du jour
  if ($menu_du_jour) {
      echo "<h2>Menu du jour</h2>";
      echo "<div id='menu_du_jour'>";
      echo "<h3>Petit Déjeuner</h3>";
      echo "<p><strong>Menu :</strong> " . htmlspecialchars($menu_du_jour["petit_dejeuner_menu"]) . "</p>";
      echo "<p><strong>Recette :</strong> " . htmlspecialchars($menu_du_jour["petit_dejeuner_recette"]) . "</p>";
  
      echo "<h3>Repas du Midi</h3>";
      echo "<p><strong>Menu :</strong> " . htmlspecialchars($menu_du_jour["repas_midi_menu"]) . "</p>";
      echo "<p><strong>Recette :</strong> " . htmlspecialchars($menu_du_jour["repas_midi_recette"]) . "</p>";
  
      echo "<h3>Souper</h3>";
      echo "<p><strong>Menu :</strong> " . htmlspecialchars($menu_du_jour["souper_menu"]) . "</p>";
      echo "<p><strong>Recette :</strong> " . htmlspecialchars($menu_du_jour["souper_recette"]) . "</p>";
      echo "</div>";
  } else {
      echo "<p>Aucun menu trouvé pour aujourd'hui.</p>";
  }


    $conn->close();
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>

<script>
function afficherMenu() {
    const selectedValue = document.getElementById('menu_selector').value;
    if (selectedValue) {
        try {
            const menu = JSON.parse(selectedValue);
            console.log(menu); // Vérifie les données récupérées

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
        } catch (error) {
            console.error("Erreur lors de l'analyse JSON : ", error);
        }
    } else {
        document.getElementById('recette_affichee').innerHTML = '';
    }
}
</script>








