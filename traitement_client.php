<?php
// Activer les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();

// Connexion à la base MySQL locale (MAMP)
try {
    $db = new PDO('mysql:host=localhost;dbname=cours;charset=utf8', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Connexion MySQL réussie");
} catch (PDOException $e) {
    error_log("Erreur de connexion MySQL : " . $e->getMessage());
    die("Erreur de connexion à la base MySQL : " . $e->getMessage());
}

// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $email = $_POST['email'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $dept = $_POST['dept'] ?? '';
    $activites = $_POST['activites'] ?? '';
    $besoin = $_POST['besoin'] ?? '';

    // Vérifier que les champs obligatoires sont remplis
    if (empty($nom) || empty($prenom) || empty($dept)) {
        error_log("Champs obligatoires manquants : nom=$nom, prenom=$prenom, dept=$dept");
        die("Erreur : Tous les champs obligatoires doivent être remplis.");
    }

    // Insérer dans la table client
    try {
        $stmt = $db->prepare("INSERT INTO client (nom, prenom, tel, email, adresse, ville, dept, activites, besoin) 
                              VALUES (:nom, :prenom, :tel, :email, :adresse, :ville, :dept, :activites, :besoin)");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':tel' => $tel,
            ':email' => $email,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':dept' => $dept,
            ':activites' => $activites,
            ':besoin' => $besoin
        ]);
        error_log("Insertion dans client réussie pour $nom $prenom, dept=$dept");
    } catch (PDOException $e) {
        error_log("Erreur insertion client : " . $e->getMessage());
        die("Erreur lors de l'insertion : " . $e->getMessage());
    }

    // Vérifier s’il y a un nageur dans le même département
    try {
        $stmt = $db->prepare("SELECT prenom FROM nageur WHERE dept = :dept LIMIT 1");
        $stmt->execute([':dept' => $dept]);
        $nageur = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Recherche nageur dans dept $dept : " . ($nageur ? "trouvé" : "non trouvé"));
    } catch (PDOException $e) {
        error_log("Erreur recherche nageur : " . $e->getMessage());
        die("Erreur lors de la recherche de nageur : " . $e->getMessage());
    }

    // Définir le message pour merci_client.php
    if ($nageur) {
        $_SESSION['message'] = "Voici les maîtres-nageurs disponibles dans votre département.";
    } else {
        $_SESSION['message'] = "Pour l’instant, nous n’avons pas de maître-nageur disponible dans votre département.";
    }
    $_SESSION['dept'] = $dept; // Stocker le département pour merci_client.php
    error_log("Message défini dans session : " . $_SESSION['message']);

    // Redirection vers la page de remerciement
    header("Location: /natation/merci_client.php");
    exit;
} else {
    error_log("Requête non POST reçue");
    die("Méthode non autorisée");
}
?>