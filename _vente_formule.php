<?php
require 'vendor/autoload.php';
require 'settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configuration de Monetico
define('MONETICO_TPE', '6684349');
define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
define('MONETICO_COMPANY', 'AQUACANNES');
define('MONETICO_URL', 'https://p.monetico-services.com/test/paiement.cgi');
define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation.php');
define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation.php');

$produit = [
    'nom' => 'Séance Cryo',
    'prix' => 99.00,
    'devise' => 'EUR',
    'description' => 'Cryo'
];

$reference = 'CMD' . date('YmdHis') . rand(100, 999);

function calculateMAC($fields, $keyHex) {
    $recognizedKeys = [
        'TPE', 'contexte_commande', 'date', 'lgue', 'mail', 'montant', 'reference',
        'societe', 'texte-libre', 'url_retour_err', 'url_retour_ok', 'version'
    ];

    $macFields = [];
    foreach ($recognizedKeys as $key) {
        $macFields[$key] = isset($fields[$key]) ? mb_convert_encoding($fields[$key], 'UTF-8', 'auto') : '';
    }

    ksort($macFields, SORT_STRING);
    $chaine = '';
    foreach ($macFields as $k => $v) {
        $chaine .= "$k=$v*";
    }
    $chaine = rtrim($chaine, '*');

    $binaryKey = pack('H*', $keyHex);
    $mac = strtoupper(hash_hmac('sha1', $chaine, $binaryKey));
    file_put_contents('monetico_debug.txt', "CHAINE SIGNEE:\n$chaine\n\nMAC:\n$mac\n", FILE_APPEND);
    return $mac;
}

$dateCommande = date('d/m/Y:H:i:s');
$contexteCommande = base64_encode(json_encode([
    'billing' => [
        'addressLine1' => 'Allée des Mimosas',
        'city' => 'Mandelieu',
        'postalCode' => '06400',
        'country' => 'FR'
    ]
], JSON_UNESCAPED_UNICODE));

