<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit;
}

// Lire l'email depuis la session
$email = $_SESSION["email"];

// Configuration de la base de données
require 'vendor/autoload.php';

use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\Redis\Config;

// Paramètres de configuration
$settings = [];

$settings['ttl'] = intval(getenv("REDIS_TTL"));
$settings['dbhost'] = getenv("MYSQL_ADDON_HOST");
$settings['dbport'] = getenv("MYSQL_ADDON_PORT");

$settings['dbname'] = getenv("MYSQL_ADDON_DB");
$settings['dbusername'] = getenv("MYSQL_ADDON_USER");
$settings['dbpassword'] = getenv("MYSQL_ADDON_PASSWORD");

$settings['mjhost'] = "in.mailjet.com";
$settings['mjusername'] = getenv("MAILJET_USERNAME");
$settings['mjpassword'] = getenv("MAILJET_PASSWORD");
$settings['mjfrom'] = "info@aquavelo.com";

// Connexion à la base de données
try {
    $conn = new PDO(
        'mysql:host=' . $settings['dbhost'] . ';port=' . $settings['dbport'] . ';dbname=' . $settings['dbname'],
        $settings['dbusername'],
        $settings['dbpassword']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Couldn't connect to MySQL: " . $e->getMessage());
}

// Fonction pour obtenir l'historique de suivi des mensurations pour un utilisateur donné
function getUserSuivi($conn, $email) {
    $sql = "SELECT * FROM suivie WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $suivi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($suivi as &$row) {
        $row['Date'] = date("d/m/Y", strtotime($row['Date'])); // Formater la date
    }
    return $suivi;
}

// Rechercher les informations de base de l'utilisateur
$sql = "SELECT * FROM mensurations WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculer l'IMC
if ($userInfo) {
    $poids = $userInfo["Poids"];
    $taille = $userInfo["Taille"];
    if ($poids > 0 && $taille > 0) {
        $imc = $poids / (($taille / 100) * ($taille / 100));
        $imc = round($imc, 2);
    } else {
        $imc = 0;
    }
}

// Obtenir l'historique de suivi pour l'utilisateur
$userSuivi = getUserSuivi($conn, $email);

// Si une demande de suppression est effectuée
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $delete_id = $_POST["delete_id"];
    $delete_sql = "DELETE FROM suivie WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    if ($delete_stmt->execute([$delete_id])) {
        // Rafraîchir la page après suppression
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Erreur lors de la suppression de l'enregistrement.";
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Mensurations</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .center-button {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .center-button button, .center-button a {
            background-color: #add8e6;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            margin: 5px;
            color: black;
        }
        .container {
            text-align: center;
        }
        .delete-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .imc-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 20px 0;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

<div class="center-button">
    <?php
    // Vérifier si tous les champs sont remplis
    $fields_filled = !empty($userInfo["Nom"]) && !empty($userInfo["Prenom"]) && !empty($userInfo["email"]) && !empty($userInfo["Age"]) && !empty($userInfo["Poids"]) && !empty($userInfo["Taille"]) && !empty($userInfo["Trtaille"]) && !empty($userInfo["Trhanches"]) && !empty($userInfo["Trfesses"]);

    if (!$fields_filled): ?>
        <a href="saisieMensurations.php">Saisie de l'identité et des premières mensurations</a>
    <?php endif; ?>
    <a href="suivi.php">Saisie des nouvelles mensurations</a>
    <a href="index.php">Se déconnecter</a>
</div>

<div class="container">
    <?php if ($userInfo): ?>
        <h2>Informations Utilisateur</h2>
        <table>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Age</th>
                <th>Poids</th>
                <th>Taille</th>
                <th>Tour de Taille</th>
                <th>Tour de Hanches</th>
                <th>Tour de Fesses</th>
                <th>IMC</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($userInfo["Nom"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Prenom"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["email"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Phone"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Age"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Poids"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Taille"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Trtaille"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Trhanches"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Trfesses"]); ?></td>
                <td><?php echo $imc; ?></td>
            </tr>
        </table>
    <?php endif; ?>

    <div class="imc-box">
        <?php if (isset($imc)) echo "IMC = " . number_format($imc, 2); ?>
    </div>

    <?php if ($userSuivi && !empty($userSuivi)): ?>
        <h2>Historique de Suivi</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Poids</th>
                <th>Tour de Taille</th>
                <th>Tour de Hanches</th>
                <th>Tour de Fesses</th>
                <th>IMC</th>
                <th>Action</th>
            </tr>
            <?php foreach ($userSuivi as $suivi): ?>
            <tr>
                <td><?php echo htmlspecialchars($suivi["Date"]); ?></td>
                <td><?php echo htmlspecialchars($suivi["Poids"]); ?></td>
                <td><?php echo htmlspecialchars($suivi["Trtaille"]); ?></td>
                <td><?php echo htmlspecialchars($suivi["Trhanches"]); ?></td>
                <td><?php echo htmlspecialchars($suivi["Trfesses"]); ?></td>
                <td><?php echo $imc; ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($suivi['id']); ?>">
                        <button type="submit" class="delete-button">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Graphique de Suivi</h2>
        <canvas id="myChart" width="400" height="200"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Passer les données PHP à JavaScript
            const labels = <?php echo json_encode(array_column($userSuivi, 'Date')); ?>;
            const poids = <?php echo json_encode(array_column($userSuivi, 'Poids')); ?>;
            const trtaille = <?php echo json_encode(array_column($userSuivi, 'Trtaille')); ?>;
            const trhanches = <?php echo json_encode(array_column($userSuivi, 'Trhanches')); ?>;
            const trfesses = <?php echo json_encode(array_column($userSuivi, 'Trfesses')); ?>;

            // Configuration du graphique
            const data = {
                labels: labels,
                datasets: [
                    {
                        label: 'Poids',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        data: poids,
                        fill: false
                    },
                    {
                        label: 'Tour de Taille',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        data: trtaille,
                        fill: false
                    },
                    {
                        label: 'Tour de Hanches',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        data: trhanches,
                        fill: false
                    },
                    {
                        label: 'Tour de Fesses',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        data: trfesses,
                        fill: false
                    }
                ]
            };

            // Initialiser et afficher le graphique
            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Valeurs'
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
    <!-- Ajouter le logo AQUAVELO sous le formulaire -->
    <img src="images/content/LogoAQUASPORTMINCEUR.webp" alt="Logo AQUAVELO" style="margin-top: 20px; width: 250px;">
</div>

</body>
</html>




