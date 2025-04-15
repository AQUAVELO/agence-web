<?php
session_start();

$prenom    = $_SESSION['prenom']    ?? '';
$nom       = $_SESSION['nom']       ?? '';
$telephone = $_SESSION['telephone'] ?? '';
$email     = $_SESSION['email']     ?? '';
$achat     = $_SESSION['achat']     ?? '';
$montant   = $_SESSION['montant']   ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Merci pour votre achat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #cc3366;
        }
        p {
            font-size: 1.1em;
            margin: 10px 0;
        }
        .highlight {
            font-weight: bold;
            color: #444;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Merci <span class="highlight"><?= htmlspecialchars($prenom . ' ' . $nom) ?></span> !</h1>
    <p>Votre achat de <span class="highlight"><?= htmlspecialchars($achat) ?></span> pour un montant de <span class="highlight"><?= htmlspecialchars($montant) ?></span> a bien été confirmé.</p>
    <p>📧 Email : <span class="highlight"><?= htmlspecialchars($email) ?></span></p>
    <p>📱 Téléphone : <span class="highlight"><?= htmlspecialchars($telephone) ?></span></p>
    <p>👉 Pour prendre rendez-vous, contactez <strong>Loredana</strong> via WhatsApp au <strong>07 55 00 73 87</strong>.</p>
    <p>À bientôt,<br><strong>Claude – Équipe AQUAVELO</strong></p>
</div>
</body>
</html>

