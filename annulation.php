<?php
// annulation.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement Annulé - Aquavelo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Lien vers FontAwesome pour les éventuelles icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #00aaff;
            --accent-color: #ff9900;
            --light-color: #f5f9ff;
            --dark-color: #001a33;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            margin: 0;
            padding: 0;
        }
        header, footer {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            padding: 20px;
        }
        main {
            padding: 50px;
            text-align: center;
        }
        .btn {
            background-color: var(--accent-color);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #e68a00;
        }
    </style>
</head>
<body>
    <header>
        <h1>Aquavelo</h1>
    </header>
    <main>
        <h2>Paiement Annulé</h2>
        <p>Votre paiement a été annulé ou n'a pas pu être validé.</p>
        <p>Si vous avez la moindre question, n'hésitez pas à nous contacter ou à essayer à nouveau.</p>
        <a href="https://www.aquavelo.com/vente_formule.php" class="btn">Retour à la page d'accueil</a>
    </main>
    <footer>
        <p>© <?php echo date('Y'); ?> Aquavelo - Tous droits réservés</p>
    </footer>
</body>
</html>
