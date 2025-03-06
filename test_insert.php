<?php
require '_settings.php'; // Inclut la connexion à la base ($database)
session_start();

// Activer les logs pour le débogage
error_log("Début test_insert.php - " . date('Y-m-d H:i:s'));

// Insertion dans la table `client`
try {
    $stmt_client = $database->prepare('
        INSERT INTO client (nom, prenom, tel, email, adresse, ville, dept, activites, besoin)
        VALUES (:nom, :prenom, :tel, :email, :adresse, :ville, :dept, :activites, :besoin)
    ');
    
    $client_data = [
        ':nom' => 'Dupont',
        ':prenom' => 'Jean',
        ':tel' => '0123456789',
        ':email' => 'jean.dupont@example.com',
        ':adresse' => '123 Rue Exemple',
        ':ville' => 'Paris',
        ':dept' => '75',
        ':activites' => 'Natation',
        ':besoin' => 'Cours individuel'
    ];
    
    $stmt_client->execute($client_data);
    $client_id = $database->lastInsertId(); // Récupérer l'ID auto-incrémenté
    error_log("Insertion réussie dans client - ID: $client_id");
    $message = "Enregistrement client réussi (ID: $client_id).<br>";
} catch (PDOException $e) {
    $error = "Erreur client : " . $e->getMessage();
    error_log($error);
    $message = $error . "<br>";
}

// Insertion dans la table `nageur`
try {
    $stmt_nageur = $database->prepare('
        INSERT INTO nageur (nom, prenom, tel, ville, dept, diplome, presentation, prix, dispo, preference, email)
        VALUES (:nom, :prenom, :tel, :ville, :dept, :diplome, :presentation, :prix, :dispo, :preference, :email)
    ');
    
    $nageur_data = [
        ':nom' => 'Martin',
        ':prenom' => 'Sophie',
        ':tel' => '0987654321',
        ':ville' => 'Lyon',
        ':dept' => '69',
        ':diplome' => 'BPJEPS AAN',
        ':presentation' => 'Maître-nageuse expérimentée',
        ':prix' => '30.00',
        ':dispo' => 'Lundi et Mercredi 14h-18h',
        ':preference' => 'Cours collectifs',
        ':email' => 'sophie.martin@example.com'
    ];
    
    $stmt_nageur->execute($nageur_data);
    $nageur_id = $database->lastInsertId(); // Récupérer l'ID auto-incrémenté
    error_log("Insertion réussie dans nageur - ID: $nageur_id");
    $message .= "Enregistrement nageur réussi (ID: $nageur_id).";
} catch (PDOException $e) {
    $error = "Erreur nageur : " . $e->getMessage();
    error_log($error);
    $message .= $error;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test d'Insertion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Test d'Insertion</h2>
        <p><?= htmlspecialchars($message); ?></p>
        <p><a href="test_insert.php">Relancer le test</a></p>
    </div>
</body>
</html>
