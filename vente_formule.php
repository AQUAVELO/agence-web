<?php 
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
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars_decode($value, ENT_QUOTES) . '">';
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
    <title>Cryolipolyse - Réservez votre séance</title>
</head>
<body>
    <h1>Séance Cryolipolyse - 60 minutes</h1>
    <img src="images/cryo.jpg" alt="Cryolipolyse" style="width:100%;max-width:600px;">
    <p>La cryolipolyse est une technique non invasive qui élimine les graisses localisées par le froid. Elle cible les cellules adipeuses, qui sont cristallisées puis éliminées naturellement par l'organisme.</p>
    <h2>Réservez votre séance à 99€</h2>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="email">Votre email :</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <button type="submit">Payer 99€ et réserver</button>
    </form>
</body>
</html>



