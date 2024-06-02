<?php
session_start();

// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Mensurations";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fonction pour vérifier les identifiants
function checkLogin($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT password FROM mensurations WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return password_verify($password, $row['password']);
    }
    return false;
}

// Fonction pour inscrire un nouvel utilisateur
function registerUser($conn, $email, $password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO mensurations (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashed_password);
    if ($stmt->execute()) {
        return true;
    } else {
        if ($stmt->errno == 1062) {
            return "Email déjà utilisé.";
        } else {
            return "Erreur lors de l'inscription: " . $stmt->error;
        }
    }
}

$error_message = "";

// Gestion de l'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_result = registerUser($conn, $email, $password);
        if ($registration_result === true) {
            $error_message = "Inscription réussie. Vous pouvez maintenant vous connecter.";
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
            header("Location: menu.php");
            exit;
        } else {
            $error_message = "Identifiants incorrects.";
        }
    } else {
        $error_message = "Email invalide.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription et Connexion</title>
    <style>
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
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    if (!empty($error_message)) {
        echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
    }
    ?>
    <h2>1) Inscrivez vous en écrivant votre email <br>et créez un mot de passe</h2>
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

    <h2>2) Une fois que l'inscription est faite,</h2>
    <h3> re-notez ci dessous votre email et mot de passe <br> pour rentrer dans l'application.</h3>
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


