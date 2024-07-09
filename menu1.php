<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
} catch (PDOException $e) {
    die("Couldn't connect to MySQL: " . $e->getMessage());
}

// Fonction pour calculer l'IMC
function calculateIMC($weight, $height) {
    if ($weight > 0 && $height > 0) {
        $imc = $weight / (($height / 100) * ($height / 100));
        return round($imc, 2);
    }
    return null;
}

// Fonction pour déterminer le type morphologique
function determineMorphology($tour_taille, $tour_hanches, $gender) {
    if ($tour_taille > 0 && $tour_hanches > 0) {
        $ratio = $tour_taille / $tour_hanches;
        if ($gender === 'female') {
            return $ratio > 0.85 ? "androïde (forme de pomme)" : "gynoïde (forme de poire)";
        } elseif ($gender === 'male') {
            return $ratio > 0.90 ? "androïde (forme de pomme)" : "gynoïde (forme de poire)";
        }
    }
    return null;
}

session_start();

$bmi_message = "";
$tour_message = "";
$morphology_message = "";

// Gestion de l'IMC et des mesures
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['calculate_bmi'])) {
        $weight = $_POST["weight"];
        $height = $_POST["height"];
        $imc = calculateIMC($weight, $height);
        if ($imc !== null) {
            if ($imc < 20) {
                $bmi_message = "votre IMC est de $imc, donc vous êtes trop maigre.";
            } elseif ($imc > 25) {
                $bmi_message = "votre IMC est de $imc, donc vous êtes en surcharge pondérale.";
            } elseif ($imc >= 20 && $imc <= 25) {
                $bmi_message = "votre IMC est de $imc, donc félicitations, vous avez un IMC normal, continuez à vous entretenir.";
            }
        } else {
            $bmi_message = "Veuillez entrer des valeurs valides pour le poids et la taille.";
        }
    } elseif (isset($_POST['submit_measures'])) {
        $tour_taille = $_POST["tour_taille"];
        $tour_hanches = $_POST["tour_hanches"];
        $tour_fesses = $_POST["tour_fesses"];
        $gender = isset($_POST["gender"]) ? $_POST["gender"] : null;
        $tour_message = "Vos mesures sont - Tour de Taille: $tour_taille cm, Tour de Hanches: $tour_hanches cm, Tour de Fesses: $tour_fesses cm.";
        $morphology = determineMorphology($tour_taille, $tour_hanches, $gender);
        if ($morphology !== null) {
            $morphology_message = "Votre répartition corporelle est : $morphology.";
        } else {
            $morphology_message = "Veuillez entrer des valeurs valides pour les mesures.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Calcul de l'IMC et Mesures</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .container {
            display: flex;
            justify-content: center;
            padding: 50px;
            flex-wrap: wrap;
        }
        .form-container, .info-container {
            width: 45%;
            margin: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label, input {
            display: block;
            margin: auto;
        }
        button {
            background-color: #69d5ef;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            margin: 5px;
            color: black;
        }
        .info-box {
            border: 1px solid #000;
            padding: 20px;
            text-align: center;
        }
        .highlight-box {
            border: 2px solid #000;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="highlight-box">
        <h2>Calculez votre IMC et évaluer votre morphologique en fonction de vos mensurations</h2>
    </div>
    <div class="form-container">
        <h3>Calculez votre IMC</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="weight">Poids (kg):</label>
                <input type="number" id="weight" name="weight" required>
            </div>
            <div class="form-group">
                <label for="height">Taille (cm):</label>
                <input type="number" id="height" name="height" required>
            </div>
            <button type="submit" name="calculate_bmi">Calculer l'IMC</button>
        </form>
        <?php if (!empty($bmi_message)): ?>
            <div class="info-box">
                <p><?php echo htmlspecialchars($bmi_message); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <h3>Entrez vos mesures</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="tour_taille">Tour de Taille (cm):</label>
                <input type="number" id="tour_taille" name="tour_taille" required>
            </div>
            <div class="form-group">
                <label for="tour_hanches">Tour de Hanches (cm):</label>
                <input type="number" id="tour_hanches" name="tour_hanches" required>
            </div>
            <div class="form-group">
                <label for="tour_fesses">Tour de Fesses (cm):</label>
                <input type="number" id="tour_fesses" name="tour_fesses" required>
            </div>
            <div class="form-group">
                <label for="gender">Sexe:</label>
                <select id="gender" name="gender" required>
                    <option value="female">Femme</option>
                    <option value="male">Homme</option>
                </select>
            </div>
            <button type="submit" name="submit_measures">Soumettre les mesures</button>
        </form>
        <?php if (!empty($tour_message) || !empty($morphology_message)): ?>
            <div class="info-box">
                <p><?php echo htmlspecialchars($tour_message); ?></p>
                <p><?php echo htmlspecialchars($morphology_message); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>



