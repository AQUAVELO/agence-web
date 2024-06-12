<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location:_analyse.php");
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

// Rechercher le dernier poids inséré dans la table suivie
$sql = "SELECT * FROM suivie WHERE email = ? ORDER BY Date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);
$lastSuivi = $stmt->fetch(PDO::FETCH_ASSOC);

// Utiliser le dernier poids si disponible, sinon utiliser le poids de base
$latestWeight = $lastSuivi ? $lastSuivi["Poids"] : $userInfo["Poids"];

// Calculer l'IMC et déterminer le message à afficher
$message = '';
if ($userInfo) {
    $taille = $userInfo["Taille"];
    if ($latestWeight > 0 && $taille > 0) {
        $imc = $latestWeight / (($taille / 100) * ($taille / 100));
        $imc = round($imc, 2);

        if ($imc < 20) {
            $message = "Vous êtes maigre, je vous conseille de prendre un peu de poids aussi il faudrait : 1) Alimentation calorique : Intégrer des aliments riches en calories et en nutriments dans votre alimentation, en privilégiant les glucides complexes, les lipides sains et les protéines maigres. comprenant des aliments tels que les noix, les graines, les avocats et les produits laitiers. 2) Repas fréquents : Opter pour des repas plus fréquents tout au long de la journée afin d'augmenter l'apport calorique total.3) Suppléments nutritionnels : Si nécessaire, prendre des suppléments vitaminiques pour compenser les éventuelles carences.4) Supervision : Évaluez la progression de la prise de poids et surveiller les éventuelles complications.5) Support psychologique : Envisager une thérapie si besoin pour traiter les problèmes émotionnels associés à l'insuffisance pondérale. 6) Activité physique : Pratiquez au moins 3h d'activité physique modérée par semaine (comme la marche rapide, le vélo ou l'Aquavélo)." ;
        } elseif ($imc >= 20 && $imc <= 25) {
            $message = "Félicitations, vous avez un IMC normal, continuez à vous entretenir, à manger crudités, légumes et fruits réguliérement. L'activité physique est bénéfique donc pratiquez 30 minutes quotidiennement une activité cardio vasculaire comme l'Aquavélo et cela vous permettra d'affiner et de raffermir votre silhouette durablement.";
        } elseif ($imc > 25 && $imc <= 30) {
            $message = "Ce stade augmente le risque de maladies cardiovasculaires, de diabète de type 2, d'hypertension et de certains cancers (comme le cancer du sein et du côlon). Les problèmes articulaires et les apnées du sommeil peuvent également commencer à apparaître. La solution : 1) Alimentation équilibrée, 2) Activité physique : Pratiquez au moins 3h d'activité physique modérée par semaine (comme la marche rapide, le vélo ou l'Aquavélo), ou 30 minutes d'Aquavélo par jour. Hydratation : Boire au minimum 1,5l d'eau tout au long de la journée. 4) Réduction des sucres et des graisses saturées : Limitez la consommation d'aliments sucrés et gras.";
        } elseif ($imc > 30 && $imc <= 35) {
            $message = "Vous êtes en obésité. Aussi adopter un régime alimentaire équilibré et hypocalorique : Consommez davantage de fruits, légumes, céréales complètes et protéines maigres. Réduisez les aliments riches en graisses saturées, sucres ajoutés et sel. Évitez les boissons sucrées et la restauration rapide. Contrôlez vos portions : Utilisez des assiettes plus petites pour limiter les portions. Mangez lentement et savourez chaque bouchée pour reconnaître la satiété. Planification des repas : Préparez vos repas à l'avance pour éviter les choix alimentaires impulsifs. Préparez des collations saines pour prévenir les fringales. Exercice régulier : Pratiquez au moins 150 minutes d'activité physique modérée par semaine (comme la marche rapide, l’Aquavélo, ou le cyclisme). Ajoutez des exercices de renforcement musculaire au moins deux jours par semaine. Intégrez davantage d'activité dans votre routine quotidienne. Hydratation : Buvez au moins 1,5 à 2 litres d'eau par jour. Évitez les boissons sucrées et alcoolisées. Gestion du stress : Pratiquez des techniques de relaxation comme la méditation, le yoga ou la respiration profonde. Assurez-vous de dormir suffisamment (7 à 9 heures par nuit). Suivi et soutien : Tenez un journal alimentaire pour suivre ce que vous mangez.";
        } elseif ($imc > 35 ) {
            $message = "Vous êtes en obésité morbide au delà de 35. Aussi vous devez adopter une alimentation équilibrée et hypocalorique : Consommez plus de fruits, légumes, grains entiers, et protéines maigres. Limitez les aliments riches en graisses saturées comme les sucres ajoutés, et le sel. Ne pas boire de boissons sucrées et les fast-foods, à remplacer par de l'antésite.
Prendre des portions contrôlées. Utilisez des assiettes plus petites pour réduire les portions. Mangez lentement et savourez chaque bouchée pour reconnaître la satiété.
Planification des repas : Planifiez vos repas à l'avance pour éviter les choix alimentaires impulsifs. Préparez des collations saines comme les fruits ou crudités pour éviter les fringales.
Exercice régulier :Faites au moins 150 minutes d'activité physique modérée par semaine (comme la marche rapide, l‘Aquavélo, ou le cyclisme). Incorporez des exercices de renforcement musculaire au moins deux jours par semaine. Intégrer plus d'activité dans la routine quotidienne . Hydratation : Buvez au moins 1,5 à 2 litres d'eau par jour. Évitez les boissons sucrées et alcoolisées.
Gestion du stress :Pratiquez des techniques de relaxation comme la méditation, le yoga ou la respiration profonde. Assurez-vous de dormir suffisamment (7 à 9 heures par nuit). Suivi et soutien :Tenez un journal alimentaire pour suivre ce que vous mangez.";
        }
        
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
        <a href="_saisieMensurations.php">Saisie de l'identité et des premières mensurations</a>
    <?php endif; ?>
    <a href="_suivi.php">Saisie des nouvelles mensurations</a>
    <a href="index.php">Se déconnecter</a>
</div>

<div class="container">
    <?php if ($userInfo): ?>
        <h2>Informations de l'utilisateur</h2>
        <table>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
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
                <td><?php echo htmlspecialchars($userInfo["Age"]); ?></td>
                <td><?php echo htmlspecialchars($latestWeight); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Taille"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Trtaille"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Trhanches"]); ?></td>
                <td><?php echo htmlspecialchars($userInfo["Trfesses"]); ?></td>
                <td><?php echo $imc; ?></td>
            </tr>
        </table>
    <?php endif; ?>

    <div class="imc-box">
        <?php
        if (isset($imc)) {
            echo "IMC = " . number_format($imc, 2) . "<br>";
            echo $message;
        }
        ?>
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
            <?php 
            foreach ($userSuivi as $suivi): 
                // Calcul de l'IMC pour chaque entrée
                $imc_suivi = ($suivi['Poids'] > 0 && $userInfo['Taille'] > 0) ? round($suivi['Poids'] / (($userInfo['Taille'] / 100) * ($userInfo['Taille'] / 100)), 2) : 0;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($suivi["Date"]); ?></td>
                <td><?php echo htmlspecialchars($suivi["Poids"]); ?></td>
                <td><?php echo htmlspecialchars($suivi["Trtaille"]); ?></td>
                <td><?php echo htmlspecialchars($suivi["Trhanches"]); ?></td>
                <td><?php echo htmlspecialchars($suivi["Trfesses"]); ?></td>
                <td><?php echo $imc_suivi; ?></td>
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









