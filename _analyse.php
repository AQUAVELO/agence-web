<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\Redis\Config;
use \Mailjet\Resources;

// Paramètres de configuration
$settings = [];

$settings['ttl'] = intval(getenv("REDIS_TTL"));
$settings['dbhost'] = getenv("MYSQL_ADDON_HOST");
$settings['dbport'] = getenv("MYSQL_ADDON_PORT");

$settings['dbname'] = getenv("MYSQL_ADDON_DB");
$settings['dbusername'] = getenv("MYSQL_ADDON_USER");
$settings['dbpassword'] = getenv("MYSQL_ADDON_PASSWORD");

// Paramètres de configuration Mailjet
$settings['mjhost'] = "in-v3.mailjet.com";
$settings['mjusername'] = getenv("MJ_USERNAME");
$settings['mjpassword'] = getenv("MJ_PASSWORD");
$settings['mjfrom'] = getenv("MJ_FROM");

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

// Connexion à Redis
try {
    $redis = CacheManager::getInstance('redis', new Config([
        'host' => getenv("REDIS_HOST"),
        'port' => intval(getenv("REDIS_PORT")),
        'password' => getenv("REDIS_PASSWORD"),
    ]));
} catch (Exception $e) {
    die("Couldn't connect to Redis: " . $e->getMessage());
}

// Fonction pour inscrire un nouvel utilisateur
function registerUser($conn, $email, $password, $settings) {
    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT COUNT(*) FROM mensurations WHERE email = ?");
    $stmt->bindParam(1, $email);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Rediriger vers _menu.php si l'email existe déjà
        header("Location: _menu.php");
        exit(); // Assurez-vous de sortir après la redirection
    } else {
        // Insérer un nouvel utilisateur
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO mensurations (email, password) VALUES (?, ?)");
        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $hashed_password);
        if ($stmt->execute()) {
            $toEmail = $email;
            $toName = "Claude Alesiaminceur";
            include 'envoi.php'; // Inclure le fichier qui envoie l'email
            // Rediriger vers _menu.php 
            header("Location: _menu.php");
            return true;
        } else {
            return "Erreur lors de l'inscription: " . $stmt->errorInfo()[2];
        }
    }
}

// Fonction pour vérifier les informations de connexion
function checkLogin($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT password FROM mensurations WHERE email = ?");
    $stmt->bindParam(1, $email);
    $stmt->execute();
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $row['password'])) {
            return true;
        }
    }
    return false;
}

session_start();

$error_message = "";
$success_message = "";

// Gestion de l'inscription ou de la connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $action = $_POST["action"];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($action == "register") {
            $registration_result = registerUser($conn, $email, $password, $settings);
            if ($registration_result === true) {
                // Inscription réussie, rediriger vers _menu.php
                header("Location: _menu.php");
                exit;
            } else {
                $error_message = $registration_result;
            }
        } elseif ($action == "login") {
            if (checkLogin($conn, $email, $password)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["email"] = $email;
                // Connexion réussie, rediriger vers _menu.php
                header("Location: _menu.php");
                exit;
            } else {
                $error_message = "Identifiants incorrects.";
            }
        }
    } else {
        $error_message = "Email invalide.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription / Connexion</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .container {
            display: flex;
            justify-content: center;
            padding: 50px;
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
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        .logo-container img {
            width: 250px;
        }
        .logo-container .results-frame {
            margin-top: 20px;
            padding: 10px;
            border: 2px solid #000;
            text-align: center;
            background-color: #f0f0f0;
            width: 100%;
        }
        .logo-container button {
            margin-left: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
    <script>
        function setAction(action) {
            document.getElementById('action').value = action;
        }
    </script>
</head>
<body>
<div class="container">
    <div class="form-container">
        <?php
        if (!empty($error_message)) {
            echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
        }
        if (!empty($success_message)) {
            echo '<p class="success">' . htmlspecialchars($success_message) . '</p>';
        }
        ?>
        <h3>1) Inscrivez-vous ou connectez-vous en écrivant votre email et mot de passe</h3>
        <form method="post" action="">
            <input type="hidden" id="action" name="action" value="register">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" onclick="setAction('register')">S'inscrire</button>
            <button type="submit" onclick="setAction('login')">Se connecter</button>
        </form>
        <div class="logo-container">
            <a href="https://www.aquavelo.com">
                <img src="images/content/LogoAQUASPORTMINCEUR.webp" alt="Logo AQUAVELO">
            </a>
            <div class="results-frame">
                <p>Résultats de ce mois-ci : félicitations à Isabelle</p>
                <p>- 5 kg</p>
                <p>- 5 cm de tour de taille</p>
                <p>- 6 cm de tour de hanches</p>
                <p>- 8 cm de tour de fesses</p>
            </div>
        </div>
    </div>
    <div class="info-container">
        <div class="info-box">
            <h2>Vos mensurations</h2>
            <p>Entrez vos mensurations pour bénéficier de conseils personnalisés.</p>

            <h3>Inscription</h3>
            <p>Veuillez entrer un email et un mot de passe pour vous inscrire.</p>
            <p>Ensuite, validez votre inscription avec votre email et mot de passe.</p>

            <h3>Suivi</h3>
            <p>Vous pouvez faire le suivi de vos mensurations dans votre centre Aquavélo, profiter des conseils, et consulter vos résultats.</p>
            <p>Vous pouvez également prendre vos mensurations vous-même :</p>
            <ul>
                <li><strong>Poids</strong> : Le matin à jeun.</li>
                <li><strong>Taille</strong> : Au niveau du nombril.</li>
                <li><strong>Hanches</strong> : Au niveau des iliaques.</li>
                <li><strong>Tour de fesses</strong> : Sur la pointe des fesses.</li>
            </ul>
            <p>Notez ces mensurations pour un suivi régulier.</p>
        </div>
    </div>
</div>
</body>
</html>





















