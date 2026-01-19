<?php
require '_settings.php';
date_default_timezone_set('Europe/Paris');

$id = 234154; // Votre RDV test
$stmt = $database->prepare("SELECT * FROM am_free WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

echo "ANALYSE DU RDV " . $id . "<br>";
echo "Nom complet dans la base : " . $booking['name'] . "<br>";

$now = new DateTime();
echo "Heure actuelle : " . $now->format('d/m/Y H:i:s') . "<br>";

preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);

if (count($matches) === 3) {
    echo "Date extraite : " . $matches[1] . "<br>";
    echo "Heure extraite : " . $matches[2] . "<br>";
    
    $rdv_date = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
    if ($rdv_date) {
        echo "DateTime créé : " . $rdv_date->format('Y-m-d H:i:s') . "<br>";
        
        $diff = $now->diff($rdv_date);
        $total_minutes_until = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
        echo "Minutes restantes : " . $total_minutes_until . "<br>";
        
        if ($rdv_date > $now) {
            echo "Le RDV est dans le futur ✅<br>";
        } else {
            echo "Le RDV est dans le passé ❌<br>";
        }
        
        if ($total_minutes_until >= 120 && $total_minutes_until <= 240) {
            echo "Condition de temps (120-240 min) : OK ✅<br>";
        } else {
            echo "Condition de temps (120-240 min) : ECHEC ❌<br>";
        }
    } else {
        echo "Erreur lors de la création de l'objet DateTime ❌<br>";
    }
} else {
    echo "Regex : ECHEC ❌ (Pas de correspondance trouvée)<br>";
}
?>
