<?php
// Désactiver l'affichage des erreurs en production (comme dans _settings.php)
ini_set('display_errors', 0);
error_reporting(0);

// Définir la clé API OpenAI
define('API_KEY', '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');

// Vérifier si la requête est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Seules les requêtes POST sont autorisées.']);
    exit;
}

// Lire les données JSON envoyées depuis le formulaire
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Vérifier si les données sont valides
if (empty($data) || !isset($data['prompt'])) {
    http_response_code(400); // Requête incorrecte
    echo json_encode(['error' => 'Données invalides ou champ "prompt" manquant.']);
    exit;
}

// Récupérer le prompt depuis les données
$prompt = $data['prompt'];

// Envoyer la requête à l'API OpenAI
$url = 'https://api.openai.com/v1/chat/completions';
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . API_KEY
];
// Récupérer max_tokens depuis les données si fourni, sinon utiliser 400 par défaut
$max_tokens = isset($data['max_tokens']) ? (int)$data['max_tokens'] : 400;

$body = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => $prompt]],
    'max_tokens' => $max_tokens
];

// Utiliser cURL pour envoyer la requête
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500); // Erreur serveur
    echo json_encode(['error' => 'Erreur cURL : ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Vérifier que la réponse n'est pas vide
if (empty($response)) {
    http_response_code(500);
    echo json_encode(['error' => 'Réponse vide de l\'API OpenAI']);
    exit;
}

// Décoder la réponse JSON
$responseData = json_decode($response, true);

// Vérifier si le JSON est valide
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['error' => 'Réponse JSON invalide de l\'API: ' . json_last_error_msg()]);
    exit;
}

// Vérifier le code HTTP de la réponse
if ($httpCode !== 200) {
    http_response_code($httpCode);
    $errorMessage = 'Erreur API OpenAI';
    if (isset($responseData['error']['message'])) {
        $errorMessage = $responseData['error']['message'];
    } elseif (isset($responseData['error'])) {
        $errorMessage = is_string($responseData['error']) ? $responseData['error'] : json_encode($responseData['error']);
    }
    echo json_encode(['error' => $errorMessage, 'http_code' => $httpCode]);
    exit;
}

// Vérifier si la réponse contient une erreur (même avec code HTTP 200)
if (isset($responseData['error'])) {
    http_response_code(500);
    $errorMessage = isset($responseData['error']['message']) ? $responseData['error']['message'] : 'Erreur API OpenAI';
    echo json_encode(['error' => $errorMessage]);
    exit;
}

// Vérifier que la structure de réponse est correcte
if (!isset($responseData['choices']) || !is_array($responseData['choices']) || empty($responseData['choices'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Réponse API invalide: structure "choices" manquante ou vide']);
    exit;
}

// Renvoyer la réponse de l'API OpenAI au client
echo $response;
?>
