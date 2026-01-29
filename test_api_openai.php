<?php
/**
 * Script de test pour vérifier la connexion à l'API OpenAI
 */
require '_settings.php';

// Utiliser la même clé API que dans api_handler.php
$api_key = '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';

echo "<h2>Test de connexion à l'API OpenAI</h2>";
echo "<pre>";

// Test simple
$url = 'https://api.openai.com/v1/chat/completions';
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
];
$body = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => 'Bonjour, répondez simplement "OK"']],
    'max_tokens' => 10
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Code HTTP : $httpCode\n\n";

if (curl_errno($ch)) {
    echo "❌ Erreur cURL : " . curl_error($ch) . "\n";
} else {
    $responseData = json_decode($response, true);
    
    if ($httpCode === 200) {
        if (isset($responseData['choices'][0]['message']['content'])) {
            echo "✅ API fonctionnelle !\n";
            echo "Réponse : " . $responseData['choices'][0]['message']['content'] . "\n";
        } else {
            echo "⚠️ Réponse reçue mais format inattendu\n";
            echo "Réponse complète :\n";
            print_r($responseData);
        }
    } else {
        echo "❌ Erreur API\n";
        if (isset($responseData['error'])) {
            echo "Message d'erreur : " . (isset($responseData['error']['message']) ? $responseData['error']['message'] : json_encode($responseData['error'])) . "\n";
        } else {
            echo "Réponse complète :\n";
            print_r($responseData);
        }
    }
}

curl_close($ch);

echo "\n\n✅ Test terminé";
echo "</pre>";
?>
