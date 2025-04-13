<?php
$key_hex = 'AB477436DAE9200BF71E755208720A3CD52805';

$chaine = "TPE=6684349*date=13/04/2025:02:58:00*lgue=FR*mail=jacquesverdier4@gmail.com*montant=000000099.00EUR*reference=CMD20250413025800321*societe=ALESIAMINCEUR*texte-libre=Séance d'aquavelo de 45 minutes*url_retour_err=https://www.aquavelo.com/annulation.php*url_retour_ok=https://www.aquavelo.com/confirmation.php*version=3.0";

$mac = strtoupper(hash_hmac('sha1', $chaine, pack('H*', $key_hex)));

echo "Chaîne : $chaine\n";
echo "MAC    : $mac\n";
