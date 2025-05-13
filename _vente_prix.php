<?php
// Configuration Monetico
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'AQUACANNES');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation_prix.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation_prix.php');

// Fonction pour calculer le MAC
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
                'nom'      => $nom,
                'prenom'   => $prenom,
                'telephone'=> $tel,
                'email'    => $email,
                'montant'  => number_format($montant, 2, '.', '') . 'EUR',
                'detail'   => $detail
            ], '', ';'),
            'version'           => '3.0',
            'lgue'              => 'FR',
            'societe'           => MONETICO_COMPANY,
            'mail'              => $email,
            'url_retour_ok'     => MONETICO_RETURN_URL,
            'url_retour_err'    => MONETICO_CANCEL_URL
        ];
        $fields['MAC'] = calculateMAC($fields, MONETICO_KEY);

        // Construction de l'URL de paiement à transmettre au client
        $params = [];
        foreach ($fields as $k => $v) {
            $params[] = urlencode($k) . '=' . urlencode($v);
        }
        $urlPaiement = MONETICO_URL . '?' . implode('&', $params);
    } else {
        $error = "Tous les champs doivent être remplis correctement.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Paiement Prestation Monetico</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f8fb; }
    .container { max-width: 500px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    label { display: block; margin-top: 15px; }
    input { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    button { margin-top: 20px; padding: 12px; background: #104e8b; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
    .error { color: red; }
    .success { color: green; }
    .url { word-break: break-all; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Créer un lien de paiement Monetico</h2>
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
</body>
</html>



