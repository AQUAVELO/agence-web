<?php
/**
 * Admin Planning - Version "CRM Réel"
 */

require '_settings.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. CONNEXION
$password_secret = "aquavelo2026";
$authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;
if (isset($_POST['login_pass']) && $_POST['login_pass'] === $password_secret) {
    $_SESSION['admin_auth'] = true;
    $authenticated = true;
}

// 2. ACTIONS
if ($authenticated && isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $database->prepare("DELETE FROM am_free WHERE id = ?")->execute([intval($_GET['id'])]);
    }
    // Action spéciale : Nettoyage des tests (RODRIGO, deja@reserve.com, etc.)
    if ($_GET['action'] === 'cleanup_tests') {
        $database->prepare("DELETE FROM am_free WHERE email = 'deja@reserve.com' OR name LIKE '%RODRIGO%' OR name LIKE '%TROI%' OR name LIKE '%Client Web%'")->execute();
    }
    echo "<script>window.location.href='index.php?p=admin_planning';</script>";
    exit;
}

if (!$authenticated): ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2 style="color: #00a8cc;">Administration</h2>
          <form method="POST"><input type="password" name="login_pass" placeholder="Mot de passe" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;"><button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">CONNEXION</button></form>
        </div>
      </div>
    </section>
<?php return; endif;

// 3. RÉCUPÉRATION DES DONNÉES RÉELLES
$all_free_query = $database->prepare("SELECT * FROM am_free WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%' ORDER BY id DESC");
$all_free_query->execute();
$all_free = $all_free_query->fetchAll(PDO::FETCH_ASSOC);

// Map pour le planning visuel
$bookings_visuel = [];
foreach ($all_free as $b) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $b['name'], $matches);
    if ($matches) {
        $key = $matches[1] . '|' . $matches[2];
        $bookings_visuel[$key] = $b;
    }
}

$creneaux_semaine = ['09:45', '11:00', '12:15', '13:30', '14:45', '16:00', '17:15', '18:30'];
$creneaux_samedi  = ['09:45', '11:00', '12:15', '13:30'];
$calendar = [];
$today = new DateTime();
for ($i = 0; $i < 14; $i++) {
    $date = clone $today; $date->modify("+$i day");
    $day_num = $date->format('N');
    if ($day_num <= 6) {
        $calendar[] = [
            'full_date' => $date->format('d/m/Y'),
            'day_name'  => ($day_num == 6) ? 'Samedi' : ($day_num == 1 ? 'Lundi' : ($day_num == 2 ? 'Mardi' : ($day_num == 3 ? 'Mercredi' : ($day_num == 4 ? 'Jeudi' : 'Vendredi')))),
            'slots'     => ($day_num == 6) ? $creneaux_samedi : $creneaux_semaine
        ];
    }
}
?>

<section class="content-area bg1" style="padding: 40px 0;">
  <div class="container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: #00a8cc; margin: 0;">🗓️ Gestion du Planning</h2>
        <div>
            <a href="index.php?p=admin_planning&action=cleanup_tests" onclick="return confirm('Attention : Cela va supprimer tous vos RDV de test (Rodrigo, etc.). Continuer ?')" class="btn btn-warning" style="margin-right: 10px;">🧹 Nettoyer les tests</a>
            <a href="index.php?p=admin_planning&logout=1" class="btn btn-default">Déconnexion</a>
        </div>
    </div>

    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <div style="display: flex; overflow-x: auto; gap: 10px; padding-bottom: 20px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 185px; border: 1px solid #f0f0f0; border-radius: 10px; padding: 10px; background: #fafafa;">
            <div style="text-align: center; font-weight: bold; border-bottom: 1px solid #eee; margin-bottom: 10px; padding-bottom: 5px;">
                <?= $day['day_name'] ?><br><small><?= $day['full_date'] ?></small>
            </div>
            <?php foreach ($day['slots'] as $slot) : 
                $key = $day['full_date'] . '|' . $slot;
                $res = $bookings_visuel[$key] ?? null;
                $is_manual = ($res && $res['email'] == 'deja@reserve.com');
            ?>
                <div style="padding: 10px; border-radius: 8px; margin-bottom: 8px; font-size: 0.8rem; background: <?= $res ? ($is_manual ? '#eceff1' : '#fff9c4') : '#fff' ?>; border: 1px solid <?= $res ? ($is_manual ? '#b0bec5' : '#fbc02d') : '#eee' ?>; min-height: 80px; display: flex; flex-direction: column; justify-content: space-between;">
                  <div>
                      <b><?= $slot ?></b>
                      <?php if ($res) : ?>
                        <div style="margin-top: 5px; font-weight: bold; color: #333; line-height: 1.2;"><?= trim(explode('(RDV:', $res['name'])[0]) ?></div>
                        <div style="color: #666; font-size: 0.75rem;"><?= $res['phone'] ?></div>
                      <?php else : ?>
                        <div style="color: #bbb; margin-top: 5px;">Disponible</div>
                      <?php endif; ?>
                  </div>
                  
                  <?php if ($res) : ?>
                    <div style="margin-top: 8px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 5px;">
                        <a href="index.php?p=admin_planning&action=delete&id=<?= $res['id'] ?>" 
                           onclick="return confirm('Annuler ce RDV pour <?= htmlspecialchars(trim(explode('(RDV:', $res['name'])[0])) ?> ?')" 
                           style="color: #d32f2f; font-size: 0.7rem; font-weight: bold; text-decoration: none;">❌ ANNULER</a>
                    </div>
                  <?php endif; ?>
                </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <p style="margin-top: 20px; text-align: center; color: #999; font-size: 0.9rem;">
        <i>L'admin affiche les 14 prochains jours. Seuls les rendez-vous présents en base de données sont affichés.</i>
    </p>

  </div>
</section>
