<?php
// VERSION DEBUG - Afficher TOUTES les erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!-- DEBUG START -->";
echo "<!-- Session loggedin: " . (isset($_SESSION["loggedin"]) ? 'OUI' : 'NON') . " -->";

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: /suiviReguliers.php");
    exit;
}

// Lire l'email depuis la session
$email = $_SESSION["email"];
echo "<!-- Email: " . htmlspecialchars($email) . " -->";

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

// Connexion à la base de données
try {
    $conn = new PDO(
        'mysql:host=' . $settings['dbhost'] . ';port=' . $settings['dbport'] . ';dbname=' . $settings['dbname'],
        $settings['dbusername'],
        $settings['dbpassword']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<!-- Connexion DB: OK -->";
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Fonction pour obtenir l'historique COMPLET de suivi (mensurations initiales + suivie)
function getUserSuivi($conn, $email) {
    echo "<!-- getUserSuivi appelée pour: " . htmlspecialchars($email) . " -->";
    $allMensurations = [];
    
    try {
        // 1. Récupérer les mensurations initiales de la table mensurations
        $sql_init = "SELECT 
            email,
            created_at as Date,
            Poids,
            Trtaille,
            Trhanches,
            Trfesses
        FROM mensurations 
        WHERE email = ?";
        
        $stmt_init = $conn->prepare($sql_init);
        $stmt_init->execute([$email]);
        $mensurationInitiale = $stmt_init->fetch(PDO::FETCH_ASSOC);
        
        echo "<!-- Mensuration initiale trouvée: " . ($mensurationInitiale ? 'OUI' : 'NON') . " -->";
        if ($mensurationInitiale) {
            echo "<!-- Date initiale: " . ($mensurationInitiale['Date'] ?? 'NULL') . " -->";
        }
        
        // Ajouter les mensurations initiales si elles existent
        if ($mensurationInitiale && $mensurationInitiale['Date']) {
            $mensurationInitiale['id'] = 'Initial';
            $mensurationInitiale['source'] = 'initial';
            $allMensurations[] = $mensurationInitiale;
            echo "<!-- Mensuration initiale ajoutée -->";
        }
        
        // 2. Récupérer l'historique de la table suivie
        $sql_suivi = "SELECT 
            id,
            email,
            Date,
            Poids,
            Trtaille,
            Trhanches,
            Trfesses
        FROM suivie 
        WHERE email = ? 
        ORDER BY Date ASC";
        
        $stmt_suivi = $conn->prepare($sql_suivi);
        $stmt_suivi->execute([$email]);
        $suiviData = $stmt_suivi->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<!-- Suivis trouvés: " . count($suiviData) . " -->";
        
        // Ajouter les données de suivi avec le marqueur source
        foreach ($suiviData as $row) {
            $row['source'] = 'suivi';
            $allMensurations[] = $row;
        }
        
        echo "<!-- Total mensurations: " . count($allMensurations) . " -->";
        
        // 3. Trier par date (du plus ancien au plus récent)
        if (count($allMensurations) > 0) {
            usort($allMensurations, function($a, $b) {
                $dateA = strtotime($a['Date']);
                $dateB = strtotime($b['Date']);
                return $dateA - $dateB;
            });
            echo "<!-- Tri effectué -->";
        }
        
        // 4. Formater les dates pour l'affichage
        foreach ($allMensurations as $key => $row) {
            $allMensurations[$key]['DateOriginal'] = $row['Date'];
            $allMensurations[$key]['DateFormatted'] = date("d/m/Y", strtotime($row['Date']));
        }
        
        // Retourner en ordre inverse (plus récent en premier) pour l'affichage du tableau
        $result = array_reverse($allMensurations);
        echo "<!-- Retour de " . count($result) . " mensurations -->";
        return $result;
        
    } catch (Exception $e) {
        echo "<!-- ERREUR getUserSuivi: " . htmlspecialchars($e->getMessage()) . " -->";
        error_log("Erreur dans getUserSuivi: " . $e->getMessage());
        return [];
    }
}

// Rechercher les informations de base de l'utilisateur
$sql = "SELECT * FROM mensurations WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<!-- UserInfo trouvé: " . ($userInfo ? 'OUI' : 'NON') . " -->";

// Obtenir l'historique de suivi pour l'utilisateur
$userSuivi = getUserSuivi($conn, $email);
echo "<!-- userSuivi retourné: " . (is_array($userSuivi) ? count($userSuivi) : 'NULL') . " éléments -->";

// Rechercher le dernier poids
$sql = "SELECT * FROM suivie WHERE email = ? ORDER BY Date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);
$lastSuivi = $stmt->fetch(PDO::FETCH_ASSOC);

// Utiliser le dernier poids si disponible, sinon utiliser le poids de base
$latestWeight = $lastSuivi ? $lastSuivi["Poids"] : $userInfo["Poids"];

// Calculer l'IMC
$message = '';
$imcColor = '#666';
if ($userInfo) {
    $taille = $userInfo["Taille"];
    if ($latestWeight > 0 && $taille > 0) {
        $imc = $latestWeight / (($taille / 100) * ($taille / 100));
        $imc = round($imc, 2);

        if ($imc < 20) {
            $message = "Vous êtes en insuffisance pondérale. Pensez à bien vous nourrir.";
            $imcColor = '#ff9800';
        } elseif ($imc > 25) {
            $message = "Vous êtes en surcharge pondérale. Continuez vos efforts !";
            $imcColor = '#ff5722';
        } elseif ($imc >= 20 && $imc <= 25) {
            $message = "Félicitations, vous avez un IMC normal ! Continuez à vous entretenir.";
            $imcColor = '#4caf50';
        }
    } else {
        $imc = 0;
    }
}

// Calculer les statistiques
$totalMensurations = count($userSuivi);
$firstMensuration = !empty($userSuivi) ? end($userSuivi) : null;
$progressWeight = 0;
if ($firstMensuration && $latestWeight > 0) {
    $progressWeight = round($firstMensuration['Poids'] - $latestWeight, 2);
}

echo "<!-- DEBUG END -->";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEBUG - Mon Tableau de Bord | Aquavelo</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .debug { background: #ffffcc; border: 2px solid #ff0000; padding: 20px; margin: 20px 0; }
        .debug h2 { color: #cc0000; }
        .debug pre { background: white; padding: 10px; overflow: auto; }
    </style>
</head>
<body>

<div class="debug">
    <h2>🔍 INFORMATIONS DE DEBUG</h2>
    
    <h3>Email utilisateur:</h3>
    <pre><?php echo htmlspecialchars($email); ?></pre>
    
    <h3>UserInfo existe ?</h3>
    <pre><?php echo $userInfo ? 'OUI' : 'NON'; ?></pre>
    
    <h3>Données UserInfo:</h3>
    <pre><?php print_r($userInfo); ?></pre>
    
    <h3>UserSuivi est un array ?</h3>
    <pre><?php echo is_array($userSuivi) ? 'OUI' : 'NON'; ?></pre>
    
    <h3>Nombre d'éléments dans UserSuivi:</h3>
    <pre><?php echo is_array($userSuivi) ? count($userSuivi) : 'PAS UN ARRAY'; ?></pre>
    
    <h3>Contenu de UserSuivi:</h3>
    <pre><?php print_r($userSuivi); ?></pre>
    
    <h3>Condition d'affichage du tableau:</h3>
    <pre>
$userSuivi && !empty($userSuivi) = <?php echo ($userSuivi && !empty($userSuivi)) ? 'TRUE' : 'FALSE'; ?>
isset($userSuivi) = <?php echo isset($userSuivi) ? 'TRUE' : 'FALSE'; ?>
!empty($userSuivi) = <?php echo !empty($userSuivi) ? 'TRUE' : 'FALSE'; ?>
    </pre>
    
    <h3>Requête SQL mensurations initiales:</h3>
    <pre>
SELECT email, created_at as Date, Poids, Trtaille, Trhanches, Trfesses
FROM mensurations 
WHERE email = '<?php echo htmlspecialchars($email); ?>'
    </pre>
    
    <h3>Requête SQL suivie:</h3>
    <pre>
SELECT id, email, Date, Poids, Trtaille, Trhanches, Trfesses
FROM suivie 
WHERE email = '<?php echo htmlspecialchars($email); ?>'
ORDER BY Date ASC
    </pre>
</div>

<hr>

<h2>TEST D'AFFICHAGE DU TABLEAU</h2>

<?php if ($userSuivi && !empty($userSuivi)): ?>
    <p style="color: green; font-weight: bold;">✅ CONDITION VRAIE - Le tableau DEVRAIT s'afficher</p>
    
    <table border="1" cellpadding="10">
        <tr>
            <th>Date</th>
            <th>Poids</th>
            <th>Type</th>
        </tr>
        <?php foreach ($userSuivi as $suivi): ?>
        <tr>
            <td><?php echo htmlspecialchars($suivi['DateFormatted'] ?? $suivi['Date']); ?></td>
            <td><?php echo htmlspecialchars($suivi['Poids']); ?> kg</td>
            <td><?php echo ($suivi['source'] ?? '') === 'initial' ? '🟢 Initial' : '🔵 Suivi'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p style="color: red; font-weight: bold;">❌ CONDITION FAUSSE - Le tableau NE s'affiche PAS</p>
    <p>Raisons possibles:</p>
    <ul>
        <li>$userSuivi est NULL ou FALSE</li>
        <li>$userSuivi est un array vide</li>
        <li>Erreur dans la fonction getUserSuivi()</li>
    </ul>
<?php endif; ?>

<hr>

<p><a href="_menu.php">← Retour au menu normal</a></p>

</body>
</html>










