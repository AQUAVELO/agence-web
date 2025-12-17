<?php
session_start();

// Si déjà connecté, rediriger vers le menu
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: _menu.php");
    exit;
}

// Configuration de la base de données
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$settings = [];
$settings['dbhost'] = getenv("MYSQL_ADDON_HOST");
$settings['dbport'] = getenv("MYSQL_ADDON_PORT");
$settings['dbname'] = getenv("MYSQL_ADDON_DB");
$settings['dbusername'] = getenv("MYSQL_ADDON_USER");
$settings['dbpassword'] = getenv("MYSQL_ADDON_PASSWORD");
$settings['mjhost'] = getenv("MAILJET_HOST") ?: "in-v3.mailjet.com";
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

// Récupérer la liste des centres Aquavelo
$centers_list = [];
try {
    $centers_query = $conn->prepare('SELECT id, city FROM am_centers WHERE online = 1 AND aquavelo = 1 ORDER BY city ASC');
    $centers_query->execute();
    $centers_list = $centers_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur récupération centres: " . $e->getMessage());
}

// Variables pour les messages
$success_message = '';
$error_message = '';

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $centre = intval($_POST['centre'] ?? 0);
    $age = intval($_POST['age'] ?? 0);
    $poids = floatval($_POST['poids'] ?? 0);
    $taille = floatval($_POST['taille'] ?? 0);
    $trtaille = floatval($_POST['trtaille'] ?? 0);
    $trhanches = floatval($_POST['trhanches'] ?? 0);
    $trfesses = floatval($_POST['trfesses'] ?? 0);

    // Validation des champs obligatoires
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $error_message = "Tous les champs marqués * sont obligatoires.";
    } elseif ($centre <= 0) {
        $error_message = "Veuillez sélectionner votre centre Aquavelo.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "L'adresse email n'est pas valide.";
    } elseif (strlen($password) < 6) {
        $error_message = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($age < 16 || $age > 120) {
        $error_message = "L'âge doit être compris entre 16 et 120 ans.";
    } elseif ($poids <= 0 || $taille <= 0 || $trtaille <= 0 || $trhanches <= 0 || $trfesses <= 0) {
        $error_message = "Toutes les mensurations doivent être renseignées et supérieures à 0.";
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
                $insert_sql = "INSERT INTO mensurations (Nom, Prenom, email, password, Centre, Age, Poids, Taille, Trtaille, Trhanches, Trfesses) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                
                if ($insert_stmt->execute([$nom, $prenom, $email, $password_hash, $centre, $age, $poids, $taille, $trtaille, $trhanches, $trfesses])) {
                    
                    // ========== RÉCUPÉRER LES INFOS DU CENTRE ==========
                    $center_query = $conn->prepare('SELECT * FROM am_centers WHERE id = ? AND online = 1 AND aquavelo = 1');
                    $center_query->execute([$centre]);
                    $center_info = $center_query->fetch(PDO::FETCH_ASSOC);
                    
                    $city = $center_info['city'] ?? 'Aquavelo';
                    $email_center = $center_info['email'] ?? '';
                    $address = $center_info['address'] ?? '';
                    $hours = $center_info['openhours'] ?? '';
                    $phone = $center_info['phone'] ?? '';
                    
                    // Calculer l'IMC
                    $imc = 0;
                    if ($poids > 0 && $taille > 0) {
                        $imc = $poids / (($taille / 100) * ($taille / 100));
                        $imc = round($imc, 2);
                    }
                    
                    // ========== EMAIL 1 : AU CENTRE ==========
                    if (!empty($email_center)) {
                        try {
                            $mail_center = new PHPMailer(true);
                            $mail_center->IsSMTP();
                            $mail_center->Host = $settings['mjhost'];
                            $mail_center->isHTML(true);
                            $mail_center->SMTPAuth = true;
                            $mail_center->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail_center->Port = 587;
                            $mail_center->Username = $settings['mjusername'];
                            $mail_center->Password = $settings['mjpassword'];
                            
                            $mail_center->setFrom('service.clients@aquavelo.com', 'Service clients Aquavelo');
                            $mail_center->addAddress($email_center, 'Aquavelo ' . $city);
                            $mail_center->addReplyTo($email, $prenom . ' ' . $nom);
                            
                            $mail_center->Subject = 'Aquavelo - Nouvelle inscription au suivi des mensurations - ' . $city;
                            
                            $mail_center->Body = '
                                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                                    <div style="background: linear-gradient(135deg, #4caf50, #388e3c); color: white; padding: 20px; text-align: center;">
                                        <h2 style="margin: 0;">📊 Nouvelle Inscription - Suivi Mensurations</h2>
                                    </div>
                                    
                                    <div style="padding: 30px; background: #f9f9f9;">
                                        <p style="font-size: 16px; color: #333;">Bonjour,</p>
                                        
                                        <p style="font-size: 16px; color: #333;">Un nouveau membre s\'est inscrit au système de suivi des mensurations pour votre centre de <strong>' . htmlspecialchars($city) . '</strong>.</p>
                                        
                                        <div style="background: white; padding: 20px; border-left: 4px solid #4caf50; margin: 20px 0;">
                                            <h3 style="color: #4caf50; margin-top: 0;">👤 Informations du Membre</h3>
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="padding: 8px; font-weight: bold; width: 40%;">Nom :</td>
                                                    <td style="padding: 8px;">' . htmlspecialchars($nom) . '</td>
                                                </tr>
                                                <tr style="background: #f5f5f5;">
                                                    <td style="padding: 8px; font-weight: bold;">Prénom :</td>
                                                    <td style="padding: 8px;">' . htmlspecialchars($prenom) . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 8px; font-weight: bold;">Email :</td>
                                                    <td style="padding: 8px;"><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></td>
                                                </tr>
                                                <tr style="background: #f5f5f5;">
                                                    <td style="padding: 8px; font-weight: bold;">Âge :</td>
                                                    <td style="padding: 8px;">' . htmlspecialchars($age) . ' ans</td>
                                                </tr>
                                            </table>
                                        </div>
                                        
                                        <div style="background: #e8f5e9; padding: 20px; border-left: 4px solid #00d4ff; margin: 20px 0;">
                                            <h3 style="color: #00a8cc; margin-top: 0;">📏 Mensurations Initiales</h3>
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="padding: 8px; font-weight: bold; width: 40%;">Poids :</td>
                                                    <td style="padding: 8px;">' . htmlspecialchars($poids) . ' kg</td>
                                                </tr>
                                                <tr style="background: rgba(255,255,255,0.5);">
                                                    <td style="padding: 8px; font-weight: bold;">Taille :</td>
                                                    <td style="padding: 8px;">' . htmlspecialchars($taille) . ' cm</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 8px; font-weight: bold;">IMC :</td>
                                                    <td style="padding: 8px;"><strong style="color: #4caf50;">' . $imc . '</strong></td>
                                                </tr>
                                                <tr style="background: rgba(255,255,255,0.5);">
                                                    <td style="padding: 8px; font-weight: bold;">Tour de Taille :</td>
                                                    <td style="padding: 8px;">' . htmlspecialchars($trtaille) . ' cm</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 8px; font-weight: bold;">Tour de Hanches :</td>
                                                    <td style="padding: 8px;">' . htmlspecialchars($trhanches) . ' cm</td>
                                                </tr>
                                                <tr style="background: rgba(255,255,255,0.5);">
                                                    <td style="padding: 8px; font-weight: bold;">Tour de Fesses :</td>
                                                    <td style="padding: 8px;">' . htmlspecialchars($trfesses) . ' cm</td>
                                                </tr>
                                            </table>
                                        </div>
                                        
                                        <div style="background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; margin: 20px 0;">
                                            <p style="margin: 0; color: #666;">
                                                <strong>💡 Note :</strong> Ce membre pourra suivre son évolution et mettre à jour ses mensurations régulièrement via son espace personnel.
                                            </p>
                                        </div>
                                        
                                        <p style="font-size: 14px; color: #999; margin-top: 30px;">
                                            <em>Inscription effectuée le ' . date("d/m/Y à H:i") . ' depuis le site aquavelo.com</em>
                                        </p>
                                        
                                        <p style="color: #333;">
                                            Cordialement,<br>
                                            <strong>L\'équipe Aquavelo</strong><br>
                                            <a href="https://www.aquavelo.com" style="color: #4caf50;">www.aquavelo.com</a>
                                        </p>
                                    </div>
                                </div>';
                            
                            $mail_center->AltBody = 'Nouvelle inscription au suivi des mensurations - ' . $prenom . ' ' . $nom . ' - Email: ' . $email . ' - Centre: ' . $city;
                            
                            $mail_center->send();
                            
                        } catch (Exception $e) {
                            error_log("Erreur envoi email au centre: {$mail_center->ErrorInfo}");
                        }
                    }
                    
                    // ========== EMAIL 2 : À L'UTILISATEUR ==========
                    try {
                        $mail_user = new PHPMailer(true);
                        $mail_user->IsSMTP();
                        $mail_user->Host = $settings['mjhost'];
                        $mail_user->isHTML(true);
                        $mail_user->SMTPAuth = true;
                        $mail_user->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail_user->Port = 587;
                        $mail_user->Username = $settings['mjusername'];
                        $mail_user->Password = $settings['mjpassword'];
                        
                        $mail_user->setFrom('claude@alesiaminceur.com', 'Suivi Aquavelo');
                        $mail_user->addAddress($email, $prenom . ' ' . $nom);
                        $mail_user->addReplyTo('claude@alesiaminceur.com', 'Suivi Aquavelo');
                        
                        $mail_user->Subject = 'Bienvenue sur votre espace de suivi Aquavelo !';
                        
                        $mail_user->Body = '
                            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                                <div style="background: linear-gradient(135deg, #4caf50, #388e3c); color: white; padding: 30px; text-align: center;">
                                    <h1 style="margin: 0; font-size: 28px;">🎉 Bienvenue ' . htmlspecialchars($prenom) . ' !</h1>
                                    <p style="margin: 10px 0 0 0; font-size: 16px;">Votre espace de suivi des mensurations est prêt</p>
                                </div>
                                
                                <div style="padding: 30px; background: #f9f9f9;">
                                    <p style="font-size: 16px; color: #333;">Bonjour <strong>' . htmlspecialchars($prenom) . '</strong>,</p>
                                    
                                    <p style="font-size: 16px; color: #333;">Nous sommes ravis de vous accueillir dans votre espace personnel de suivi des mensurations Aquavelo ! 🌊</p>
                                    
                                    <div style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9); padding: 20px; border-radius: 10px; margin: 20px 0;">
                                        <h3 style="color: #2e7d32; margin-top: 0;">✅ Votre compte est activé</h3>
                                        <p style="margin: 0; color: #333;">Vous pouvez dès maintenant accéder à votre tableau de bord pour suivre vos progrès !</p>
                                    </div>
                                    
                                    <div style="background: white; padding: 20px; border-left: 4px solid #00d4ff; margin: 20px 0;">
                                        <h3 style="color: #00a8cc; margin-top: 0;">📊 Vos Mensurations Initiales</h3>
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 8px; font-weight: bold;">Poids :</td>
                                                <td style="padding: 8px; text-align: right;"><strong style="color: #4caf50;">' . htmlspecialchars($poids) . ' kg</strong></td>
                                            </tr>
                                            <tr style="background: #f5f5f5;">
                                                <td style="padding: 8px; font-weight: bold;">IMC :</td>
                                                <td style="padding: 8px; text-align: right;"><strong style="color: #00d4ff;">' . $imc . '</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px; font-weight: bold;">Tour de Taille :</td>
                                                <td style="padding: 8px; text-align: right;">' . htmlspecialchars($trtaille) . ' cm</td>
                                            </tr>
                                            <tr style="background: #f5f5f5;">
                                                <td style="padding: 8px; font-weight: bold;">Tour de Hanches :</td>
                                                <td style="padding: 8px; text-align: right;">' . htmlspecialchars($trhanches) . ' cm</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px; font-weight: bold;">Tour de Fesses :</td>
                                                <td style="padding: 8px; text-align: right;">' . htmlspecialchars($trfesses) . ' cm</td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                    <div style="background: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0;">
                                        <h3 style="color: #1976d2; margin-top: 0;">🎯 Prochaines Étapes</h3>
                                        <ul style="color: #333; line-height: 1.8;">
                                            <li>Connectez-vous à votre espace pour voir votre tableau de bord</li>
                                            <li>Ajoutez de nouvelles mensurations après chaque séance</li>
                                            <li>Suivez votre progression avec des graphiques</li>
                                            <li>Atteignez vos objectifs forme et bien-être !</li>
                                        </ul>
                                    </div>
                                    
                                    <div style="text-align: center; margin: 30px 0;">
                                        <a href="https://www.aquavelo.com/suivi-mensurations" 
                                           style="display: inline-block; background: linear-gradient(135deg, #4caf50, #388e3c); color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 16px;">
                                            Accéder à Mon Espace
                                        </a>
                                    </div>';
                        
                        // Ajouter les infos du centre si disponibles
                        if (!empty($phone) || !empty($address)) {
                            $mail_user->Body .= '
                                    <div style="background: white; padding: 20px; border-left: 4px solid #ff9800; margin: 20px 0;">
                                        <h3 style="color: #ff9800; margin-top: 0;">📍 Votre Centre ' . htmlspecialchars($city) . '</h3>';
                            
                            if (!empty($address)) {
                                $mail_user->Body .= '<p style="margin: 5px 0; color: #333;"><strong>Adresse :</strong> ' . htmlspecialchars($address) . '</p>';
                            }
                            if (!empty($phone)) {
                                $mail_user->Body .= '<p style="margin: 5px 0; color: #333;"><strong>Téléphone :</strong> ' . htmlspecialchars($phone) . '</p>';
                            }
                            if (!empty($hours)) {
                                $mail_user->Body .= '<p style="margin: 5px 0; color: #333;"><strong>Horaires :</strong> ' . htmlspecialchars($hours) . '</p>';
                            }
                            
                            $mail_user->Body .= '
                                    </div>';
                        }
                        
                        $mail_user->Body .= '
                                    <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin: 20px 0;">
                                        <p style="margin: 0; color: #666; font-size: 14px;">
                                            💡 <strong>Conseil :</strong> Prenez vos mesures à la même heure, de préférence le matin à jeun, pour un suivi plus précis.
                                        </p>
                                    </div>
                                    
                                    <p style="color: #333; font-size: 16px; margin-top: 30px;">
                                        Nous vous souhaitons beaucoup de succès dans l\'atteinte de vos objectifs ! 💪
                                    </p>
                                    
                                    <p style="color: #333;">
                                        Sportivement,<br>
                                        <strong>L\'équipe Aquavelo</strong><br>
                                        <a href="https://www.aquavelo.com" style="color: #4caf50;">www.aquavelo.com</a>
                                    </p>
                                    
                                    <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">
                                    
                                    <p style="font-size: 12px; color: #999; text-align: center;">
                                        Besoin d\'aide ? Contactez-nous à <a href="mailto:claude@alesiaminceur.com" style="color: #4caf50;">claude@alesiaminceur.com</a>
                                    </p>
                                </div>
                            </div>';
                        
                        $mail_user->AltBody = 'Bienvenue ' . $prenom . ' ! Votre espace de suivi des mensurations Aquavelo est prêt. Connectez-vous sur https://www.aquavelo.com/suivi-mensurations pour accéder à votre tableau de bord.';
                        
                        $mail_user->send();
                        
                    } catch (Exception $e) {
                        error_log("Erreur envoi email à l'utilisateur: {$mail_user->ErrorInfo}");
                    }
                    
                    // Connexion automatique après inscription
                    $_SESSION["loggedin"] = true;
                    $_SESSION["email"] = $email;
                    $_SESSION["nom"] = $nom;
                    $_SESSION["prenom"] = $prenom;
                    $_SESSION["centre"] = $centre;
                    
                    // Analytics tracking
                    if (function_exists('gtag')) {
                        echo "<script>
                        gtag('event', 'signup_success', {
                            'event_category': 'conversion',
                            'event_label': 'mensurations_registration_centre_" . $centre . "'
                        });
                        </script>";
                    }
                    
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
        
        .centre-highlight {
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: white;
        }
        
        .centre-select {
            border: 3px solid #00d4ff !important;
            font-size: 1.1rem !important;
            font-weight: 600;
        }
        
        .centre-info-box {
            background: linear-gradient(135deg, #e1f5fe, #b3e5fc);
            border-left: 4px solid #00d4ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .centre-info-box i {
            color: #00a8cc;
            margin-right: 8px;
            font-size: 1.2rem;
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
        
        select.form-control {
            height: auto;
            padding: 12px;
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
        
        .alert-danger a {
            color: #c62828;
            text-decoration: underline;
            font-weight: 600;
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
        
        <!-- Section 1 : Votre Centre Aquavelo -->
        <div class="section-title centre-highlight">
            <i class="fa fa-map-marker"></i> 1. Votre Centre Aquavelo
        </div>

        <div class="centre-info-box">
            <i class="fa fa-info-circle"></i>
            <p><strong>Important :</strong> Sélectionnez le centre Aquavelo où vous pratiquez l'aquabiking. Cela nous permettra de personnaliser votre suivi.</p>
        </div>

        <div class="form-group">
            <label for="centre">Centre Aquavelo <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                <select class="form-control centre-select" id="centre" name="centre" required>
                    <option value="">-- Sélectionnez votre centre --</option>
                    <?php foreach ($centers_list as $center): ?>
                        <option value="<?= htmlspecialchars($center['id']); ?>" 
                                <?= (isset($_POST['centre']) && $_POST['centre'] == $center['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($center['city']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <small class="help-block">
                <i class="fa fa-question-circle"></i> Votre centre d'inscription ou celui que vous fréquentez le plus souvent
            </small>
        </div>

        <!-- Section 2 : Informations Personnelles -->
        <div class="section-title">
            <i class="fa fa-user"></i> 2. Informations Personnelles
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

        <!-- Section 3 : Mensurations Initiales -->
        <div class="section-title">
            <i class="fa fa-calculator"></i> 3. Vos Mensurations Actuelles
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

        <!-- Aperçu IMC -->
        <div id="imc-preview" style="display:none;" class="info-box">
            <p><strong><i class="fa fa-calculator"></i> IMC estimé :</strong> <span id="imc-value">0</span></p>
            <p id="imc-message"></p>
        </div>

        <!-- Bouton de soumission -->
        <div style="margin-top: 40px;">
            <button type="submit" class="btn btn-inscription">
                <i class="fa fa-check-circle"></i> Créer Mon Compte Gratuit
            </button>
        </div>

        <div style="text-align: center;">
            <a href="connexion_mensurations.php" class="btn-retour">
                <i class="fa fa-arrow-left"></i> Retour à la Connexion
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
  
  gtag('event', 'page_view', {
    'page_title': 'Inscription Mensurations',
    'page_location': window.location.href
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('inscriptionForm');
    const poidsInput = document.getElementById('poids');
    const tailleInput = document.getElementById('taille');
    const imcPreview = document.getElementById('imc-preview');
    const imcValue = document.getElementById('imc-value');
    const imcMessage = document.getElementById('imc-message');
    
    // Calcul IMC en temps réel
    function calculateIMC() {
        const poids = parseFloat(poidsInput.value);
        const taille = parseFloat(tailleInput.value) / 100;
        
        if (poids > 0 && taille > 0) {
            const imc = (poids / (taille * taille)).toFixed(2);
            imcValue.textContent = imc;
            
            let message = '';
            let color = '';
            
            if (imc < 20) {
                message = 'Vous êtes en insuffisance pondérale.';
                color = '#ff9800';
            } else if (imc > 25) {
                message = 'Vous êtes en surcharge pondérale.';
                color = '#ff5722';
            } else {
                message = 'Félicitations ! Vous avez un IMC normal.';
                color = '#4caf50';
            }
            
            imcMessage.textContent = message;
            imcMessage.style.color = color;
            imcPreview.style.display = 'block';
        } else {
            imcPreview.style.display = 'none';
        }
    }
    
    poidsInput.addEventListener('input', calculateIMC);
    tailleInput.addEventListener('input', calculateIMC);
    
    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        const centre = document.getElementById('centre').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!centre || centre === '') {
            e.preventDefault();
            alert('Veuillez sélectionner votre centre Aquavelo.');
            document.getElementById('centre').focus();
            return false;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Veuillez entrer une adresse email valide.');
            return false;
        }
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 6 caractères.');
            return false;
        }
        
        if (typeof gtag !== 'undefined') {
            gtag('event', 'submit_inscription', {
                'event_category': 'conversion',
                'event_label': 'mensurations_signup_centre_' + centre
            });
        }
        
        return true;
    });
    
    // Highlight du centre quand sélectionné
    const centreSelect = document.getElementById('centre');
    centreSelect.addEventListener('change', function() {
        if (this.value) {
            this.style.borderColor = '#00d4ff';
            this.style.backgroundColor = '#e1f5fe';
        } else {
            this.style.borderColor = '#e0e0e0';
            this.style.backgroundColor = 'white';
        }
    });
});
</script>

</body>
</html>

