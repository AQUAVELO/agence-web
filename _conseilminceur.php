<!-- Header Page -->
<header class="main-header clearfix">
  <div class="container">
    <h1 class="page-title pull-left">Conseils Minceur & Menus Équilibrés</h1>
    <ol class="breadcrumb pull-right">
      <li><a href="./">Accueil</a></li>
      <li class="active">Menus Minceur</li>
    </ol>
  </div>
</header>

<!-- Bannière Promo -->
<section class="content-area" style="background: linear-gradient(135deg, #00d4ff, #00a8cc); padding: 40px 0; margin-bottom: 0;">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center" style="color: white;">
        <h2 style="color: white; font-size: 2rem; margin: 0 0 15px 0;">
          🍽️ Menus à Moins de 1500 Calories + Aquabiking
        </h2>
        <p style="font-size: 1.2rem; margin-bottom: 10px; opacity: 0.95;">
          La combinaison gagnante pour perdre du poids efficacement et durablement
        </p>
        <p style="font-size: 1.1rem; opacity: 0.9;">
          <strong>Menus quotidiens</strong> + <strong>Brûlez 400-600 calories</strong> par séance d'aquabiking
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Section Principale -->
<section class="content-area bg1">
  <div class="container" style="background-color: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">

    <?php
    try {
        date_default_timezone_set('Europe/Paris');
        $jour_du_mois = date('j');
        $date_cache_buster = date('YmdHis');
    ?>


    <!-- VERSION ALTERNATIVE - Section Suivi Mensurations (Plus sobre) -->
<!-- À insérer AVANT l'Étape 1 du calculateur -->

<!-- Option 1 : Banner Horizontal Compact -->
<div style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border: 3px solid #4caf50; border-radius: 12px; padding: 30px; margin-bottom: 35px; box-shadow: 0 5px 15px rgba(76, 175, 80, 0.15);">
  <div class="row">
    <div class="col-md-9">
      <h3 style="color: #2e7d32; margin: 0 0 15px 0; font-size: 1.5rem;">
        <i class="fa fa-line-chart"></i> Suivez vos mensurations en temps réel
      </h3>
      <p style="margin: 0 0 10px 0; color: #555; font-size: 1.05rem; line-height: 1.6;">
        Créez votre <strong>espace personnel gratuit</strong> pour enregistrer vos mesures (poids, taille, hanches...), 
        visualiser votre évolution avec des graphiques et calculer automatiquement votre IMC.
      </p>
      <div style="margin-top: 15px;">
        <span style="background: white; padding: 6px 12px; border-radius: 15px; margin-right: 10px; display: inline-block; font-size: 0.9rem; color: #2e7d32;">
          <i class="fa fa-check"></i> 100% Gratuit
        </span>
        <span style="background: white; padding: 6px 12px; border-radius: 15px; margin-right: 10px; display: inline-block; font-size: 0.9rem; color: #2e7d32;">
          <i class="fa fa-lock"></i> Données sécurisées
        </span>
        <span style="background: white; padding: 6px 12px; border-radius: 15px; display: inline-block; font-size: 0.9rem; color: #2e7d32;">
          <i class="fa fa-mobile"></i> Accessible partout
        </span>
      </div>
    </div>
    <div class="col-md-3 text-center" style="display: flex; flex-direction: column; justify-content: center; gap: 10px;">
      <a href="/saisieMensurations.php" 
         class="btn btn-lg"
         onclick="if(typeof gtag !== 'undefined'){gtag('event','click_suivi_mensurations',{event_category:'conversion',event_label:'from_menus_page_compact'});}"
         style="background: linear-gradient(135deg, #4caf50, #388e3c); color: white; border: none; padding: 15px 25px; font-size: 1.1rem; border-radius: 25px; font-weight: 600; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3); text-decoration: none; display: block;">
        <i class="fa fa-user-plus"></i> Commencer
      </a>
      <a href="/suiviReguliers.php" 
         style="color: #4caf50; font-size: 0.95rem; text-decoration: none; font-weight: 600;">
        <i class="fa fa-sign-in"></i> Déjà inscrit
      </a>
    </div>
  </div>
</div>


    

    <!-- Calculateur Calories -->
    <div style="background: linear-gradient(135deg, #fff3cd, #ffe8a1); padding: 30px; border-radius: 15px; border-left: 5px solid #ff9800; margin-bottom: 40px; box-shadow: 0 5px 15px rgba(255, 152, 0, 0.2);">
      <div class="row">
        <div class="col-md-8">
          <h3 style="color: #f57c00; margin-top: 0; font-size: 1.6rem;">
            <i class="fa fa-calculator"></i> Étape 1 : Calculez Votre Besoin Calorique
          </h3>
          <p style="font-size: 1.1rem; color: #666; margin-bottom: 20px; line-height: 1.6;">
            Avant de commencer, déterminez vos besoins caloriques quotidiens pour une perte de poids efficace et personnalisée.
          </p>
          <ul style="list-style: none; padding: 0; margin: 0 0 20px 0;">
            <li style="padding: 5px 0;"><i class="fa fa-check" style="color: #ff9800;"></i> Calcul personnalisé selon votre profil</li>
            <li style="padding: 5px 0;"><i class="fa fa-check" style="color: #ff9800;"></i> Objectifs de perte de poids réalistes</li>
            <li style="padding: 5px 0;"><i class="fa fa-check" style="color: #ff9800;"></i> Conseils nutritionnels adaptés</li>
          </ul>
        </div>
        <div class="col-md-4 text-center">
          <a href="#" 
             class="btn btn-lg btn-block" 
             onclick="ouvre_popup('/resultatMinceur.php'); return false;" 
             title="Calculateur de calories et conseils pour perdre du poids" 
             aria-label="Calculateur calories & conseils minceur"
             style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white; border: none; padding: 20px; font-size: 1.2rem; border-radius: 50px; font-weight: 600; box-shadow: 0 5px 20px rgba(255, 152, 0, 0.4); margin-top: 20px;">
            <i class="fa fa-calculator"></i> Calculer Mes Besoins
          </a>
          <p style="margin-top: 15px; color: #999; font-size: 0.9rem;">
            <i class="fa fa-lock"></i> Gratuit et sécurisé
          </p>
        </div>
      </div>
    </div>

    <!-- Étape 2 -->
    <div style="background: linear-gradient(135deg, #e8f8fc, #d4f1f9); padding: 30px; border-radius: 15px; border-left: 5px solid #00d4ff; margin-bottom: 40px;">
      <h3 style="color: #00a8cc; margin-top: 0; font-size: 1.6rem;">
        <i class="fa fa-cutlery"></i> Étape 2 : Suivez Nos Menus Équilibrés à Moins de 1500 Calories
      </h3>
      <p style="font-size: 1.1rem; color: #666; margin: 0; line-height: 1.6;">
        Respectez les menus ci-dessous pour perdre du poids rapidement tout en restant en bonne santé. 
        <strong>Chaque jour un menu complet</strong> : petit-déjeuner, déjeuner, dîner et collation.
      </p>
    </div>

    <hr style="margin: 40px 0; border-color: #00d4ff;">

    <!-- DIAGNOSTIC TABLES -->
    <?php if (isset($_GET['debug_tables']) && isset($database)): ?>
    <pre style="background:#333;color:#0f0;padding:20px;border-radius:10px;font-size:12px;overflow:auto;max-height:600px;">
<?php
$jour = date('d');
echo "=== COMPARAISON TABLE menu vs menus (jour $jour) ===\n\n";

echo "--- TABLE 'menu' ---\n";
try {
    $cols_q = $database->query("SHOW COLUMNS FROM menu");
    $cols = $cols_q->fetchAll(PDO::FETCH_COLUMN);
    echo "Colonnes: " . implode(', ', $cols) . "\n";
    $q = $database->prepare("SELECT * FROM menu WHERE day_number = :j");
    $q->execute([':j' => $jour]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if ($row) { foreach ($row as $k => $v) echo "  $k = " . substr($v ?? '(null)', 0, 80) . "\n"; }
    else echo "  (aucune ligne pour jour $jour)\n";
    $cnt = $database->query("SELECT COUNT(*) FROM menu")->fetchColumn();
    echo "  Total lignes: $cnt\n";
} catch (Exception $e) { echo "ERREUR: " . $e->getMessage() . "\n"; }

echo "\n--- TABLE 'menus' ---\n";
try {
    $cols_q = $database->query("SHOW COLUMNS FROM menus");
    $cols = $cols_q->fetchAll(PDO::FETCH_COLUMN);
    echo "Colonnes: " . implode(', ', $cols) . "\n";
    $q = $database->prepare("SELECT * FROM menus WHERE day_number = :j");
    $q->execute([':j' => $jour]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if ($row) { foreach ($row as $k => $v) echo "  $k = " . substr($v ?? '(null)', 0, 80) . "\n"; }
    else echo "  (aucune ligne pour jour $jour)\n";
    $cnt = $database->query("SELECT COUNT(*) FROM menus")->fetchColumn();
    echo "  Total lignes: $cnt\n";
} catch (Exception $e) { echo "ERREUR: " . $e->getMessage() . "\n"; }
?>
    </pre>
    <?php endif; ?>

    <!-- Menu du Jour -->
    <?php
        if (isset($menu_datam)) {
            $date_du_jour = date('d/m/Y'); 
            echo "<div style='background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-bottom: 40px;'>";
            echo "<div style='text-align: center; margin-bottom: 30px;'>";
            echo "<h2 style='color: #00a8cc; margin: 0 0 10px 0; font-size: 2rem;'>";
            echo "<i class='fa fa-calendar'></i> Menu du Jour";
            echo "</h2>";
            echo "<p style='font-size: 1.2rem; color: #666;'>";
            echo "Date : <strong>" . $date_du_jour . "</strong> • ";
            echo "Total : <strong style='color: #ff9800;'>" . htmlspecialchars($menu_datam['total_calories']) . " kcal</strong>";
            echo "</p>";
            echo "</div>";

            $sections = [
                "Petit Déjeuner" => [
                    "menu" => "petit_dejeuner_menu", 
                    "recette" => "petit_dejeuner_recette", 
                    "photo" => "photo_pet_dej",
                    "icon" => "fa-coffee",
                    "color" => "#ff9800"
                ],
                "Déjeuner" => [
                    "menu" => "repas_midi_menu", 
                    "recette" => "repas_midi_recette", 
                    "photo" => "photo_repas_midi",
                    "icon" => "fa-cutlery",
                    "color" => "#00d4ff"
                ],
                "Dîner" => [
                    "menu" => "souper_menu", 
                    "recette" => "souper_recette", 
                    "photo" => "photo_souper",
                    "icon" => "fa-moon-o",
                    "color" => "#00a8cc"
                ],
                "Collation" => [
                    "menu" => "collation_menu", 
                    "recette" => "collation_recette", 
                    "photo" => "photo_collation",
                    "icon" => "fa-apple",
                    "color" => "#4caf50"
                ]
            ];

            echo "<div class='row'>";
            foreach ($sections as $title => $fields) {
                echo "<div class='col-md-6 col-sm-6' style='margin-bottom: 30px;'>";
                echo "<div style='background: white; border: 2px solid " . $fields['color'] . "; border-radius: 15px; padding: 25px; height: 100%; box-shadow: 0 5px 15px rgba(0,0,0,0.05);'>";
                
                echo "<h3 style='color: " . $fields['color'] . "; margin: 0 0 15px 0; font-size: 1.4rem; border-bottom: 2px solid " . $fields['color'] . "; padding-bottom: 10px;'>";
                echo "<i class='fa " . $fields['icon'] . "'></i> $title";
                echo "</h3>";
                
                echo "<div style='margin-bottom: 15px;'>";
                echo "<p style='margin-bottom: 8px;'><strong style='color: #00a8cc;'><i class='fa fa-list'></i> Menu :</strong></p>";
                echo "<p style='color: #666; font-size: 1.05rem; line-height: 1.6; margin: 0;'>" . htmlspecialchars($menu_datam[$fields['menu']] ?? 'Non disponible') . "</p>";
                echo "</div>";

                echo "<div style='margin-bottom: 15px;'>";
                echo "<p style='margin-bottom: 8px;'><strong style='color: #00a8cc;'><i class='fa fa-book'></i> Recette :</strong></p>";
                echo "<p style='color: #666; font-size: 0.95rem; line-height: 1.6; margin: 0;'>" . htmlspecialchars($menu_datam[$fields['recette']] ?? 'Non disponible') . "</p>";
                echo "</div>";

                $photo_db = $menu_datam[$fields['photo']] ?? '';
                $final_src = '';

                if (!empty($photo_db)) {
                    $candidates = [
                        __DIR__ . '/images/' . $photo_db,
                        __DIR__ . '/images/recette/' . $photo_db,
                        __DIR__ . '/images/' . basename($photo_db),
                        __DIR__ . '/images/recette/' . basename($photo_db),
                    ];
                    foreach ($candidates as $path) {
                        if (file_exists($path)) {
                            $final_src = str_replace(__DIR__ . '/', '', $path);
                            break;
                        }
                    }
                    if (!$final_src) {
                        $target_name = strtolower(basename($photo_db));
                        $search_dirs = [__DIR__ . '/images/', __DIR__ . '/images/recette/'];
                        foreach ($search_dirs as $dir) {
                            if (!is_dir($dir)) continue;
                            foreach (scandir($dir) as $file) {
                                if (strtolower($file) === $target_name) {
                                    $final_src = str_replace(__DIR__ . '/', '', $dir) . $file;
                                    break 2;
                                }
                            }
                        }
                    }
                }

                echo "<div style='text-align: center; margin-top: 20px;'>";
                if ($final_src) {
                    echo "<img src='$final_src?v=$date_cache_buster' alt='Photo $title' style='width: 100%; max-width: 300px; height: 200px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);'>";
                } else {
                    echo "<div style='width: 100%; max-width: 300px; height: 200px; margin: 0 auto; border-radius: 10px; background: linear-gradient(135deg, " . $fields['color'] . "22, " . $fields['color'] . "44); display: flex; align-items: center; justify-content: center; flex-direction: column;'>";
                    echo "<i class='fa " . $fields['icon'] . "' style='font-size: 3rem; color: " . $fields['color'] . "; opacity: 0.6;'></i>";
                    echo "<span style='color: " . $fields['color'] . "; opacity: 0.6; margin-top: 10px; font-size: 0.85rem;'>$title</span>";
                    echo "</div>";
                }
                
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-info' style='border-radius: 10px; border-left: 4px solid #00d4ff;'>";
            echo "<i class='fa fa-info-circle'></i> Aucun menu trouvé pour aujourd'hui (jour $jour_du_mois).";
            echo "</div>";
        }
    ?>

    <hr style="margin: 40px 0; border-color: #00d4ff;">

    <!-- Sélecteur Menus -->
    <div style="background: #f8f9fa; padding: 30px; border-radius: 15px; margin-bottom: 40px;">
      <h3 style="color: #00a8cc; margin-top: 0; font-size: 1.6rem;">
        <i class="fa fa-search"></i> Explorer Tous Nos Menus
      </h3>
      <p style="font-size: 1.05rem; color: #666; margin-bottom: 20px;">
        Découvrez tous nos menus équilibrés à moins de 1500 calories. Sélectionnez un jour pour afficher le détail complet.
      </p>
      
      <div class="row">
        <div class="col-md-8">
          <select id="menu_selector" 
                  class="form-control" 
                  style="padding: 15px; font-size: 1.05rem; border: 2px solid #00d4ff; border-radius: 10px;">
            <option value="">-- Sélectionner un menu --</option>
            <?php foreach ($all_menus as $menu): ?>
                <?php
                    $pd = explode(',', $menu['petit_dejeuner_menu'])[0];
                    $dejeuner = explode(',', $menu['repas_midi_menu'])[0];
                    $diner = explode(',', $menu['souper_menu'])[0];
                ?>
                <option value='<?php echo htmlspecialchars(json_encode($menu)); ?>'>
                    Jour <?php echo htmlspecialchars($menu['day_number']); ?> - 
                    PD: <?php echo htmlspecialchars($pd); ?> - 
                    Déjeuner: <?php echo htmlspecialchars($dejeuner); ?> - 
                    Dîner: <?php echo htmlspecialchars($diner); ?>
                </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <button onclick="afficherMenu()" 
                  class="btn btn-block"
                  style="background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; border: none; padding: 15px; font-size: 1.1rem; border-radius: 10px; font-weight: 600; box-shadow: 0 5px 15px rgba(0, 168, 204, 0.3);">
            <i class="fa fa-eye"></i> Afficher le Menu
          </button>
        </div>
      </div>

      <!-- Zone affichage menu sélectionné -->
      <div id="recette_affichee" style="margin-top: 30px;"></div>
    </div>

    <hr style="margin: 40px 0; border-color: #00d4ff;">

    <!-- Section Combinaison Gagnante -->
    <div style="background: linear-gradient(135deg, #e8f8fc, #d4f1f9); padding: 40px; border-radius: 15px; margin-bottom: 40px; text-align: center;">
      <h2 style="color: #00a8cc; margin: 0 0 20px 0; font-size: 2rem;">
        <i class="fa fa-line-chart"></i> La Combinaison Gagnante
      </h2>
      <div class="row">
        <div class="col-md-6">
          <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #ff9800; font-size: 1.4rem;">
              <i class="fa fa-cutlery"></i> Menus -1500 kcal
            </h3>
            <p style="font-size: 1.05rem; color: #666; line-height: 1.6;">
              Alimentation équilibrée et variée pour créer un <strong>déficit calorique sain</strong>
            </p>
          </div>
        </div>
        <div class="col-md-6">
          <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #00d4ff; font-size: 1.4rem;">
              <i class="fa fa-bicycle"></i> Aquabiking
            </h3>
            <p style="font-size: 1.05rem; color: #666; line-height: 1.6;">
              <strong>400-600 calories brûlées</strong> par séance de 45 minutes
            </p>
          </div>
        </div>
      </div>
      <p style="font-size: 1.3rem; color: #00a8cc; font-weight: 600; margin: 20px 0 30px 0;">
        = Perte de Poids Rapide et Durable ! 🎯
      </p>
      <a href="/free" 
         class="btn btn-lg"
         style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white; border: none; padding: 20px 50px; font-size: 1.3rem; border-radius: 50px; font-weight: 600; box-shadow: 0 5px 20px rgba(255, 152, 0, 0.4); text-decoration: none;">
        <i class="fa fa-gift"></i> Testez l'Aquabiking Gratuitement
      </a>
    </div>

    <hr style="margin: 40px 0; border-color: #00d4ff;">

    <!-- Articles Récents -->
    <?php
        if (isset($news_datas) && !empty($news_datas)) {
            echo "<h2 style='color: #00a8cc; font-size: 2rem; margin-bottom: 30px; text-align: center;'>";
            echo "<i class='fa fa-newspaper-o'></i> Articles Récents & Conseils Minceur";
            echo "</h2>";
            
            foreach ($news_datas as $index => $article) {
                echo '<div class="article" style="display: flex; align-items: flex-start; margin-bottom: 30px; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); flex-wrap: wrap;">';

                if (!empty($article["photo"])) {
                    echo '<div style="flex: 0 0 300px; margin-right: 30px; margin-bottom: 20px;">';
                    echo '<img src="' . htmlspecialchars($article["photo"]) . '?v=' . $date_cache_buster . '" ';
                    echo 'alt="Image de l\'article" ';
                    echo 'style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">';
                    echo '</div>';
                }

                echo '<div class="article-content" style="flex: 1; min-width: 300px;">';
                echo '<h3 style="margin-top: 0; color: #00a8cc; font-size: 1.5rem; margin-bottom: 15px;">';
                echo '<i class="fa fa-file-text-o"></i> ' . htmlspecialchars($article["titre"]);
                echo '</h3>';

                $formattedText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $article["news"]);
                $formattedText = nl2br($formattedText);

                echo '<p style="line-height: 1.8; margin: 0; color: #666; font-size: 1.05rem;">' . $formattedText . '</p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<div class='alert alert-info' style='border-radius: 10px; text-align: center;'>";
            echo "<i class='fa fa-info-circle'></i> Aucun article disponible pour le moment.";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger' style='border-radius: 10px;'>";
        echo "<i class='fa fa-exclamation-triangle'></i> <strong>Erreur de connexion :</strong> " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
    ?>

  </div>
</section>

<!-- CTA Final -->
<section class="content-area bg2" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white; padding: 60px 0;">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center">
        <h2 style="color: white; font-size: 2.2rem; margin-bottom: 20px;">
          <i class="fa fa-rocket"></i> Prêt(e) à Transformer Votre Corps ?
        </h2>
        <p style="font-size: 1.2rem; margin-bottom: 30px; opacity: 0.95;">
          Suivez nos menus + Pratiquez l'aquabiking 2-3 fois par semaine<br/>
          <strong>Résultats visibles dès le premier mois !</strong>
        </p>
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
          <a href="/free" class="btn btn-lg" style="background: white; color: #ff9800; border: none; padding: 20px 40px; font-size: 1.2rem; border-radius: 50px; font-weight: 600; box-shadow: 0 5px 20px rgba(0,0,0,0.2); text-decoration: none;">
            <i class="fa fa-gift"></i> Séance Gratuite
          </a>
          <a href="/centres" class="btn btn-lg" style="background: transparent; color: white; border: 3px solid white; padding: 20px 40px; font-size: 1.2rem; border-radius: 50px; font-weight: 600; text-decoration: none;">
            <i class="fa fa-map-marker"></i> Trouver un Centre
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- JavaScript -->
<script>
function ouvre_popup(url) {
    window.open(url, 'Calculateur', 'width=700,height=600,scrollbars=yes,resizable=yes');
    
    // Tracking analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'open_calculator', {
            'event_category': 'engagement',
            'event_label': 'calorie_calculator'
        });
    }
    
    return false;
}

function afficherMenu() {
    const selectedValue = document.getElementById('menu_selector').value;
    const recetteContainer = document.getElementById('recette_affichee');

    if (selectedValue) {
        try {
            const menu = JSON.parse(selectedValue);
            
            const sections = [
                {title: "Petit Déjeuner", menu: menu.petit_dejeuner_menu, recette: menu.petit_dejeuner_recette, icon: "fa-coffee", color: "#ff9800"},
                {title: "Déjeuner", menu: menu.repas_midi_menu, recette: menu.repas_midi_recette, icon: "fa-cutlery", color: "#00d4ff"},
                {title: "Dîner", menu: menu.souper_menu, recette: menu.souper_recette, icon: "fa-moon-o", color: "#00a8cc"},
                {title: "Collation", menu: menu.collation_menu, recette: menu.collation_recette, icon: "fa-apple", color: "#4caf50"}
            ];

            let recetteHTML = `
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-top: 20px;">
                    <h3 style="color: #00a8cc; margin-top: 0; text-align: center; font-size: 1.6rem; border-bottom: 2px solid #00d4ff; padding-bottom: 15px; margin-bottom: 25px;">
                        <i class="fa fa-calendar"></i> Menu Jour ${menu.day_number}
                    </h3>
                    <div class="row">
            `;

            sections.forEach(section => {
                recetteHTML += `
                    <div class="col-md-6" style="margin-bottom: 25px;">
                        <div style="background: white; border: 2px solid ${section.color}; border-radius: 10px; padding: 20px; height: 100%;">
                            <h4 style="color: ${section.color}; margin: 0 0 15px 0; font-size: 1.3rem; border-bottom: 2px solid ${section.color}; padding-bottom: 10px;">
                                <i class="fa ${section.icon}"></i> ${section.title}
                            </h4>
                            <p style="margin-bottom: 10px;"><strong style="color: #00a8cc;">Menu :</strong></p>
                            <p style="color: #666; margin-bottom: 15px; line-height: 1.6;">${section.menu || 'Non disponible'}</p>
                            <p style="margin-bottom: 10px;"><strong style="color: #00a8cc;">Recette :</strong></p>
                            <p style="color: #666; margin: 0; line-height: 1.6; font-size: 0.95rem;">${section.recette || 'Non disponible'}</p>
                        </div>
                    </div>
                `;
            });

            recetteHTML += `
                    </div>
                </div>
            `;
            
            recetteContainer.innerHTML = recetteHTML;
            
            // Scroll vers le menu affiché
            recetteContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
            // Tracking analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'view_menu', {
                    'event_category': 'engagement',
                    'event_label': 'day_' + menu.day_number
                });
            }
            
        } catch (e) {
            recetteContainer.innerHTML = `
                <div class="alert alert-danger" style="border-radius: 10px; margin-top: 20px;">
                    <i class="fa fa-exclamation-triangle"></i> Erreur lors de l'affichage du menu.
                </div>
            `;
        }
    } else {
        recetteContainer.innerHTML = `
            <div class="alert alert-warning" style="border-radius: 10px; margin-top: 20px; text-align: center;">
                <i class="fa fa-info-circle"></i> Veuillez sélectionner un menu dans la liste ci-dessus.
            </div>
        `;
    }
}

// Track clics CTA
document.querySelectorAll('a[href*="/free"]').forEach(function(link) {
    link.addEventListener('click', function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click_free_trial', {
                'event_category': 'conversion',
                'event_label': 'from_menus_page'
            });
        }
    });
});
</script>

<style>
/* Styles spécifiques page menus */
.article:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 168, 204, 0.2) !important;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3) !important;
    transition: all 0.3s ease;
}

#menu_selector:focus {
    border-color: #00a8cc;
    box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
}

@media (max-width: 768px) {
    .article {
        flex-direction: column !important;
    }
    
    .article img {
        margin-right: 0 !important;
        margin-bottom: 20px !important;
        flex: 1 1 100% !important;
    }
    
    div[style*="flex"] {
        display: block !important;
    }
    
    div[style*="flex"] > a {
        width: 100%;
        margin-bottom: 15px;
    }
}
</style>










