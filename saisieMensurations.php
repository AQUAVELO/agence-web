<?php
session_start();

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
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Variables pour les messages
$success_message = '';
$error_message = '';

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et validation des données
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $age = intval($_POST['age'] ?? 0);
    $poids = floatval($_POST['poids'] ?? 0);
    $taille = floatval($_POST['taille'] ?? 0);
    $trtaille = floatval($_POST['trtaille'] ?? 0);
    $trhanches = floatval($_POST['trhanches'] ?? 0);
    $trfesses = floatval($_POST['trfesses'] ?? 0);

    // Validation des champs obligatoires
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $error_message = "Tous les champs marqués * sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "L'adresse email n'est pas valide.";
    } elseif (strlen($password) < 6) {
        $error_message = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        try {
            // Vérifier si l'email existe déjà
            $check_sql = "SELECT email FROM mensurations WHERE email = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->execute([$email]);
            
            if ($check_stmt->rowCount() > 0) {
                $error_message = "Cette adresse email est déjà utilisée. <a href='connexion_mensurations.php'>Se connecter</a>";
            } else {
                // Hash du mot de passe
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertion dans la base de données
                $insert_sql = "INSERT INTO mensurations (Nom, Prenom, email, password, Age, Poids, Taille, Trtaille, Trhanches, Trfesses) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                
                if ($insert_stmt->execute([$nom, $prenom, $email, $password_hash, $age, $poids, $taille, $trtaille, $trhanches, $trfesses])) {
                    // Connexion automatique après inscription
                    $_SESSION["loggedin"] = true;
                    $_SESSION["email"] = $email;
                    $_SESSION["nom"] = $nom;
                    $_SESSION["prenom"] = $prenom;
                    
                    // Redirection vers le dashboard
                    header("Location: _menu.php");
                    exit;
                } else {
                    $error_message = "Erreur lors de l'inscription. Veuillez réessayer.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Erreur : " . $e->getMessage();
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
    <title>Inscription - Suivi des Mensurations | Aquavelo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            padding: 20px 0;
            min-height: 100vh;
        }
        
        .inscription-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4caf50;
        }
        
        .page-header h1 {
            color: #2e7d32;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .page-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .section-title {
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 30px 0 20px 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .section-title i {
            margin-right: 8px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }
        
        .form-group label .required {
            color: #f44336;
            margin-left: 3px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4caf50;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.2);
        }
        
        .input-group-addon {
            background: #f5f5f5;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 8px 0 0 8px;
            padding: 12px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        
        .help-block {
            color: #999;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #00d4ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .info-box i {
            color: #00a8cc;
            margin-right: 8px;
            font-size: 1.2rem;
        }
        
        .info-box p {
            margin: 0;
            color: #666;
            line-height: 1.6;
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 25px;
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
        
        .btn-inscription {
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(76, 175, 80, 0.4);
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-inscription:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.5);
            color: white;
        }
        
        .btn-retour {
            background: transparent;
            color: #666;
            border: 2px solid #e0e0e0;
            padding: 12px 30px;
            font-size: 1rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
            margin-top: 15px;
        }
        
        .btn-retour:hover {
            border-color: #4caf50;
            color: #4caf50;
            text-decoration: none;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            color: #999;
        }
        
        .footer-note a {
            color: #4caf50;
            text-decoration: none;
            font-weight: 600;
        }
        
        .footer-note a:hover {
            text-decoration: underline;
        }
        
        .security-badge {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .security-badge i {
            color: #4caf50;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .security-badge p {
            margin: 0;
            color: #2e7d32;
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .inscription-container {
                padding: 25px 20px;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .section-title {
                font-size: 1.1rem;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>

<div class="inscription-container">
    <!-- Header -->
    <div class="page-header">
        <h1><i class="fa fa-user-plus"></i> Inscription Gratuite</h1>
        <p>Créez votre compte en 2 minutes et commencez à suivre vos progrès !</p>
    </div>

    <!-- Security Badge -->
    <div class="security-badge">
        <i class="fa fa-shield"></i>
        <p><i class="fa fa-lock"></i> Vos données sont 100% sécurisées et confidentielles</p>
    </div>

    <!-- Messages -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire -->
    <form method="POST" action="" id="inscriptionForm">
        
        <!-- Section 1 : Informations Personnelles -->
        <div class="section-title">
            <i class="fa fa-user"></i> 1. Informations Personnelles
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input type="text" class="form-control" id="nom" name="nom" required 
                           placeholder="Votre nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="prenom">Prénom <span class="required">*</span></label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required 
                           placeholder="Votre prénom" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="votre@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <small class="help-block">Utilisé pour vous connecter</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password">Mot de passe <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Minimum 6 caractères" minlength="6">
                    </div>
                    <small class="help-block">Minimum 6 caractères</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="age">Âge <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-birthday-cake"></i></span>
                        <input type="number" class="form-control" id="age" name="age" required 
                               placeholder="Votre âge" min="16" max="120" value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2 : Mensurations Initiales -->
        <div class="section-title">
            <i class="fa fa-calculator"></i> 2. Vos Mensurations Actuelles
        </div>

        <div class="info-box">
            <i class="fa fa-info-circle"></i>
            <p><strong>Conseil :</strong> Prenez vos mesures le matin à jeun pour plus de précision. Vous pourrez les mettre à jour régulièrement.</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="poids">Poids (kg) <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-balance-scale"></i></span>
                        <input type="number" class="form-control" id="poids" name="poids" required 
                               placeholder="Ex: 70.5" step="0.1" min="30" max="300" value="<?php echo htmlspecialchars($_POST['poids'] ?? ''); ?>">
                        <span class="input-group-addon">kg</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="taille">Taille (cm) <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-arrows-v"></i></span>
                        <input type="number" class="form-control" id="taille" name="taille" required 
                               placeholder="Ex: 165" step="0.1" min="100" max="250" value="<?php echo htmlspecialchars($_POST['taille'] ?? ''); ?>">
                        <span class="input-group-addon">cm</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="trtaille">Tour de Taille (cm) <span class="required">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="trtaille" name="trtaille" required 
                               placeholder="Ex: 75" step="0.1" min="40" max="200" value="<?php echo htmlspecialchars($_POST['trtaille'] ?? ''); ?>">
                        <span class="input-group-addon">cm</span>
                    </div>
                    <small class="help-block">Au niveau du nombril</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="trhanches">Tour de Hanches (cm) <span class="required">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="trhanches" name="trhanches" required 
                               placeholder="Ex: 95" step="0.1" min="40" max="200" value="<?php echo htmlspecialchars($_POST['trhanches'] ?? ''); ?>">
                        <span class="input-group-addon">cm</span>
                    </div>
                    <small class="help-block">À l'endroit le plus large</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="trfesses">Tour de Fesses (cm) <span class="required">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="trfesses" name="trfesses" required 
                               placeholder="Ex: 100" step="0.1" min="40" max="200" value="<?php echo htmlspecialchars($_POST['trfesses'] ?? ''); ?>">
                        <span class="input-group-addon">cm</span>
                    </div>
                    <small class="help-block">À l'endroit le plus bombé</small>
                </div>
            </div>
        </div>

        <!-- Bouton de soumission -->
        <div style="margin-top: 40px;">
            <button type="submit" class="btn btn-inscription">
                <i class="fa fa-check-circle"></i> Créer Mon Compte Gratuit
            </button>
        </div>

        <div style="text-align: center;">
            <a href="/suivi-mensurations" class="btn-retour">
                <i class="fa fa-arrow-left"></i> Retour
            </a>
        </div>
    </form>

    <!-- Footer Note -->
    <div class="footer-note">
        <p>Vous avez déjà un compte ? <a href="connexion_mensurations.php"><i class="fa fa-sign-in"></i> Se connecter</a></p>
        <p style="margin-top: 10px; font-size: 0.85rem;">
            <i class="fa fa-lock"></i> En vous inscrivant, vos données sont chiffrées et sécurisées.<br>
            Service 100% gratuit • Sans engagement • Données privées
        </p>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-26LRGBE9X2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-26LRGBE9X2');
  
  // Track page view
  gtag('event', 'page_view', {
    'page_title': 'Inscription Mensurations',
    'page_location': window.location.href
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('inscriptionForm');
    
    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        // Validation email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Veuillez entrer une adresse email valide.');
            return false;
        }
        
        // Validation mot de passe
        if (password.length < 6) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 6 caractères.');
            return false;
        }
        
        // Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'submit_inscription', {
                'event_category': 'conversion',
                'event_label': 'mensurations_signup'
            });
        }
        
        return true;
    });
    
    // Calcul IMC en temps réel (optionnel)
    const poidsInput = document.getElementById('poids');
    const tailleInput = document.getElementById('taille');
    
    function calculateIMC() {
        const poids = parseFloat(poidsInput.value);
        const taille = parseFloat(tailleInput.value) / 100; // Convertir en mètres
        
        if (poids > 0 && taille > 0) {
            const imc = (poids / (taille * taille)).toFixed(2);
            console.log('IMC estimé:', imc);
        }
    }
    
    poidsInput.addEventListener('input', calculateIMC);
    tailleInput.addEventListener('input', calculateIMC);
});
</script>

</body>
</html>

