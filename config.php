<?php



// Tes clés d'API Stripe (à remplacer par tes vraies clés)
$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: 'sk_test_51R0razK3GWmEE8Ic7OHpRMz8BcnfnuYSHmixqzKPD8Ww3UVrY5Fsti9B3Hiq8canrQ36M6lYpOSDg7uZcocLfikg00ipggxD7x'; // Utilise la variable d'env si définie
$stripePublicKey = getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_51R0razK3GWmEE8IcMClhIck5shXPh8iGQ41LwFbec7CTnfK3pKGbUA5FpW9Zw4Bm3Ho2GckSpEUMl7FKyggvD07S004FHPm73I';  // Utilise la variable d'env si définie

//Définition de la clé secrète
stripe\Stripe::setApiKey($stripeSecretKey);

// Vérification pour être certain que l'environnement est bien configuré.
if ($stripeSecretKey == 'sk_test_51R0razK3GWmEE8Ic7OHpRMz8BcnfnuYSHmixqzKPD8Ww3UVrY5Fsti9B3Hiq8canrQ36M6lYpOSDg7uZcocLfikg00ipggxD7x' || $stripePublicKey == 'pk_test_51R0razK3GWmEE8IcMClhIck5shXPh8iGQ41LwFbec7CTnfK3pKGbUA5FpW9Zw4Bm3Ho2GckSpEUMl7FKyggvD07S004FHPm73I') {
    error_log("⚠️  Les clés Stripe ne sont pas correctement configurées. L'application ne fonctionnera pas !!!");
}
?>
