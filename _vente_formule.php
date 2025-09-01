<?php
require 'vendor/autoload.php';
require 'settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configuration de Monetico
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'AQUACANNES');
define('MONETICO_URL', 'https://p.monetico-services.com/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation.php');

$formules = [
    ['nom' => 'Formule 1', 'prix' => 58.50, 'description' => 'Les 40 séances payable en 10 x 58.50 € soit la séance à 14 €. Je paie la première échéance 58.50 €.'],
    ['nom' => 'Formule 2', 'prix' => 75, 'description' => 'Les 80 séances payable en 12 x 75 € soit la séance à 11 €. Je paie la première échéance 75 €.'],
    ['nom' => 'Formule 3', 'prix' => 89, 'description' => 'Les 104 séances payable en 12 x 89 € soit la séance à 10 €. Je paie la première échéance 89 €.'],
    ['nom' => 'Formule 4', 'prix' => 99.00, 'description' => '12 mois illimité soit la séance à 8 € en venant 3 fois/semaine, payable en 12 x 99 €. Je paie la première échéance 99 €.']
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom'] ?? '');
    $prenom  = trim($_POST['prenom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $tel     = trim($_POST['telephone'] ?? '');
    $choix   = intval($_POST['formule'] ?? 0);

    if (!isset($formules[$choix])) {
        $error = "Formule invalide.";
    } elseif (
        $nom !== '' && $prenom !== '' &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        preg_match('/^[0-9\s\-\+\(\)]+$/', $tel)
    ) {
        $produit = $formules[$choix];

        $detail = $produit['description'];
        $stmt = $conn->prepare("INSERT INTO formule (nom, prenom, tel, prix, email, vente, detail) VALUES (?, ?, ?, ?, ?, 0, ?)");
        $stmt->execute([$nom, $prenom, $tel, $produit['prix'], $email, $detail]);
        
        $texteLibreInfos = [
            'email'     => $email,
            'nom'       => $nom,
            'prenom'    => $prenom,
            'telephone' => $tel,
            'achat'     => $produit['description'],
            'montant'   => number_format($produit['prix'], 2, '.', '') . 'EUR'
        ];
        
        $fields = [
            'TPE'               => MONETICO_TPE,
            'contexte_commande' => $contexteCommande,
            'date'              => $dateCommande,
            'montant'           => sprintf('%012.2f', $produit['prix']) . 'EUR',
            'reference'         => $reference,
            'texte-libre'       => http_build_query(array_merge(['detail' => $detail], $texteLibreInfos), '', ';'),
            'version'           => '3.0',
            'lgue'              => 'FR',
            'societe'           => MONETICO_COMPANY,
            'mail'              => $email,
            'url_retour_ok'     => MONETICO_RETURN_URL,
            'url_retour_err'    => MONETICO_CANCEL_URL
        ];

        $fields['MAC'] = calculateMAC($fields, MONETICO_KEY);

        echo '<div style="text-align:center; font-family:sans-serif; margin-top:30px; color:green;">Merci, votre réservation a bien été enregistrée ! Vous allez être redirigé vers le paiement.</div>';
        echo '<form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars_decode($value, ENT_QUOTES) . '">';
        }
        echo '</form>';
        echo '<script>setTimeout(() => document.getElementById("form-monetico").submit(), 2000);</script>';
        exit;
    } else {
        $error = "Tous les champs doivent être remplis correctement.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Choisissez votre formule et inscrivez-vous</title>
  <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f8fb;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .section {
      max-width: 800px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
    h1, h2 {
      color: #104e8b;
      text-align: center;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      background-color: #104e8b;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 20px;
      width: 100%;
    }
    button:hover {
      background-color: #0d3e70;
    }
    .error {
      color: red;
      text-align: center;
    }
    /* Style pour l'image insérée */
    .hero-image {
      display: block;
      margin: 0 auto 20px;
      max-width: 100%;
      border-radius: 12px;
    }
  </style>
</head>
<body>
  <div class="section">
    <!-- Image harmonieuse en-tête -->
    <img src="/images/center_179/1.jpg" alt="Aquavelo" class="hero-image">
    <h1>Choisissez votre formule et inscrivez-vous</h1>
    <?php if (isset($error)): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="post">
      <label>Prénom* <input type="text" name="prenom" required></label>
      <label>Nom* <input type="text" name="nom" required></label>
      <label>Téléphone* <input type="tel" name="telephone" required></label>
      <label>Email* <input type="email" name="email" required></label>
      <label>Formule :
        <select name="formule">
          <?php foreach ($formules as $index => $formule): ?>
            <option value="<?= $index ?>"><?= $formule['description'] ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <p style="font-style: italic; color: #555;">Je fournis un RIB à la première séance pour les autres échéances.</p>
      <button type="submit">Payer le premier mois</button>
    </form>
  </div>
</body>
</html>



























































