<?php
require_once '../Include/config.php'; // Charge la clé API de manière sécurisée

header('Content-Type: application/json');

// Vérifie si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['prompt'])) {
        echo json_encode(['error' => 'Prompt is required']);
        http_response_code(400);
        exit;
    }

    $prompt = $data['prompt'];

    // Appelle l'API OpenAI
    $apiUrl = 'https://api.openai.com/v1/chat/completions';
    $headers = [
        'Authorization: Bearer ' . API_KEY,
        'Content-Type: application/json'
    ];
    $body = json_encode([
        'model' => 'gpt-3.5-turbo',
        'messages' => [['role' => 'user', 'content' => $prompt]],
        'max_tokens' => 250
    ]);

    // Effectue une requête cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        echo $response;
    } else {
        echo json_encode(['error' => 'API request failed', 'details' => $response]);
        http_response_code($httpCode);
    }
    exit;
}

// Si la requête n'est pas POST
http_response_code(405); // Méthode non autorisée
echo json_encode(['error' => 'Invalid request method']);
?>

