<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$body = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => $prompt]],
    'max_tokens' => 250 // Limite la réponse à 250 tokens
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

// Renvoyer la réponse de l'API OpenAI au client
echo $response;
?>
