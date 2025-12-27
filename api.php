<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure _config.php
require '_config.php';
error_log("Config loaded!");

// Définir le type de contenu
header('Content-Type: application/json');

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Seules les requêtes POST sont autorisées.']);
    exit;
}

// Lire les données JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (empty($data)) {
    http_response_code(400); // Requête incorrecte
    echo json_encode(['error' => 'Aucune donnée reçue']);
    exit;
}

// Vérifier si "prompt" est présent
if (!isset($data['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Le champ "prompt" est requis.']);
    exit;
}

// Log des données reçues
error_log("Données reçues : " . print_r($data, true));

// Réponse de test
echo json_encode([
    'message' => 'api.php fonctionne correctement',
    'prompt' => $data['prompt'],
    'api_key' => defined('API_KEY') ? 'API_KEY est définie' : 'API_KEY non définie'
]);
exit;
?>


