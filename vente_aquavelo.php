<?php
require 'vendor/autoload.php';
require 'settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'AQUACANNES');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation.php');

$formules = [
    '20_seances' => [
        'nom' => '20 séances',
        'prix' => 95.00,
        'description' => '20 séances – 95€ x 4 mois (19€/séance), je paie le 1er mois soit 95€'
    ],
    '45_seances' => [
        'nom' => '45 séances',
        'prix' => 63.00,
        'description' => '45 séances – 63€ x 10 mois (14€/séance), je paie le 1er mois soit 63€'
    ],
    '88_seances' => [
        'nom' => '88 séances',
        'prix' => 79.00,
        'description' => '88 séances – 79€ x 12 mois (11€/séance), je paie le 1er mois soit 79€'
    ],
    '114_seances' => [
        'nom' => '114 séances',
        'prix' => 97.00,
        'description' => '114 séances – 97€ x 12 mois (10€/séance), je paie le 1er mois soit 97€'
    ],
    'illimite' => [
        'nom' => 'Illimité',
        'prix' => 99.00,
        'description' => 'Illimité – 99€ x 12 mois (≈8€/séance), je paie le 1er mois soit 99€'
    ]
];

$selected = $_POST['formule'] ?? '20_seances';
$produit = $formules[$selected] ?? $formules['20_seances'];

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
    'montant'           => sprintf('%012.2f', $produit['prix']) . 'EUR',
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
        $stmt = $conn->prepare("INSERT INTO formule (nom, prenom, tel, prix, email, vente) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$nom, $prenom, $tel, $produit['prix'], $email]);

        echo '<div style="text-align:center; font-family:sans-serif; margin-top:30px; color:green;">Merci, votre réservation a bien été enregistrée ! Vous allez être redirigé vers le paiement.</div>';

        $texteLibreInfos = [
            'email'     => $email,
            'nom'       => $nom,
            'prenom'    => $prenom,
            'telephone' => $tel,
            'achat'     => $produit['description'],
            'montant'   => number_format($produit['prix'], 2, '.', '') . 'EUR'
        ];

        $fields['texte-libre'] .= ';' . http_build_query($texteLibreInfos, '', ';');
        $fields['mail'] = $email;
        $fields['MAC'] = calculateMAC($fields, MONETICO_KEY);

        echo '<form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value, ENT_QUOTES) . '">';
        }
        echo '</form>';
        echo '<script>setTimeout(() => document.getElementById("form-monetico").submit(), 2000);</script>';
        exit;
    } else {
        echo "<p style='color:red; text-align:center;'>Tous les champs sont requis et doivent être valides.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Formules Aquavelo</title>
  <link rel="stylesheet" href="/css/bootstrap.css">
  <link rel="stylesheet" href="/css/style.css">
  <style>
    body {
      background: #f4f8fb;
      font-family: 'Segoe UI', sans-serif;
      padding: 20px;
    }
    .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
    h1 {
      color: #104e8b;
      margin-bottom: 20px;
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
    p.italic-note {
      font-style: italic;
      margin-top: 10px;
      color: #555;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Choisissez votre formule</h1>
    <form method="post">
      <label>Prénom* <input type="text" name="prenom" required></label>
      <label>Nom* <input type="text" name="nom" required></label>
      <label>Téléphone* <input type="tel" name="telephone" required></label>
      <label>Email* <input type="email" name="email" required></label>
      <label>Formule :
        <select name="formule">
          <?php foreach ($formules as $key => $formule): ?>
            <option value="<?= $key ?>"><?= $formule['description'] ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <p class="italic-note">💡 Je fournis un RIB à la première séance pour les autres échéances.</p>
      <button type="submit">Payer le premier mois</button>
    </form>
  </div>
</body>
</html>



