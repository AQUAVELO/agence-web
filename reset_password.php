<?php
session_start();
require '_settings.php';

use \Mailjet\Resources;

// Fonction pour générer un token unique
function generateToken() {
    return bin2hex(random_bytes(32));
}

// Fonction pour envoyer l'email de réinitialisation
function sendResetEmail($email, $token, $settings) {
    try {
        $mj = new \Mailjet\Client($settings['mjusername'], $settings['mjpassword'], true, ['version' => 'v3.1']);
        
        $resetLink = "https://aquavelo.com/new_password.php?token=" . $token;
        
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => 'claude@alesiaminceur.com',
                        'Name' => "AQUAVELO"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => "Utilisateur"
                        ]
                    ],
                    'Subject' => "Reinitialisation de votre mot de passe - Aquavelo",
                    'HTMLPart' => "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                            <div style='text-align: center; margin-bottom: 30px;'>
                                <img src='https://aquavelo.com/images/content/logo.png' alt='Aquavelo' style='max-width: 150px;'>
                            </div>
                            <h2 style='color: #00a8cc; text-align: center;'>Reinitialisation de mot de passe</h2>
                            <p style='font-size: 16px; color: #333;'>Bonjour,</p>
                            <p style='font-size: 16px; color: #333;'>Vous avez demande la reinitialisation de votre mot de passe pour votre compte Aquavelo.</p>
                            <p style='font-size: 16px; color: #333;'>Cliquez sur le bouton ci-dessous pour creer un nouveau mot de passe :</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{$resetLink}' style='background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 16px;'>
                                    Reinitialiser mon mot de passe
                                </a>
                            </div>
                            <p style='font-size: 14px; color: #666;'>Ce lien est valide pendant <strong>1 heure</strong>.</p>
                            <p style='font-size: 14px; color: #666;'>Si vous n'avez pas demande cette reinitialisation, ignorez simplement cet email.</p>
                            <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                            <p style='font-size: 12px; color: #999; text-align: center;'>
                                Cet email a ete envoye automatiquement par Aquavelo.<br>
                                Ne repondez pas a cet email.
                            </p>
                        </div>
                    "
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    } catch (Exception $e) {
        return false;
    }
}

$error_message = "";
$success_message = "";

// Créer la table password_resets si elle n'existe pas (au chargement de la page)
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL,
        expiry DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_token (token),
        INDEX idx_email (email)
    )");
} catch (PDOException $e) {
    // Table existe déjà ou autre erreur non bloquante
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Vérifier si l'email existe dans la base de données
            $stmt = $conn->prepare("SELECT * FROM mensurations WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() == 1) {
                $token = generateToken();
                $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

                // Supprimer les anciens tokens pour cet email
                try {
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                    $stmt->execute([$email]);
                } catch (PDOException $e) {
                    // Ignorer si la table n'existe pas encore
                }

                // Insérer le nouveau token
                $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
                $stmt->execute([$email, $token, $expiry]);

                if (sendResetEmail($email, $token, $settings)) {
                    $success_message = "Un email de réinitialisation a été envoyé à votre adresse email. Vérifiez également vos spams.";
                } else {
                    $error_message = "Erreur lors de l'envoi de l'email. Veuillez réessayer ou contacter le support.";
                }
            } else {
                // Pour des raisons de sécurité, on affiche le même message
                $success_message = "Si un compte existe avec cette adresse email, vous recevrez un lien de réinitialisation.";
            }
        } catch (PDOException $e) {
            $error_message = "Erreur technique. Veuillez réessayer plus tard.";
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
    <title>Mot de passe oublié - Aquavelo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            padding: 20px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .reset-container {
            max-width: 450px;
            width: 100%;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .reset-header img {
            max-width: 180px;
            margin-bottom: 20px;
        }
        
        .reset-header h1 {
            color: #00a8cc;
            font-size: 1.6rem;
            margin-bottom: 10px;
        }
        
        .reset-header p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #00d4ff;
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.2);
        }
        
        .input-group-addon {
            background: #f5f5f5;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 8px 0 0 8px;
            padding: 12px 15px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: #ffebee;
            border: 2px solid #f44336;
            color: #c62828;
        }
        
        .alert-success {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            color: #2e7d32;
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(0, 168, 204, 0.4);
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 168, 204, 0.5);
            color: white;
        }
        
        .footer-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        
        .footer-links a {
            color: #00a8cc;
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-block;
            margin: 5px 0;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .reset-container {
                margin: 0 15px;
                padding: 30px 20px;
            }
            
            .reset-header h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>

<div class="reset-container">
    <!-- Header -->
    <div class="reset-header">
        <a href="https://www.aquavelo.com">
            <img src="/images/content/logo.png" alt="Aquavelo" onerror="this.style.display='none'">
        </a>
        <h1><i class="fa fa-unlock-alt"></i> Mot de passe oublié</h1>
        <p>Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
    </div>

    <!-- Message d'erreur -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <!-- Message de succès -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($success_message)): ?>
    <!-- Formulaire -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Adresse email</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="votre@email.com" required>
            </div>
        </div>

        <button type="submit" class="btn btn-reset">
            <i class="fa fa-paper-plane"></i> Envoyer le lien de réinitialisation
        </button>
    </form>
    <?php endif; ?>

    <!-- Footer Links -->
    <div class="footer-links">
        <a href="/connexion_mensurations.php">
            <i class="fa fa-arrow-left"></i> Retour à la connexion
        </a>
        <br>
        <a href="https://www.aquavelo.com">
            <i class="fa fa-home"></i> Retour au site Aquavelo
        </a>
    </div>
</div>

</body>
</html>
