<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nageurs'])) {
    $selectedNageurs = $_POST['nageurs']; // Tableau des IDs des nageurs sélectionnés
    $message = "Vous avez sélectionné " . count($selectedNageurs) . " maître(s)-nageur(s). Nous vous mettrons en contact prochainement.";
} else {
    $message = "Aucun maître-nageur sélectionné.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .message-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #47c3e6;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>Confirmation</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
    </div>
</body>
</html>