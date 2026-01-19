<?php
require "_settings.php";
header('Content-Type: text/plain');

echo "Heure serveur : " . date('Y-m-d H:i:s') . "\n";

$stmt = $db->prepare("SELECT id, nom, email, booking_date, rappel_24h, rappel_3h FROM am_free WHERE email = 'claude@alesiaminceur.com' ORDER BY id DESC LIMIT 1");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo "RDV trouvé : ID " . $row['id'] . "\n";
    echo "Date : " . $row['booking_date'] . "\n";
    
    $rdvDate = new DateTime($row['booking_date']);
    $now = new DateTime();
    $diff = $now->diff($rdvDate);
    
    // Calcul précis en minutes
    $totalMinutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
    if ($diff->invert) $totalMinutes = -$totalMinutes;
    $hours = $totalMinutes / 60;
    
    echo "Différence : " . round($hours, 2) . " heures (" . $totalMinutes . " minutes)\n";
    echo "Rappel 24h envoyé : " . ($row['rappel_24h'] ? "OUI ✅" : "NON ❌") . "\n";
    echo "Rappel 3h envoyé : " . ($row['rappel_3h'] ? "OUI ✅" : "NON ❌") . "\n";
    
    if ($hours >= 18 && $hours <= 30 && !$diff->invert && !$row['rappel_24h']) {
        echo "\n>>> STATUT : Éligible pour le rappel 24h. Il sera envoyé lors du prochain passage du cron (toutes les heures).\n";
    }
} else {
    echo "Aucun rendez-vous trouvé pour claude@alesiaminceur.com\n";
}
