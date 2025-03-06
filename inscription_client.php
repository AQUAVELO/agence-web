<?php require '_settings.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Client</title>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container" style="margin-top: 20px;">
        <div class="form-container">
            <h2>Inscription Client</h2>
            <form action="traitement_client.php" method="POST">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required>
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required>
                <label for="tel">Téléphone</label>
                <input type="text" id="tel" name="tel">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse">
                <label for="ville">Ville</label>
                <input type="text" id="ville" name="ville">
                <label for="dept">Département *</label>
                <select id="dept" name="dept" required>
                    <option value="">Sélectionnez un département</option>
                    <option value="01">01 - Ain</option>
                    <option value="02">02 - Aisne</option>
                    <option value="03">03 - Allier</option>
                    <option value="04">04 - Alpes-de-Haute-Provence</option>
                    <option value="05">05 - Hautes-Alpes</option>
                    <option value="06">06 - Alpes-Maritimes</option>
                    <option value="07">07 - Ardèche</option>
                    <option value="08">08 - Ardennes</option>
                    <option value="09">09 - Ariège</option>
                    <option value="10">10 - Aube</option>
                </select>
                <label for="activites">Activités souhaitées (natation, cours collectifs...)</label>
                <textarea id="activites" name="activites" rows="4"></textarea>
                <label for="besoin">Spécificités (handicap, domicile...)</label>
                <textarea id="besoin" name="besoin" rows="4"></textarea>
                <button type="submit">S'inscrire</button>
            </form>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>



