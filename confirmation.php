<?php
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

$data = $_POST;
$mac_attendu = $data['MAC'] ?? '';

// Liste des champs attendus pour le MAC, selon la doc page 92
$champs = [
    'TPE', 'date', 'montant', 'reference', 'texte-libre',
    'code-retour', 'cvx', 'vld', 'brand', 'status3ds',
    'numauto', 'motifrefus', 'originecb', 'bincb', 'hpancb',
    'ipclient', 'originetr', 'veres', 'pares'
];

// Construction de la chaîne à signer (uniquement les champs présents)
$chaine = '';
foreach ($champs as $champ) {
    $chaine .= ($data[$champ] ?? '') . '*';
}
$chaine = rtrim($chaine, '*');

// Calcul du MAC
$mac_calcule = strtoupper(hash_hmac('sha1', $chaine, pack('H*', MONETICO_KEY)));

// Log de debug
file_put_contents('log_confirmation.txt', date('Y-m-d H:i:s') . "\nChaîne signée : $chaine\nMAC attendu : $mac_attendu\nMAC calculé : $mac_calcule\n\n", FILE_APPEND);

// Réponse à Monetico
if ($mac_calcule === $mac_attendu) {
    http_response_code(200);
    echo "OK";
    // TODO : traitement de commande ici
} else {
    http_response_code(200); // Toujours OK pour éviter relances
    echo "KO - MAC invalide";
}




