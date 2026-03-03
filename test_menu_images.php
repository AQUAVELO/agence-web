<?php
require '_settings.php';

echo "<pre>";
echo "<h3>Contenu de la table 'menu' pour le jour " . date("d") . "</h3>";

$stmt = $database->prepare("SELECT day_number, photo_pet_dej, photo_repas_midi, photo_souper, photo_collation FROM menu WHERE day_number = ?");
$stmt->execute([date("d")]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if ($menu) {
    print_r($menu);
    
    echo "\n<h3>Vérification des fichiers images :</h3>";
    $fields = ['photo_pet_dej', 'photo_repas_midi', 'photo_souper', 'photo_collation'];
    foreach ($fields as $field) {
        $filename = $menu[$field];
        if (!empty($filename)) {
            $path = __DIR__ . '/images/' . $filename;
            if (file_exists($path)) {
                echo "✅ $field: $filename EXISTE (" . filesize($path) . " bytes)\n";
            } else {
                echo "❌ $field: $filename MANQUANT\n";
            }
        } else {
            echo "⚠️ $field: VIDE en base de données\n";
        }
    }
} else {
    echo "Aucun menu trouvé pour le jour " . date("d");
}

echo "\n<h3>Liste des fichiers dans le dossier images/ (limité à 20) :</h3>";
$files = glob(__DIR__ . '/images/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
$count = 0;
foreach ($files as $file) {
    if ($count >= 20) break;
    echo basename($file) . "\n";
    $count++;
}

echo "</pre>";
