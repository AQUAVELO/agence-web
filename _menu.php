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
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Fonction pour obtenir l'historique COMPLET de suivi (mensurations initiales + suivie)
function getUserSuivi($conn, $email) {
    $allMensurations = [];
    
    // 1. Récupérer les mensurations initiales de la table mensurations
    $sql_init = "SELECT 
        'Initial' as id,
        email,
        created_at as Date,
        Poids,
        Trtaille,
        Trhanches,
        Trfesses
    FROM mensurations 
    WHERE email = ?";
    $stmt_init = $conn->prepare($sql_init);
    $stmt_init->execute([$email]);
    $mensurationInitiale = $stmt_init->fetch(PDO::FETCH_ASSOC);
    
    // Ajouter les mensurations initiales si elles existent
    if ($mensurationInitiale && $mensurationInitiale['Date']) {
        $mensurationInitiale['source'] = 'initial';
        $allMensurations[] = $mensurationInitiale;
    }
    
    // 2. Récupérer l'historique de la table suivie
    $sql_suivi = "SELECT 
        id,
        email,
        Date,
        Poids,
        Trtaille,
        Trhanches,
        Trfesses
    FROM suivie 
    WHERE email = ? 
    ORDER BY Date ASC";
    $stmt_suivi = $conn->prepare($sql_suivi);
    $stmt_suivi->execute([$email]);
    $suiviData = $stmt_suivi->fetchAll(PDO::FETCH_ASSOC);
    
    // Ajouter les données de suivi
    foreach ($suiviData as $row) {
        $row['source'] = 'suivi';
        $allMensurations[] = $row;
    }
    
    // 3. Trier par date (du plus ancien au plus récent)
    usort($allMensurations, function($a, $b) {
        return strtotime($a['Date']) - strtotime($b['Date']);
    });
    
    // 4. Formater les dates pour l'affichage
    foreach ($allMensurations as &$row) {
        $row['DateOriginal'] = $row['Date']; // Garder la date originale pour le graphique
        $row['Date'] = date("d/m/Y", strtotime($row['Date']));
    }
    
    // Retourner en ordre inverse (plus récent en premier) pour l'affichage du tableau
    return array_reverse($allMensurations);
}

// Rechercher les informations de base de l'utilisateur
$sql = "SELECT * FROM mensurations WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Rechercher le dernier poids inséré dans la table suivie
$sql = "SELECT * FROM suivie WHERE email = ? ORDER BY Date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);
$lastSuivi = $stmt->fetch(PDO::FETCH_ASSOC);

// Utiliser le dernier poids si disponible, sinon utiliser le poids de base
$latestWeight = $lastSuivi ? $lastSuivi["Poids"] : $userInfo["Poids"];

// Calculer l'IMC et déterminer le message à afficher
$message = '';
$imcColor = '#666';
if ($userInfo) {
    $taille = $userInfo["Taille"];
    if ($latestWeight > 0 && $taille > 0) {
        $imc = $latestWeight / (($taille / 100) * ($taille / 100));
        $imc = round($imc, 2);

        if ($imc < 20) {
            $message = "Vous êtes en insuffisance pondérale. Pensez à bien vous nourrir.";
            $imcColor = '#ff9800';
        } elseif ($imc > 25) {
            $message = "Vous êtes en surcharge pondérale. Continuez vos efforts !";
            $imcColor = '#ff5722';
        } elseif ($imc >= 20 && $imc <= 25) {
            $message = "Félicitations, vous avez un IMC normal ! Continuez à vous entretenir.";
            $imcColor = '#4caf50';
        }
    } else {
        $imc = 0;
    }
}

// Obtenir l'historique de suivi pour l'utilisateur
$userSuivi = getUserSuivi($conn, $email);

// Calculer les statistiques
$totalMensurations = count($userSuivi);
$firstMensuration = !empty($userSuivi) ? end($userSuivi) : null;
$progressWeight = 0;
if ($firstMensuration && $latestWeight > 0) {
    $progressWeight = round($firstMensuration['Poids'] - $latestWeight, 2);
}

