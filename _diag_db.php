<?php
/**
 * Admin Planning - Diagnostic Total (SANS LOGIN POUR 2 MINS)
 */

require '_settings.php';

// RÉCUPÉRATION DES CENTRES POUR DIAGNOSTIC
$centers_query = $database->prepare("SELECT id, city FROM am_centers");
$centers_query->execute();
$centers_map = [];
foreach ($centers_query->fetchAll(PDO::FETCH_ASSOC) as $c) {
    $centers_map[$c['id']] = $c['city'];
}

// RÉCUPÉRATION DES 100 DERNIERS PROSPECTS DE am_free
$all_query = $database->prepare("SELECT * FROM am_free ORDER BY id DESC LIMIT 100");
$all_query->execute();
$all_prospects = $all_query->fetchAll(PDO::FETCH_ASSOC);

// RÉCUPÉRATION DES 100 DERNIERS CLIENTS DE client
$clients_query = $database->prepare("SELECT * FROM client ORDER BY id DESC LIMIT 100");
$clients_query->execute();
$all_clients = $clients_query->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diagnostic Database</title>
    <link rel="stylesheet" href="/css/bootstrap.css">
</head>
<body style="padding: 20px;">
    <h2>100 derniers prospects (am_free)</h2>
    <table class="table table-bordered table-condensed" style="font-size: 11px;">
        <thead><tr><th>ID</th><th>Date</th><th>Center</th><th>Ville</th><th>Nom</th><th>Email</th><th>Segment</th></tr></thead>
        <tbody>
            <?php foreach ($all_prospects as $p) : ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= $p['date'] ?></td>
                    <td><?= $p['center_id'] ?></td>
                    <td><?= $centers_map[$p['center_id']] ?? '?' ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['email']) ?></td>
                    <td><?= htmlspecialchars($p['segment_id']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <hr>

    <h2>100 derniers clients (table client)</h2>
    <table class="table table-bordered table-condensed" style="font-size: 11px;">
        <thead><tr><th>ID</th><th>Nom</th><th>Prenom</th><th>Email</th><th>Tel</th><th>Ville</th></tr></thead>
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
</body>
</html>
<?php exit; ?>
