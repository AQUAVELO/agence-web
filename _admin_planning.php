<?php
/**
 * Admin Planning - Vision Globale et Réelle
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

// 2. ACTIONS (ANNULER / BLOQUER)
if ($authenticated && isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $database->prepare("DELETE FROM am_free WHERE id = ?")->execute([intval($_GET['id'])]);
    }
    if ($_GET['action'] === 'block' && isset($_GET['date']) && isset($_GET['hour'])) {
        $ref = 'BLOCK' . date('his');
        $name = "BLOCAGE MANUEL (RDV: " . $_GET['date'] . " à " . $_GET['hour'] . ")";
        $database->prepare("INSERT INTO am_free (reference, center_id, name, email, phone, free) VALUES (?, 305, ?, 'admin@aquavelo.com', '0000', 3)")->execute([$ref, $name]);
    }
    echo "<script>window.location.href='index.php?p=admin_planning';</script>";
    exit;
}

if (!$authenticated): ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2>Accès Admin</h2>
          <form method="POST"><input type="password" name="login_pass" placeholder="Mot de passe" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;"><button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">CONNEXION</button></form>
        </div>
      </div>
    </section>
<?php return; endif;

// 3. RÉCUPÉRATION DES DONNÉES
// On récupère TOUT am_free pour les centres concernés, trié par le plus récent
$all_query = $database->prepare("SELECT * FROM am_free WHERE center_id IN (305, 347, 349) ORDER BY id DESC LIMIT 100");
$all_query->execute();
$all_prospects = $all_query->fetchAll(PDO::FETCH_ASSOC);

$existing_bookings = [];
foreach ($all_prospects as $b) {
    if (strpos($b['name'], '(RDV:') !== false) {
        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $b['name'], $matches);
        if ($matches) {
            $key = $matches[1] . '|' . $matches[2];
            $existing_bookings[$key] = $b;
        }
    }
}

// Config planning
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
    
    <!-- SECTION 1 : PLANNING VISUEL -->
    <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); margin-bottom: 40px;">
      <h2 style="color: #00a8cc; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px;">🗓️ Planning des Rendez-vous</h2>
      <div style="display: flex; overflow-x: auto; gap: 10px; padding-bottom: 15px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 200px; border: 1px solid #eee; border-radius: 8px; padding: 10px; background: #fdfdfd;">
            <div style="text-align: center; font-weight: bold; font-size: 0.9rem; margin-bottom: 10px;"><?= $day['day_name'] ?> <?= $day['full_date'] ?></div>
            <?php foreach ($day['slots'] as $slot) : 
                $key = $day['full_date'] . '|' . $slot;
                $res = $existing_bookings[$key] ?? null;
            ?>
                <div style="padding: 6px; border-radius: 5px; margin-bottom: 5px; font-size: 0.8rem; background: <?= $res ? '#e3f2fd' : '#fff' ?>; border: 1px solid <?= $res ? '#bbdefb' : '#eee' ?>;">
                  <b><?= $slot ?></b>
                  <?php if ($res) : ?>
                    <div style="margin: 3px 0; font-weight: bold; color: #1976d2;"><?= trim(explode('(RDV:', $res['name'])[0]) ?></div>
                    <a href="index.php?p=admin_planning&action=delete&id=<?= $res['id'] ?>" onclick="return confirm('Annuler ce RDV ?')" style="color: #d32f2f; font-size: 0.7rem; text-decoration: underline;">Supprimer</a>
                  <?php else : ?>
                    <div style="color: #ccc;">Libre</div>
                  <?php endif; ?>
                </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- SECTION 2 : TOUS LES PROSPECTS (VUE RÉELLE BASE DE DONNÉES) -->
    <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <h2 style="color: #00a8cc; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px;">👥 Liste réelle des 100 derniers prospects (am_free)</h2>
      <div class="table-responsive">
        <table class="table table-striped" style="font-size: 0.9rem;">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom / RDV</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_prospects as $p) : ?>
              <tr>
                <td><?= $p['id'] ?></td>
                <td>
                    <b><?= htmlspecialchars($p['name']) ?></b>
                </td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['phone']) ?></td>
                <td>
                  <a href="index.php?p=admin_planning&action=delete&id=<?= $p['id'] ?>" 
                     onclick="return confirm('Supprimer définitivement ce prospect ?')" 
                     class="btn btn-xs btn-danger">Supprimer</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>
