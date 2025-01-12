<?php
require_once '../config.php'; // 

header('Content-Type: application/json');

// Lire les données POST envoyées depuis le frontend
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que les données nécessaires sont présentes
if (!isset($data['prompt'])) {
    echo json_encode(['error' => 'Prompt is required']);
    http_response_code(400);
    exit;
}

$prompt = $data['prompt'];

// Configurer les paramètres pour l'API OpenAI
$apiUrl = 'https://api.openai.com/v1/chat/completions';
$apiHeaders = [
    'Authorization: Bearer ' . API_KEY,
    'Content-Type: application/json'
];

$apiBody = json_encode([
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => $prompt]],
    'max_tokens' => 250
]);

// Effectuer une requête cURL vers l'API
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $apiBody);
curl_setopt($ch, CURLOPT_HTTPHEADER, $apiHeaders);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo $response;
} else {
    echo json_encode(['error' => 'API request failed', 'details' => $response]);
    http_response_code($httpCode);
}
?>
