<?php
/**
 * Script simplifié d'envoi de formulaire - Compatible iOS
 * Version sans dépendances complexes
 */

// Activer l'affichage des erreurs pour debug (désactiver en production)
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Headers pour éviter les problèmes de cache
header('Content-Type: text/html; charset=utf-8');

// Vérifier que c'est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Méthode non autorisée');
}

// Récupérer les données du formulaire
$centre = isset($_POST['centre']) ? trim($_POST['centre']) : '';
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : 'Séance découverte gratuite';

// Validation basique
$errors = [];

if (empty($centre)) {
    $errors[] = 'Le centre est obligatoire';
}

if (empty($nom)) {
    $errors[] = 'Le nom est obligatoire';
}

if (empty($email)) {
    $errors[] = 'L\'email est obligatoire';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'L\'email n\'est pas valide';
}

if (empty($telephone)) {
    $errors[] = 'Le téléphone est obligatoire';
}

// Si erreurs, afficher et arrêter
if (!empty($errors)) {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Erreur</title></head><body>';
    echo '<h2>Erreur dans le formulaire</h2>';
    echo '<ul>';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
    echo '<p><a href="javascript:history.back()">Retour au formulaire</a></p>';
    echo '</body></html>';
    exit;
}

// Préparer l'email
$to = 'claude@alesiaminceur.com'; // Email de destination
$subject = 'Séance Découverte Gratuite - ' . $centre;

// Corps de l'email en HTML
$email_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; margin-top: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #00a8cc; }
        .value { margin-top: 5px; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎁 Nouvelle demande de séance gratuite</h1>
        </div>
        
        <div class="content">
            <div class="field">
                <div class="label">Centre choisi :</div>
                <div class="value">' . htmlspecialchars($centre) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Nom et Prénom :</div>
                <div class="value">' . htmlspecialchars($nom) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Email :</div>
                <div class="value">' . htmlspecialchars($email) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Téléphone :</div>
                <div class="value">' . htmlspecialchars($telephone) . '</div>
            </div>
            
            ' . (!empty($date) ? '
            <div class="field">
                <div class="label">Date souhaitée :</div>
                <div class="value">' . htmlspecialchars($date) . '</div>
            </div>
            ' : '') . '
            
            ' . (!empty($message) ? '
            <div class="field">
                <div class="label">Message :</div>
                <div class="value">' . nl2br(htmlspecialchars($message)) . '</div>
            </div>
            ' : '') . '
        </div>
        
        <div class="footer">
            <p>Email envoyé depuis le formulaire de séance découverte gratuite<br>
            Aquavelo - ' . date('d/m/Y à H:i') . '</p>
        </div>
    </div>
</body>
</html>
';

// Headers pour l'email HTML
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: noreply@aquavelo.com" . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

// Envoyer l'email
$sent = mail($to, $subject, $email_body, $headers);

// Email de confirmation au client
if ($sent) {
    $client_subject = 'Confirmation - Votre demande de séance gratuite Aquavelo';
    $client_body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .highlight { background: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎁 Merci ' . htmlspecialchars($nom) . ' !</h1>
        </div>
        
        <div class="content">
            <p>Votre demande de séance découverte gratuite a bien été enregistrée.</p>
            
            <div class="highlight">
                <strong>Centre choisi :</strong> ' . htmlspecialchars($centre) . '<br>
                ' . (!empty($date) ? '<strong>Date souhaitée :</strong> ' . htmlspecialchars($date) . '<br>' : '') . '
            </div>
            
            <p><strong>Que se passe-t-il maintenant ?</strong></p>
            <ol>
                <li>Notre équipe du centre de ' . htmlspecialchars($centre) . ' va vous contacter dans les 24h</li>
                <li>Vous conviendrez ensemble d\'un créneau qui vous arrange</li>
                <li>Vous recevrez votre bon de séance gratuite par email</li>
            </ol>
            
            <p><strong>Contact direct :</strong><br>
            Téléphone : 06 22 64 70 95<br>
            Email : claude@alesiaminceur.com</p>
            
            <p>À très bientôt dans l\'eau ! 💦</p>
            
            <p style="color: #666; font-size: 0.9em; margin-top: 30px;">
            L\'équipe Aquavelo<br>
            www.aquavelo.com
            </p>
        </div>
    </div>
</body>
</html>
    ';
    
    $client_headers = "MIME-Version: 1.0" . "\r\n";
    $client_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $client_headers .= "From: Aquavelo <noreply@aquavelo.com>" . "\r\n";
    
    mail($email, $client_subject, $client_body, $client_headers);
}

// Page de confirmation
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande envoyée - Aquavelo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            padding: 50px 0;
            font-family: Arial, sans-serif;
        }
        .success-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 5rem;
            color: #4caf50;
            margin-bottom: 20px;
        }
        h1 {
            color: #2e7d32;
            margin-bottom: 20px;
        }
        .highlight-box {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }
        .btn-home {
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-home:hover {
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 168, 204, 0.4);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        
        <h1>Merci <?php echo htmlspecialchars($nom); ?> !</h1>
        
        <p style="font-size: 1.2rem; color: #666;">
            Votre demande de séance découverte gratuite a bien été envoyée.
        </p>
        
        <div class="highlight-box">
            <p style="margin: 0; font-size: 1.1rem;">
                <strong>Centre :</strong> <?php echo htmlspecialchars($centre); ?><br>
                <strong>Email :</strong> <?php echo htmlspecialchars($email); ?><br>
                <strong>Téléphone :</strong> <?php echo htmlspecialchars($telephone); ?>
            </p>
        </div>
        
        <div style="text-align: left; margin: 30px 0;">
            <h3 style="color: #00a8cc;">📧 Et maintenant ?</h3>
            <ol style="font-size: 1.05rem; line-height: 1.8;">
                <li>Vous allez recevoir un email de confirmation</li>
                <li>Notre équipe vous contactera sous 24h</li>
                <li>Vous choisirez ensemble votre créneau</li>
                <li>Vous recevrez votre bon de séance gratuite</li>
            </ol>
        </div>
        
        <p style="color: #666;">
            <strong>Contact direct :</strong><br>
            <i class="fa fa-phone"></i> 06 22 64 70 95<br>
            <i class="fa fa-envelope"></i> claude@alesiaminceur.com
        </p>
        
        <a href="https://www.aquavelo.com" class="btn-home">
            <i class="fa fa-home"></i> Retour à l'accueil
        </a>
    </div>

    <!-- Google Analytics Event -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-26LRGBE9X2"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-26LRGBE9X2');
      
      // Track conversion
      gtag('event', 'conversion', {
        'event_category': 'form',
        'event_label': 'free_trial_submitted',
        'value': 1
      });
    </script>
</body>
</html>
