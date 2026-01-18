<?php
/**
 * Admin Planning - Version Finale et Diagnostic
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
    echo "<script>window.location.href='index.php?p=admin_planning';</script>";
    exit;
}

if (!$authenticated): ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2>Accès Admin Planning</h2>
          <form method="POST"><input type="password" name="login_pass" placeholder="Mot de passe" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;"><button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">CONNEXION</button></form>
        </div>
      </div>
    </section>
<?php return; endif;

// 3. DONNÉES
// On récupère tout de am_free (prospects récents)
$all_free_query = $database->prepare("SELECT * FROM am_free ORDER BY id DESC LIMIT 100");
$all_free_query->execute();
$all_free = $all_free_query->fetchAll(PDO::FETCH_ASSOC);

// On récupère tout de client (CRM récent)
$all_clients_query = $database->prepare("SELECT * FROM client ORDER BY id DESC LIMIT 100");
$all_clients_query->execute();
$all_clients = $all_clients_query->fetchAll(PDO::FETCH_ASSOC);

// On récupère les centres pour les noms
$centers_query = $database->prepare("SELECT id, city FROM am_centers");
$centers_query->execute();
$centers_map = [];
foreach ($centers_query->fetchAll(PDO::FETCH_ASSOC) as $c) {
    $centers_map[$c['id']] = $c['city'];
}

// Construction du planning visuel
$bookings_visuel = [];
foreach ($all_free as $b) {
    if (strpos($b['name'], '(RDV:') !== false) {
        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $b['name'], $matches);
        if ($matches) {
            $key = $matches[1] . '|' . $matches[2];
            $bookings_visuel[$key] = $b;
        }
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
    
    <div style="text-align: right; margin-bottom: 20px;">
        <a href="index.php?p=admin_planning&logout=1" class="btn btn-sm btn-default">Déconnexion</a>
    </div>

    <!-- 1. PLANNING VISUEL -->
    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); margin-bottom: 40px;">
      <h2 style="color: #00a8cc; margin-bottom: 20px;">🗓️ Planning des séances gratuites (Cannes/Mandelieu/Vallauris)</h2>
      <div style="display: flex; overflow-x: auto; gap: 10px; padding-bottom: 20px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 180px; border: 1px solid #f0f0f0; border-radius: 10px; padding: 10px; background: #fafafa;">
            <div style="text-align: center; font-weight: bold; border-bottom: 1px solid #eee; margin-bottom: 10px; padding-bottom: 5px;">
                <?= $day['day_name'] ?><br><small><?= $day['full_date'] ?></small>
            </div>
            <?php foreach ($day['slots'] as $slot) : 
                $key = $day['full_date'] . '|' . $slot;
                $res = $bookings_visuel[$key] ?? null;
            ?>
                <div style="padding: 8px; border-radius: 6px; margin-bottom: 6px; font-size: 0.75rem; background: <?= $res ? '#fff9c4' : '#fff' ?>; border: 1px solid <?= $res ? '#fbc02d' : '#eee' ?>;">
                  <b><?= $slot ?></b>
                  <?php if ($res) : ?>
                    <div style="margin-top: 5px; font-weight: bold;"><?= trim(explode('(RDV:', $res['name'])[0]) ?></div>
                    <div style="color: #666;"><?= $res['phone'] ?></div>
                    <div style="margin-top: 5px;">
                        <a href="index.php?p=admin_planning&action=delete&id=<?= $res['id'] ?>" onclick="return confirm('Annuler ce RDV ?')" style="color: #d32f2f;">❌ Annuler</a>
                    </div>
                  <?php else : ?>
                    <div style="color: #bbb;">Disponible</div>
                  <?php endif; ?>
                </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- 2. TABLE AM_FREE -->
    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1); margin-bottom: 40px;">
      <h2 style="color: #00a8cc; margin-bottom: 20px;">👤 100 derniers prospects (Table am_free)</h2>
      <p style="font-size: 0.9rem; color: #666;">C'est ici que sont enregistrées les demandes de séances gratuites.</p>
      <div class="table-responsive">
        <table class="table table-hover" style="font-size: 0.85rem;">
          <thead style="background: #f8f9fa;">
            <tr>
              <th>ID</th>
              <th>Date</th>
              <th>Centre</th>
              <th>Nom / RDV</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_free as $p) : ?>
              <tr>
                <td><?= $p['id'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($p['date'])) ?></td>
                <td><b><?= $centers_map[$p['center_id']] ?? $p['center_id'] ?></b></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['phone']) ?></td>
                <td>
                  <a href="index.php?p=admin_planning&action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce prospect ?')" class="btn btn-xs btn-danger">Supprimer</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. TABLE CLIENT -->
    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <h2 style="color: #00a8cc; margin-bottom: 20px;">📑 100 derniers clients (Table client)</h2>
      <p style="font-size: 0.9rem; color: #666;">C'est votre base de données CRM générale.</p>
      <div class="table-responsive">
        <table class="table table-hover" style="font-size: 0.85rem;">
          <thead style="background: #f8f9fa;">
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Prénom</th>
              <th>Email</th>
              <th>Tel</th>
              <th>Ville</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_clients as $c) : ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['nom']) ?></td>
                <td><?= htmlspecialchars($c['prenom']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['tel']) ?></td>
                <td><?= htmlspecialchars($c['ville']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>
