<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Autoriser les requêtes CORS
header('Access-Control-Allow-Methods: POST'); // Autoriser uniquement POST
header('Access-Control-Allow-Headers: Content-Type'); // Autoriser les en-têtes JSON

// Vérifier que la méthode est bien POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Méthode non autorisée. Utilisez POST.']);
    exit;
}

// Lire les données JSON envoyées
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Données JSON invalides.']);
    exit;
}

// Exemple de traitement des données
$prompt = $input['prompt'] ?? '';
if (empty($prompt)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Le champ "prompt" est requis.']);
    exit;
}

// Simuler une réponse (remplacez ceci par un appel à l'API OpenAI)
$response = [
    'choices' => [
        [
            'message' => [
                'content' => 'Ceci est une réponse simulée pour : ' . $prompt
            ]
        ]
    ]
];

echo json_encode($response);
?>
