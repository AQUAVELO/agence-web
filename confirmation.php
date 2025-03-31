<?php
// Récupération des paramètres de retour
$statut = $_GET['statut'];
$reference = $_GET['reference'];

// Traitement du statut de paiement
if ($statut == 'succes') {
    // Paiement réussi
    echo "<h1>Paiement réussi</h1>";
    echo "<p>Votre paiement pour la référence " . $reference . " a été effectué avec succès.</p>";
    // Ici, vous pouvez afficher des informations supplémentaires sur la commande,
    // envoyer un e-mail de confirmation, etc.
} elseif ($statut == 'echec') {
    // Paiement échoué
    echo "<h1>Paiement échoué</h1>";
    echo "<p>Votre paiement pour la référence " . $reference . " a échoué. Veuillez réessayer ou contacter le service client.</p>";
    // Ici, vous pouvez afficher un message d'erreur plus détaillé,
    // proposer à l'utilisateur de réessayer, etc.
} else {
    // Statut inconnu
    echo "<h1>Statut de paiement inconnu</h1>";
    echo "<p>Le statut de votre paiement est inconnu. Veuillez contacter le service client.</p>";
}

// Vous pouvez également récupérer d'autres informations de la commande
// à partir de votre base de données en utilisant la référence.
// ...

?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de paiement</title>
</head>
<body>
    <?php
    // Le contenu dynamique (messages de succès/échec) est déjà affiché ci-dessus.
    ?>
</body>
</html>
