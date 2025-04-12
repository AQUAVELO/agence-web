<?php
// Configuration de Monetico
define('MONETICO_TPE', '6684349');             // Votre numéro de TPE
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');   // Votre clé secrète
define('MONETICO_COMPANY', 'ALESIA MINCEUR');        // Nom de votre société
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi'); // URL de paiement Monetico
define('MONETICO_RETURN_URL', 'https://aquavelo.com/confirmation.php'); // URL de retour après paiement
define('MONETICO_CANCEL_URL', 'https://aquavelo.com/annulation.php');   // URL en cas d'annulation

// Information du produit
$produit = [
    'nom' => 'Séance Aquavelo',
    'prix' => 99.00,
    'devise' => 'EUR',
    'description' => 'Séance d\'aquavelo de 45 minutes'
];

// Génération d'un ID de commande unique
$reference = 'CMD' . date('YmdHis') . rand(100, 999);

// Fonction pour générer le MAC (Message Authentication Code)
function calculateMAC($fields, $key) {
    // Préparation de la chaîne à hacher
    $content = '';
    foreach ($fields as $field => $value) {
        if ($field != 'MAC') {
            $content .= $value . '*';
        }
    }
    $content .= $key;
    
    // Génération du MAC avec l'algorithme HMAC-SHA1
    $mac = strtoupper(hash_hmac('sha1', $content, $key));
    return $mac;
}

// Préparation des données pour Monetico
$dateCommande = date('d/m/Y:H:i:s');
$contextCommande = base64_encode(json_encode([
    'billing' => [
        'addressLine1' => '',
        'city' => '',
        'postalCode' => '',
        'country' => 'FRA'
    ]
]));

$fields = [
    'TPE' => MONETICO_TPE,
    'date' => $dateCommande,
    'montant' => $produit['prix'] . $produit['devise'],
    'reference' => $reference,
    'texte-libre' => $produit['description'],
    'version' => '3.0',
    'lgue' => 'FR',
    'societe' => MONETICO_COMPANY,
    'context_commande' => $contextCommande,
    'mail' => '',  // Sera rempli par le formulaire
    'url_retour_ok' => MONETICO_RETURN_URL,
    'url_retour_err' => MONETICO_CANCEL_URL
];

// Si le formulaire est soumis, on génère le MAC et on redirige vers Monetico
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $fields['mail'] = $_POST['email'];
        
        // Calcul du MAC
        $mac = calculateMAC($fields, MONETICO_KEY);
        $fields['MAC'] = $mac;
        
        // Stockage des informations de la commande en base de données ou en session
        // ...
        
        // Redirection via un formulaire auto-soumis
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Redirection vers la page de paiement</title>
            <script type="text/javascript">
                window.onload = function() {
                    document.getElementById("form-monetico").submit();
                }
            </script>
        </head>
        <body>
            <p>Redirection vers la page de paiement en cours...</p>
            <form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value, ENT_QUOTES) . '">';
        }
        
        echo '</form>
        </body>
        </html>';
        exit;
    } else {
        $error = "Veuillez saisir une adresse email valide";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquavelo - Réservez votre séance à 99€</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #00aaff;
            --accent-color: #ff9900;
            --light-color: #f5f9ff;
            --dark-color: #001a33;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
        }
        
        .logo span {
            color: var(--accent-color);
        }
        
        .hero {
            text-align: center;
            padding: 50px 0;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .product {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 30px auto;
            max-width: 800px;
        }
        
        .product-image {
            height: 300px;
            background-image: url('images/aquavelo.jpg');
            background-size: cover;
            background-position: center;
        }
        
        .product-details {
            padding: 30px;
        }
        
        .product-title {
            font-size: 1.8rem;
            margin-top: 0;
            color: var(--primary-color);
        }
        
        .product-price {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--accent-color);
            margin: 20px 0;
        }
        
        .product-description {
            margin-bottom: 30px;
        }
        
        .features {
            margin: 30px 0;
        }
        
        .feature {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        
        .feature i {
            color: var(--primary-color);
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        .checkout-form {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin-top: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-block;
            text-decoration: none;
            text-align: center;
        }
        
        .btn:hover {
            background-color: #e68a00;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .secure-payment {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            color: #666;
        }
        
        .secure-payment i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .error {
            color: #d9534f;
            margin-top: 5px;
        }
        
        footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
        }
        
        @media (max-width: 768px) {
            .product-image {
                height: 200px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .product-title {
                font-size: 1.5rem;
            }
            
            .product-price {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">Aqua<span>Velo</span></div>
            <p>Votre centre de fitness aquatique</p>
        </div>
    </header>
    
    <main class="container">
        <section class="hero">
            <h1>Découvrez les bénéfices de l'Aquavelo</h1>
            <p>Une activité complète qui associe les bienfaits de l'eau et du vélo</p>
        </section>
        
        <section class="product">
            <div class="product-image"></div>
            <div class="product-details">
                <h2 class="product-title">Séance d'Aquavelo - 45 minutes</h2>
                <div class="product-price">99€</div>
                <div class="product-description">
                    <p>Profitez d'une séance d'Aquavelo de 45 minutes encadrée par nos coachs professionnels. Une expérience unique qui combine les bienfaits de l'aquagym et du cyclisme pour une activité sportive complète et peu traumatisante pour les articulations.</p>
                </div>
                
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <div>Brûle jusqu'à 500 calories par séance</div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <div>Renforce le système cardiovasculaire</div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <div>Idéal pour la rééducation et la remise en forme</div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <div>Améliore la circulation sanguine et lymphatique</div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <div>Zéro impact sur les articulations</div>
                    </div>
                </div>
                
                <div class="checkout-form">
                    <h3>Réservez votre séance maintenant</h3>
                    <?php if (isset($error)): ?>
                        <p class="error"><?php echo $error; ?></p>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="email">Votre email :</label>
                            <input type="email" id="email" name="email" required placeholder="Entrez votre adresse email">
                        </div>
                        <button type="submit" class="btn btn-block">Payer 99€ et réserver ma séance</button>
                    </form>
                    <div class="secure-payment">
                        <i class="fas fa-lock"></i>
                        <div>Paiement sécurisé par Monetico</div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Aquavelo - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>
