<?php
/**
 * Script de diagnostic pour tester l'API OpenAI
 */
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Diagnostic API Calculateur</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .test-section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
    .success { background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 5px; margin: 5px 0; }
    .error { background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin: 5px 0; }
    .info { background: #e3f2fd; color: #1565c0; padding: 10px; border-radius: 5px; margin: 5px 0; }
    pre { background: white; padding: 10px; border-radius: 5px; overflow-x: auto; }
    button { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
    button:hover { background: #45a049; }
  </style>
</head>
<body>
  <h1>🔍 Diagnostic API Calculateur de Calories</h1>
  
  <div class="test-section">
    <h2>1. Test de connexion directe à l'API OpenAI</h2>
    <button onclick="testDirectAPI()">Tester API OpenAI</button>
    <div id="direct-api-result"></div>
  </div>
  
  <div class="test-section">
    <h2>2. Test via api_handler.php</h2>
    <button onclick="testHandler()">Tester api_handler.php</button>
    <div id="handler-result"></div>
  </div>
  
  <div class="test-section">
    <h2>3. Test complet (comme dans le calculateur)</h2>
    <button onclick="testFull()">Tester calcul complet</button>
    <div id="full-result"></div>
  </div>

  <script>
    async function testDirectAPI() {
      const resultDiv = document.getElementById('direct-api-result');
      resultDiv.innerHTML = '<div class="info">Test en cours...</div>';
      
      try {
        // Note: Cette requête nécessiterait CORS si appelée depuis le navigateur
        // On va plutôt tester via api_handler.php
        resultDiv.innerHTML = '<div class="info">⚠️ Test direct impossible depuis le navigateur (CORS). Utilisez le test via api_handler.php.</div>';
      } catch (error) {
        resultDiv.innerHTML = '<div class="error">Erreur: ' + error.message + '</div>';
      }
    }
    
    async function testHandler() {
      const resultDiv = document.getElementById('handler-result');
      resultDiv.innerHTML = '<div class="info">Test en cours...</div>';
      
      try {
        const prompt = 'Répondez simplement "OK" en une phrase.';
        console.log('Envoi requête à api_handler.php...');
        
        const response = await fetch('./api_handler.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            prompt: prompt,
            max_tokens: 50
          })
        });
        
        console.log('Status:', response.status);
        const responseText = await response.text();
        console.log('Réponse brute:', responseText);
        
        let data;
        try {
          data = JSON.parse(responseText);
        } catch (e) {
          resultDiv.innerHTML = '<div class="error">❌ Erreur parsing JSON: ' + e.message + 
            '<br><pre>' + responseText.substring(0, 500) + '</pre></div>';
          return;
        }
        
        if (data.error) {
          resultDiv.innerHTML = '<div class="error">❌ Erreur API: ' + 
            (typeof data.error === 'string' ? data.error : JSON.stringify(data.error)) + 
            '<br><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
          return;
        }
        
        if (data.choices && data.choices[0] && data.choices[0].message) {
          resultDiv.innerHTML = '<div class="success">✅ Succès!<br>' +
            '<strong>Réponse:</strong> ' + data.choices[0].message.content +
            '<br><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
        } else {
          resultDiv.innerHTML = '<div class="error">⚠️ Structure inattendue:<br><pre>' + 
            JSON.stringify(data, null, 2) + '</pre></div>';
        }
        
      } catch (error) {
        console.error('Erreur complète:', error);
        resultDiv.innerHTML = '<div class="error">❌ Erreur: ' + error.message + 
          '<br><pre>' + error.stack + '</pre></div>';
      }
    }
    
    async function testFull() {
      const resultDiv = document.getElementById('full-result');
      resultDiv.innerHTML = '<div class="info">Test en cours...</div>';
      
      try {
        const prompt = 'Donne des conseils pour une personne de 30 ans, 70 kg, 170 cm, IMC 24.2. Cellulite: ventre. Moral: tout va bien. Sport: 3 heures par semaine. Eau: 1,5 litre et plus. Besoin calorique: 2000 calories. Propose l\'Aquavelo, des conseils alimentaires et des ajustements pour atteindre les objectifs minceur. Limite à 10 lignes et 350 tokens. Ne parle pas de professionnel de santé, ni de nutritionniste.';
        
        console.log('Test complet avec prompt complet...');
        const response = await fetch('./api_handler.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            prompt: prompt,
            max_tokens: 400
          })
        });
        
        console.log('Status:', response.status);
        const responseText = await response.text();
        console.log('Réponse (premiers 500 caractères):', responseText.substring(0, 500));
        
        let data;
        try {
          data = JSON.parse(responseText);
        } catch (e) {
          resultDiv.innerHTML = '<div class="error">❌ Erreur parsing JSON: ' + e.message + 
            '<br><pre>' + responseText.substring(0, 1000) + '</pre></div>';
          return;
        }
        
        if (data.error) {
          resultDiv.innerHTML = '<div class="error">❌ Erreur API: ' + 
            (typeof data.error === 'string' ? data.error : JSON.stringify(data.error)) + 
            '<br><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
          return;
        }
        
        if (data.choices && data.choices[0] && data.choices[0].message && data.choices[0].message.content) {
          resultDiv.innerHTML = '<div class="success">✅ Succès!<br>' +
            '<strong>Conseils générés:</strong><br>' +
            '<div style="background: white; padding: 15px; margin: 10px 0; border-radius: 5px; white-space: pre-wrap;">' + 
            data.choices[0].message.content + '</div>' +
            '<details><summary>Structure complète</summary><pre>' + 
            JSON.stringify(data, null, 2) + '</pre></details></div>';
        } else {
          resultDiv.innerHTML = '<div class="error">⚠️ Structure inattendue - pas de contenu trouvé:<br><pre>' + 
            JSON.stringify(data, null, 2) + '</pre></div>';
        }
        
      } catch (error) {
        console.error('Erreur complète:', error);
        resultDiv.innerHTML = '<div class="error">❌ Erreur: ' + error.message + 
          '<br><pre>' + error.stack + '</pre></div>';
      }
    }
  </script>
</body>
</html>
