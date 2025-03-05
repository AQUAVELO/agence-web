<?php
// Démarrer la session
session_start();

// Activer les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base MySQL locale (MAMP)
try {
    $db = new PDO('mysql:host=localhost;dbname=cours;charset=utf8', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Connexion MySQL réussie dans merci_nageur.php");
} catch (PDOException $e) {
    error_log("Erreur de connexion MySQL dans merci_nageur.php : " . $e->getMessage());
    die("Erreur de connexion à la base MySQL : " . $e->getMessage());
}

// Récupérer le département et le message depuis la session
$dept = isset($_SESSION['dept']) ? $_SESSION['dept'] : null;
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "Erreur : aucun message disponible.";
error_log("Département récupéré : " . ($dept ?? "aucun") . ", Message : $message");

// Récupérer les clients dans le même département
$clients = [];
if ($dept) {
    try {
        $stmt = $db->prepare("SELECT prenom, ville FROM client WHERE dept = :dept");
        $stmt->execute([':dept' => $dept]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Clients trouvés pour dept $dept : " . count($clients));
    } catch (PDOException $e) {
        error_log("Erreur recherche clients dans merci_nageur.php : " . $e->getMessage());
        $message = "Erreur lors de la recherche des clients.";
    }
} else {
    error_log("Aucun département fourni dans la session");
}

// Nettoyer la session après usage
unset($_SESSION['message']);
unset($_SESSION['dept']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .message-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #00afdf;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #00afdf;
            color: white;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>Merci de votre inscription !</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php if (!empty($clients)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Ville</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($client['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($client['ville']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>