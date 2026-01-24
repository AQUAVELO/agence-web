<?php
/**
 * Script de test pour l'envoi de SMS via SMSFactor
 */
require '_settings.php';

$destinataire = isset($_GET['phone']) ? $_GET['phone'] : '0622647095'; // Votre numéro par défaut
$message = "Test SMS Aquavelo - " . date('H:i:s');

echo "<pre>";
echo "Tentative d'envoi SMS à : " . $destinataire . "\n";
echo "Message : " . $message . "\n";
echo "Token configuré : " . (empty($settings['smsfactor_token']) ? "NON (❌ Erreur : variable SMSFACTOR vide)" : "OUI (✅)") . "\n\n";

if (!empty($settings['smsfactor_token'])) {
    $result = sendSMS($destinataire, $message);
    echo "Réponse de l'API SMSFactor :\n";
    echo $result;
} else {
    echo "L'envoi a été annulé car le token est manquant.";
}
echo "</pre>";
?>
