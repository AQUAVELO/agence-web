<?php

require_once('vendor/autoload.php'); // Si tu utilises Composer

// Tes clés d'API Stripe (à remplacer par tes vraies clés)
$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: 'sk_test_xxxxxxxxxxxxx'; // Utilise la variable d'env si définie
$stripePublicKey = getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_xxxxxxxxxxxxx';  // Utilise la variable d'env si définie

//Définition de la clé secrète
stripe\Stripe::setApiKey($stripeSecretKey);

// Vérification pour être certain que l'environnement est bien configuré.
if ($stripeSecretKey == 'sk_test_xxxxxxxxxxxxx' || $stripePublicKey == 'pk_test_xxxxxxxxxxxxx') {
    error_log("⚠️  Les clés Stripe ne sont pas correctement configurées. L'application ne fonctionnera pas !!!");
}
?>
