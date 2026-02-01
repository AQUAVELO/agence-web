<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('API_KEY', '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');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST only']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (empty($data) || !isset($data['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing prompt']);
    exit;
}

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . API_KEY
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => $data['prompt']]],
    'max_tokens' => $data['max_tokens'] ?? 400
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo $response;
    exit;
}

echo $response;
?>
