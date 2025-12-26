<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: connexion_mensurations.php");
    exit;
}

// Lire l'email depuis la session
$email = $_SESSION["email"];

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

// Récupérer les informations de l'utilisateur pour pré-remplir
$sql = "SELECT * FROM mensurations WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer la dernière mensuration
$sql_last = "SELECT * FROM suivie WHERE email = ? ORDER BY Date DESC LIMIT 1";
$stmt_last = $conn->prepare($sql_last);
$stmt_last->execute([$email]);
$lastMensuration = $stmt_last->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et validation des données
    $date = $_POST['date'] ?? date('Y-m-d');
    $poids = floatval($_POST['poids'] ?? 0);
    $trtaille = floatval($_POST['trtaille'] ?? 0);
    $trhanches = floatval($_POST['trhanches'] ?? 0);
    $trfesses = floatval($_POST['trfesses'] ?? 0);

    // Validation des champs obligatoires
    if (empty($date) || $poids <= 0) {
        $error_message = "La date et le poids sont obligatoires.";
    } else {
        try {
            // Insertion dans la base de données
            $insert_sql = "INSERT INTO suivie (email, Date, Poids, Trtaille, Trhanches, Trfesses) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            
            if ($insert_stmt->execute([$email, $date, $poids, $trtaille, $trhanches, $trfesses])) {
                $success_message = "Vos mensurations ont été enregistrées avec succès !";
                
                // Calculer l'IMC pour afficher un message personnalisé
                if ($userInfo && $userInfo["Taille"] > 0) {
                    $taille = $userInfo["Taille"];
                    $imc = $poids / (($taille / 100) * ($taille / 100));
                    $imc = round($imc, 2);
                    
                    if ($imc < 20) {
                        $success_message .= " Votre IMC est de " . $imc . ". Pensez à bien vous nourrir.";
                    } elseif ($imc > 25) {
                        $success_message .= " Votre IMC est de " . $imc . ". Continuez vos efforts !";
                    } else {
                        $success_message .= " Votre IMC est de " . $imc . ". Félicitations, continuez ainsi !";
                    }
                }
                
                // Analytics tracking
                if (function_exists('gtag')) {
                    echo "<script>
                        gtag('event', 'mensuration_saved', {
                            'event_category': 'engagement',
                            'event_label': 'suivi_mensurations'
                        });
                    </script>";
                }
            } else {
                $error_message = "Erreur lors de l'enregistrement. Veuillez réessayer.";
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
    <title>Nouveau Suivi - Mensurations | Aquavelo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            padding: 20px 0;
            min-height: 100vh;
        }
        
        .suivi-container {
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
        
        .user-badge {
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        
        .user-badge i {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        
        .user-badge strong {
            font-size: 1.2rem;
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
        
        .last-mensuration-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .last-mensuration-box h4 {
            color: #e65100;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .last-mensuration-box .value {
            display: inline-block;
            background: white;
            padding: 5px 12px;
            border-radius: 5px;
            margin: 5px 5px 5px 0;
            font-weight: 600;
            color: #e65100;
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
        
        .btn-enregistrer {
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
        
        .btn-enregistrer:hover {
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
        
        .progress-badge {
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .progress-badge i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .progress-badge p {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .suivi-container {
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

<div class="suivi-container">
    <!-- Header -->
    <div class="page-header">
        <h1><i class="fa fa-line-chart"></i> Nouveau Suivi</h1>
        <p>Enregistrez vos nouvelles mensurations et suivez votre progression !</p>
    </div>

    <!-- User Badge -->
    <div class="user-badge">
        <i class="fa fa-user-circle"></i>
        <strong><?php echo htmlspecialchars($userInfo["Prenom"] ?? '') . " " . htmlspecialchars($userInfo["Nom"] ?? ''); ?></strong>
    </div>

    <!-- Progress Badge -->
    <div class="progress-badge">
        <i class="fa fa-trophy"></i>
        <p>Chaque mesure est un pas de plus vers vos objectifs !</p>
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

    <!-- Dernières mensurations -->
    <?php if ($lastMensuration): ?>
    <div class="last-mensuration-box">
        <h4><i class="fa fa-history"></i> Vos dernières mensurations (<?php echo date("d/m/Y", strtotime($lastMensuration['Date'])); ?>)</h4>
        <span class="value"><i class="fa fa-balance-scale"></i> <?php echo htmlspecialchars($lastMensuration['Poids']); ?> kg</span>
        <span class="value"><i class="fa fa-arrows-h"></i> Taille: <?php echo htmlspecialchars($lastMensuration['Trtaille']); ?> cm</span>
        <span class="value"><i class="fa fa-arrows-h"></i> Hanches: <?php echo htmlspecialchars($lastMensuration['Trhanches']); ?> cm</span>
        <span class="value"><i class="fa fa-arrows-h"></i> Fesses: <?php echo htmlspecialchars($lastMensuration['Trfesses']); ?> cm</span>
    </div>
    <?php endif; ?>

    <!-- Formulaire -->
    <form method="POST" action="" id="suiviForm">
        
        <!-- Section : Date -->
        <div class="section-title">
            <i class="fa fa-calendar"></i> Date de la mesure
        </div>

        <div class="form-group">
            <label for="date">Date <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control" id="date" name="date" required 
                       value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
            </div>
            <small class="help-block">Par défaut : aujourd'hui</small>
        </div>

        <!-- Section : Mensurations -->
        <div class="section-title">
            <i class="fa fa-calculator"></i> Vos Nouvelles Mensurations
        </div>

        <div class="info-box">
            <i class="fa fa-lightbulb-o"></i>
            <p><strong>Conseil :</strong> Prenez vos mesures le matin à jeun, toujours au même moment de la journée pour une meilleure comparaison.</p>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="poids">Poids (kg) <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-balance-scale"></i></span>
                        <input type="number" class="form-control" id="poids" name="poids" required 
                               placeholder="Ex: 70.5" step="0.1" min="30" max="300" 
                               value="<?php echo htmlspecialchars($lastMensuration['Poids'] ?? ''); ?>">
                        <span class="input-group-addon">kg</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="trtaille">Tour de Taille (cm)</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="trtaille" name="trtaille" 
                               placeholder="Ex: 75" step="0.1" min="40" max="200" 
                               value="<?php echo htmlspecialchars($lastMensuration['Trtaille'] ?? ''); ?>">
                        <span class="input-group-addon">cm</span>
                    </div>
                    <small class="help-block">Au niveau du nombril</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="trhanches">Tour de Hanches (cm)</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="trhanches" name="trhanches" 
                               placeholder="Ex: 95" step="0.1" min="40" max="200" 
                               value="<?php echo htmlspecialchars($lastMensuration['Trhanches'] ?? ''); ?>">
                        <span class="input-group-addon">cm</span>
                    </div>
                    <small class="help-block">À l'endroit le plus large</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="trfesses">Tour de Fesses (cm)</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="trfesses" name="trfesses" 
                               placeholder="Ex: 100" step="0.1" min="40" max="200" 
                               value="<?php echo htmlspecialchars($lastMensuration['Trfesses'] ?? ''); ?>">
                        <span class="input-group-addon">cm</span>
                    </div>
                    <small class="help-block">À l'endroit le plus bombé</small>
                </div>
            </div>
        </div>

        <!-- Calcul IMC en temps réel -->
        <div id="imc-preview" style="display:none;" class="info-box">
            <p><strong><i class="fa fa-calculator"></i> IMC estimé :</strong> <span id="imc-value">0</span></p>
            <p id="imc-message"></p>
        </div>

        <!-- Bouton de soumission -->
        <div style="margin-top: 40px;">
            <button type="submit" class="btn btn-enregistrer">
                <i class="fa fa-save"></i> Enregistrer Mes Mensurations
            </button>
        </div>

        <div style="text-align: center;">
            <a href="_menu.php" class="btn-retour">
                <i class="fa fa-arrow-left"></i> Retour au Tableau de Bord
            </a>
        </div>
    </form>

    <!-- Footer Note -->
    <div class="footer-note">
        <p><i class="fa fa-info-circle"></i> Vos données sont sauvegardées de manière sécurisée</p>
        <p style="margin-top: 10px;">
            <a href="_menu.php"><i class="fa fa-bar-chart"></i> Voir mon historique complet</a>
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
    'page_title': 'Suivi Mensurations',
    'page_location': window.location.href
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('suiviForm');
    const poidsInput = document.getElementById('poids');
    const imcPreview = document.getElementById('imc-preview');
    const imcValue = document.getElementById('imc-value');
    const imcMessage = document.getElementById('imc-message');
    
    // Taille de l'utilisateur (depuis PHP)
    const taille = <?php echo $userInfo['Taille'] ?? 0; ?>;
    
    // Calcul IMC en temps réel
    function calculateIMC() {
        const poids = parseFloat(poidsInput.value);
        
        if (poids > 0 && taille > 0) {
            const imc = (poids / ((taille / 100) * (taille / 100))).toFixed(2);
            imcValue.textContent = imc;
            
            // Message personnalisé selon l'IMC
            let message = '';
            let color = '';
            
            if (imc < 20) {
                message = 'Vous êtes en insuffisance pondérale. Pensez à bien vous nourrir.';
                color = '#ff9800';
            } else if (imc > 25) {
                message = 'Vous êtes en surcharge pondérale. Continuez vos efforts !';
                color = '#ff5722';
            } else {
                message = 'Félicitations ! Vous avez un IMC normal. Continuez ainsi !';
                color = '#4caf50';
            }
            
            imcMessage.textContent = message;
            imcMessage.style.color = color;
            imcPreview.style.display = 'block';
        } else {
            imcPreview.style.display = 'none';
        }
    }
    
    // Calculer IMC au chargement si poids déjà rempli
    if (poidsInput.value) {
        calculateIMC();
    }
    
    // Calculer IMC à chaque modification du poids
    poidsInput.addEventListener('input', calculateIMC);
    
    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        const poids = parseFloat(poidsInput.value);
        const date = document.getElementById('date').value;
        
        if (!date) {
            e.preventDefault();
            alert('Veuillez sélectionner une date.');
            return false;
        }
        
        if (poids <= 0 || poids > 300) {
            e.preventDefault();
            alert('Veuillez entrer un poids valide (entre 30 et 300 kg).');
            return false;
        }
        
        // Confirmation avant soumission
        const confirmation = confirm('Êtes-vous sûr de vouloir enregistrer ces mensurations ?');
        if (!confirmation) {
            e.preventDefault();
            return false;
        }
        
        return true;
    });
    
    // Limiter la date à aujourd'hui ou avant
    const dateInput = document.getElementById('date');
    dateInput.max = new Date().toISOString().split('T')[0];
});
</script>

</body>
</html>
