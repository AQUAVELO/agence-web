<?php
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'ALESIAMINCEUR');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation.php');

$produit = [
    'nom' => 'Seance Cryo',
    'prix' => 99.00,
    'devise' => 'EUR',
    'description' => 'Cryo'
];

$reference = 'CMD' . date('YmdHis') . rand(100, 999);

function calculateMAC($fields, $keyHex) {
    $recognizedKeys = [
        'TPE',
        'contexte_commande',
        'date',
        'lgue',
        'mail',
        'montant',
        'reference',
        'societe',
        'texte-libre',
        'url_retour_err',
        'url_retour_ok',
        'version'
    ];

    $macFields = [];
    foreach ($recognizedKeys as $key) {
        $macFields[$key] = isset($fields[$key]) ? mb_convert_encoding($fields[$key], 'UTF-8', 'auto') : '';
    }

    ksort($macFields, SORT_STRING);
    $chaine = '';
    foreach ($macFields as $k => $v) {
        $chaine .= "$k=$v*";
    }
    $chaine = rtrim($chaine, '*');

    $binaryKey = pack('H*', $keyHex);
    $mac = strtoupper(hash_hmac('sha1', $chaine, $binaryKey));
    file_put_contents('monetico_debug.txt', "CHAINE SIGNEE:\n$chaine\n\nMAC:\n$mac\n", FILE_APPEND);
    return $mac;
}

$dateCommande = date('d/m/Y:H:i:s');
$contextCommande = base64_encode(json_encode([
    'billing' => [
        'addressLine1' => 'Allée des Mimosas',
        'city' => 'Mandelieu',
        'postalCode' => '06400',
        'country' => 'FR'
    ]
], JSON_UNESCAPED_UNICODE));

$fields = [
    'TPE'               => MONETICO_TPE,
    'contexte_commande'=> $contextCommande,
    'date'              => $dateCommande,
    'montant'           => sprintf('%012.2f', $produit['prix']) . $produit['devise'],
    'reference'         => $reference,
    'texte-libre'       => $produit['description'],
    'version'           => '3.0',
    'lgue'              => 'FR',
    'societe'           => MONETICO_COMPANY,
    'mail'              => '',
    'url_retour_ok'     => MONETICO_RETURN_URL,
    'url_retour_err'    => MONETICO_CANCEL_URL
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $fields['mail'] = $_POST['email'];
        $fields['MAC'] = calculateMAC($fields, MONETICO_KEY);
        file_put_contents('monetico_log.txt', print_r($fields, true));

        echo '<form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars_decode($value, ENT_QUOTES) . '">' . "\n";
        }
        echo '<input type="submit" value="Payer maintenant">';
        echo '</form>';
        echo '<script>document.getElementById("form-monetico").submit();</script>';
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
        /* Vos styles CSS ici */
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
            <p>© <?php echo date('Y'); ?> Aquavelo - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>


