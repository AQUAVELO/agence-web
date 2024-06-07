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
$settings['mjusername'] = "3fa4567226e2b0b497f13a566724f340";
$settings['mjpassword'] = "2b43a31333dfa67f915940b19ae219a9";
$settings['mjfrom'] = "claude@alesiaminceur.com";

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

// Fonction pour envoyer un email de remerciement

function sendThankYouEmail($email, $settings) {
    $mj = new \Mailjet\Client($settings['mjusername'], $settings['mjpassword'], true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $settings['mjfrom'],
                    'Name' => "Aquavelo"
                ],
                'To' => [
                    [
                        'Email' => $email,
                        'Name' => "Nouveau Utilisateur"
                    ]
                ],
                'Subject' => "Merci pour votre inscription",
                'TextPart' => "Merci pour votre inscription chez Aquavelo.",
                'HTMLPart' => "<h3>Merci pour votre inscription chez Aquavelo.</h3>"
            ]
        ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    if ($response->success()) {
        return true;
    } else {
        return "Erreur lors de l'envoi de l'email: " . $response->getReasonPhrase();
    }
}

// Fonction pour inscrire un nouvel utilisateur
function registerUser($conn, $email, $password, $settings) {
    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT COUNT(*) FROM mensurations WHERE email = ?");
    $stmt->bindParam(1, $email);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Rediriger vers menu.php si l'email existe déjà
        header("Location: menu.php");
        exit(); // Assurez-vous de sortir après la redirection
    } else {
        // Insérer un nouvel utilisateur
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO mensurations (email, password) VALUES (?, ?)");
        $stmt->bindParam(1, $email);
        $stmt->bindParam(2, $hashed_password);
        if ($stmt->execute()) {
            // Envoyer l'email de remerciement
            echo $email;
            $email_result = sendThankYouEmail("$email", $settings);
            if ($email_result === true) {
                return true;
            } else {
                return $email_result;
            }
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
        $registration_result = registerUser($conn, $email, $password, $settings);
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
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .info-box {
            border: 1px solid #000;
            padding: 20px;
            text-align: center;
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        .logo-container img {
            width: 250px;
        }
        .logo-container button {
            margin-left: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
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
        <div class="logo-container">
            <img src="images/content/LogoAQUASPORTMINCEUR.webp" alt="Logo AQUAVELO">
            <button onclick="window.location.href='https://www.aquavelo.com'">Retour Aquavélo</button>
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









