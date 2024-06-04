<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// début

// Inclure les fichiers nécessaires (si vous en avez)
include 'config.php'; // Par exemple, pour les configurations de base de données

// Fonction pour vérifier et enregistrer un utilisateur
function registerUser($pdo, $email, $name) {
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM mensurations WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Rediriger vers menu.php si l'email existe déjà
        header("Location: menu.php");
        exit(); // Assurez-vous de sortir après la redirection
    } else {
        // Insérer un nouvel utilisateur
        $stmt = $pdo->prepare("INSERT INTO mensurations (email, name) VALUES (:email, :name)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':name', $name);
        if ($stmt->execute()) {
            echo "Inscription réussie!";
        } else {
            echo "Erreur: L'inscription a échoué.";
        }
    }
}

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=your_host;dbname=your_db', 'your_username', 'your_password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtenir les données (email et nom) à partir de la requête ou d'une autre source
    $email = 'albiiit@gmail.com'; // Par exemple, à remplacer par $_POST['email'] si les données viennent d'un formulaire
    $name = 'Claude'; // Par exemple, à remplacer par $_POST['name']

    // Appeler la fonction pour vérifier et enregistrer l'utilisateur
    registerUser($pdo, $email, $name);
} catch (PDOException $e) {
    echo 'Échec de la connexion : ' . $e->getMessage();
}

// fin
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
function registerUser($conn, $email, $password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO mensurations (email, password) VALUES (?, ?)");
    $stmt->bindParam(1, $email);
    $stmt->bindParam(2, $hashed_password);
    if ($stmt->execute()) {
        return true;
    } else {
        if ($stmt->errorCode() == 23000) {
            return "Email déjà utilisé.";
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

// Gestion de l'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_result = registerUser($conn, $email, $password);
        if ($registration_result === true) {
            // Inscription réussie, rediriger vers menu.php
            header("Location: menu.php");
            exit;
        } else {
            $error_message = $registration_result;
        }
    } else {
        $error_message = "Email invalide.";
    }
}

// Gestion de la connexion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_btn'])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (checkLogin($conn, $email, $password)) {
            $_SESSION["loggedin"] = true;
            $_SESSION["email"] = $email;
            // Connexion réussie, rediriger vers menu.php
            header("Location: menu.php");
            exit;
        } else {
            $error_message = "Identifiants incorrects.";
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
            text-align: center;
            padding: 50px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label, input {
            display: block;
            margin: auto;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    if (!empty($error_message)) {
        echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
    }
    if (!empty($success_message)) {
        echo '<p class="success">' . htmlspecialchars($success_message) . '</p>';
    }
    ?>
    <h3>1) Inscrivez-vous en écrivant votre email <br>et créez un mot de passe</h3>
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="register">S'inscrire</button>
    </form>

    <h3>2) Une fois que l'inscription est faite,</h3>
    <h3> re-notez ci-dessous votre email et mot de passe <br> pour rentrer dans l'application.</h3>
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="login_btn">Se connecter</button>
    </form>
</div>
</body>
</html>





