<?php
/**
 * Script pour générer le fichier .env à partir de google_key.json
 * Usage unique pour la migration
 */

$googleKeyFile = 'google_key.json';
$envFile = '.env';

if (!file_exists($googleKeyFile)) {
    die("❌ Erreur : Le fichier google_key.json n'existe pas.\n");
}

echo "<h1>🔧 Génération du fichier .env</h1>";

// 1. Lire le fichier google_key.json
$keyContent = file_get_contents($googleKeyFile);
$keyData = json_decode($keyContent, true);

if (!$keyData) {
    die("❌ Erreur : Impossible de parser google_key.json\n");
}

echo "<p>✅ Fichier google_key.json lu avec succès</p>";

// 2. Générer le contenu du .env
$envContent = "# Configuration Aquavelo - Généré automatiquement\n";
$envContent .= "# Date: " . date('Y-m-d H:i:s') . "\n\n";

// Option 1 : JSON complet en base64 (recommandé)
$envContent .= "# Google Calendar API - JSON complet en base64\n";
$envContent .= "GOOGLE_CALENDAR_KEY_JSON_BASE64=" . base64_encode($keyContent) . "\n\n";

// Option 2 : Champs individuels (alternative)
$envContent .= "# Ou bien, utiliser les champs individuels :\n";
$envContent .= "# GOOGLE_PROJECT_ID=" . ($keyData['project_id'] ?? '') . "\n";
$envContent .= "# GOOGLE_PRIVATE_KEY_ID=" . ($keyData['private_key_id'] ?? '') . "\n";
$envContent .= "# GOOGLE_PRIVATE_KEY=\"" . str_replace("\n", "\\n", $keyData['private_key'] ?? '') . "\"\n";
$envContent .= "# GOOGLE_CLIENT_EMAIL=" . ($keyData['client_email'] ?? '') . "\n";
$envContent .= "# GOOGLE_CLIENT_ID=" . ($keyData['client_id'] ?? '') . "\n\n";

// Autres variables d'environnement communes
$envContent .= "# Autres variables (optionnel)\n";
$envContent .= "# DB_HOST=\n";
$envContent .= "# DB_NAME=\n";
$envContent .= "# DB_USER=\n";
$envContent .= "# DB_PASS=\n";

// 3. Écrire le fichier .env
if (file_exists($envFile)) {
    echo "<p>⚠️ Le fichier .env existe déjà.</p>";
    echo "<p>📄 Contenu suggéré pour .env :</p>";
    echo "<pre style='background: #f0f0f0; padding: 15px; border-radius: 5px; max-height: 400px; overflow: auto;'>";
    echo htmlspecialchars($envContent);
    echo "</pre>";
    echo "<p><b>Voulez-vous remplacer le fichier existant ?</b></p>";
    echo "<form method='post'>";
    echo "<button type='submit' name='overwrite' value='1' style='background: #ff9800; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Oui, remplacer</button> ";
    echo "<button type='button' onclick='window.location.reload()' style='background: #ccc; color: #333; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Non, annuler</button>";
    echo "</form>";
    
    if (isset($_POST['overwrite'])) {
        file_put_contents($envFile, $envContent);
        echo "<p style='color: green;'>✅ <b>Fichier .env mis à jour avec succès !</b></p>";
    }
} else {
    file_put_contents($envFile, $envContent);
    echo "<p style='color: green;'>✅ <b>Fichier .env créé avec succès !</b></p>";
}

echo "<hr>";
echo "<h2>📋 Instructions pour la production (Clever Cloud)</h2>";
echo "<ol>";
echo "<li>Copiez le contenu du .env ci-dessus</li>";
echo "<li>Allez dans <b>Clever Cloud Console</b> → Votre application</li>";
echo "<li>Section <b>Environment variables</b></li>";
echo "<li>Ajoutez la variable : <code>GOOGLE_CALENDAR_KEY_JSON_BASE64</code></li>";
echo "<li>Collez la valeur base64 (la longue chaîne)</li>";
echo "<li>Redémarrez l'application</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🧪 Test</h2>";
echo "<p>Pour tester que tout fonctionne :</p>";
echo "<ol>";
echo "<li><a href='test_google_calendar_cannes.php'>Tester Google Calendar</a></li>";
echo "<li><a href='cron_sync_google.php'>Lancer la synchronisation</a></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🔐 Sécurité</h2>";
echo "<p>✅ Le fichier <code>.env</code> est dans <code>.gitignore</code> et ne sera pas commité</p>";
echo "<p>✅ Le fichier <code>google_key.json</code> peut maintenant être supprimé (il sera généré dynamiquement)</p>";
echo "<p>⚠️ <b>Ne partagez jamais le contenu du .env publiquement !</b></p>";
?>
