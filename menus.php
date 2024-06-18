<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Partenariats</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .menu-container {
            text-align: center;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .menu-container h1 {
            margin-bottom: 20px;
        }
        .menu-container a {
            display: block;
            margin: 10px 0;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .menu-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1>Menu Partenariats</h1>
        <a href="insert_partenariats.php">Créer une fiche de partenariat</a>
        <a href="affichage.php">Afficher toutes les fiches de partenariats</a>
        <a href="affichagelocal.php">Site Internet</a>
        <a href="reset_password.php">Réinitialiser le mot de passe</a>
        <a href="menus.php">Retour au Menu</a>
    </div>
</body>
</html>
