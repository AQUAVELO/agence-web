<?php

session_start();
// **** Configuration ****
$monetico_tpe = "AQUACANNES"; // Remplacez par votre code site
$monetico_cle = "AB477436DAE9200BF71E755208720A3CD5280594"; // Remplacez par votre clé de sécurité
$monetico_mail_surveillance = "claude@alesiaminceur.com"; // Remplacez par votre adresse email de surveillance
$monetico_debug = true; // Mettez à false en production
$isTest = true; // à false en production

// **** Fonctions Utiles ****

function log_message($message) {
    global $monetico_debug;
    $log_file = "monetico_log.txt"; // Nom du fichier de log (sécurisez l'accès à ce fichier !)
    $date = date("Y-m-d H:i:s");
    $message = "[" . $date . "] " . $message . "\n";
    if ($monetico_debug) {
        error_log($message, 3, $log_file);
    }
}

function verify_signature($params, $cle) {
    $str = "";
    foreach ($params as $key => $value) {
        if (strtoupper($key) != "MAC") { // Utiliser MAC ici (et non signature)
            $str .= $value;
        }
    }
    $calculated_signature = strtoupper(hash_hmac("sha1", $str, $cle));
    return $calculated_signature == strtoupper($params["MAC"]); // Comparer avec MAC
}

// **** Réception des données de Monetico ****

log_message("--- Début de la réception des données de Monetico ---");

// Récupérer tous les paramètres POST
$params = $_POST;

// Journaliser les paramètres reçus
log_message("Paramètres reçus : " . json_encode($params));

// **** Vérification de la signature ****

if (isset($params["MAC"])) { // Vérifier la présence de MAC
    if (verify_signature($params, $monetico_cle)) {
        log_message("Signature valide.");

        // **** Traitement de la transaction ****

        $montant = $params["montant"];
        $devise = substr($montant, -3); // Extraire la devise du montant
        $montant_net = substr($montant, 0, strlen($montant) - 3); // Extraire le montant numérique
        $reference = $params["reference"];
        $code_retour = $params["code_retour"];
        $texte_libre = $params["texte-libre"]; // Informations supplémentaires
        $motif_refus = isset($params["motif_refus"]) ? $params["motif_refus"] : ""; // Motif du refus s'il y en a un

        log_message("Montant: " . $montant . ", Référence: " . $reference . ", Code Retour: " . $code_retour);

        if ($code_retour == "paiement") {
            // Paiement réussi

            log_message("Paiement réussi.");

            // **** MISE À JOUR DE LA BASE DE DONNÉES (À IMPLÉMENTER) ****
            // Ici, vous devez :
            // 1. Vous connecter à votre base de données.
            // 2. Rechercher la commande correspondant à la référence ($reference).
            // 3. Vérifier que le montant et la devise correspondent.
            // 4. Mettre à jour l'état de la commande à "payée".
            // 5. Enregistrer les informations de la transaction (ID de transaction, etc.).

            // Exemple (à adapter à votre base de données) :
            // $conn = new mysqli("localhost", "user", "password", "database");
            // $sql = "UPDATE commandes SET etat = 'payee' WHERE reference = '$reference' AND montant = '$montant' AND devise = '$devise'";
            // if ($conn->query($sql) === TRUE) {
            //     log_message("Commande $reference mise à jour avec succès.");
            // } else {
            //     log_message("Erreur lors de la mise à jour de la commande $reference: " . $conn->error);
            //     // Gérer l'erreur (envoyer un email d'alerte, etc.)
            // }
            // $conn->close();
         if($isTest) {
              log_message("MODE TEST ACTIF : Pas de mise à jour de la BDD.");
         }

            // Envoyer un email de confirmation à l'administrateur
            $subject = "Paiement réussi - Référence: " . $reference;
            $message = "Un paiement de " . $montant_net . " " . $devise . " a été effectué avec succès pour la référence " . $reference . ".";
            mail($monetico_mail_surveillance, $subject, $message);

        } else {
            // Paiement refusé ou annulé

            log_message("Paiement refusé ou annulé. Code retour: " . $code_retour . ", Motif refus: " . $motif_refus);

            // **** MISE À JOUR DE LA BASE DE DONNÉES (À IMPLÉMENTER) ****
            // Ici, vous devez :
            // 1. Vous connecter à votre base de données.
            // 2. Rechercher la commande correspondant à la référence ($reference).
            // 3. Mettre à jour l'état de la commande à "annulée" ou "refusée".
            // 4. Enregistrer le motif du refus.

            // Envoyer un email d'alerte à l'administrateur
            $subject = "Paiement refusé - Référence: " . $reference;
            $message = "Un paiement a été refusé pour la référence " . $reference . ". Code retour: " . $code_retour . ", Motif refus: " . $motif_refus;
            mail($monetico_mail_surveillance, $subject, $message);
        }

        if (isset($_SESSION['produit_reference'])) {
            unset($_SESSION['produit_reference']); // Suppression de la session après traitement
            log_message("Suppression de la référence de session.");
        }

    } else {
        // Signature invalide

        log_message("Signature invalide!");

        // Envoyer un email d'alerte à l'administrateur
        $subject = "ALERTE : Signature invalide reçue de Monetico!";
        $message = "Une tentative de paiement avec une signature invalide a été détectée. Veuillez vérifier les logs.";
        mail($monetico_mail_surveillance, $subject, $message);
    }
} else {
    log_message("Aucune signature reçue!");
    $subject = "ALERTE : Aucune signature reçue de Monetico!";
    $message = "Aucune signature reçue de Monetico.";
    mail($monetico_mail_surveillance, $subject, $message);
}

// **** Réponse à Monetico (OBLIGATOIRE) ****

echo "OK"; // Important : Monetico attend "OK" comme réponse en cas de succès (même partiel)
log_message("Réponse envoyée à Monetico: OK");

log_message("--- Fin de la réception des données de Monetico ---");

?>
