<?php
// Initialisation de la session
session_start();

// Configuration de Stripe - Remplacez par vos clés réelles
$stripeSecretKey = 'sk_test_51R0razK3GWmEE8Ic7OHpRMz8BcnfnuYSHmixqzKPD8Ww3UVrY5Fsti9B3Hiq8canrQ36M6lYpOSDg7uZcocLfikg00ipggxD7x';
$stripePublicKey = 'pk_test_51R0razK3GWmEE8IcMClhIck5shXPh8iGQ41LwFbec7CTnfK3pKGbUA5FpW9Zw4Bm3Ho2GckSpEUMl7FKyggvD07S004FHPm73I';

// Inclusion de la bibliothèque Stripe
require_once('vendor/autoload.php');

// Configuration de l'API Stripe
\Stripe\Stripe::setApiKey($stripeSecretKey);

// Définition des produits
$produits = [
    'produit_50' => [
        'nom' => 'Produit Premium',
        'prix' => 5000, // En centimes (50€)
        'description' => 'Notre offre premium complète'
    ],
    'produit_10' => [
        'nom' => 'Produit Standard',
        'prix' => 1000, // En centimes (10€)
        'description' => 'Notre offre standard accessible à tous'
    ]
];

// Traitement du formulaire
$erreurs = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des données
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $produit_id = filter_input(INPUT_POST, 'produit_id', FILTER_SANITIZE_STRING);
    $token = $_POST['stripeToken'];
    
    // Vérification des champs
    if (!$nom) $erreurs[] = "Le nom est requis.";
    if (!$prenom) $erreurs[] = "Le prénom est requis.";
    if (!$email) $erreurs[] = "L'email est invalide.";
    if (!$telephone) $erreurs[] = "Le numéro de téléphone est requis.";
    if (!isset($produits[$produit_id])) $erreurs[] = "Le produit sélectionné est invalide.";
    
    // Si aucune erreur, traitement du paiement
    if (empty($erreurs)) {
        try {
            // Création du client dans Stripe
            $customer = \Stripe\Customer::create([
                'email' => $email,
                'source' => $token,
                'name' => $prenom . ' ' . $nom,
                'phone' => $telephone
            ]);
            
            // Création de la charge
            $charge = \Stripe\Charge::create([
                'customer' => $customer->id,
                'amount' => $produits[$produit_id]['prix'],
                'currency' => 'eur',
                'description' => 'Achat: ' . $produits[$produit_id]['nom'],
                'metadata' => [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'telephone' => $telephone
                ]
            ]);
            
            // Paiement réussi
            $success = true;
            
            // Vous pourriez ici enregistrer la vente dans votre base de données
            // ...
            
        } catch (\Stripe\Exception\CardException $e) {
            $erreurs[] = "Erreur de paiement: " . $e->getMessage();
        } catch (\Exception $e) {
            $erreurs[] = "Une erreur est survenue: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de vente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .product-card {
            cursor: pointer;
            transition: all 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-card.selected {
            border: 2px solid #4CAF50;
            background-color: #f8fff8;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="mb-4">Nos Offres</h1>
                <p class="lead">Choisissez l'offre qui vous convient le mieux</p>
            </div>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success text-center">
            <h4>Paiement réussi !</h4>
            <p>Merci pour votre achat. Vous recevrez un email de confirmation.</p>
            <a href="?" class="btn btn-outline-success mt-3">Retour à la page d'accueil</a>
        </div>
        <?php else: ?>
        
        <?php if (!empty($erreurs)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($erreurs as $erreur): ?>
                <li><?php echo htmlspecialchars($erreur); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <form id="payment-form" method="POST" action="">
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card product-card h-100" data-product="produit_50">
                        <div class="card-body text-center">
                            <h3 class="card-title"><?php echo htmlspecialchars($produits['produit_50']['nom']); ?></h3>
                            <p class="display-4 text-primary">50€</p>
                            <p class="card-text"><?php echo htmlspecialchars($produits['produit_50']['description']); ?></p>
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input product-radio" type="radio" name="produit_id" id="produit_50" value="produit_50" required>
                                <label class="form-check-label ms-2" for="produit_50">Sélectionner</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card product-card h-100" data-product="produit_10">
                        <div class="card-body text-center">
                            <h3 class="card-title"><?php echo htmlspecialchars($produits['produit_10']['nom']); ?></h3>
                            <p class="display-4 text-primary">10€</p>
                            <p class="card-text"><?php echo htmlspecialchars($produits['produit_10']['description']); ?></p>
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input product-radio" type="radio" name="produit_id" id="produit_10" value="produit_10" required>
                                <label class="form-check-label ms-2" for="produit_10">Sélectionner</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Vos informations</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom *</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom *</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Informations de paiement</h3>
                </div>
                <div class="card-body">
                    <div id="card-element" class="form-control mb-3">
                        <!-- Stripe Card Element -->
                    </div>
                    <div id="card-errors" class="text-danger mb-3"></div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">Payer maintenant</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration de Stripe
        var stripe = Stripe('<?php echo $stripePublicKey; ?>');
        var elements = stripe.elements();
        
        // Style pour le champ de carte
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        
        // Création de l'élément carte
        var card = elements.create('card', {style: style});
        card.mount('#card-element');
        
        // Gestion des erreurs
        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Sélection du produit
        var productCards = document.querySelectorAll('.product-card');
        productCards.forEach(function(card) {
            card.addEventListener('click', function() {
                var productId = this.dataset.product;
                document.getElementById(productId).checked = true;
                
                // Style des cartes
                productCards.forEach(function(c) {
                    c.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });
        
        // Soumission du formulaire
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', result.token.id);
                    form.appendChild(hiddenInput);
                    form.submit();
                }
            });
        });
    });
    </script>
</body>
</html>
