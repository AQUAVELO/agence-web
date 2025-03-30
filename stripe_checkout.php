<?php



function sanitizeString($str) {
    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str);
    return $str;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     // Récupération des données du formulaire
    $nom = sanitizeString($_POST['nom']);
    $prenom = sanitizeString($_POST['prenom']);
    $telephone = sanitizeString($_POST['telephone']);
    $email = sanitizeString($_POST['email']);
    $plan = sanitizeString($_POST['plan']);
    $stripePublicKey = $_POST['stripePublicKey'];

  // Gestion des prix des forfaits
    $formules = array(
    "20seances" => array("nom" => "20 séances", "prix" => 36000),
    "40seances" => array("nom" => "40 séances", "prix" => 58500),
    "80seances" => array("nom" => "80 séances", "prix" => 89100),
     "illimite" => array("nom" => "Illimité", "prix" => 9900) # centimes
);

    if (!array_key_exists($plan, $formules)){
        http_response_code(400);
        error_log("Formule inconnue détectée");
        header('Location: index.php?error=Formule+inconnue');
         exit;
    }

    # Montant et devise
     $MONTANT = $formules[$plan]['prix'];

    #Calcul des 10 pourcents
    $montant_acompte = round(($MONTANT * 0.1), 0);
    $DEVISE = "eur";

    $nom_produit = "Acompte : 10% sur la formule " . $formules[$plan]['nom'] ; #Nom du produit


    try {
        // Crée une session de paiement avec Stripe
        $checkout_session = Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $DEVISE,
                    'unit_amount' => $montant_acompte, #L'acompte pour stripe
                    'product_data' => [
                        'name' => $nom_produit, #Le nom de ton produit
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            #Pour la partie test, il faut avoir la même URL de succès et de défaite. On a le bon ID
          
                'success_url' => 'https://coursnatation.aquavelocannes.com/success.php?session_id={CHECKOUT_SESSION_ID}', #Redirection en cas de succès
           
            'cancel_url' => 'https://coursnatation.aquavelocannes.com/error.php', #La page de paiement échouée
            'client_reference_id' =>  $nom  , # On transmet les données du nom
           'metadata' => [
                        'nom' => $nom,
                        'prenom' => $prenom,
                       'email' => $email,
                       'plan' => $plan,
                                                'montant_total' => number_format($MONTANT / 100, 2, '.', '') # Conserver le montant total en euros

                    ]
        ]);

         //On envoi les infos
header("HTTP/1.1 303 See Other");
         header("Location: " . $checkout_session->url); //Redirection vers stripe
    } catch (Exception $e) {
        http_response_code(500);
        header('Location: index.php?error=Une+erreur+est+survenue+:+'.$e->getMessage());
        // echo json_encode(['error' => $e->getMessage()]);
    }
exit(); #Finis ton document
}
else {
    http_response_code(405);
        error_log("Methode : La methode n'est pas bonne, vérifiez vos envois, ");
            header('Location: index.php?error=La methode d\'envois des informations n\'est pas bonne.');
    // Gérer les requêtes GET ou autres
    #Il faut la mettre.
    exit(); # On sort
}
