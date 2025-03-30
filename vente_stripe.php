<?php
require_once(__DIR__ . '/config.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnements Aqua Connect</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        .error-message {
            color: red;
        }
        .plan-option {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Choisissez votre abonnement Aqua Connect</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="stripe_checkout.php" method="post">
            <div class="form-section">
                <h2>Informations Personnelles</h2>
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom :</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone :</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>

            <div class="form-section">
                <h2>Choisissez votre formule</h2>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="plan" id="20seances" value="20seances" data-price="36000" required>
                    <label class="form-check-label" for="20seances">
                        20 séances - 360 €
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="plan" id="40seances" value="40seances" data-price="58500" required>
                    <label class="form-check-label" for="40seances">
                        40 séances - 585 € (10 x 58,5 €)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="plan" id="80seances" value="80seances" data-price="89100" required>
                    <label class="form-check-label" for="80seances">
                        80 séances - 891 € (10 x 89,1 €)
                    </label>
                </div>
                 <div class="form-check">
                    <input class="form-check-input" type="radio" name="plan" id="illimite" value="illimite" data-price="9900" required>
                    <label class="form-check-label" for="illimite">
                      Illimité 12 mois - 99 €/mois
                    </label>
                </div>
            </div>

            <div class="form-section">
                <h2>Informations de Paiement</h2>
                
                    Acompte initial de 10%. Vous serez redirigé vers Stripe pour le paiement sécurisé.
                
            </div>

            <input type="hidden" name="stripePublicKey" value="<?php echo htmlspecialchars($stripePublicKey); ?>">
            

            <button type="submit" class="btn btn-primary">Payer l'acompte</button>
        </form>
    </div>
</body>
</html>
