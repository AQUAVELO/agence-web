<?php
// Informations Monetico (à remplacer par vos informations)
$monetico_tpe = "6684349";
$monetico_cle = "AB477436DAE9200BF71E755208720A3CD5280594";
$monetico_devise = "EUR";
$monetico_langue = "FR";
$monetico_url_retour = "https://www.aquavelo.com/confirmation.php";
$monetico_url_notify = "https://www.aquavelo.com/notification.php";
$monetico_mail_surveillance = "aqua.cannes@gmail.com";
$monetico_version = "3.0";
$monetico_societe = "ALESIAMINCEUR";
$monetico_url_home = "https://www.aquavelo.com/vente_formule.php";

// Informations de la commande
$montant = "99.00";
$reference = uniqid();

// Calcul de la signature
$texte_libre = "";
$date = gmdate("YmdHis");
$concat = $monetico_tpe . "*" . $date . "*" . $montant . "*" . $monetico_devise . "*" . $reference . "*" . $texte_libre . "*" . $monetico_cle;
$signature = hash("sha256", $concat);

// URL du formulaire Monetico
$url_monetico = "https://p.monetico-services.com/paiement.cgi";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Achat Séance Cryolipolyse - Aquavelo Cannes</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
        }
        img {
            max-width: 50%; /* Image réduite de moitié */
            height: auto;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        input[type="submit"] {
            background-color: #008CBA; /* Couleur de fond du bouton */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 18px; /* Taille de la police du bouton */
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 10px; /* Bords arrondis du bouton */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Achat d'une séance de Cryolipolyse - Aquavelo Cannes</h1>
        <img src="images/cryo.jpg" alt="Séance Cryolipolyse Aquavelo Cannes">
        <p>
            Profitez de notre offre exceptionnelle : une séance de Cryolipolyse d'une heure à seulement 99 € !
            Ciblez la zone de votre choix (ventre, cuisses, fessier, etc.) et définissez-la avec notre praticienne expérimentée.
            Offre valable jusqu'au 10/04/2025.
        </p>
        <p>
            Rendez-vous chez Aquavelo : 60 avenue du Docteur Raymond Picaud, Cannes.
        </p>

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
    </div>
</body>
</html>
