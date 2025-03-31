<?php
// Informations Monetico (à remplacer par vos informations)
$monetico_tpe = "VOTRE_TPE"; // Numéro TPE
$monetico_cle = "VOTRE_CLE"; // Clé de sécurité
$monetico_devise = "EUR"; // Devise
$monetico_langue = "FR"; // Langue
$monetico_url_retour = "http://votresite.com/confirmation.php"; // URL de retour après paiement
$monetico_url_notify = "http://votresite.com/notification.php"; // URL de notification (IPN)
$monetico_mail_surveillance = "votre@email.com"; // Email de surveillance
$monetico_version = "3.0"; // Version du protocole
$monetico_societe = "VOTRE_SOCIETE"; // Nom de votre société
$monetico_url_home = "http://votresite.com"; // URL de votre site

// Informations de la commande
$montant = "99.00"; // Montant de la commande
$reference = uniqid(); // Référence unique de la commande

// Calcul de la signature
$texte_libre = ""; // Texte libre (peut être vide)
$date = gmdate("YmdHis");
$concat = $monetico_tpe . "*" . $date . "*" . $montant . "*" . $monetico_devise . "*" . $reference . "*" . $texte_libre . "*" . $monetico_cle;
$signature = hash("sha256", $concat);

// URL du formulaire Monetico
$url_monetico = "https://p.monetico-services.com/paiement.cgi";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Achat Formule</title>
</head>
<body>
    <h1>Formule à 99 €</h1>
    <p>Cliquez sur le bouton ci-dessous pour payer votre formule.</p>

    <form action="<?php echo $url_monetico; ?>" method="post">
        <input type="hidden" name="version" value="<?php echo $monetico_version; ?>">
        <input type="hidden" name="TPE" value="<?php echo $monetico_tpe; ?>">
        <input type="hidden" name="date" value="<?php echo $date; ?>">
        <input type="hidden" name="montant" value="<?php echo $montant; ?>">
        <input type="hidden" name="devise" value="<?php echo $monetico_devise; ?>">
        <input type="hidden" name="reference" value="<?php echo $reference; ?>">
        <input type="hidden" name="texte-libre" value="<?php echo $texte_libre; ?>">
        <input type="hidden" name="MAC" value="<?php echo $signature; ?>">
        <input type="hidden" name="url_retour" value="<?php echo $monetico_url_retour; ?>">
        <input type="hidden" name="url_retour_ok" value="<?php echo $monetico_url_retour; ?>">
        <input type="hidden" name="url_retour_err" value="<?php echo $monetico_url_retour; ?>">
        <input type="hidden" name="lgue" value="<?php echo $monetico_langue; ?>">
        <input type="hidden" name="societe" value="<?php echo $monetico_societe; ?>">
        <input type="hidden" name="mail" value="<?php echo $monetico_mail_surveillance; ?>">
        <input type="hidden" name="url_ipn" value="<?php echo $monetico_url_notify; ?>">
        <input type="hidden" name="url_boutique" value="<?php echo $monetico_url_home; ?>">
        <input type="submit" value="Payer 99 €">
    </form>
</body>
</html>
