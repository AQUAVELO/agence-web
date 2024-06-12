<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: _menu.php");
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

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST["Nom"];
    $prenom = $_POST["Prenom"];
    $phone = $_POST["Phone"];
    $age = $_POST["Age"];
    $poids = $_POST["Poids"];
    $taille = $_POST["Taille"];
    $trtaille = $_POST["Trtaille"];
    $hanches = $_POST["Trhanches"];
    $fesses = $_POST["Trfesses"];

    // Mettre à jour les autres champs pour cet email
    $stmt = $conn->prepare("UPDATE mensurations SET Nom = ?, Prenom = ?, Phone = ?, Age = ?, Poids = ?, Taille = ?, Trtaille = ?, Trhanches = ?, Trfesses = ? WHERE email = ?");
    if ($stmt->execute([$nom, $prenom, $phone, $age, $poids, $taille, $trtaille, $hanches, $fesses, $email])) {
        // Rediriger vers menu.php après la mise à jour réussie
        header("Location: menu.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour: " . $stmt->errorInfo()[2];
    }
    $stmt->closeCursor();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie des Mensurations</title>
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

    <h2 class="form-title">Formulaire de Saisie des Mensurations</h2>
    
    <!-- Affichage de l'email de l'utilisateur -->
    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-grid">
        <div class="form-column">
            <label for="Nom">Nom:</label>
            <input type="text" id="Nom" name="Nom" required>

            <label for="Prenom">Prénom:</label>
            <input type="text" id="Prenom" name="Prenom" required>

            <label for="Phone">Téléphone:</label>
            <input type="text" id="Phone" name="Phone">

            <label for="Age">Âge:</label>
            <input type="number" id="Age" name="Age" required>
        </div>

        <div class="form-column">
            <label for="Taille">Taille en cm:</label>
            <input type="number" id="Taille" name="Taille" required>

            <label for="Poids">Poids:</label>
            <input type="number" id="Poids" name="Poids" required>

            <label for="Trtaille">Tour de taille:</label>
            <input type="number" id="Trtaille" name="Trtaille" required>

            <label for="Trhanches">Tour de hanches:</label>
            <input type="number" id="Trhanches" name="Trhanches" required>

            <label for="Trfesses">Tour de fesses:</label>
            <input type="number" id="Trfesses" name="Trfesses" required>
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</div>

</body>
</html>

