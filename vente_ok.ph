<?php
session_start();

// Récupérer la référence de la session
if (isset($_SESSION['produit_reference'])) {
    $reference = $_SESSION['produit_reference'];
} else {
    $reference = "Référence inconnue"; // Ou rediriger vers une page d'erreur
}

// **** RECUPERATION DES INFOS DE LA BASE DE DONNEES (à adapter) ****
// Ici, vous devez récupérer les informations de la commande à partir de votre base de données
// en utilisant la référence ($reference).

?>

<!DOCTYPE html>
<html>
<head>
    <title>Paiement Réussi</title>
</head>
<body>
    <h1>Paiement Réussi</h1>
    <p>Merci pour votre achat ! Votre paiement a été effectué avec succès.</p>
    <p>Référence de la commande : <?php echo htmlspecialchars($reference); ?></p>
    <p>Vous allez recevoir un email de confirmation.</p>
    <a href="index.php">Retour à l'accueil</a>
</body>
</html>
