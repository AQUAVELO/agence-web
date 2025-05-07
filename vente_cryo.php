<?php
// vente_cryo.php
// Page de préparation et redirection vers Monetico

require 'settings.php';    // Définissez ici :
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'ALESIAMINCEUR');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation_cryo.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation_cryo.php');


// Si on arrive en POST, on prépare le paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Récupération et validation des données client
    $email     = filter_input(INPUT_POST, 'email',     FILTER_VALIDATE_EMAIL);
    $prenom    = filter_input(INPUT_POST, 'prenom',    FILTER_SANITIZE_STRING);
    $nom       = filter_input(INPUT_POST, 'nom',       FILTER_SANITIZE_STRING);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $achat     = filter_input(INPUT_POST, 'achat',     FILTER_SANITIZE_STRING);
    $montant   = filter_input(INPUT_POST, 'montant',   FILTER_SANITIZE_STRING); 
    if (!$email || !$prenom || !$nom || !$achat || !$montant) {
        die("Données incomplètes ou invalides.");
    }

    // 2) Génération d’une référence unique
    $reference = $TPE . date('YmdHis') . rand(1000,9999);

    // 3) Construction de texte-libre (séparateur ;)
    $texteLibre = http_build_query([
        'email'     => $email,
        'prenom'    => $prenom,
        'nom'       => $nom,
        'telephone' => $telephone,
        'achat'     => $achat,
        'montant'   => $montant
    ], '', ';');

    // 4) Paramètres Monetico
    $params = [
        'TPE'            => $TPE,
        'date'           => date('d/m/Y:H:i:s'),
        'montant'        => $montant,
        'reference'      => $reference,
        'texte-libre'    => $texteLibre,
        'version'        => '2',
        'url_retour'     => $url_retour,
        'url_retour_ok'  => $url_retour_ok,
        'url_retour_err' => $url_retour_err,
    ];

    // 5) Calcul du MAC HMAC-SHA1
    ksort($params, SORT_STRING);
    $data = '';
    foreach ($params as $k => $v) {
        $data .= "$k=$v*";
    }
    $data = rtrim($data, '*');
    $MAC = strtoupper(hash_hmac('sha1', $data, pack('H*', MONETICO_KEY)));

    // 6) Redirection automatique vers Monetico
    ?>
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"><title>Redirection paiement</title></head>
    <body onload="document.forms['monetico'].submit();">
      <form name="monetico" method="post" action="https://p.monetico-services.com/test/paiement.cgi">
        <?php foreach ($params as $k => $v): ?>
          <input type="hidden" name="<?php echo htmlspecialchars($k) ?>"
                 value="<?php echo htmlspecialchars($v) ?>">
        <?php endforeach; ?>
        <input type="hidden" name="MAC" value="<?php echo $MAC ?>">
        <noscript>
          <p>Veuillez cliquer sur le bouton ci-dessous pour payer :</p>
          <button type="submit">Payer <?php echo htmlspecialchars($montant) ?></button>
        </noscript>
      </form>
    </body>
    </html>
    <?php
    exit;
}

// Si on arrive en GET, on affiche le formulaire de collecte
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Achat Cryo</title></head>
<body>
<h1>Réservez votre séance Cryo</h1>
<form method="post" action="">
  <label>Email      : <input type="email"    name="email"     required></label><br>
  <label>Prénom     : <input type="text"     name="prenom"    required></label><br>
  <label>Nom        : <input type="text"     name="nom"       required></label><br>
  <label>Téléphone  : <input type="text"     name="telephone" required></label><br>
  <label>Offre      :
    <select name="achat">
      <option value="Séance Cryo">Séance Cryo – 1 séance</option>
      <option value="Pack 3 Séances">Pack Cryo – 3 séances</option>
      <option value="Pack 5 Séances">Pack Cryo – 5 séances</option>
    </select>
  </label><br>
  <label>Montant (ex: 50.00EUR) : <input type="text" name="montant" required></label><br>
  <button type="submit">Payer maintenant</button>
</form>
</body>
</html>









