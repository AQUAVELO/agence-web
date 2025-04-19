<?php
require 'settings.php'; // Connexion à la base de données via $conn

try {
    $stmt = $conn->query("SELECT * FROM formule ORDER BY date DESC");
    $formules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des réservations Cryo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f5f5f5;
        }
        h1 {
            text-align: center;
            color: #104e8b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #104e8b;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f0f8ff;
        }
        .ok {
            color: green;
            font-weight: bold;
        }
        .enattente {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Liste des réservations - Séance Cryo</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Prix</th>
                <th>Date</th>
                <th>Vente</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($formules as $f): ?>
                <tr>
                    <td><?= $f['id'] ?></td>
                    <td><?= htmlspecialchars($f['nom']) ?></td>
                    <td><?= htmlspecialchars($f['prenom']) ?></td>
                    <td><?= htmlspecialchars($f['tel']) ?></td>
                    <td><?= htmlspecialchars($f['email']) ?></td>
                    <td><?= number_format($f['prix'], 2, ',', ' ') ?> €</td>
                    <td><?= date('d/m/Y H:i', strtotime($f['date'])) ?></td>
                    <td class="<?= $f['vente'] ? 'ok' : 'enattente' ?>">
                        <?= $f['vente'] ? '✅ Payé' : '⏳ En attente' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

