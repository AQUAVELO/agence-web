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
    $ville = $_POST['ville'] ?? '';
    $dept = $_POST['dept'] ?? '';
    $diplome = $_POST['diplome'] ?? '';
    $presentation = $_POST['presentation'] ?? '';
    $prix = !empty($_POST['prix']) ? $_POST['prix'] : null;
    $dispo = $_POST['dispo'] ?? '';
    $preference = $_POST['preference'] ?? '';

    // Vérifier que les champs obligatoires sont remplis
    if (empty($nom) || empty($prenom) || empty($tel) || empty($email) || empty($ville) || empty($dept) || empty($diplome)) {
        error_log("Champs obligatoires manquants : nom=$nom, prenom=$prenom, tel=$tel, email=$email, ville=$ville, dept=$dept, diplome=$diplome");
        die("Erreur : Tous les champs obligatoires doivent être remplis.");
    }

    // Gestion de la photo
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $photo = $uploadDir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    // Insérer dans la base
    try {
        $stmt = $db->prepare("INSERT INTO nageur (nom, prenom, tel, email, photo, ville, dept, diplome, presentation, prix, dispo, preference) 
                              VALUES (:nom, :prenom, :tel, :email, :photo, :ville, :dept, :diplome, :presentation, :prix, :dispo, :preference)");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':tel' => $tel,
            ':email' => $email,
            ':photo' => $photo,
            ':ville' => $ville,
            ':dept' => $dept,
            ':diplome' => $diplome,
            ':presentation' => $presentation,
            ':prix' => $prix,
            ':dispo' => $dispo,
            ':preference' => $preference
        ]);
        error_log("Insertion dans nageur réussie pour $nom $prenom, dept=$dept");
    } catch (PDOException $e) {
        error_log("Erreur insertion nageur : " . $e->getMessage());
        die("Erreur lors de l'insertion : " . $e->getMessage());
    }

    // Vérifier s’il y a des clients dans le même département
    try {
        $stmt = $db->prepare("SELECT id, prenom, nom FROM client WHERE dept = :dept");
        $stmt->execute([':dept' => $dept]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Recherche clients dans dept $dept : " . count($clients) . " trouvés");
    } catch (PDOException $e) {
        error_log("Erreur recherche clients : " . $e->getMessage());
        die("Erreur lors de la recherche de clients : " . $e->getMessage());
    }

    // Définir le message pour merci_nageur.php
    if (!empty($clients)) {
        $_SESSION['message'] = "Nous allons proposer vos services aux clients suivants dans votre département.";
    } else {
        $_SESSION['message'] = "Aucun client n’est actuellement disponible dans votre département.";
    }
    $_SESSION['dept'] = $dept; // Stocker le département pour merci_nageur.php
    error_log("Message défini dans session : " . $_SESSION['message']);

    // Redirection vers la page de remerciement
    header("Location: /natation/merci_nageur.php");
    exit;
} else {
    error_log("Requête non POST reçue");
    die("Méthode non autorisée");
}
?>