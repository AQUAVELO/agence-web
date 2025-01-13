<html>
<head>
    <style>.lw { font-size: 60px; }</style>
</head>
<body>
    <!-- Bouton rond "Fermer la fenêtre" en haut à droite -->
    <button id="bouton-fermeture">×</button>

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
        /* Style pour le bouton rond de fermeture */
        #bouton-fermeture {
            position: fixed; /* Position fixe pour rester en haut à droite */
            top: 20px; /* Espacement depuis le haut */
            right: 20px; /* Espacement depuis la droite */
            background-color: #ff4444; /* Couleur rouge pour le bouton */
            color: white;
            border: none;
            width: 40px; /* Largeur du bouton */
            height: 40px; /* Hauteur du bouton */
            border-radius: 50%; /* Rend le bouton rond */
            font-size: 24px; /* Taille du symbole × */
            cursor: pointer;
            z-index: 1000; /* Assure que le bouton est au-dessus des autres éléments */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #bouton-fermeture:hover {
            background-color: #cc0000; /* Couleur au survol */
        }
    </style>

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
    <!-- Div vide pour afficher la réponse -->
    <div id="response"></div>

    <script>
        // Remplacez par votre clé API OpenAI
        const apiKey = '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';

        // Fonction pour interagir avec l'API ChatGPT
        async function getAdvice(age, weight, localisation, moral, sport, eau) {
            const prompt = `Donne-moi des conseils pour perdre ${weight} kg pour une personne âgée de ${age} ans, avec une prise de poids localisée ${localisation}, qui se sent ${moral}, pratique une activité sportive cardiovasculaire ${sport}, et boit ${eau} d'eau par jour. Parle de son âge, propose l'aquavelo comme activité physique pour solutionner son problème de poids localisé, explique ce qu'il faut manger durant les repas, cite le nombre de kilos à perdre, et donne des conseils pour améliorer son moral et son hydratation si nécessaire. Limite la réponse à 12 lignes. Ne parle pas de consultation auprès d'un médecin.`;
            const url = 'https://api.openai.com/v1/chat/completions';

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${apiKey}`
                },
                body: JSON.stringify({
                    model: "gpt-3.5-turbo",
                    messages: [{ role: "user", content: prompt }],
                    max_tokens: 250 // Limite la réponse à 250 tokens
                })
            });

            const data = await response.json();
            return data.choices[0].message.content;
        }

        // Gestionnaire de formulaire
        document.getElementById('weightForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const age = document.getElementById('age').value;
            const weight = document.getElementById('weight').value;
            const localisation = document.getElementById('localisation').value;
            const moral = document.getElementById('moral').value;
            const sport = document.getElementById('sport').value;
            const eau = document.getElementById('eau').value;
            const responseDiv = document.getElementById('response');

            responseDiv.textContent = 'Je vais vous donner une réponse adaptée à vous, chargement...';

            try {
                const advice = await getAdvice(age, weight, localisation, moral, sport, eau);
                responseDiv.textContent = advice;
            } catch (error) {
                responseDiv.textContent = 'Une erreur s\'est produite. Veuillez réessayer.';
                console.error(error);
            }
        });

        // Gestionnaire pour le bouton de fermeture
        document.getElementById('bouton-fermeture').addEventListener('click', () => {
            window.close(); // Ferme la fenêtre du navigateur
        });
    </script>
</body>
</html>

