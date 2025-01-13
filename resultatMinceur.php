<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseils pour perdre du poids</title>
    <style>
        /* Styles restent les mêmes */
    </style>
</head>
<body>
    <h1>Conseils pour perdre du poids</h1>
    <form id="weightForm">
        <!-- Éléments du formulaire restent les mêmes -->
        <button type="submit">Obtenir des conseils</button>
    </form>
    <div id="response"></div>

    <script>
        document.getElementById('weightForm').addEventListener('submit', async (event) => {
            event.preventDefault(); // Empêche le comportement par défaut

            // Récupère les données du formulaire
            var age = document.getElementById('age').value;
            var weight = document.getElementById('weight').value;
            var localisation = document.getElementById('localisation').value;
            var moral = document.getElementById('moral').value;
            var sport = document.getElementById('sport').value;
            var eau = document.getElementById('eau').value;

            // Construit le prompt dynamique
            var prompt = 'Conseils pour perdre du poids pour une personne de ' + age + ' ans, avec ' + weight + ' kg en trop, principalement sur ' + localisation + ', se sent ' + moral + ', fait ' + sport + ' de sport par semaine, et boit ' + eau + ' d\'eau par jour.';

            // Affiche un message de chargement
            document.getElementById('response').innerHTML = 'En cours de traitement...';

            // Appelle getAdvice avec le prompt
            var advice = await getAdvice(prompt);

            // Affiche la réponse
            document.getElementById('response').innerHTML = advice;
        });

        async function getAdvice(prompt) {
            try {
                const response = await fetch('http://www.aquavelo.com/api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': '
