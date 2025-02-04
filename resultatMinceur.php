<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conseils pour perdre du poids & Calculateur de calories</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 20px;
      color: #333;
    }
    h1, h2 {
      text-align: center;
      color: #4CAF50;
    }
    form {
      max-width: 500px;
      margin: 20px auto;
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
    #calorieResult, #response {
      margin-top: 20px;
      padding: 15px;
      background: #e8f5e9;
      border-radius: 4px;
      color: #2e7d32;
    }
    /* Bouton rond de fermeture */
    #bouton-fermeture {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #ff4444;
      color: white;
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      font-size: 24px;
      cursor: pointer;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    #bouton-fermeture:hover {
      background-color: #cc0000;
    }
  </style>
</head>
<body>
  <!-- Bouton rond "Fermer la fenêtre" -->
  <button id="bouton-fermeture">×</button>

  <h2>Calculateur de calories & Conseils pour perdre du poids</h2>
  <form id="combinedForm">
    <!-- Partie calculateur de calories -->
    <label for="gender">Sexe :</label>
    <select id="gender" name="gender" required>
      <option value="male">Homme</option>
      <option value="female">Femme</option>
    </select>

    <label for="height">Taille (cm) :</label>
    <!-- Ce sélecteur sera rempli dynamiquement -->
    <select id="height" name="height" required></select>

    <label for="weightCalc">Poids (kg) :</label>
    <!-- Ce sélecteur sera rempli dynamiquement -->
    <select id="weightCalc" name="weightCalc" required></select>

    <label for="ageCalc">Âge (ans) :</label>
    <!-- Ce sélecteur sera rempli dynamiquement -->
    <select id="ageCalc" name="ageCalc" required></select>

    <!-- Partie conseils pour perdre du poids -->
    <label for="localisation">Où est localisée votre cellulite ?</label>
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
      <option value="Pas du tout">Pas du tout</option>
      <option value="1 heure par semaine">1 heure par semaine</option>
      <option value="3 heures par semaine">3 heures par semaine</option>
      <option value="entre 3 heures à 5 heures par semaine">entre 3 heures à 5 heures par semaine</option>
      <option value="plus de 6 heures par semaine">plus de 6 heures par semaine</option>
    </select>

    <label for="eau">Combien d'eau buvez-vous par jour ?</label>
    <select id="eau" name="eau">
      <option value="1,5 litre et plus">1,5 litre et plus</option>
      <option value="1 litre">1 litre</option>
      <option value="1/2 litre et moins">1/2 litre et moins</option>
    </select>

    <button type="submit">Calcul de votre besoin calorique journalier et obtenir des conseils Minceur</button>
  </form>

  <!-- Zones d'affichage des résultats -->
  <div id="calorieResult"></div>
  <div id="response"></div>

  <script>
    // Remplissage dynamique du sélecteur pour la taille (height)
    const heightSelect = document.getElementById('height');
    for (let i = 145; i <= 195; i++) {
      const option = document.createElement('option');
      option.value = i;
      option.textContent = i;
      heightSelect.appendChild(option);
    }
    const extraHeightOption = document.createElement('option');
    extraHeightOption.value = "195+";
    extraHeightOption.textContent = "195 et plus";
    heightSelect.appendChild(extraHeightOption);

    // Remplissage dynamique du sélecteur pour le poids (weightCalc)
    const weightSelect = document.getElementById('weightCalc');
    const under40 = document.createElement('option');
    under40.value = "under40";
    under40.textContent = "Moins de 40";
    weightSelect.appendChild(under40);
    for (let i = 40; i <= 99; i++) {
      const option = document.createElement('option');
      option.value = i;
      option.textContent = i;
      weightSelect.appendChild(option);
    }
    const extraWeightOption = document.createElement('option');
    extraWeightOption.value = "99+";
    extraWeightOption.textContent = "99 et plus";
    weightSelect.appendChild(extraWeightOption);

    // Remplissage dynamique du sélecteur pour l'âge (ageCalc)
    const ageSelect = document.getElementById('ageCalc');
    const under18 = document.createElement('option');
    under18.value = "under18";
    under18.textContent = "Moins de 18";
    ageSelect.appendChild(under18);
    for (let i = 18; i <= 80; i++) {
      const option = document.createElement('option');
      option.value = i;
      option.textContent = i;
      ageSelect.appendChild(option);
    }
    const extraAgeOption = document.createElement('option');
    extraAgeOption.value = "80+";
    extraAgeOption.textContent = "80 et plus";
    ageSelect.appendChild(extraAgeOption);

    // Fonction pour obtenir des conseils personnalisés (exemple via une API)
    async function getAdvice(age, weight, localisation, moral, sport, eau) {
      const prompt = `Donne-moi des conseils pour perdre ${weight} kg pour une personne âgée de ${age} ans, avec une cellulite localisée ${localisation}, qui se sent ${moral}, pratique une activité sportive cardiovasculaire ${sport}, et boit ${eau} d'eau par jour. Parle de son âge, propose l'aquavelo comme activité physique pour solutionner son problème de poids localisé, explique ce qu'il faut manger durant les repas, cite le nombre de kilos à perdre, et donne des conseils pour améliorer son moral et son hydratation si nécessaire. Limite la réponse à 12 lignes. Ne parle pas de consultation auprès d'un médecin.`;
      
      const response = await fetch('./api_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt })
      });
      
      const data = await response.json();
      return data.choices[0].message.content;
    }

    // Gestionnaire du formulaire combiné
    document.getElementById('combinedForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Récupération des valeurs du calculateur
      const gender = document.getElementById('gender').value;
      
      // Pour la taille, si l'option "195 et plus" est sélectionnée, on utilise 195 comme base
      const heightVal = document.getElementById('height').value;
      const height = (heightVal === "195+") ? 195 : parseFloat(heightVal);
      
      // Pour le poids, si "Moins de 40" est sélectionné, on utilisera 40 ; si "99 et plus", 99
      const weightVal = document.getElementById('weightCalc').value;
      const weightCalc = (weightVal === "under40") ? 40 : ((weightVal === "99+") ? 99 : parseFloat(weightVal));
      
      // Pour l'âge, si "Moins de 18" est sélectionné, on utilisera 18 ; si "80 et plus", 80
      const ageVal = document.getElementById('ageCalc').value;
      const ageCalc = (ageVal === "under18") ? 18 : ((ageVal === "80+") ? 80 : parseFloat(ageVal));
      
      // Utilisation du champ "sport" pour le calcul du besoin calorique
      const sportValue = document.getElementById('sport').value;
      let multiplier;
      switch(sportValue) {
        case "Pas du tout":
          multiplier = 1.2;
          break;
        case "1 heure par semaine":
          multiplier = 1.375;
          break;
        case "3 heures par semaine":
          multiplier = 1.55;
          break;
        case "entre 3 heures à 5 heures par semaine":
          multiplier = 1.725;
          break;
        case "plus de 6 heures par semaine":
          multiplier = 1.9;
          break;
        default:
          multiplier = 1.2;
      }
      
      // Calcul du BMR selon la formule de Harris-Benedict
      let bmr;
      if (gender === 'male') {
        bmr = 66 + (13.7 * weightCalc) + (5 * height) - (6.8 * ageCalc);
      } else {
        bmr = 655 + (9.6 * weightCalc) + (1.8 * height) - (4.7 * ageCalc);
      }
      const tdee = bmr * multiplier;
      document.getElementById('calorieResult').textContent = 
        'Votre besoin calorique journalier est d\'environ ' + Math.round(tdee) + ' calories par jour.';

      // Récupération des autres valeurs pour obtenir des conseils
      const localisation = document.getElementById('localisation').value;
      const moral = document.getElementById('moral').value;
      const sport = sportValue; // réutilisation de la valeur du champ "sport"
      const eau = document.getElementById('eau').value;
      
      // Affichage d'un message de chargement
      const responseDiv = document.getElementById('response');
      responseDiv.textContent = 'Je vais vous donner une réponse adaptée, chargement...';
      
      try {
        const advice = await getAdvice(ageCalc, weightCalc, localisation, moral, sport, eau);
        responseDiv.textContent = advice;
      } catch (error) {
        responseDiv.textContent = 'Une erreur s\'est produite. Veuillez réessayer.';
        console.error(error);
      }
    });

    // Gestion du bouton de fermeture
    document.getElementById('bouton-fermeture').addEventListener('click', () => {
      window.close(); // Ferme la fenêtre du navigateur
    });
  </script>
</body>
</html>




