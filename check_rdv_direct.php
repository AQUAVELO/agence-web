<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '_settings.php';

echo "<pre>";
echo "Heure serveur : " . date('Y-m-d H:i:s') . "\n";

// On cherche le dernier RDV de Claude pour tester
$email_test = 'claude@alesiaminceur.com';
$stmt = $database->prepare("SELECT id, name, email, reminder_sent, reminder_3h_sent FROM am_free WHERE email = ? AND name LIKE '%(RDV:%' ORDER BY id DESC LIMIT 1");
$stmt->execute([$email_test]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo "RDV trouvé : ID " . $row['id'] . "\n";
    echo "Contenu du champ name : " . $row['name'] . "\n";
    
    // Extraction de la date comme dans le cron
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $row['name'], $matches);
    
    if (count($matches) === 3) {
        $rdvDate = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
        $now = new DateTime();
        
        if ($rdvDate) {
            $diff = $now->diff($rdvDate);
            $totalMinutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
            if ($rdvDate < $now) $totalMinutes = -$totalMinutes;
            
            echo "Date RDV extraite : " . $rdvDate->format('Y-m-d H:i') . "\n";
            echo "Temps restant : " . round($totalMinutes / 60, 2) . " heures (" . $totalMinutes . " minutes)\n";
            echo "Rappel 24h envoyé : " . ($row['reminder_sent'] ? "OUI ✅" : "NON ❌") . "\n";
            echo "Rappel 3h envoyé : " . ($row['reminder_3h_sent'] ? "OUI ✅" : "NON ❌") . "\n";
            
            // Simulation de la fenêtre d'envoi 24h (entre 18h et 30h)
            $hours = $totalMinutes / 60;
            if ($hours >= 18 && $hours <= 30) {
                echo "👉 STATUT : Le RDV est dans la fenêtre d'envoi des 24h !\n";
            } else {
                echo "👉 STATUT : Hors fenêtre d'envoi 24h (Fenêtre = entre 18h et 30h avant).\n";
            }
        }
    } else {
        echo "❌ Impossible d'extraire la date du champ name. Format attendu: 'Nom (RDV: DD/MM/YYYY à HH:MM)'\n";
    }
} else {
    echo "Aucun RDV trouvé pour l'email : $email_test\n";
}
echo "</pre>";
