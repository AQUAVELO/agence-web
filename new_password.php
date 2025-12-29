<?php
session_start();
require '_settings.php';

$error_message = "";
$success_message = "";
$token_valid = false;
$token = $_GET['token'] ?? '';

// Vérifier si le token est valide
if (!empty($token)) {
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expiry > NOW()");
    $stmt->execute([$token]);
    
    if ($stmt->rowCount() == 1) {
        $token_valid = true;
        $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error_message = "Ce lien de réinitialisation est invalide ou a expiré.";
    }
} else {
    $error_message = "Aucun token de réinitialisation fourni.";
}

// Traitement du formulaire de nouveau mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valid) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($password) || empty($password_confirm)) {
        $error_message = "Veuillez remplir tous les champs.";
    } elseif (strlen($password) < 6) {
        $error_message = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $password_confirm) {
        $error_message = "Les mots de passe ne correspondent pas.";
    } else {
        // Mettre à jour le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE mensurations SET password = ? WHERE email = ?");
        $stmt->execute([$hashed_password, $reset_data['email']]);
        
        // Supprimer le token utilisé
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$reset_data['email']]);
        
        $success_message = "Votre mot de passe a été réinitialisé avec succès !";
        $token_valid = false; // Cache le formulaire
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - Aquavelo</title>
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
        
        .btn-connexion {
            background: #4caf50;
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(76, 175, 80, 0.4);
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }
        
        .btn-connexion:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.5);
            color: white;
            text-decoration: none;
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
        
        .password-requirements {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.85rem;
            color: #666;
        }
        
        .password-requirements i {
            color: #00a8cc;
            margin-right: 5px;
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
        <h1><i class="fa fa-key"></i> Nouveau mot de passe</h1>
        <?php if ($token_valid): ?>
            <p>Choisissez un nouveau mot de passe pour votre compte.</p>
        <?php endif; ?>
    </div>

    <!-- Message d'erreur -->
    <?php if ($error_message && !$success_message): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <!-- Message de succès -->
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
        </div>
        <a href="/connexion_mensurations.php" class="btn-connexion">
            <i class="fa fa-sign-in"></i> Se connecter
        </a>
    <?php elseif ($token_valid): ?>

    <!-- Formulaire -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="password">Nouveau mot de passe</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Votre nouveau mot de passe" required minlength="6">
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                       placeholder="Confirmez votre mot de passe" required minlength="6">
            </div>
            <div class="password-requirements">
                <i class="fa fa-info-circle"></i> Le mot de passe doit contenir au moins 6 caractères.
            </div>
        </div>

        <button type="submit" class="btn btn-reset">
            <i class="fa fa-check"></i> Enregistrer le nouveau mot de passe
        </button>
    </form>
    <?php endif; ?>

    <!-- Footer Links -->
    <div class="footer-links">
        <?php if (!$success_message): ?>
            <a href="/reset_password.php">
                <i class="fa fa-refresh"></i> Demander un nouveau lien
            </a>
            <br>
        <?php endif; ?>
        <a href="/connexion_mensurations.php">
            <i class="fa fa-arrow-left"></i> Retour à la connexion
        </a>
        <br>
        <a href="https://www.aquavelo.com">
            <i class="fa fa-home"></i> Retour au site Aquavelo
        </a>
    </div>
</div>

<script>
// Validation côté client
document.querySelector('form')?.addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirm').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas.');
    }
});
</script>

</body>
</html>

