<?php
// Activer les erreurs en mode développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Récupération des données envoyées en GET par Monetico
$montant = $_GET['montant'] ?? '';
$reference = $_GET['reference'] ?? '';
$libelle = $_GET['texte-libre'] ?? '';
$date = $_GET['date'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement confirmé</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: sans-serif;
            background: #f4f4f4;
            color: #333;
            text-align: center;
            padding: 50px;
        }
        .confirmation {
            background: white;
            padding: 30px;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: green;
        }
    </style>
</head>
<body>
    <div class="confirmation">
        <h1>Paiement confirmé ✅</h1>
        <p><strong>Montant :</strong> <?= htmlspecialchars($montant) ?></p>
        <p><strong>Référence :</strong> <?= htmlspecialchars($reference) ?></p>
        <p><strong>Service :</strong> <?= htmlspecialchars($libelle) ?></p>
        <p><strong>Date :</strong> <?= htmlspecialchars($date) ?></p>
        <p>
        Merci pour votre achat !<br><br>
        Pour planifier votre séance de <strong>Cryo</strong>, merci d’envoyer un message WhatsApp à <strong>Loredana</strong> au <a href="https://wa.me/33755007387" target="_blank">07&nbsp;55&nbsp;00&nbsp;73&nbsp;87</a>.<br><br>
        À très bientôt,<br>
        <strong>Claude – AQUAVELO</strong>
        </p>

    </div>
</body>
</html>
