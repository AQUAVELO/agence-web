<?php
session_start();

// **** Configuration ****
define('MONETICO_TPE', '6684349'); // Remplacez par votre code site
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594'); // Remplacez par votre clé de sécurité
define('MONETICO_COMPANY', 'ALESIAMINCEUR');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi'); // ou https://p.monetico.net/paiement.cgi en production
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation.php');  // Veiller à respecter exactement l'URL enregistrée (ici avec www)
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation.php');
define('MONETICO_VERSION', '3.0');
define('MONETICO_LANGUE', 'FR');

// Information du produit
$produit = [
    'nom' => 'Séance Aquavelo',
    'prix' => 99.00,
    'devise' => 'EUR',
    'description' => 'Séance d\'aquavelo de 45 minutes'
];

// Génération d'un ID de commande unique
$reference = 'CMD' . date('YmdHis') . rand(100, 999);

/**
 * Fonction de calcul du MAC.
 */
function calculateMAC($fields, $key) {
    // 1. Trier les paramètres par ordre ASCII
    ksort($fields, SORT_STRING);

    // 2. Construction de la chaîne à signer
    $chaine = '';
    foreach ($fields as $param => $value) {
        // **Ajouter cette ligne ici :**
        $value = mb_convert_encoding($value, 'UTF-8', 'auto');

        $chaine .= $param . '=' . $value . '*';
    }
    $chaine = rtrim($chaine, '*'); // Supprimer le dernier '*'

    // 3. Clé binaire
    $binaryKey = pack('H*', $key);

    // 4. Calcul du HMAC-SHA1 en majuscules
    return strtoupper(hash_hmac('sha1', $chaine, $binaryKey));
}

// Préparation des données pour Monetico
$dateCommande = date('d/m/Y:H:i:s');

$fields = [
    'TPE'             => MONETICO_TPE,
    'date'            => $dateCommande,
    'lgue'            => MONETICO_LANGUE,
    'mail'            => '', // Ce champ sera rempli lors de la soumission
    // Format du montant sur 12 chiffres (avec zéros à gauche) et 2 décimales, suivi de la devise
    'montant'         => sprintf('%012.2f', $produit['prix']) . $produit['devise'],
    'reference'       => $reference,
    'societe'         => MONETICO_COMPANY,
    'texte-libre'     => $produit['description'],
    'url_retour_err'  => MONETICO_CANCEL_URL,
    'url_retour_ok'   => MONETICO_RETURN_URL,
    'version'         => MONETICO_VERSION
];

// Stocker la référence en session pour la récupérer après le retour de Monetico
$_SESSION['produit_reference'] = $reference;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $fields['mail'] = $_POST['email'];

        // Calcul du MAC
        $mac = calculateMAC($fields, MONETICO_KEY);
        $fields['MAC'] = $mac;

        // Log des données pour débogage (fichier "monetico_log.txt" situé dans le même répertoire)
        file_put_contents('monetico_log.txt', "Date: " . date('Y-m-d H:i:s') . "\nDonnées envoyées à Monetico:\n" . print_r($fields, true), FILE_APPEND);

        // Redirection par soumission automatique du formulaire
        echo '<form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES) . '" value="' . htmlspecialchars($value, ENT_QUOTES) . '">';
        }
        echo '<input type="submit" value="Payer maintenant">';
        echo '</form>';
        echo '<script>document.getElementById("form-monetico").submit();</script>';
        exit;
    } else {
        $error = "Veuillez saisir une adresse email valide";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquavelo - Réservez votre séance à 99€</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* (Styles CSS - Identiques à votre code précédent) */
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">Aqua<span>Velo</span></div>
            <p>Votre centre de fitness aquatique</p>
        </div>
    </header>
    <main class="container">
        <section class="hero">
            <h1>Découvrez les bénéfices de l'Aquavelo</h1>
            <p>Une activité complète qui associe les bienfaits de l'eau et du vélo</p>
        </section>
        <section class="product">
            <div class="product-image"></div>
            <div class="product-details">
                <h2 class="product-title">Séance d'Aquavelo - 45 minutes</h2>
                <div class="product-price">99€</div>
                <div class="product-description">
                    <p>Profitez d'une séance d'Aquavelo de 45 minutes encadrée par nos coachs professionnels. Une expérience unique qui combine les bienfaits de l'aquagym et du cyclisme pour une activité sportive complète et peu traumatisante pour les articulations.</p>
                </div>
                <div class="checkout-form">
                    <h3>Réservez votre séance maintenant</h3>
                    <?php if (isset($error)): ?>
                        <p class="error"><?php echo $error; ?></p>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="email">Votre email :</label>
                            <input type="email" id="email" name="email" required placeholder="Entrez votre adresse email">
                        </div>
                        <button type="submit" class="btn btn-block">Payer 99€ et réserver ma séance</button>
                    </form>
                    <div class="secure-payment">
                        <i class="fas fa-lock"></i>
                        <div>Paiement sécurisé par Monetico</div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <div class="container">
            <p>© <?php echo date('Y'); ?> Aquavelo - Tous droits réservés</p>
        </div>
    </footer>
    <script>
    // Soumission automatique du formulaire une fois le mail validé
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            if (form.checkValidity()) {
                event.preventDefault(); // Empêche la soumission classique
                // Soumet le formulaire en JavaScript
                form.submit();
            }
        });
    });
    </script>
</body>
</html>
