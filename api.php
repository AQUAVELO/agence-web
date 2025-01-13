<?php
require_once '../include/config.php'; // Assurez-vous que ce chemin est correct

header('Content-Type: application/json');

// Étape de débogage : vérifier si le fichier est exécuté
error_log("api.php est appelé");

// Vérifiez la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Seules les requêtes POST sont autorisées.']);
    exit;
}

// Lire les données JSON
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data)) {
    http_response_code(400); // Requête incorrecte
    echo json_encode(['error' => 'Aucune donnée reçue']);
    exit;
}

// Étape de débogage : afficher les données reçues
error_log("Données reçues : " . print_r($data, true));

// Vérifiez si "prompt" est présent
if (!isset($data['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Le champ "prompt" est requis.']);
    exit;
}

// Testez en renvoyant une réponse fictive
echo json_encode(['message' => 'api.php fonctionne correctement', 'prompt' => $data['prompt']]);
exit;
?>


