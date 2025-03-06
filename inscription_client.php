<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Client</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #47c3e6;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
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
    
    <?php include 'footer.php'; ?>
</body>
</html>



