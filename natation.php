<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Natation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }
        .container {
            display: flex;
            width: 80%;
            max-width: 1000px;
            height: 80%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .left, .right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            text-align: center;
        }
        .left {
            background-color: #00afdf; /* Bleu vif */
            border-right: 2px solid #ccc; /* Trait vertical */
            color: white;
        }
        .right {
            background-color: #47c3e6; /* Bleu clair */
            color: #333;
        }
        img {
            max-width: 354px;
            height: auto;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        h2 {
            margin-bottom: 20px;
            color: inherit;
        }
        .large-text {
            font-size: 21px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <img src="images/cours.jpeg" alt="Cours de natation">
            <h2>Vous êtes maître-nageur ?</h2>
            <p class="large-text">Inscrivez-vous gratuitement pour proposer vos services !</p>
            <a href="inscription_nageur.php"><button>Cliquez ici</button></a>
        </div>
        <div class="right">
            <img src="images/couple_cherchant.jpeg" alt="Maître-nageur">
            <h2>Vous recherchez un maître-nageur ?</h2>
            <p class="large-text">Pour des cours de natation ou animer une séance, cliquez ici !</p>
            <a href="inscription_client.php"><button>Cliquez ici</button></a>
        </div>
    </div>
</body>
</html>