<?php
require '_settings.php'; // Inclut la connexion à la base de données ($database)
$successMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nom && $prenom && $telephone && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $database->prepare("INSERT INTO formule (nom, prenom, tel, email, vente, date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$nom, $prenom, $telephone, $email, true]);
        $successMessage = "✅ Votre réservation a bien été enregistrée. Merci !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Séance Découverte de Cryolipolyse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f8fb;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .section {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #104e8b;
            text-align: center;
        }
        p {
            font-size: 1.1em;
        }
        ul {
            padding-left: 20px;
        }
        .form-section, .image-section, .avis-section {
            margin-top: 40px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            background-color: #104e8b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #0d3e70;
        }
        .error {
            color: red;
            text-align: center;
        }
        .image-section img {
            width: 75%;
            border-radius: 10px;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .avis-section {
            background: #e8f0fe;
            padding: 20px;
            border-radius: 10px;
        }
        .avis {
            font-style: italic;
            margin-bottom: 15px;
        }
        .avis strong {
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="section">
        <h1>Vous souhaitez mincir ou perdre du poids ?</h1>
        <h2>Découvrez l'amincissement par cryolipolyse à 99€</h2>

        <div class="image-section">
            <img src="https://www.institutcryo.fr/wp-content/uploads/2022/11/cryolipolyse-institut.jpg" alt="Séance de cryolipolyse">
        </div>

        <h2>Qu’est-ce que la Cryolipolyse ?</h2>
        <p>La Cryolipolyse est une méthode d’amincissement qui permet :</p>
        <ul>
            <li>De sculpter la silhouette grâce à l’application de plaques de froid</li>
            <li>De tonifier les zones traitées</li>
            <li>De traiter de nombreuses zones : ventre, cuisses, hanches, bras…</li>
            <li>De réduire les cellules graisseuses de manière naturelle</li>
        </ul>

        <div class="avis-section">
            <h2>Ce qu’en pensent nos clients</h2>
            <div class="avis">"Très satisfaite de ma séance, j’ai vu une vraie différence au bout de 3 semaines."<br><strong>— Julie R.</strong></div>
            <div class="avis">"Accueil chaleureux, protocole bien expliqué. Je recommande vivement."<br><strong>— Caroline B.</strong></div>
            <div class="avis">"Top ! Le centre est propre, les machines sont modernes et efficaces."<br><strong>— Nathalie D.</strong></div>
        </div>

        <div class="form-section">
            <h2>Réservez votre séance découverte</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <?php if (isset($successMessage)): ?>
                <p class="success" id="successMessage" style="text-align:center; color:green; font-weight:bold; margin-top: 10px;">
                    <?= $successMessage ?>
                </p>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById('successMessage');
                        if (msg) msg.style.display = 'none';
                    }, 5000);
                </script>
            <?php endif; ?>
            <form method="post" action="">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required>

                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required>

                <label for="telephone">Téléphone *</label>
                <input type="tel" id="telephone" name="telephone" required>

                <label for="email">Adresse email *</label>
                <input type="email" id="email" name="email" required>

                <button type="submit">Réserver ma séance à 99€</button>
            </form>
        </div>
            <div style="text-align: center; margin-top: 30px; font-size: 1.1em; color: #333;">
            📍 <strong>AQUAVELO</strong><br>
            <a href="https://maps.google.com/?q=60 avenue du Docteur Raymond Picaud, Cannes" target="_blank">60 avenue du Docteur Raymond Picaud à CANNES</a><br>
            ☎️ <strong>04 93 93 05 65</strong>
        </div>
    </div>
</body>
</html>









