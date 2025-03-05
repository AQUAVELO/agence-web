<?php
// Vérifier si une session est déjà démarrée avant de la démarrer
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Activer les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base MySQL locale (MAMP)
try {
    $db = new PDO('mysql:host=localhost;dbname=cours;charset=utf8', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Connexion MySQL réussie dans merci_client.php");
} catch (PDOException $e) {
    error_log("Erreur de connexion MySQL dans merci_client.php : " . $e->getMessage());
    die("Erreur de connexion à la base MySQL : " . $e->getMessage());
}

// Récupérer le département et le message depuis la session
$dept = isset($_SESSION['dept']) ? $_SESSION['dept'] : null;
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "Erreur : aucun message disponible.";
error_log("Département récupéré : " . ($dept ?? "aucun") . ", Message : $message");

// Récupérer les nageurs dans le même département
$nageurs = [];
if ($dept) {
    try {
        $stmt = $db->prepare("SELECT id, prenom, ville FROM nageur WHERE dept = :dept");
        $stmt->execute([':dept' => $dept]);
        $nageurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Nageurs trouvés pour dept $dept : " . count($nageurs));
    } catch (PDOException $e) {
        error_log("Erreur recherche nageurs dans merci_client.php : " . $e->getMessage());
        $message = "Erreur lors de la recherche des nageurs.";
    }
} else {
    error_log("Aucun département fourni dans la session");
}
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
            color: #47c3e6;
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
            background-color: #47c3e6;
            color: white;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>Merci de votre inscription !</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php if (!empty($nageurs)) : ?>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="tonemailpaypal@example.com"> <!-- Remplace par ton email PayPal -->
                <input type="hidden" name="lc" value="FR">
                <input type="hidden" name="item_name" value="Coordonnées des maîtres-nageurs">
                <input type="hidden" name="amount" value="9.00">
                <input type="hidden" name="currency_code" value="EUR">
                <input type="hidden" name="return" value="http://localhost:8888/natation/coordonnees_nageurs.php">
                <input type="hidden" name="cancel_return" value="http://localhost:8888/natation/merci_client.php">
                <table>
                    <thead>
                        <tr>
                            <th>Sélectionner</th>
                            <th>Prénom</th>
                            <th>Ville</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nageurs as $nageur) : ?>
                            <tr>
                                <td><input type="checkbox" name="nageurs[]" value="<?php echo $nageur['id']; ?>"></td>
                                <td><?php echo htmlspecialchars($nageur['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($nageur['ville']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <input type="hidden" id="selectedNageurs" name="custom">
                <input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_buynow_LG.gif" border="0" name="submit" alt="Payer maintenant avec PayPal" onclick="updateSelectedNageurs()">
            </form>
            <script>
                function updateSelectedNageurs() {
                    const checkboxes = document.querySelectorAll('input[name="nageurs[]"]:checked');
                    const selectedIds = Array.from(checkboxes).map(cb => cb.value).join(',');
                    document.getElementById('selectedNageurs').value = selectedIds;
                    if (!selectedIds) {
                        alert('Veuillez sélectionner au moins un maître-nageur avant de procéder au paiement.');
                        event.preventDefault();
                    }
                }
            </script>
        <?php endif; ?>
    </div>
</body>
</html>