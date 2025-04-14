<?php
// confirmation.php - Vérification du retour de paiement Monetico

define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

// Utilisation de $_REQUEST pour lire aussi bien POST que GET (cas du retour Monetico)
$data = $_REQUEST;
$mac_attendu = $data['MAC'] ?? '';

// Reconstruction de la chaîne à signer selon documentation (page 92)
$champs = [
    'TPE', 'date', 'montant', 'reference', 'texte-libre', 'version', 'code-retour',
    'cvx', 'vld', 'brand', 'status3ds', 'numauto', 'motifrefus',
    'originecb', 'bincb', 'hpancb', 'ipclient', 'originetr', 'veres', 'pares'
];

$chaine = '';
foreach ($champs as $champ) {
    $chaine .= ($data[$champ] ?? '') . '*';
}
$chaine = rtrim($chaine, '*');

// Calcul du MAC
$mac_calcule = strtoupper(hash_hmac('sha1', $chaine, pack('H*', MONETICO_KEY)));

// Log pour vérification
file_put_contents('log_confirmation.txt', date('Y-m-d H:i:s') . "\nChaîne : $chaine\nMAC attendu : $mac_attendu\nMAC calculé : $mac_calcule\n\n", FILE_APPEND);

// Vérification du MAC
if ($mac_calcule === $mac_attendu) {
    http_response_code(200);
    echo "OK"; // Ceci est l'accusé de réception que Monetico attend
} else {
    http_response_code(200); // Ne jamais renvoyer une erreur HTTP ici
    echo "KO - MAC invalide";
}


