<?php
// Vérification de la signature (MAC)
// ...

// Traitement des données de Monetico
$reference = $_POST['reference'];
$montant = $_POST['montant'];
$code_retour = $_POST['code-retour'];

if ($code_retour == 'paiement') {
    // Paiement réussi
    // Mettre à jour la base de données
    // Envoyer des emails de confirmation
} else {
    // Paiement échoué
    // Mettre à jour la base de données
    // Enregistrer l'erreur dans un journal
}

// Réponse à Monetico (obligatoire)
echo 'OK';
?>
