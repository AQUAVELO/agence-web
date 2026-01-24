<?php
// require 'vendor/autoload.php';
// require 'settings.php';

// Configuration Monetico
if (!defined('MONETICO_TPE')) define('MONETICO_TPE', '6684349');
if (!defined('MONETICO_KEY')) define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
if (!defined('MONETICO_COMPANY')) define('MONETICO_COMPANY', 'ALESIAMINCEUR');
if (!defined('MONETICO_URL')) define('MONETICO_URL', 'https://p.monetico-services.com/paiement.cgi');
if (!defined('MONETICO_RETURN_URL')) define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation_prix.php');
if (!defined('MONETICO_CANCEL_URL')) define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation_prix.php');

// Fonction pour calculer le MAC
if (!function_exists('calculateMAC_prix')) {
    function calculateMAC_prix($fields, $keyHex) {
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
}

$error = '';
$urlPaiement = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom'] ?? '');
    $prenom  = trim($_POST['prenom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $tel     = trim($_POST['telephone'] ?? '');
    $montant = trim($_POST['montant'] ?? '');
    $detail  = trim($_POST['detail'] ?? 'Prestation personnalisée');

    if (
        $nom !== '' && $prenom !== '' &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        preg_match('/^[0-9\s\-\+\(\)]+$/', $tel) &&
        is_numeric($montant) && $montant > 0 &&
        $detail !== ''
    ) {
        $reference = 'CMD' . date('YmdHis') . rand(100, 999);
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
            'montant'           => sprintf('%012.2f', $montant) . 'EUR',
            'reference'         => $reference,
            'texte-libre'       => http_build_query([
                'email'     => $email,
                'nom'       => $nom,
                'prenom'    => $prenom,
                'telephone' => $tel,
                'detail'    => $detail,
                'montant'   => number_format($montant, 2, '.', '') . 'EUR'
            ], '', ';'),
            'version'           => '3.0',
            'lgue'              => 'FR',
            'societe'           => MONETICO_COMPANY,
            'mail'              => $email,
            'url_retour_ok'     => MONETICO_RETURN_URL,
            'url_retour_err'    => MONETICO_CANCEL_URL
        ];

        $fields['MAC'] = calculateMAC_prix($fields, MONETICO_KEY);

        $params = [];
        foreach ($fields as $key => $val) {
            $params[] = "$key=" . urlencode($val);
        }
        $urlPaiement = MONETICO_URL . '?' . implode('&', $params);

    } else {
        $error = "Tous les champs doivent être remplis correctement.";
    }
}
?>

<style>
    .vente-section {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
    .vente-section h1 {
      color: #104e8b;
      text-align: center;
      margin-bottom: 30px;
    }
    .vente-section label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    .vente-section input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .vente-section button {
      background-color: #104e8b;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 20px;
      width: 100%;
      font-size: 1.1em;
      font-weight: bold;
    }
    .vente-section button:hover {
      background-color: #0d3e70;
    }
    .vente-section .error {
      color: red;
      text-align: center;
      margin-bottom: 15px;
    }
    .vente-section .success {
      color: green;
      text-align: center;
      margin-bottom: 15px;
    }
    .vente-section .url {
      word-break: break-all;
      text-align: center;
      margin-top: 15px;
      font-size: 1.1em;
    }
    .hero-image {
      display: block;
      margin: 0 auto 20px;
      max-width: 100%;
      border-radius: 12px;
    }
</style>

<div class="vente-section">
    <img src="<?= BASE_PATH ?>images/center_179/1.jpg" alt="Aquavelo" class="hero-image">
    <h1>Créer un lien de paiement Monetico</h1>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($urlPaiement): ?>
      <div class="success">Lien à transmettre au client :</div>
      <div class="url"><a href="<?= htmlspecialchars($urlPaiement) ?>" target="_blank"><?= htmlspecialchars($urlPaiement) ?></a></div>
    <?php else: ?>
      <form method="post">
        <label>Prénom* <input type="text" name="prenom" required></label>
        <label>Nom* <input type="text" name="nom" required></label>
        <label>Téléphone* <input type="tel" name="telephone" required></label>
        <label>Email* <input type="email" name="email" required></label>
        <label>Montant à encaisser (€)* <input type="number" step="0.01" min="1" name="montant" required></label>
        <label>Intitulé de la prestation* <input type="text" name="detail" required></label>
        <button type="submit">Générer le lien de paiement</button>
      </form>
    <?php endif; ?>
</div>
