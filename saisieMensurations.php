<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit;
}

// Lire l'email depuis la session
$email = $_SESSION["email"];

// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Mensurations";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST["Nom"];
    $prenom = $_POST["Prenom"];
    $phone = $_POST["Phone"];
    $age = $_POST["Age"];
    $poids = $_POST["Poids"];
    $taille = $_POST["Taille"];
    $trtaille = $_POST["Trtaille"];
    $hanches = $_POST["Trhanches"];
    $fesses = $_POST["Trfesses"];

    // Mettre à jour les autres champs pour cet email
    $stmt = $conn->prepare("UPDATE mensurations SET Nom = ?, Prenom = ?, Phone = ?, Age = ?, Poids = ?, Taille = ?, Trtaille = ?, Trhanches = ?, Trfesses = ? WHERE email = ?");
    $stmt->bind_param("ssssiiiiis", $nom, $prenom, $phone, $age, $poids, $taille, $trtaille, $hanches, $fesses, $email);
    if ($stmt->execute()) {
        // Rediriger vers menu.php après la mise à jour réussie
        header("Location: menu.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie des Mensurations</title>
    <style>
        .container {
            text-align: center;
        }
        .form-title {
            margin-bottom: 20px;
        }
        .form-grid {
            display: flex;
            justify-content: center;
        }
        .form-column {
            margin-right: 20px; /* Espacement entre les colonnes */
        }
        .form-group {
            margin-top: 20px;
            margin-right: 20px; /* Espacement entre les groupes de champs */
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        .menu-button {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .menu-button button {
            background-color: #add8e6;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <form class="menu-button" action="menu.php" method="get">
        <button type="submit">Retour vers le Menu</button>
    </form>

    <h2 class="form-title">Formulaire de Saisie des Mensurations</h2>
    
    <!-- Affichage de l'email de l'utilisateur -->
    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-grid">
        <div class="form-column">
            <label for="Nom">Nom:</label>
            <input type="text" id="Nom" name="Nom" required>

            <label for="Prenom">Prénom:</label>
            <input type="text" id="Prenom" name="Prenom" required>

            <label for="Phone">Téléphone:</label>
            <input type="text" id="Phone" name="Phone">

            <label for="Age">Âge:</label>
            <input type="number" id="Age" name="Age" required>
        </div>

        <div class="form-column">
            <label for="Taille">Taille en cm:</label>
            <input type="number" id="Taille" name="Taille" required>

            <label for="Poids">Poids:</label>
            <input type="number" id="Poids" name="Poids" required>

            <label for="Trtaille">Tour de taille:</label>
            <input type="number" id="Trtaille" name="Trtaille" required>

            <label for="Trhanches">Tour de hanches:</label>
            <input type="number" id="Trhanches" name="Trhanches" required>

            <label for="Trfesses">Tour de fesses:</label>
            <input type="number" id="Trfesses" name="Trfesses" required>
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</div>

</body>
</html>
