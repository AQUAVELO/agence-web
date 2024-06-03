<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit;
}

// Lire l'email, le nom et le prénom depuis la session
$email = $_SESSION["email"];
$nom = $_SESSION["Nom"];
$prenom = $_SESSION["Prenom"];

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

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $poids = isset($_POST["Poids"]) ? intval($_POST["Poids"]) : null;
    $trtaille = isset($_POST["Trtaille"]) ? intval($_POST["Trtaille"]) : null;
    $trhanches = isset($_POST["Trhanches"]) ? intval($_POST["Trhanches"]) : null;
    $trfesses = isset($_POST["Trfesses"]) ? intval($_POST["Trfesses"]) : null;

    // Vérifier que tous les champs sont fournis
    if ($poids && $trtaille && $trhanches && $trfesses) {
        // Utiliser la date actuelle
        $dateSuivi = date("Y-m-d H:i:s");


        echo "Email: $email, Date: $dateSuivi, Poids: $poids, Trtaille: $trtaille, Trhanches: $trhanches, Trfesses: $trfesses";

        // Insérer les données dans la table suivie
        $stmt = $conn->prepare("INSERT INTO suivie (email, Date, Poids, Trtaille, Trhanches, Trfesses) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$email, $dateSuivi, $poids, $trtaille, $trhanches, $trfesses])) {
            // Rediriger vers menu.php après la mise à jour réussie
            header("Location: menu.php");
            exit;
        } else {
            echo "Erreur lors de l'enregistrement dans la table suivie: " . implode(", ", $stmt->errorInfo());
        }
        $stmt->closeCursor();
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie des Mensurations et Suivi</title>
    <style>
        .container {
            text-align: center;
        }
        .form-title {
            margin-bottom: 20px;
        }
        .form-grid {
            display: flex;
            justify-content: center;
        }
        .form-column {
            margin-right: 20px; /* Espacement entre les colonnes */
        }
        .form-group {
            margin-top: 20px;
            margin-right: 20px; /* Espacement entre les groupes de champs */
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        .menu-button {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .menu-button button {
            background-color: #add8e6;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <form class="menu-button" action="menu.php" method="get">
        <button type="submit">Retour vers le Menu</button>
    </form>

    <h2 class="form-title">Formulaire de Saisie des Mensurations d'aujourd'hui</h2>
    
    <!-- Affichage des informations de l'utilisateur -->
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <p>Nom: <?php echo htmlspecialchars($nom); ?></p>
    <p>Prénom: <?php echo htmlspecialchars($prenom); ?></p>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-grid">
        <div class="form-column">
            <label for="Poids">Poids:</label>
            <input type="number" id="Poids" name="Poids" required>

            <label for="Trtaille">Tour de taille en cm:</label>
            <input type="number" id="Trtaille" name="Trtaille" required>

            <label for="Trhanches">Tour de hanches en cm:</label>
            <input type="number" id="Trhanches" name="Trhanches" required>

            <label for="Trfesses">Tour de fesses en cm:</label>
            <input type="number" id="Trfesses" name="Trfesses" required>
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</div>

</body>
</html>








