<?php
require 'vendor/autoload.php'; // Assurez-vous que Composer a installé Mailjet

use \Mailjet\Resources;

// Configuration Mailjet
$apiKey = 'adf33e0c77039ed69396e3a8a07400cb';
$apiSecret = '05906e966c8e2933b1dc8b0f8bb1e18b';

$mj = new \Mailjet\Client($apiKey, $apiSecret, true, ['version' => 'v3.1']);

$recipientEmail = "aqua.cannes@gmail.com"; // Remplacez par votre email de test
$recipientName = "Test User"; // Nom du destinataire

$body = [
    'Messages' => [
        [
            'From' => [
                'Email' => "jacquesverdier4@gmail.com", // L'email de l'expéditeur
                'Name' => "Aquavelo Natation"
            ],
            'To' => [
                [
                    'Email' => $recipientEmail,
                    'Name' => $recipientName
                ]
            ],
            'Subject' => "Test d'Email avec Mailjet",
            'TextPart' => "Bonjour, ceci est un test d'envoi d'email avec Mailjet.",
            'HTMLPart' => "<h3>Bonjour,</h3><p>Ceci est un test d'envoi d'email avec Mailjet.</p>"
        ]
    ]
];

// Envoi de l'email
$response = $mj->post(Resources::$Email, ['body' => $body]);

// Vérification du succès de l'envoi
if ($response->success()) {
    echo "✅ Email envoyé avec succès à $recipientEmail";
} else {
    echo "❌ Échec de l'envoi de l'email.";
    echo "<pre>";
    print_r($response->getData()); // Affiche la réponse de Mailjet pour debug
    echo "</pre>";
}
?>
