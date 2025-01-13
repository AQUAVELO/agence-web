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
        #closeButton {
            position: fixed; /* Position fixe pour rester en haut à droite */
            top: 20px; /* Espacement depuis le haut */
            right: 20px; /* Espacement depuis la droite */
            background-color: #ff4444; /* Couleur rouge pour le bouton */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000; /* Assure que le bouton est au-dessus des autres éléments */
        }
        #closeButton:hover {
            background-color: #cc0000; /* Couleur au survol */
        }
    </style>
</head>
<body>
    <!-- Bouton "Fermer la fenêtre" en haut à droite -->
    <button id="closeButton">Fermer la fenêtre</button>

    <h1>Conseils pour perdre du poids</h1>
    <form id="weightForm">
        <!-- Formulaire inchangé -->
    </form>
    <div id="response"></div>

    <script>
        // Fonction pour interagir avec l'API via api_handler.php
        async function getAdvice(age, weight, localisation, moral, sport, eau) {
            const prompt = `Donne-moi des conseils pour perdre ${weight} kg pour une personne âgée de ${age} ans, avec une prise de poids localisée ${localisation}, qui se sent ${moral}, pratique une activité sportive cardiovasculaire ${sport}, et boit ${eau} d'eau par jour. Parle de son âge, propose l'aquavelo comme activité physique pour solutionner son problème de poids localisé, explique ce qu'il faut manger durant les repas, cite le nombre de kilos à perdre, et donne des conseils pour améliorer son moral et son hydratation si nécessaire. Limite la réponse à 12 lignes. Ne parle pas de consultation auprès d'un médecin.`;

            const response = await fetch('./api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ prompt })
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
                responseDiv.innerHTML = `
                    <p>${advice}</p>
                `;
            } catch (error) {
                responseDiv.textContent = 'Une erreur s\'est produite. Veuillez réessayer.';
                console.error(error);
            }
        });

        // Gestionnaire pour le bouton de fermeture
        document.getElementById('closeButton').addEventListener('click', () => {
            window.close(); // Ferme la fenêtre du navigateur
        });
    </script>
</body>
</html>

