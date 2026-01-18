<?php
/**
 * Admin Planning - Diagnostic Total
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

if (!$authenticated): ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2>Accès Admin Diagnostic</h2>
          <form method="POST"><input type="password" name="login_pass" placeholder="Mot de passe" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;"><button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">CONNEXION</button></form>
        </div>
      </div>
    </section>
<?php return; endif;

// RÉCUPÉRATION DES CENTRES POUR DIAGNOSTIC
$centers_query = $database->prepare("SELECT id, city FROM am_centers WHERE online = 1");
$centers_query->execute();
$centers_map = [];
foreach ($centers_query->fetchAll(PDO::FETCH_ASSOC) as $c) {
    $centers_map[$c['id']] = $c['city'];
}

// RÉCUPÉRATION DES 200 DERNIERS PROSPECTS DE TOUTE LA BASE
$all_query = $database->prepare("SELECT * FROM am_free ORDER BY id DESC LIMIT 200");
$all_query->execute();
$all_prospects = $all_query->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="content-area bg1" style="padding: 40px 0;">
  <div class="container">
    <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <h2 style="color: #d32f2f;">🛠️ DIAGNOSTIC TOTAL : 200 dernières entrées am_free</h2>
      <p>Cette liste montre TOUT ce qui rentre dans la base, peu importe le centre.</p>
      
      <div class="table-responsive">
        <table class="table table-striped" style="font-size: 0.8rem;">
          <thead>
            <tr>
              <th>ID</th>
              <th>Date</th>
              <th>Centre ID</th>
              <th>Ville (si connue)</th>
              <th>Nom / RDV</th>
              <th>Email</th>
              <th>Tel</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_prospects as $p) : ?>
              <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['date'] ?></td>
                <td><b><?= $p['center_id'] ?></b></td>
                <td><?= $centers_map[$p['center_id']] ?? '?' ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['phone']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
