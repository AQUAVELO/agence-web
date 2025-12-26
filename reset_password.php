<?php
session_start();
require 'vendor/autoload.php';
require 'settings.php';

use \Mailjet\Resources;

// Fonction pour générer un token unique
function generateToken() {
    return bin2hex(random_bytes(50));
}

// Fonction pour envoyer l'email de réinitialisation
function sendResetEmail($email, $token) {
    global $settings;

    $mj = new \Mailjet\Client($settings['mjusername'], $settings['mjpassword'], true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $settings['mjfrom'],
                    'Name' => "AQUAVELO"
                ],
                'To' => [
                    [
                        'Email' => $email,
                        'Name' => "User"
                    ]
                ],
                'Subject' => "Ré-initialisation de mot de passe",
                'HTMLPart' => "<h3>Bonjour,</h3><br /><p>Pour ré-initialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous :</p><br /><a href='https://votre-site.com/new_password.php?token={$token}'>Ré-initialiser le mot de passe</a>"
            ]
        ]
    ];

    $response = $mj->post(Resources::$Email, ['body' => $body]);
    return $response->success();
}

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Vérifier si l'email existe dans la base de données
        $stmt = $conn->prepare("SELECT * FROM mensurations WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() == 1) {
            $token = generateToken();
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Insérer le token dans la base de données
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expiry]);

            if (sendResetEmail($email, $token)) {
                $success_message = "Un email de ré-initialisation a été envoyé à votre adresse email.";
            } else {
                $error_message = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
            }
        } else {
            $error_message = "Aucun compte trouvé avec cette adresse email.";
        }
    } else {
        $error_message = "Adresse email invalide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ré-initialisation du mot de passe</title>
    <style>
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            margin-top: 20px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ré-initialisation du mot de passe</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit">Envoyer le lien de ré-initialisation</button>
        </form>
        <div class="message">
            <?php
            if (!empty($error_message)) {
                echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
            }
            if (!empty($success_message)) {
                echo '<p class="success">' . htmlspecialchars($success_message) . '</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
