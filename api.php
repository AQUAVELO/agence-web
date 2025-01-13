<?php
header('Content-Type: application/json');

// Récupérer les données du formulaire
$data = json_decode(file_get_contents('php://input'), true);

// Clé API (stockée de manière sécurisée)
$apiKey = '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';

// Appeler l'API OpenAI
$url = 'https://api.openai.com/v1/chat/completions';
$response = http_post_data($url, json_encode([
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => $data['prompt']]],
    'max_tokens' => 250
]), [
    'headers' => [
        'Content-Type: application/json',
        'Authorization' => 'Bearer ' . $apiKey
    ]
]);

echo $response;
?>
