<?php
/**
 * Interface d'administration discrète pour le planning - Version Gestion Clientèle
 */

require '_settings.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. GESTION DE LA CONNEXION
$password_secret = "aquavelo2026";
$authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;

if (isset($_POST['login_pass']) && $_POST['login_pass'] === $password_secret) {
    $_SESSION['admin_auth'] = true;
    $authenticated = true;
}

// 2. LOGIQUE D'ADMINISTRATION
if ($authenticated && isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $stmt = $database->prepare("DELETE FROM am_free WHERE id = ?");
        $stmt->execute([intval($_GET['id'])]);
    }
    if ($_GET['action'] === 'block' && isset($_GET['date']) && isset($_GET['hour'])) {
        $ref = 'BLOCK' . date('his');
        $name = "BLOCAGE MANUEL (RDV: " . $_GET['date'] . " à " . $_GET['hour'] . ")";
        $stmt = $database->prepare("INSERT INTO am_free (reference, center_id, name, email, phone, free) VALUES (?, 305, ?, 'admin@aquavelo.com', '0000', 3)");
        $stmt->execute([$ref, $name]);
    }
    echo "<script>window.location.href='index.php?p=admin_planning';</script>";
    exit;
}

if (!$authenticated): ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2 style="margin-bottom: 20px;">Accès Privé</h2>
          <form method="POST" action="index.php?p=admin_planning">
            <input type="password" name="login_pass" placeholder="Mot de passe" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; font-weight: bold; color: white;">CONNEXION</button>
          </form>
        </div>
      </div>
    </section>
<?php return; endif;

// 3. RÉCUPÉRATION DES DONNÉES (Toutes les clientes Cannes/Mandelieu/Vallauris)
$bookings_query = $database->prepare("SELECT id, name, email, phone FROM am_free WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%'");
$bookings_query->execute();
$raw_bookings = $bookings_query->fetchAll(PDO::FETCH_ASSOC);

$existing_bookings = [];
foreach ($raw_bookings as $b) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $b['name'], $matches);
    if ($matches) {
        $key = $matches[1] . '|' . $matches[2];
        $client_name = trim(explode('(RDV:', $b['name'])[0]);
        $existing_bookings[$key] = [
            'id' => $b['id'], 
            'name' => $client_name,
            'email' => $b['email'],
            'phone' => $b['phone'],
            'rdv_brut' => substr($b['name'], strpos($b['name'], "(RDV:") + 6, -1)
        ];
    }
}

// Configuration créneaux
$creneaux_semaine = ['09:45', '11:00', '12:15', '13:30', '14:45', '16:00', '17:15', '18:30'];
$creneaux_samedi  = ['09:45', '11:00', '12:15', '13:30'];

$calendar = [];
$today = new DateTime();
for ($i = 0; $i < 14; $i++) {
    $date = clone $today;
    $date->modify("+$i day");
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
    <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
        <h2 style="margin: 0; color: #00a8cc;"><i class="fa fa-users"></i> Gestion des Clients (Planning)</h2>
        <a href="index.php" class="btn btn-default">Quitter l'admin</a>
      </div>

      <div style="display: flex; overflow-x: auto; gap: 15px; padding-bottom: 20px;">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 220px; border: 1px solid #eee; border-radius: 10px; padding: 10px; background: #f9f9f9;">
            <div style="text-align: center; font-weight: bold; color: #333; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                <?= $day['day_name'] ?><br><small><?= $day['full_date'] ?></small>
            </div>
            <div>
              <?php foreach ($day['slots'] as $slot) : 
                $key = $day['full_date'] . '|' . $slot;
                $is_taken = isset($existing_bookings[$key]);
              ?>
                <div style="margin-bottom: 12px; padding: 10px; border-radius: 8px; background: <?= $is_taken ? '#e3f2fd' : 'white' ?>; border: 1px solid <?= $is_taken ? '#bbdefb' : '#eee' ?>; font-size: 0.85rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                  <div style="font-weight: bold; margin-bottom: 5px; color: #00a8cc; border-bottom: 1px solid #f0f0f0;"><?= $slot ?></div>
                  
                  <?php if ($is_taken) : 
                    $b = $existing_bookings[$key];
                  ?>
                    <div style="font-weight: bold; margin-bottom: 3px;">👤 <?= $b['name'] ?></div>
                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 3px;"><i class="fa fa-phone"></i> <?= $b['phone'] ?></div>
                    <div style="font-size: 0.75rem; color: #666; margin-bottom: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><i class="fa fa-envelope"></i> <?= $b['email'] ?></div>
                    
                    <div style="display: flex; gap: 5px;">
                        <a href="index.php?p=calendrier_cannes&center=305&nom=<?= urlencode($b['name']) ?>&email=<?= urlencode($b['email']) ?>&phone=<?= urlencode($b['phone']) ?>&old_rdv=<?= urlencode($b['rdv_brut']) ?>" 
                           style="flex: 1; text-align: center; background: #00a8cc; color: white; padding: 5px; border-radius: 4px; text-decoration: none; font-size: 0.7rem; font-weight: bold;">
                           MODIFIER
                        </a>
                        <a href="index.php?p=admin_planning&action=delete&id=<?= $b['id'] ?>" 
                           onclick="return confirm('Annuler définitivement ce rendez-vous ?')" 
                           style="flex: 1; text-align: center; background: #ff4d4d; color: white; padding: 5px; border-radius: 4px; text-decoration: none; font-size: 0.7rem; font-weight: bold;">
                           ANNULER
                        </a>
                    </div>
                  <?php else : ?>
                    <a href="index.php?p=admin_planning&action=block&date=<?= $day['full_date'] ?>&hour=<?= $slot ?>" 
                       style="display: block; text-align: center; color: #999; padding: 6px; border: 1px dashed #ccc; border-radius: 4px; text-decoration: none; font-size: 0.75rem;">
                       BLOQUER
                    </a>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
