<?php
include 'settings.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM partenariats WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("Location: affichage_fiches_partenariats.php");
            exit();
        } else {
            echo "Erreur lors de la suppression de la fiche.";
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de la fiche : " . $e->getMessage();
    }
} else {
    echo "Méthode de requête non supportée.";
}
?>
