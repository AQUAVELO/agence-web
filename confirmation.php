<?php
// confirmation.php - Vérification du retour de paiement Monetico (corrigée avec contexte_commande)

define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

// Récupération des données POST
$data = $_POST;
$mac_attendu = $data['MAC'] ?? '';

// Reconstruction de la chaîne à signer selon documentation (avec contexte_commande inclus)
$champs = [
    'TPE', 'contexte_commande', 'date', 'montant', 'reference', 'texte-libre', 'version',
    'code-retour', 'cvx', 'vld', 'brand', 'status3ds', 'numauto', 'motifrefus',
    'originecb', 'bincb', 'hpancb', 'ipclient', 'originetr', 'veres', 'pares'
];

$chaine = '';
foreach ($champs as $champ) {
    $chaine .= ($data[$champ] ?? '') . '*';
}
$chaine = rtrim($chaine, '*');

// Calcul du MAC
$mac_calcule = strtoupper(hash_hmac('sha1', $chaine, pack('H*', MONETICO_KEY)));

// Log pour debug
file_put_contents('log_confirmation.txt', date('Y-m-d H:i:s') . "\nChaîne : $chaine\nMAC attendu : $mac_attendu\nMAC calculé : $mac_calcule\n\n", FILE_APPEND);

// Vérification du MAC
if ($mac_calcule === $mac_attendu) {
    http_response_code(200);
    echo "OK"; // Réponse attendue par Monetico
    // TODO : traiter la commande, enregistrer en base, notifier le client, etc.
} else {
    http_response_code(200); // Toujours 200 pour éviter relances
    echo "KO - MAC invalide";
}
?>



