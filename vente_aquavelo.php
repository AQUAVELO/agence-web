<?php
require 'vendor/autoload.php';
require 'settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configuration de Monetico
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'AQUACANNES');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation.php');

$formules = [
    '20_seances' => ['nom' => '20 séances', 'prix' => 380, 'description' => '20 séances à 380€'],
    '45_seances' => ['nom' => '45 séances', 'prix' => 63 * 10 * 0.1, 'description' => '10% - 45 séances (63€/mois x10)'],
    '88_seances' => ['nom' => '88 séances', 'prix' => 79 * 12 * 0.1, 'description' => '10% - 88 séances (79€/mois x12)'],
    '114_seances' => ['nom' => '114 séances', 'prix' => 97 * 12 * 0.1, 'description' => '10% - 114 séances (97€/mois x12)'],
    'illimite' => ['nom' => 'Illimité', 'prix' => 99 * 12 * 0.1, 'description' => '10% - Illimité (99€/mois x12)'],
];

$choix = $_POST['formule'] ?? '';
$paiement10 = isset($_POST['acompte']);
$produit = $formules[$choix] ?? ['nom' => 'Séance découverte', 'prix' => 0, 'description' => 'Offre non reconnue'];

$prixFinal = $paiement10 ? round($produit['prix'], 2) : round($produit['prix'] * 10, 2);

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
    return strtoupper(hash_hmac('sha1', $chaine, $binaryKey));
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
    'montant'           => sprintf('%012.2f', $prixFinal) . 'EUR',
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
    $nom     = trim($_POST['nom'] ?? '');
    $prenom  = trim($_POST['prenom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $tel     = trim($_POST['telephone'] ?? '');

    if (
        $nom !== '' && $prenom !== '' &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        preg_match('/^[0-9\s\-\+\(\)]+$/', $tel)
    ) {
        echo '<div style="text-align:center; font-family:sans-serif; margin-top:30px; color:green;">Merci, votre réservation a bien été enregistrée ! Vous allez être redirigé vers le paiement.</div>';

        $texteLibreInfos = [
            'email'     => $email,
            'nom'       => $nom,
            'prenom'    => $prenom,
            'telephone' => $tel,
            'achat'     => $produit['description'],
            'montant'   => number_format($prixFinal, 2, '.', '') . 'EUR'
        ];

        $fields['texte-libre'] .= ';' . http_build_query($texteLibreInfos, '', ';');
        $fields['mail'] = $email;
        $fields['MAC'] = calculateMAC($fields, MONETICO_KEY);

        echo '<form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars_decode($value, ENT_QUOTES) . '">';
        }
        echo '</form>';
        echo '<script>setTimeout(() => document.getElementById("form-monetico").submit(), 2000);</script>';
        exit;
    } else {
        echo "<p style='color:red;text-align:center;'>Veuillez remplir tous les champs correctement.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservation Aquavelo</title>
    <link href="https://www.aquavelo.com/wp-content/themes/aquavelo/style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f8fb;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #104e8b;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            width: 100%;
            background-color: #104e8b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #0d3e70;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Réservez votre formule Aquavelo</h1>
        <form method="post" action="">
            <label for="prenom">Prénom *</label>
            <input type="text" name="prenom" id="prenom" required>

            <label for="nom">Nom *</label>
            <input type="text" name="nom" id="nom" required>

            <label for="telephone">Téléphone *</label>
            <input type="tel" name="telephone" id="telephone" required>

            <label for="email">Adresse email *</label>
            <input type="email" name="email" id="email" required>

            <label for="formule">Choisissez une formule :</label>
            <select name="formule" id="formule" required>
                <option value="20_seances">20 séances à 380 €</option>
                <option value="45_seances">45 séances - 63 € x 10 mois</option>
                <option value="88_seances">88 séances - 79 € x 12 mois</option>
                <option value="114_seances">114 séances - 97 € x 12 mois</option>
                <option value="illimite">Illimité - 99 € x 12 mois</option>
            </select>

            <input type="checkbox" name="acompte" id="acompte">
            <label for="acompte">Je souhaite régler 10% maintenant</label>

            <button type="submit">Valider la réservation</button>
        </form>
    </div>
</body>
</html>