// Si une demande de suppression est effectuée
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $delete_id = $_POST["delete_id"];
    $delete_sql = "DELETE FROM suivie WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    if ($delete_stmt->execute([$delete_id])) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Erreur lors de la suppression de l'enregistrement.";
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Tableau de Bord - Suivi des Mensurations | Aquavelo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            padding: 0;
            margin: 0;
            min-height: 100vh;
        }
        
        /* Header */
        .dashboard-header {
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            padding: 30px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            margin-bottom: 30px;
        }
        
        .dashboard-header h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .dashboard-header p {
            margin: 0;
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .user-badge {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 15px;
            backdrop-filter: blur(10px);
        }
        
        .user-badge i {
            margin-right: 8px;
        }
        
        /* Navigation Buttons */
        .nav-buttons {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .nav-buttons a {
            background: white;
            color: #4caf50;
            border: 2px solid #4caf50;
            padding: 12px 25px;
            font-size: 1rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            margin: 5px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .nav-buttons a:hover {
            background: #4caf50;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        
        .nav-buttons a.btn-primary {
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            border: none;
        }
        
        .nav-buttons a.btn-danger {
            background: transparent;
            color: #f44336;
            border-color: #f44336;
        }
        
        .nav-buttons a.btn-danger:hover {
            background: #f44336;
            color: white;
        }
        
        /* Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }
        
        /* Stats Cards */
        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            flex: 1;
            min-width: 200px;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stat-card.green i { color: #4caf50; }
        .stat-card.blue i { color: #00d4ff; }
        .stat-card.orange i { color: #ff9800; }
        .stat-card.purple i { color: #9c27b0; }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }
        
        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 1rem;
        }
        
        /* User Info Section */
        .section-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            color: #2e7d32;
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0 0 20px 0;
            padding-bottom: 15px;
            border-bottom: 3px solid #4caf50;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        /* IMC Box */
        .imc-box {
            background: linear-gradient(135deg, #f5f5f5, #e8e8e8);
            border-left: 5px solid;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        
        .imc-box h3 {
            margin: 0 0 15px 0;
            font-size: 1.4rem;
            color: #333;
        }
        
        .imc-value {
            font-size: 3rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .imc-message {
            font-size: 1.1rem;
            margin: 10px 0 0 0;
            font-weight: 600;
        }
        
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .data-table th {
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            color: #666;
        }
        
        .data-table tr:hover {
            background: #f5f5f5;
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        /* History Table */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .history-table th {
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .history-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
        }
        
        .history-table tr:hover {
            background: #f5f5f5;
        }
        
        .delete-button {
            background: #f44336;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .delete-button:hover {
            background: #d32f2f;
            transform: scale(1.05);
        }
        
        /* Chart Container */
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        /* Footer */
        .dashboard-footer {
            text-align: center;
            margin-top: 40px;
            padding: 30px 0;
        }
        
        .dashboard-footer img {
            max-width: 200px;
            margin-bottom: 20px;
        }
        
        .app-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .app-links a {
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .app-links a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ccc;
        }
        
        .empty-state h3 {
            color: #666;
            margin-bottom: 15px;
        }
        
        .empty-state p {
            margin-bottom: 25px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stats-row {
                flex-direction: column;
            }
            
            .dashboard-header h1 {
                font-size: 1.5rem;
            }
            
            .nav-buttons a {
                display: block;
                margin: 10px auto;
                max-width: 300px;
            }
            
            .history-table {
                font-size: 0.85rem;
            }
            
            .history-table th,
            .history-table td {
                padding: 8px 5px;
            }
        }
        
        /* Alert */
        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: #ffebee;
            border: 2px solid #f44336;
            color: #c62828;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="dashboard-header">
    <div class="container">
        <h1><i class="fa fa-line-chart"></i> Mon Tableau de Bord</h1>
        <p>Suivez votre progression et atteignez vos objectifs</p>
        <?php if ($userInfo): ?>
        <div class="user-badge">
            <i class="fa fa-user-circle"></i>
            <strong><?php echo htmlspecialchars($userInfo["Prenom"]) . " " . htmlspecialchars($userInfo["Nom"]); ?></strong>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Navigation -->
<div class="nav-buttons">
    <a href="suivi.php" class="btn-primary">
        <i class="fa fa-plus-circle"></i> Nouvelle Mensuration
    </a>
    <a href="https://www.aquavelo.com" target="_blank">
        <i class="fa fa-home"></i> Retour au Site
    </a>
    <a href="index.php" class="btn-danger">
        <i class="fa fa-sign-out"></i> Se Déconnecter
    </a>
</div>

<!-- Main Container -->
<div class="main-container">

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card green">
            <i class="fa fa-balance-scale"></i>
            <h3><?php echo htmlspecialchars($latestWeight); ?> kg</h3>
            <p>Poids Actuel</p>
        </div>
        <div class="stat-card blue">
            <i class="fa fa-calculator"></i>
            <h3><?php echo $imc; ?></h3>
            <p>IMC Actuel</p>
        </div>
        <div class="stat-card orange">
            <i class="fa fa-history"></i>
            <h3><?php echo $totalMensurations; ?></h3>
            <p>Mensurations Enregistrées</p>
        </div>
        <?php if ($progressWeight != 0): ?>
        <div class="stat-card purple">
            <i class="fa fa-trophy"></i>
            <h3><?php echo $progressWeight > 0 ? '-' : '+'; ?><?php echo abs($progressWeight); ?> kg</h3>
            <p>Progression Totale</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- User Info Section -->
    <?php if ($userInfo): ?>
    <div class="section-card">
        <h2 class="section-title">
            <i class="fa fa-user"></i> Mes Informations
        </h2>
        
        <div class="row">
            <div class="col-md-8">
                <table class="data-table">
                    <tr>
                        <th style="width: 40%;">Information</th>
                        <th>Valeur</th>
                    </tr>
                    <tr>
                        <td><strong>Nom Complet</strong></td>
                        <td><?php echo htmlspecialchars($userInfo["Prenom"]) . " " . htmlspecialchars($userInfo["Nom"]); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td><?php echo htmlspecialchars($userInfo["email"]); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Âge</strong></td>
                        <td><?php echo htmlspecialchars($userInfo["Age"]); ?> ans</td>
                    </tr>
                    <tr>
                        <td><strong>Taille</strong></td>
                        <td><?php echo htmlspecialchars($userInfo["Taille"]); ?> cm</td>
                    </tr>
                    <tr>
                        <td><strong>Poids Actuel</strong></td>
                        <td><?php echo htmlspecialchars($latestWeight); ?> kg</td>
                    </tr>
                    <tr>
                        <td><strong>Tour de Taille</strong></td>
                        <td><?php echo htmlspecialchars($userInfo["Trtaille"]); ?> cm</td>
                    </tr>
                    <tr>
                        <td><strong>Tour de Hanches</strong></td>
                        <td><?php echo htmlspecialchars($userInfo["Trhanches"]); ?> cm</td>
                    </tr>
                    <tr>
                        <td><strong>Tour de Fesses</strong></td>
                        <td><?php echo htmlspecialchars($userInfo["Trfesses"]); ?> cm</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <div class="imc-box" style="border-left-color: <?php echo $imcColor; ?>;">
                    <h3><i class="fa fa-heartbeat"></i> Votre IMC</h3>
                    <div class="imc-value" style="color: <?php echo $imcColor; ?>;">
                        <?php echo number_format($imc, 2); ?>
                    </div>
                    <div class="imc-message" style="color: <?php echo $imcColor; ?>;">
                        <?php echo $message; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- History Section -->
    <?php if ($userSuivi && !empty($userSuivi)): ?>
    <div class="section-card">
        <h2 class="section-title">
            <i class="fa fa-history"></i> Historique de Suivi
        </h2>
        
        <div style="overflow-x: auto;">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Poids (kg)</th>
                        <th>Tour Taille (cm)</th>
                        <th>Tour Hanches (cm)</th>
                        <th>Tour Fesses (cm)</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userSuivi as $suivi): 
                        // Calculer IMC pour chaque ligne
                        $suiviIMC = 0;
                        if ($suivi["Poids"] > 0 && $userInfo["Taille"] > 0) {
                            $suiviIMC = $suivi["Poids"] / (($userInfo["Taille"] / 100) * ($userInfo["Taille"] / 100));
                            $suiviIMC = round($suiviIMC, 2);
                        }
                        $isInitial = ($suivi['source'] ?? '') === 'initial';
                    ?>
                    <tr style="<?php echo $isInitial ? 'background: #e8f5e9;' : ''; ?>">
                        <td><strong><?php echo htmlspecialchars($suivi["Date"]); ?></strong></td>
                        <td><?php echo htmlspecialchars($suivi["Poids"]); ?></td>
                        <td><?php echo htmlspecialchars($suivi["Trtaille"]); ?></td>
                        <td><?php echo htmlspecialchars($suivi["Trhanches"]); ?></td>
                        <td><?php echo htmlspecialchars($suivi["Trfesses"]); ?></td>
                        <td>
                            <?php if ($isInitial): ?>
                                <span style="background: #4caf50; color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: 600;">
                                    <i class="fa fa-star"></i> Initial
                                </span>
                            <?php else: ?>
                                <span style="color: #00d4ff; font-size: 0.85rem;">
                                    <i class="fa fa-check-circle"></i> Suivi
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isInitial): ?>
                                <span style="color: #999; font-size: 0.85rem;">
                                    <i class="fa fa-lock"></i> Protégé
                                </span>
                            <?php else: ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($suivi['id']); ?>">
                                    <button type="submit" class="delete-button" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette mensuration ?');">
                                        <i class="fa fa-trash"></i> Supprimer
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-container">
        <h2 class="section-title">
            <i class="fa fa-area-chart"></i> Graphique d'Évolution
        </h2>
        <canvas id="myChart" height="100"></canvas>
    </div>
    
    <?php else: ?>
    <!-- Empty State -->
    <div class="section-card">
        <div class="empty-state">
            <i class="fa fa-line-chart"></i>
            <h3>Aucune mensuration enregistrée</h3>
            <p>Commencez à suivre votre progression en ajoutant votre première mensuration !</p>
            <a href="suivi.php" style="background: linear-gradient(135deg, #4caf50, #388e3c); color: white; padding: 15px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; display: inline-block;">
                <i class="fa fa-plus-circle"></i> Ajouter une Mensuration
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="dashboard-footer">
        <a href="https://www.aquavelo.com" target="_blank">
            <img src="images/content/LogoAQUASPORTMINCEUR.webp" alt="Logo AQUAVELO" onerror="this.style.display='none'">
        </a>
        
        <div class="app-links">
            <a href="https://play.google.com/store/apps/details?id=com.resamania.resamaniav2&hl=fr" target="_blank">
                <i class="fa fa-android"></i> Télécharger sur Android
            </a>
            <a href="https://apps.apple.com/lu/app/resamania-v2/id1482410619" target="_blank">
                <i class="fa fa-apple"></i> Télécharger sur iOS
            </a>
        </div>
    </div>

</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-26LRGBE9X2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-26LRGBE9X2');
  
  gtag('event', 'page_view', {
    'page_title': 'Dashboard Mensurations',
    'page_location': window.location.href
  });
</script>

<?php if ($userSuivi && !empty($userSuivi)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Préparer les données dans l'ordre chronologique (du plus ancien au plus récent)
    const allData = <?php echo json_encode($userSuivi); ?>;
    
    // Inverser car getUserSuivi retourne en ordre inverse (récent → ancien)
    // On veut chronologique (ancien → récent) pour le graphique
    const chronologicalData = allData.reverse();
    
    // Extraire les données
    const labels = chronologicalData.map(item => item.Date);
    const poids = chronologicalData.map(item => parseFloat(item.Poids));
    const trtaille = chronologicalData.map(item => parseFloat(item.Trtaille));
    const trhanches = chronologicalData.map(item => parseFloat(item.Trhanches));
    const trfesses = chronologicalData.map(item => parseFloat(item.Trfesses));

    // Configuration du graphique
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Poids (kg)',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderColor: '#4caf50',
                    borderWidth: 3,
                    data: poids,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tour de Taille (cm)',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    borderColor: '#ff9800',
                    borderWidth: 3,
                    data: trtaille,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tour de Hanches (cm)',
                    backgroundColor: 'rgba(0, 212, 255, 0.1)',
                    borderColor: '#00d4ff',
                    borderWidth: 3,
                    data: trhanches,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Tour de Fesses (cm)',
                    backgroundColor: 'rgba(156, 39, 176, 0.1)',
                    borderColor: '#9c27b0',
                    borderWidth: 3,
                    data: trfesses,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            family: 'Open Sans'
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Valeurs',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
});
</script>
<?php endif; ?>

</body>
</html>










