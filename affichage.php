<?php
include 'settings.php';

try {
    // Jointure pour obtenir le nom de la ville et l'activité à partir des tables ville et activite
    $stmt = $conn->prepare("
        SELECT p.*, v.Ville, a.Activity 
        FROM partenariats p
        JOIN ville v ON p.Ville = v.id
        JOIN activite a ON p.Activite = a.id
        ORDER BY v.Ville, a.Activity
    ");
    $stmt->execute();
    $partenariats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des partenariats : " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des fiches de partenariats</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .table-container {
            width: 80%;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
        }
        td img {
            width: 100px;
            height: auto;
        }
        .button-container {
            display: flex;
            gap: 10px;
        }
        .back-button, .edit-button, .delete-button {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button {
            background-color: #007bff;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .edit-button {
            background-color: #28a745;
        }
        .edit-button:hover {
            background-color: #218838;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .photo-column {
            width: 12.5%; /* 1/8th of 100% */
        }
    </style>
</head>
<body>
    <div class="table-container">
        <h2>Fiches de partenariats</h2>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Téléphone</th>
                    <th>Enseigne</th>
                    <th>Ville</th>
                    <th>Activité</th>
                    <th>Promotion</th>
                    <th>Détail</th>
                    <th>Adresse du Centre</th>
                    <th>Ville du Centre</th>
                    <th class="photo-column">Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($partenariats as $partenariat) {
                    echo '<tr>
                            <td>' . htmlspecialchars($partenariat['email']) . '</td>
                            <td>' . htmlspecialchars($partenariat['Nom']) . '</td>
                            <td>' . htmlspecialchars($partenariat['Prenom']) . '</td>
                            <td>' . htmlspecialchars($partenariat['Phone']) . '</td>
                            <td>' . htmlspecialchars($partenariat['Enseigne']) . '</td>
                            <td>' . htmlspecialchars($partenariat['Ville']) . '</td>
                            <td>' . htmlspecialchars($partenariat['Activity']) . '</td>
                            <td>' . htmlspecialchars($partenariat['Promotion']) . '</td>
                            <td>' . htmlspecialchars($partenariat['Detail']) . '</td>
                            <td>' . htmlspecialchars($partenariat['AdresseCentre']) . '</td>
                            <td>' . htmlspecialchars($partenariat['VilleCentre']) . '</td>
                            <td class="photo-column"><img src="' . htmlspecialchars($partenariat['Photo']) . '" alt="Photo" width="100"></td>
                            <td class="button-container">
                                <button class="edit-button" onclick="window.location.href=\'modifier_partenariat.php?id=' . $partenariat['id'] . '\'">Modifier</button>
                                <form method="POST" action="supprimer_partenariat.php" style="display:inline;">
                                    <input type="hidden" name="id" value="' . $partenariat['id'] . '">
                                    <button type="submit" class="delete-button">Supprimer</button>
                                </form>
                            </td>
                          </tr>';
                }
                ?>
            </tbody>
        </table>
        <button class="back-button" onclick="window.location.href='menus.php';">Retour au Menu</button>
    </div>
</body>
</html>










