<?php
session_start();

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Charger les variables d'environnement (similaire à os.getenv en Python)
$host = getenv('MYSQL_ADDON_HOST') ?: 'localhost'; // Valeur par défaut si non définie
$user = getenv('MYSQL_ADDON_USER') ?: 'root';
$password = getenv('MYSQL_ADDON_PASSWORD') ?: 'root';
$database = getenv('MYSQL_ADDON_DB') ?: 'cours';
$port = getenv('MYSQL_ADDON_PORT') ?: 3306;

// Connexion à la base de données avec PDO
try {
    $dsn = "mysql:host=$host;dbname=$database;port=$port;charset=utf8";
    $db = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $message = "Connexion à la base de données réussie !";
    error_log($message);
} catch (PDOException $e) {
    $message = "Erreur de connexion : " . $e->getMessage();
    error_log($message);
    die($message);
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->execute([
            ':username' => $username,
            ':password' => $password // Attention : en production, utiliser un hash sécurisé (ex. password_hash)
        ]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $message = "Vous êtes maintenant connecté.";
            error_log("Connexion réussie pour $username");
        } else {
            $message = "Identifiant ou mot de passe incorrect.";
            error_log("Échec de connexion pour $username");
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de la vérification : " . $e->getMessage();
        error_log($message);
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    $message = "Vous avez été déconnecté.";
    error_log($message);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            color: #333;
        }
        .logout {
            margin-top: 10px;
            display: inline-block;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Test de Connexion</h2>
        <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) : ?>
            <form method="POST" action="test_connexion.php">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>
        <?php else : ?>
            <p>Bienvenue, <?= htmlspecialchars($_SESSION['username']); ?> !</p>
            <a href="test_connexion.php?logout=1" class="logout">Se déconnecter</a>
        <?php endif; ?>
        <div class="message">
            <?= htmlspecialchars($message); ?>
        </div>
    </div>
</body>
</html>
