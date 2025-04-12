<?php
session_start();

// **** Configuration ****
$monetico_tpe = "AQUACANNES"; // Remplacez par votre code site
$monetico_cle = "AB477436DAE9200BF71E755208720A3CD5280594"; // Remplacez par votre clé de sécurité
$monetico_devise = "EUR";
$monetico_langue = "FR";
$monetico_url_home = "https://www.aquavelo.com/vente_formule.php"; // Important en mode test
$monetico_url_retour = "https://www.aquavelo.com/confirmation.php"; // URL de notification (CGI2)
$monetico_version = "3.0";
$monetico_societe = "ALESIAMINCEUR";

// **** Informations sur le produit ****
$produit_nom = "Soin Minceur";
$produit_prix = 99.00;
$produit_reference = uniqid("soin_"); // Générer une référence unique
$produit_texte_libre = "Achat du Soin Minceur"; // Texte libre pour le CGI2

// **** Création de la commande (Exemple - à adapter à votre base de données) ****
// Dans un vrai scénario, vous inséreriez ici la commande dans votre base de données
// avec un statut "en attente de paiement".

// Stocker la référence en session pour la récupérer après le retour de Monetico
$_SESSION['produit_reference'] = $produit_reference;

// **** Préparation des données pour Monetico ****
$montant = number_format($produit_prix, 2, '.', ''); // Formatage du montant (important!)

$params = array(
    "TPE" => $monetico_tpe,
    "date" => date("d/m/Y:H:i:s"),
    "montant" => $montant . $monetico_devise,
    "reference" => $produit_reference,
    "texte-libre" => $produit_texte_libre,
    "version" => $monetico_version,
    "lgue" => $monetico_langue,
    "societe" => $monetico_societe,
    "url_retour" => $monetico_url_home, // Correction importante : c'est bien l'URL home
    "url_retour_ok" => "https://www.aquavelo.com/vente_ok.php",
    "url_retour_err" => "https://www.aquavelo.com/vente_ko.php",
    "mail_surveillance" => "claude@alesiaminceur.com"
);

// **** Calcul de la signature ****
$str = "";
foreach ($params as $key => $value) {
    $str .= $value;
}
$signature = strtoupper(hash_hmac("sha1", $str, $monetico_cle));

$params["MAC"] = $signature; // Le nom du champ signature a changé depuis version 3.0
$cgi_url = "https://p.monetico.net/paiement.cgi"; // URL de paiement de Monetico (à vérifier)

?>

<!DOCTYPE html>
<html>
<head>
    <title>Achat du Soin Minceur</title>
</head>
<body>
    <h1>Achat du Soin Minceur</h1>
    <p>Vous avez choisi d'acheter le soin minceur pour 99 €.</p>
    <form action="<?php echo $cgi_url; ?>" method="POST">
        <?php
        foreach ($params as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
        }
        ?>
        <button type="submit">Payer avec Monetico</button>
    </form>
</body>
</html>
