$chaine = "TPE=6684349*date=13/04/2025:04:32:17*lgue=FR*mail=aquaveloeurope@gmail.com*montant=000000099.00EUR*reference=CMD20250413043217973*societe=ALESIAMINCEUR*texte-libre=Séance d'aquavelo de 45 minutes*url_retour_err=https://www.aquavelo.com/annulation.php*url_retour_ok=https://www.aquavelo.com/confirmation.php*version=3.0";
$key_hex = 'AB477436DAE9200BF71E755208720A3CD5280594';

$mac = strtoupper(hash_hmac('sha1', $chaine, pack('H*', $key_hex)));
echo $mac;

