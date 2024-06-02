<?php
session_start();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription et Connexion</title>
    <style>
        .container {
            text-align: center;
            padding: 50px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label, input {
            display: block;
            margin: auto;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    if (!empty($error_message)) {
        echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
    }
    ?>
    <h2>1) Inscrivez vous en écrivant votre email <br>et créez un mot de passe</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="register">S'inscrire</button>
    </form>

    <h2>2) Une fois que l'inscription est faite,</h2>
    <h3> re-notez ci dessous votre email et mot de passe <br> pour rentrer dans l'application.</h3>
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="login_btn">Se connecter</button>
    </form>
</div>
</body>
</html>