$fields = [
    'TPE'               => MONETICO_TPE,
    'contexte_commande' => $contexteCommande,
    'date'              => $dateCommande,
    'montant'           => sprintf('%012.2f', $produit['prix']) . $produit['devise'],
    'reference'         => $reference,
    'texte-libre'       => $produit['description'],
    'version'           => '3.0',
    'lgue'              => 'FR',
    'societe'           => MONETICO_COMPANY,
    'mail'              => '',
    'url_retour_ok'     => MONETICO_RETURN_URL,
    'url_retour_err'    => MONETICO_CANCEL_URL
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom'] ?? '');
    $prenom  = trim($_POST['prenom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $tel     = trim($_POST['telephone'] ?? '');

    if (
        $nom !== '' && $prenom !== '' &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        preg_match('/^[0-9\s\-\+\(\)]+$/', $tel)
    ) {
        // Anti-double soumission : on stocke la soumission dans Redis
        $key = 'vente_' . md5($email . $produit['description']);
        $cacheItem = $redis->getItem($key);
        if (!$cacheItem->isHit()) {
            try {
                $stmt = $conn->prepare("INSERT INTO formule (nom, prenom, tel, prix, email, vente) VALUES (?, ?, ?, ?, ?, 0)");
                $stmt->execute([$nom, $prenom, $tel, $produit['prix'], $email]);

                $cacheItem->set(true)->expiresAfter(600);
                $redis->save($cacheItem);
            } catch (PDOException $e) {
                file_put_contents('monetico_debug.txt', "Erreur DB : " . $e->getMessage() . "\n", FILE_APPEND);
            }
        }

        // Affichage du message de remerciement dans tous les cas
        echo '<div style="text-align:center; font-family:sans-serif; margin-top:30px; color:green;">Merci, votre réservation a bien été enregistrée ! Vous allez être redirigé vers le paiement.</div>';

        $texteLibreInfos = [
            'email'     => $email,
            'nom'       => $nom,
            'prenom'    => $prenom,
            'telephone' => $tel,
            'achat'     => $produit['description'],
            'montant'   => number_format($produit['prix'], 2, '.', '') . $produit['devise']
        ];

        $fields['texte-libre'] .= ';' . http_build_query($texteLibreInfos, '', ';');
        $fields['mail'] = $email;
        $fields['MAC'] = calculateMAC($fields, MONETICO_KEY);

        file_put_contents('monetico_log.txt', print_r($fields, true), FILE_APPEND);

        echo '<form id="form-monetico" action="' . MONETICO_URL . '" method="post">';
        foreach ($fields as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars_decode($value, ENT_QUOTES) . '">';
        }
        echo '</form>';
        echo '<script>setTimeout(() => document.getElementById("form-monetico").submit(), 2000);</script>';
        exit;
    } else {
        $error = "Tous les champs doivent être remplis correctement.";
    }
}
?>





<?php
// ... PHP code unchanged (see above)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Séance de Cryolipolyse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f8fb;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .section {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #104e8b;
            text-align: center;
        }
        p {
            font-size: 1.1em;
        }
        ul {
            padding-left: 20px;
        }
        .form-section, .image-section, .avis-section {
            margin-top: 40px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            background-color: #104e8b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #0d3e70;
        }
        .error {
            color: red;
            text-align: center;
        }
        .image-section img {
            width: 75%;
            border-radius: 10px;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .avis-section {
            background: #e8f0fe;
            padding: 20px;
            border-radius: 10px;
        }
        .avis {
            font-style: italic;
            margin-bottom: 15px;
        }
        .avis strong {
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="section">
        <h1>Vous souhaitez mincir ou perdre du poids ?</h1>
        <h2>Découvrez l'amincissement par cryolipolyse à 99€</h2>

        <div class="image-section">
            <img src="images/cryolipolyse.jpg" alt="Séance de cryolipolyse">
        </div>

        <h2>Qu’est-ce que la Cryolipolyse ?</h2>
        <p>La Cryolipolyse est une méthode d’amincissement qui permet :</p>
        <ul>
            <li>De sculpter la silhouette grâce à l’application de plaques de froid</li>
            <li>De tonifier les zones traitées</li>
            <li>De traiter de nombreuses zones : ventre, cuisses, hanches, bras…</li>
            <li>De réduire les cellules graisseuses de manière naturelle</li>
        </ul>

        <div class="avis-section">
            <h2>Ce qu’en pensent nos clients</h2>
            <div class="avis">"Très satisfaite de ma séance, j’ai vu une vraie différence au bout de 3 semaines."<br><strong>— Julie R.</strong></div>
            <div class="avis">"Accueil chaleureux, protocole bien expliqué. Je recommande vivement."<br><strong>— Caroline B.</strong></div>
            <div class="avis">"Top ! Le centre est propre, l'appareil est moderne et surtout efficace."<br><strong>— Nathalie D.</strong></div>
        </div>

        <div class="form-section">
            <h2>Réservez votre séance de Cryolipolyse</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
            <form method="post" action="">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required>

                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required>

                <label for="telephone">Téléphone *</label>
                <input type="tel" id="telephone" name="telephone" required>

                <label for="email">Adresse email *</label>
                <input type="email" id="email" name="email" required>

                <button type="submit">Réserver ma séance à 99€</button>
            </form>
        </div>
        <div style="text-align: center; margin-top: 30px; font-size: 1.1em; color: #333;">
            📍 <strong>AQUAVELO</strong><br>
            <a href="https://maps.google.com/?q=60 avenue du Docteur Raymond Picaud, Cannes" target="_blank">60 avenue du Docteur Raymond Picaud à CANNES</a><br>
            ☎️ <strong>04 93 93 05 65</strong>
        </div>
    </div>
</body>
</html>




























