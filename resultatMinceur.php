<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conseils pour perdre du poids & Calculateur de calories</title>
  <style>
    body{font-family:Arial,sans-serif;background-color:#f4f4f9;margin:0;padding:20px;color:#333;}
    h1,h2{text-align:center;color:#4CAF50;}
    form{max-width:500px;margin:20px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
    label{display:block;margin-bottom:8px;font-weight:bold;}
    select,button{width:100%;padding:10px;margin-bottom:20px;border:1px solid #ccc;border-radius:4px;font-size:16px;}
    button{background-color:#4CAF50;color:white;border:none;cursor:pointer;}
    button:hover{background-color:#45a049;}
    #calorieResult,#imcResult,#response{margin-top:20px;padding:15px;background:#e8f5e9;border-radius:4px;color:#2e7d32;}
    #bouton-fermeture{position:fixed;top:20px;right:20px;background-color:#ff4444;color:white;border:none;width:40px;height:40px;border-radius:50%;font-size:24px;cursor:pointer;z-index:1000;display:flex;align-items:center;justify-content:center;}
    #bouton-fermeture:hover{background-color:#cc0000;}
  </style>
</head>
<body>
  <button id="bouton-fermeture">×</button>
  <h2>Calculateur de calories & Conseils pour perdre du poids</h2>
  <form id="combinedForm">
    <label for="gender">Sexe :</label>
    <select id="gender" name="gender" required><option value="male">Homme</option><option value="female">Femme</option></select>
    <label for="height">Taille (cm) :</label>
    <select id="height" name="height" required></select>
    <label for="weightCalc">Poids (kg) :</label>
    <select id="weightCalc" name="weightCalc" required></select>
    <label for="ageCalc">Âge (ans) :</label>
    <select id="ageCalc" name="ageCalc" required></select>
    <label for="localisation">Où est localisée votre cellulite ou votre adiposité ?</label>
    <select id="localisation" name="localisation"><option value="ventre">Plutôt sur le ventre</option><option value="hanches">Plutôt sur les hanches</option><option value="ventre et hanches">Sur le ventre et les hanches</option></select>
    <label for="moral">Comment vous sentez-vous actuellement ?</label>
    <select id="moral" name="moral"><option value="stressé">Plutôt stressé</option><option value="fatigué">Plutôt fatigué</option><option value="tout va bien">Tout va bien</option></select>
    <label for="sport">Pratiquez-vous une activité sportive cardiovasculaire ?</label>
    <select id="sport" name="sport"><option value="Pas du tout">Pas du tout</option><option value="1 heure par semaine">1 heure par semaine</option><option value="3 heures par semaine">3 heures par semaine</option><option value="entre 3 heures à 5 heures par semaine">entre 3 heures à 5 heures par semaine</option><option value="plus de 6 heures par semaine">plus de 6 heures par semaine</option></select>
    <label for="eau">Combien d'eau buvez-vous par jour ?</label>
    <select id="eau" name="eau"><option value="1,5 litre et plus">1,5 litre et plus</option><option value="1 litre">1 litre</option><option value="1/2 litre et moins">1/2 litre et moins</option></select>
    <button type="submit">Calcul de votre besoin calorique et obtenir des conseils minceur</button>
  </form>
  <div id="calorieResult"></div>
  <div id="imcResult"></div>
  <div id="response"></div>
  <script>
    const heightSelect=document.getElementById('height');for(let i=145;i<=195;i++){const option=document.createElement('option');option.value=i;option.textContent=i;heightSelect.appendChild(option);}
    const extraHeightOption=document.createElement('option');extraHeightOption.value="195+";extraHeightOption.textContent="195 et plus";heightSelect.appendChild(extraHeightOption);
    const weightSelect=document.getElementById('weightCalc');const under40=document.createElement('option');under40.value="under40";under40.textContent="Moins de 40";weightSelect.appendChild(under40);for(let i=40;i<=99;i++){const option=document.createElement('option');option.value=i;option.textContent=i;weightSelect.appendChild(option);}
    const extraWeightOption=document.createElement('option');extraWeightOption.value="99+";extraWeightOption.textContent="99 et plus";weightSelect.appendChild(extraWeightOption);
    const ageSelect=document.getElementById('ageCalc');const under18=document.createElement('option');under18.value="under18";under18.textContent="Moins de 18";ageSelect.appendChild(under18);for(let i=18;i<=80;i++){const option=document.createElement('option');option.value=i;option.textContent=i;ageSelect.appendChild(option);}
    const extraAgeOption=document.createElement('option');extraAgeOption.value="80+";extraAgeOption.textContent="80 et plus";ageSelect.appendChild(extraAgeOption);
    function calculateIMC(weight,height){const heightInMeters=height/100;return(weight/(heightInMeters*heightInMeters)).toFixed(2);}
    async function getAdvice(age,weight,height,imc,localisation,moral,sport,eau,tdee){
      const prompt=`Donne des conseils pour une personne de ${age} ans, ${weight} kg, ${height} cm, IMC ${imc}. Cellulite: ${localisation}. Moral: ${moral}. Sport: ${sport}. Eau: ${eau}. Besoin calorique: ${tdee} calories. Propose l'Aquavelo, des conseils alimentaires et des ajustements pour atteindre les objectifs minceur. Limite à 10 lignes et 350 tokens. Ne parle pas de professionnel de santé, ni de nutritionniste.`;
      try {
        const response = await fetch('./api_handler.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            prompt: prompt,
            max_tokens: 400,
            stop: ["\n\n"]
          })
        });

        if (!response.ok) {
          const errorText = await response.text();
          let errorMsg = `Erreur HTTP: ${response.status}`;
          try {
            const errorData = JSON.parse(errorText);
            if (errorData.error) {
              errorMsg = typeof errorData.error === 'string' ? errorData.error : (errorData.error.message || errorMsg);
            }
          } catch (e) {
            errorMsg += ' - ' + errorText.substring(0, 100);
          }
          throw new Error(errorMsg);
        }

        let data;
        try {
          const responseText = await response.text();
          console.log('Réponse brute:', responseText.substring(0, 500)); // Log les 500 premiers caractères
          data = JSON.parse(responseText);
        } catch (e) {
          console.error('Erreur parsing JSON:', e);
          throw new Error('Erreur lors du parsing de la réponse JSON: ' + e.message);
        }
        
        // Log pour déboguer
        console.log('Réponse API complète:', data);
        
        // Vérifier si la réponse contient une erreur
        if (data.error) {
          const errorMsg = typeof data.error === 'string' ? data.error : (data.error.message || JSON.stringify(data.error));
          throw new Error('Erreur API: ' + errorMsg);
        }
        
        // Vérifier si la structure de réponse est correcte
        if (!data || typeof data !== 'object') {
          console.error('Réponse invalide:', data);
          throw new Error('Réponse API invalide: format de données incorrect');
        }
        
        if (!data.choices) {
          console.error('Pas de propriété choices dans la réponse:', data);
          throw new Error('Réponse API invalide: propriété "choices" manquante');
        }
        
        if (!Array.isArray(data.choices) || data.choices.length === 0) {
          console.error('Tableau choices vide ou invalide:', data.choices);
          throw new Error('Réponse API invalide: tableau "choices" vide');
        }
        
        if (!data.choices[0] || !data.choices[0].message) {
          console.error('Structure message invalide:', data.choices[0]);
          throw new Error('Réponse API invalide: structure "message" manquante');
        }
        
        if (!data.choices[0].message.content) {
          console.error('Contenu message manquant:', data.choices[0].message);
          throw new Error('Réponse API invalide: contenu du message manquant');
        }
        
        return data.choices[0].message.content;
      } catch (error) {
        console.error('Erreur lors de l\'appel API:', error);
        throw error;
      }
    }
    document.getElementById('combinedForm').addEventListener('submit',async(e)=>{
      e.preventDefault();
      const gender=document.getElementById('gender').value;
      const heightVal=document.getElementById('height').value;const height=(heightVal==="195+")?195:parseFloat(heightVal);
      const weightVal=document.getElementById('weightCalc').value;const weightCalc=(weightVal==="under40")?40:((weightVal==="99+")?99:parseFloat(weightVal));
      const ageVal=document.getElementById('ageCalc').value;const ageCalc=(ageVal==="under18")?18:((ageVal==="80+")?80:parseFloat(ageVal));
      const imc=calculateIMC(weightCalc,height);let imcMessage=`Votre IMC est de ${imc}. `;
      if(imc<18.5){imcMessage+="Vous êtes en insuffisance pondérale. Il est recommandé de prendre du poids de manière saine.";}
      else if(imc>=18.5&&imc<25){imcMessage+="Votre poids est normal. Continuez à maintenir un mode de vie sain.";}
      else if(imc>=25&&imc<30){imcMessage+="Vous êtes en surpoids. Il est recommandé de perdre du poids pour améliorer votre santé.";}
      else{imcMessage+="Vous êtes en obésité. Il est fortement recommandé de perdre du poids pour réduire les risques pour votre santé.";}
      document.getElementById('imcResult').textContent=imcMessage;
      const sportValue=document.getElementById('sport').value;let multiplier;
      switch(sportValue){
        case"Pas du tout":multiplier=1.2;break;
        case"1 heure par semaine":multiplier=1.375;break;
        case"3 heures par semaine":multiplier=1.55;break;
        case"entre 3 heures à 5 heures par semaine":multiplier=1.725;break;
        case"plus de 6 heures par semaine":multiplier=1.9;break;
        default:multiplier=1.2;}
      let bmr;if(gender==='male'){bmr=66+(13.7*weightCalc)+(5*height)-(6.8*ageCalc);}else{bmr=655+(9.6*weightCalc)+(1.8*height)-(4.7*ageCalc);}
      const tdee=Math.round(bmr*multiplier);document.getElementById('calorieResult').textContent='Votre besoin calorique journalier est d\'environ '+tdee+' calories par jour.';
      const localisation=document.getElementById('localisation').value;const moral=document.getElementById('moral').value;const sport=sportValue;const eau=document.getElementById('eau').value;
      const responseDiv=document.getElementById('response');responseDiv.textContent='Je vais vous donner une réponse adaptée, chargement...';
      try{
        const advice=await getAdvice(ageCalc,weightCalc,height,imc,localisation,moral,sport,eau,tdee);
        responseDiv.textContent=advice;
        responseDiv.style.background='#e8f5e9';
        responseDiv.style.color='#2e7d32';
      }
      catch(error){
        console.error('Erreur détaillée:', error);
        responseDiv.textContent='Une erreur s\'est produite lors de la génération des conseils. Veuillez réessayer dans quelques instants. Si le problème persiste, contactez le support.';
        responseDiv.style.background='#ffebee';
        responseDiv.style.color='#c62828';
      }
    });
    document.getElementById('bouton-fermeture').addEventListener('click',()=>{window.close();});
  </script>
</body>
</html>




