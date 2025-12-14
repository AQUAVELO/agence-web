<?php
session_start();

// Si déjà connecté, rediriger vers le dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: _menu.php");
    exit;
}

// Configuration de la base de données
require 'vendor/autoload.php';

// Paramètres de configuration
$settings = [];
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
    die("Erreur de connexion à la base de données.");
}

// Variables pour les messages
$error_message = '';

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_message = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Rechercher l'utilisateur
            $sql = "SELECT * FROM mensurations WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie
                $_SESSION["loggedin"] = true;
                $_SESSION["email"] = $user['email'];
                $_SESSION["nom"] = $user['Nom'];
                $_SESSION["prenom"] = $user['Prenom'];

                // Redirection vers le dashboard
                header("Location: _menu.php");
                exit;
            } else {
                $error_message = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $error_message = "Erreur lors de la connexion.";
        }
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Suivi des Mensurations | Aquavelo</title>
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
        
        .login-container {
            max-width: 450px;
            width: 100%;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .login-header img {
            max-width: 180px;
            margin-bottom: 20px;
        }
        
        .login-header h1 {
            color: #00a8cc;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 1rem;
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
        
        .alert-danger {
            background: #ffebee;
            border: 2px solid #f44336;
            color: #c62828;
            border-radius: 8px;
            padding: 12px 15px;
        }
        
        .btn-connexion {
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
        
        .btn-connexion:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 168, 204, 0.5);
            color: white;
        }
        
        .divider {
            text-align: center;
            margin: 30px 0 25px;
            position: relative;
        }
        
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #999;
            font-size: 0.9rem;
        }
        
        .btn-inscription {
            background: white;
            color: #4caf50;
            border: 2px solid #4caf50;
            padding: 15px;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-inscription:hover {
            background: #4caf50;
            color: white;
            text-decoration: none;
        }
        
        .footer-links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        
        .footer-links a {
            color: #00a8cc;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .security-note {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
        }
        
        .security-note i {
            color: #4caf50;
            margin-right: 5px;
        }
        
        .security-note p {
            margin: 0;
            color: #2e7d32;
            font-size: 0.9rem;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: 10px;
        }
        
        .forgot-password a {
            color: #999;
            font-size: 0.9rem;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            color: #00a8cc;
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                margin: 0 15px;
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Header -->
    <div class="login-header">
        <a href="https://www.aquavelo.com">
            <img src="/images/content/logo.png" alt="Aquavelo" onerror="this.style.display='none'">
        </a>
        <h1><i class="fa fa-sign-in"></i> Connexion</h1>
        <p>Accédez à votre suivi des mensurations</p>
    </div>

    <!-- Message d'erreur -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form method="POST" action="" id="loginForm">
        <div class="form-group">
            <label for="email">Email</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="votre@email.com" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Votre mot de passe" required>
            </div>
            <div class="forgot-password">
                <a href="#" onclick="alert('Contactez le support : claude@alesiaminceur.com'); return false;">
                    Mot de passe oublié ?
                </a>
            </div>
        </div>

        <button type="submit" class="btn btn-connexion">
            <i class="fa fa-sign-in"></i> Se Connecter
        </button>
    </form>

    <!-- Divider -->
    <div class="divider">
        <span>OU</span>
    </div>

    <!-- Bouton Inscription -->
    <a href="saisieMensurations.php" class="btn btn-inscription">
        <i class="fa fa-user-plus"></i> Créer un Compte Gratuit
    </a>

    <!-- Security Note -->
    <div class="security-note">
        <i class="fa fa-lock"></i>
        <p>Connexion sécurisée • Données chiffrées</p>
    </div>

    <!-- Footer Links -->
    <div class="footer-links">
        <a href="/suivi-mensurations">
            <i class="fa fa-arrow-left"></i> Retour à la présentation
        </a>
        <br>
        <a href="https://www.aquavelo.com" style="margin-top: 10px; display: inline-block;">
            <i class="fa fa-home"></i> Retour au site Aquavelo
        </a>
    </div>
</div>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-26LRGBE9X2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-26LRGBE9X2');
  
  // Track page view
  gtag('event', 'page_view', {
    'page_title': 'Connexion Mensurations',
    'page_location': window.location.href
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    
    // Track form submission
    form.addEventListener('submit', function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'login_attempt', {
                'event_category': 'engagement',
                'event_label': 'mensurations_login'
            });
        }
    });
    
    // Track click on inscription button
    document.querySelector('.btn-inscription').addEventListener('click', function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click_signup_from_login', {
                'event_category': 'conversion',
                'event_label': 'from_login_page'
            });
        }
    });
});
</script>

</body>
</html>
