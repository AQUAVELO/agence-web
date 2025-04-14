

<?php
// confirmation.php - Vérification du retour de paiement Monetico

// Clé HEX fournie par Monetico
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

// Récupération des données retournées par Monetico (POST recommandé)
$tpe         = $_POST['TPE'] ?? '';
$date        = $_POST['date'] ?? '';
$montant     = $_POST['montant'] ?? '';
$reference   = $_POST['reference'] ?? '';
$texte_libre = $_POST['texte-libre'] ?? '';
$version     = '3.0';
$code_retour = $_POST['code-retour'] ?? '';
$cvx         = $_POST['cvx'] ?? '';
$vld         = $_POST['vld'] ?? '';
$brand       = $_POST['brand'] ?? '';
$status3ds   = $_POST['status3ds'] ?? '';
$numauto     = $_POST['numauto'] ?? '';
$motifrefus  = $_POST['motifrefus'] ?? '';
$originecb   = $_POST['originecb'] ?? '';
$bincb       = $_POST['bincb'] ?? '';
$hpancb      = $_POST['hpancb'] ?? '';
$ipclient    = $_POST['ipclient'] ?? '';
$originetr   = $_POST['originetr'] ?? '';
$veres       = $_POST['veres'] ?? '';
$pares       = $_POST['pares'] ?? '';
$mac         = $_POST['MAC'] ?? '';

// Reconstitution de la chaîne selon la documentation (page 92)
$chaine = implode('*', [
    $tpe,
    $date,
    $montant,
    $reference,
    $texte_libre,
    $version,
    $code_retour,
    $cvx,
    $vld,
    $brand,
    $status3ds,
    $numauto,
    $motifrefus,
    $originecb,
    $bincb,
    $hpancb,
    $ipclient,
    $originetr,
    $veres,
    $pares
]) . '*';

// Calcul du MAC localement
$mac_calculated = strtoupper(hash_hmac('sha1', $chaine, pack('H*', MONETICO_KEY)));

// Affichage pour debug
file_put_contents('log_confirmation.txt', "\nMAC Attendu: $mac\nMAC Calculé: $mac_calculated\nChaîne: $chaine\n", FILE_APPEND);

if ($mac === $mac_calculated) {
    echo "Paiement validé : commande $reference acceptée.";
    // TODO : mettre à jour la base de données, envoyer un email, etc.
} else {
    echo "Erreur de vérification du MAC. Paiement rejeté.";
    // TODO : loger ou alerter l'administrateur
}
?>
