<?php
// Configuration
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

// Fonction pour recalculer le MAC
function verifyMAC($post, $key) {
    $params = [
        'TPE', 'date', 'montant', 'reference', 'texte-libre', 'version',
        'code-retour', 'cvx', 'vld', 'brand', 'status3ds', 'numauto',
        'motifrefus', 'originecb', 'bincb', 'hpancb', 'ipclient', 'originetr',
        'veres', 'pares'
    ];

    // Préparer la chaîne à signer
    $chaine = '';
    foreach ($params as $param) {
        $chaine .= (isset($post[$param]) ? $post[$param] : '') . '*';
    }
    $chaine = rtrim($chaine, '*');

    // Calcul du MAC
    $binaryKey = pack('H*', $key);
    return strtoupper(hash_hmac('sha1', $chaine, $binaryKey));
}

// Récupération des données POST
$post = $_POST;

// Vérification du MAC
$mac_calculated = verifyMAC($post, MONETICO_KEY);
$mac_received = $post['MAC'] ?? '';

if ($mac_calculated === $mac_received) {
    if ($post['code-retour'] === 'payetest' || $post['code-retour'] === 'paiement') {
        echo "<h1>Paiement accepté</h1>";
        // Vous pouvez enregistrer la commande ici
    } else {
        echo "<h1>Paiement refusé : " . htmlspecialchars($post['motifrefus'] ?? 'Inconnu') . "</h1>";
    }
} else {
    echo "<h1>Signature invalide : données corrompues ou modifiées</h1>";
    echo "<p>MAC attendu : $mac_calculated<br>MAC reçu : $mac_received</p>";
}
?>

