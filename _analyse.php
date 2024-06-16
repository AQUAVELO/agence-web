<!DOCTYPE html>
<html>
<head>
    <title>Inscription / Connexion</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .container {
            display: flex;
            justify-content: center;
            padding: 50px;
        }
        .form-container, .info-container {
            width: 45%;
            margin: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label, input {
            display: block;
            margin: auto;
        }
        button {
            background-color: #69d5ef;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            margin: 5px;
            color: black;
        }
        .info-box {
            border: 1px solid #000;
            padding: 20px;
            text-align: center;
        }
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        .logo-container img {
            width: 250px;
        }
        .logo-container .results-frame {
            margin-top: 20px;
            padding: 10px;
            border: 2px solid #000;
            text-align: center;
            background-color: #f0f0f0;
            width: 100%;
        }
        .logo-container button {
            margin-left: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
    <script>
        function setAction(action) {
            document.getElementById('action').value = action;
        }
    </script>
</head>
<body>
<div class="container">
    <div class="form-container">
        <?php
        if (!empty($error_message)) {
            echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
        }
        if (!empty($success_message)) {
            echo '<p class="success">' . htmlspecialchars($success_message) . '</p>';
        }
        ?>
        <h3>1) Inscrivez-vous ou connectez-vous en écrivant votre email et mot de passe</h3>
        <form method="post" action="">
            <input type="hidden" id="action" name="action" value="register">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" onclick="setAction('register')">S'inscrire</button>
            <button type="submit" onclick="setAction('login')">Se connecter</button>
        </form>
        <div class="logo-container">
            <a href="https://www.aquavelo.com">
                <img src="images/content/LogoAQUASPORTMINCEUR.webp" alt="Logo AQUAVELO">
            </a>
            <div class="results-frame">
                <p>Résultats de ce mois-ci : félicitations à Isabelle</p>
                <p>- 5 kg</p>
                <p>- 5 cm de tour de taille</p>
                <p>- 6 cm de tour de hanches</p>
                <p>- 8 cm de tour de fesses</p>
            </div>
        </div>
    </div>
    <div class="info-container">
        <div class="info-box">
            <h2>Vos mensurations</h2>
            <p>Entrez vos mensurations pour bénéficier de conseils personnalisés.</p>

            <h3>Inscription</h3>
            <p>Veuillez entrer un email et un mot de passe pour vous inscrire.</p>
            <p>Ensuite, validez votre inscription avec votre email et mot de passe.</p>

            <h3>Suivi</h3>
            <p>Vous pouvez faire le suivi de vos mensurations dans votre centre Aquavélo, profiter des conseils, et consulter vos résultats.</p>
            <p>Vous pouvez également prendre vos mensurations vous-même :</p>
            <ul>
                <li><strong>Poids</strong> : Le matin à jeun.</li>
                <li><strong>Taille</strong> : Au niveau du nombril.</li>
                <li><strong>Hanches</strong> : Au niveau des iliaques.</li>
                <li><strong>Tour de fesses</strong> : Sur la pointe des fesses.</li>
            </ul>
            <p>Notez ces mensurations pour un suivi régulier.</p>
        </div>
    </div>
</div>
</body>
</html>



















