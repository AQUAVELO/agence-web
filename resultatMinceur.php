<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseils pour perdre du poids</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        #response {
            margin-top: 20px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 4px;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <h1>Conseils pour perdre du poids</h1>
    <form id="weightForm">
        <label for="age">Choisissez votre tranche d'âge :</label>
        <select id="age" name="age">
            <option value="15-25">15-25 ans</option>
            <option value="26-35">26-35 ans</option>
            <option value="36-45">36-45 ans</option>
            <option value="46-55">46-55 ans</option>
            <option value="56-65">56-65 ans</option>
            <option value="66-75">66-75 ans</option>
            <option value="80+">80 ans et plus</option>
        </select>

        <label for="weight">Choisissez votre poids en trop :</label>
        <select id="weight" name="weight">
            <option value="moins de 5">5 kg et moins</option>
            <option value="6 à 9">6 à 9 kg</option>
            <option value="10 à 15">10 à 15 kg</option>
            <option value="15 à 20">15 à 20 kg</option>
            <option value="plus de 21">plus de 21 kg</option>
        </select>

        <label for="localisation">Où est localisée votre prise de poids ?</label>
        <select id="localisation" name="localisation">
            <option value="ventre">Plutôt sur le ventre</option>
            <option value="hanches">Plutôt sur les hanches</option>
            <option value="ventre et hanches">Sur le ventre et les hanches</option>
        </select>

        <label for="moral">Comment vous sentez-vous actuellement ?</label>
        <select id="moral" name="moral">
            <option value="stressé">Plutôt stressé</option>
            <option value="fatigué">Plutôt fatigué</option>
            <option value="tout va bien">Tout va bien</option>
        </select>

        <label for="sport">Pratiquez-vous une activité sportive cardiovasculaire ?</label>
        <select id="sport" name="sport">
            <option value="1 heure par semaine">1 heure par semaine</option>
            <option value="3 heures par semaine">3 heures par semaine</option>
            <option value="plus de 3 heures par semaine">Plus de 3 heures par semaine</option>
            <option value="pas du tout">Pas du tout</option>
        </select>

        <label for="eau">Combien d'eau buvez-vous par jour ?</label>
        <select id="eau" name="eau">
            <option value="1,5 litre et plus">1,5 litre et plus</option>
            <option value="1 litre">1 litre</option>
            <option value="1/2 litre et moins">1/2 litre et moins</option>
        </select>

        <button type="submit">Obtenir des conseils</button>
    </form>
    <div id="response"></div>

    <script>
        document.getElementById('weightForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            // Récupération des données du formulaire
            const age = document.getElementById('age').value;
            const weight = document.getElementById('weight').value;
            const localisation = document.getElementById('localisation').value;
            const moral = document.getElementById('moral').value;
            const sport = document.getElementById('sport').value;
            const eau = document.getElementById('eau').value;

            const responseDiv = document.getElementById('response');
            responseDiv.textContent = 'Je vais vous donner une réponse adaptée à vous, chargement...';

            // Crée le prompt et appelle le backend
            const prompt = `Donne-moi des conseils pour perdre ${weight} kg pour une personne âgée de ${age} ans, avec une prise de poids localisée ${localisation}, qui se sent ${moral}, pratique une activité sportive cardiovasculaire ${sport}, et boit ${eau} d'eau par jour.`;

            try {
                const response = await fetch('/api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt })
                });

                if (!response.ok) throw new Error('Erreur lors de la communication avec le serveur');

                const data = await response.json();
                responseDiv.textContent = data.choices[0].message.content;
            } catch (error) {
                responseDiv.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                console.error(error);
            }
        });
    </script>
</body>
</html>


