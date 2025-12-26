<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Paramètres de connexion
$host = 'localhost'; // Hôte o2switch
$dbname = 'hfqf5148_bachata'; // Nom de la base
$username = 'hfqf5148_user'; // Remplace par ton utilisateur MySQL
$password = 'ton_mot_de_passe'; // Remplace par ton mot de passe
$port = 3306; // Port par défaut

// Connexion à la base avec PDO
try {
    $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8";
    $database = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "Connexion à la base $dbname réussie !<br>";
    error_log("Connexion réussie à $dbname - " . date('Y-m-d H:i:s'));
} catch (PDOException $e) {
    $error = "Erreur de connexion : " . $e->getMessage();
    echo $error;
    error_log($error);
    exit;
}

// Tester une requête sur la table `client`
try {
    $stmt = $database->prepare("SELECT * FROM client LIMIT 1");
    $stmt->execute();
    $client = $stmt->fetch();
    
    if ($client) {
        echo "Données trouvées dans la table client :<br>";
        print_r($client);
    } else {
        echo "Aucune donnée dans la table client ou table vide.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la requête sur client : " . $e->getMessage();
    echo $error;
    error_log($error);
}
?>
