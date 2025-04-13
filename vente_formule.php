<?php
// === Configuration Monetico ===
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594'); // Clé HEX
define('MONETICO_COMPANY', 'ALESIAMINCEUR');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation.php');

// === Données produit ===
$produit = [
    'nom' => 'Cryo',
    'prix' => 99.00,
    'devise' => 'EUR',
    'description' => "Séance d'aquavelo de 45 minutes"
];

// === Génération de référence commande unique ===
$reference = 'CMD' . date('YmdHis') . rand(100, 999);

// === Contexte commande (optionnel, base64 UTF-8) ===
$contextCommande = base64_encode(json_encode([
    'billing' => [
        'addressLine1' => 'Allée des Mimosas',
        'city' => 'Mandelieu',
        'postalCode' => '06400',
        'country' => 'FR'
    ]
], JSON_UNESCAPED_UNICODE));

// === Préparation des données ===
$fields = [
    'TPE'               => MONETICO_TPE,
    'contexte_commande'=> $contextCommande, // Doit être inclus dans le MAC s’il est envoyé
    'date'              => date('d/m/Y:H:i:s'),
    'lgue'              => 'FR',
    'mail'              => '', // à remplir via formulaire
    'montant'           => sprintf('%012.2f', $produit['prix']) . $produit['devise'],
    'reference'         => $reference,
    'societe'           => MONETICO_COMPANY,
    'texte-libre'       => $produit['description'],
    'url_retour_ok'     => MONETICO_RETURN_URL,
    'url_retour_err'    => MONETICO_CANCEL_URL,
    'version'           => '3.0'
];

// === Fonction MAC Monetico ===
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

    // Ne garder que les champs attendus
    $macFields = [];
    foreach ($recognizedKeys as $key) {
        $macFields[$key] = isset($fields[$key]) ? mb_convert_encoding($fields[$key], 'UTF-8', 'auto') : '';
    }

    // Tri ASCII (sensible à la casse)
    ksort($macFields, SORT_STRING);

    // Construction de la chaîne à signer
    $chaine = '';
    foreach ($macFields as $k => $v) {
        $chaine .= "$k=$v*";
    }
    $chaine = rtrim($chaine, '*');

    // Calcul du MAC en HMAC-SHA1 avec clé binaire
    $binaryKey = pack('H*', $keyHex);
    $mac = strtoupper(hash_hmac('sha1', $chaine, $binaryKey));

    // Log de la chaîne signée (debug)
    file_put_contents('monetico_debug.txt', "CHAINE SIGNEE:\n$chaine\n\nMAC:\n$mac\n", FILE_APPEND);

    return $mac;
}

// === Traitement du formulaire HTML ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $fields['mail'] = $_POST['email'];
        $fields['MAC'] = calculateMAC($fields, MONETICO_KEY);

        // Log pour validation complète
        file_put_contents('monetico_log.txt', print_r($fields, true));

        // Formulaire vers Monetico (aucun encodage HTML !)
        echo '<form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">' . "\n";
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

<!-- === Page HTML === -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement Cryo - Aquavelo</title>
</head>
<body>
    <h2>Paiement sécurisé Monetico</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <label>Votre email :</label>
        <input type="email" name="email" required>
        <button type="submit">Payer 99 €</button>
    </form>
</body>
</html>

