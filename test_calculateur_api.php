<?php
/**
 * Script de test pour le calculateur de calories - Test de l'API
 */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Test API Calculateur</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
    button { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #45a049; }
    #result { margin-top: 20px; }
  </style>
</head>
<body>
  <h2>Test de l'API Calculateur</h2>
  <button onclick="testAPI()">Tester l'API</button>
  <div id="result"></div>

  <script>
    async function testAPI() {
      const resultDiv = document.getElementById('result');
      resultDiv.innerHTML = '<p>Test en cours...</p>';
      
      const prompt = 'Donne des conseils pour une personne de 30 ans, 70 kg, 170 cm, IMC 24.2. Cellulite: ventre. Moral: tout va bien. Sport: 3 heures par semaine. Eau: 1,5 litre et plus. Besoin calorique: 2000 calories. Propose l\'Aquavelo, des conseils alimentaires et des ajustements pour atteindre les objectifs minceur. Limite à 10 lignes et 350 tokens. Ne parle pas de professionnel de santé, ni de nutritionniste.';
      
      try {
        console.log('Envoi de la requête...');
        const response = await fetch('./api_handler.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            prompt: prompt,
            max_tokens: 400
          })
        });

        console.log('Code HTTP:', response.status);
        console.log('Headers:', [...response.headers.entries()]);

        const responseText = await response.text();
        console.log('Réponse brute:', responseText);

        let data;
        try {
          data = JSON.parse(responseText);
          console.log('Données parsées:', data);
        } catch (e) {
          resultDiv.innerHTML = '<pre>❌ Erreur parsing JSON: ' + e.message + '\n\nRéponse brute:\n' + responseText + '</pre>';
          return;
        }

        if (data.error) {
          resultDiv.innerHTML = '<pre style="background: #ffebee; color: #c62828;">❌ Erreur API:\n' + JSON.stringify(data, null, 2) + '</pre>';
          return;
        }

        if (!data.choices || !data.choices[0]) {
          resultDiv.innerHTML = '<pre style="background: #fff3cd; color: #856404;">⚠️ Structure de réponse inattendue:\n' + JSON.stringify(data, null, 2) + '</pre>';
          return;
        }

        resultDiv.innerHTML = '<pre style="background: #e8f5e9; color: #2e7d32;">✅ Succès!\n\nContenu:\n' + data.choices[0].message.content + '\n\nStructure complète:\n' + JSON.stringify(data, null, 2) + '</pre>';

      } catch (error) {
        console.error('Erreur complète:', error);
        resultDiv.innerHTML = '<pre style="background: #ffebee; color: #c62828;">❌ Erreur: ' + error.message + '\n\nStack:\n' + error.stack + '</pre>';
      }
    }
  </script>
</body>
</html>
