<?php
// Interface de confirmation (interface « Retour » Monetico)
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'ALESIAMINCEUR');

function validateMAC($params, $keyHex) {
    $recognizedKeys = [
        'TPE', 'contexte_commande', 'date', 'montant', 'reference', 'texte-libre', 'code-retour',
        'cvx', 'vld', 'brand', 'status3ds', 'numauto', 'originecb', 'bincb', 'hpancb', 'ipclient',
        'originetr', 'cbmasquee', 'modepaiement', 'authentification', 'usage', 'typecompte',
        'ecard', 'MAC'
    ];

    $macFields = [];
    foreach ($recognizedKeys as $key) {
        if (isset($_POST[$key])) {
            $macFields[$key] = mb_convert_encoding($_POST[$key], 'UTF-8', 'auto');
        }
    }

    ksort($macFields, SORT_STRING);
    $chaine = '';
    foreach ($macFields as $k => $v) {
        if ($k !== 'MAC') {
            $chaine .= "$k=$v*";
        }
    }
    $chaine = rtrim($chaine, '*');

    $binaryKey = pack('H*', $keyHex);
    $mac = strtoupper(hash_hmac('sha1', $chaine, $binaryKey));
    file_put_contents('confirmation_debug.txt', "CHAINE SIGNEE:\n$chaine\n\nMAC attendu:\n$mac\nMAC reçu:\n" . $_POST['MAC'] . "\n", FILE_APPEND);
    return hash_equals($mac, strtoupper($_POST['MAC']));
}

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['MAC']) && validateMAC($_POST, MONETICO_KEY)) {
        echo "version=2\ncdr=0\n";
    } else {
        echo "version=2\ncdr=1\n";
    }
} else {
    echo "version=2\ncdr=1\n";
}
?>





