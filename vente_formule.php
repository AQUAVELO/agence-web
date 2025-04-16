<?php
// Configuration de Monetico
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'ALESIAMINCEUR');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation.php');

$produit = [
    'nom' => 'Séance Cryo',
    'prix' => 99.00,
    'devise' => 'EUR',
    'description' => 'Cryo'
];

$reference = 'CMD' . date('YmdHis') . rand(100, 999);

function calculateMAC($fields, $keyHex) {
    $recognizedKeys = [
        'TPE', 'contexte_commande', 'date', 'lgue', 'mail', 'montant', 'reference',
        'societe', 'texte-libre', 'url_retour_err', 'url_retour_ok', 'version'
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
$contexteCommande = base64_encode(json_encode([
    'billing' => [
        'addressLine1' => 'Allée des Mimosas',
        'city' => 'Mandelieu',
        'postalCode' => '06400',
        'country' => 'FR'
    ]
], JSON_UNESCAPED_UNICODE));

$fields = [
    'TPE'               => MONETICO_TPE,
    'contexte_commande' => $contexteCommande,
    'date'              => $dateCommande,
    'montant'           => sprintf('%012.2f', $produit['prix']) . $produit['devise'],
    'reference'         => $reference,
    'texte-libre'       => $produit['description'], // sera enrichi plus bas
    'version'           => '3.0',
    'lgue'              => 'FR',
    'societe'           => MONETICO_COMPANY,
    'mail'              => '',
    'url_retour_ok'     => MONETICO_RETURN_URL,
    'url_retour_err'    => MONETICO_CANCEL_URL
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom'] ?? '');
    $prenom  = trim($_POST['prenom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $tel     = trim($_POST['telephone'] ?? '');

    if (
        $nom !== '' && $prenom !== '' &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        preg_match('/^[0-9\s\-\+\(\)]+$/', $tel)
    ) {
       $texteLibreInfos = [
        'email'     => $email,
        'nom'       => $nom,
        'prenom'    => $prenom,
        'telephone' => $tel,
        'achat'     => $produit['description'],
        'montant'   => number_format($produit['prix'], 2, '.', '') . $produit['devise']
        ];

        // ✅ Enrichir AVANT de calculer le MAC
        $fields['texte-libre'] .= ';' . http_build_query($texteLibreInfos, '', ';');
        $fields['mail'] = $email;
        
        // ✅ Puis calcul MAC avec champ enrichi
        $fields['MAC'] = calculateMAC($fields, MONETICO_KEY);

        file_put_contents('monetico_log.txt', print_r($fields, true), FILE_APPEND);

        echo '<div style="text-align:center; font-family:sans-serif; margin-top:50px;">';
        echo '<p style="font-size:1.2em; color:#cc3366;">Chargement en cours... Merci de patienter.</p>';
        echo '<div style="margin-top:20px;">';
        echo '<img src="https://i.gifer.com/YCZH.gif" alt="Chargement" width="50" height="50">';
        echo '</div>';
        echo '</div>';

        echo '<form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars_decode($value, ENT_QUOTES) . '">';
        }
        echo '</form>';
        echo '<script>setTimeout(() => document.getElementById("form-monetico").submit(), 1000);</script>';
        exit;
    } else {
        $error = "Tous les champs doivent être remplis correctement.";
    }
}
?>

<!-- FORMULAIRE HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Séance Cryo - 60 minutes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff0f5;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #fff0f5;
            text-align: center;
        }
        .product-image {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            display: block;
            border-radius: 10px;
        }
        .description {
            text-align: center;
            font-size: 1.1em;
            margin: 20px 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background-color: #cc3366;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            display: block;
            width: 100%;
        }
        .btn:hover {
            background-color: #b02e5c;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Séance Cryo - 60 minutes</h1>
    <img src="images/cryolipolyse.jpg" alt="Séance Cryo" class="product-image">
    <p class="description">
        La cryolipolyse est une technique non invasive qui élimine les graisses localisées par le froid.<br>
        Elle cible les cellules adipeuses, qui sont cristallisées puis éliminées naturellement par l'organisme.
    </p>

    <?php if (isset($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="telephone">Téléphone :</label>
            <input type="tel" id="telephone" name="telephone" required>
        </div>
        <div class="form-group">
            <label for="email">Adresse email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <button type="submit" class="btn">Réserver et payer 99€</button>
    </form>
</div>
</body>
</html>








