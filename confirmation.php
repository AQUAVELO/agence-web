<?php
// confirmation.php - Vérification du retour de paiement Monetico

define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

// Récupération des données POST
$data = $_POST;
$mac_attendu = $data['MAC'] ?? '';

// Liste des champs à utiliser pour reconstruire la chaîne (page 92 de la doc Monetico v2.0)
$champs = [
    'TPE', 'date', 'montant', 'reference', 'texte-libre', 'version', 'code-retour',
    'cvx', 'vld', 'brand', 'status3ds', 'numauto', 'motifrefus',
    'originecb', 'bincb', 'hpancb', 'ipclient', 'originetr', 'veres', 'pares'
];

// Reconstruction de la chaîne signée
$chaine = '';
foreach ($champs as $champ) {
    $chaine .= ($data[$champ] ?? '') . '*';
}
$chaine = rtrim($chaine, '*');

// Calcul du MAC local
$mac_calcule = strtoupper(hash_hmac('sha1', $chaine, pack('H*', MONETICO_KEY)));

// Log pour audit
file_put_contents('log_confirmation.txt', date('Y-m-d H:i:s') . "\nChaîne signée : $chaine\nMAC attendu : $mac_attendu\nMAC calculé : $mac_calcule\n\n", FILE_APPEND);

// Vérification du MAC
if ($mac_calcule === $mac_attendu) {
    // ✅ Paiement authentifié
    http_response_code(200);
    echo "OK";

    // TODO : enregistrer la commande dans la BDD, envoyer l'e-mail, etc.

} else {
    // ❌ MAC invalide
    http_response_code(200); // Toujours HTTP 200 pour éviter les relances Monetico
    echo "KO - MAC invalide";
}



