<?php
require '_settings.php';
date_default_timezone_set('Europe/Paris');

echo "Heure actuelle : " . date('d/m/Y H:i:s') . "<br><br>";

if (isset($_GET['reset'])) {
    $id_reset = intval($_GET['reset']);
    $database->prepare("UPDATE am_free SET reminder_3h_sent = 0 WHERE id = ?")->execute([$id_reset]);
    echo "<b>RESET EFFECTUÉ POUR L'ID $id_reset</b><br><br>";
}

$stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' ORDER BY id DESC LIMIT 10");
$stmt->execute();
$bookings = $stmt->fetchAll();

echo "<table border='1' style='border-collapse:collapse; padding:5px;'>";
echo "<tr><th>ID</th><th>Nom/RDV</th><th>Email</th><th>Rappel 3h Envoyé ?</th><th>Analyse</th></tr>";

$now = new DateTime();

foreach ($bookings as $booking) {
    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
    $status = "Format non reconnu";
    
    if (count($matches) === 3) {
        $rdv_date = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
        if ($rdv_date) {
            $diff = $now->diff($rdv_date);
            $total_minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
            $is_past = ($now > $rdv_date);
            
            if ($is_past) {
                $status = "RDV passé (" . $total_minutes . " min écoulées)";
            } else {
                $status = "RDV futur (dans " . round($total_minutes/60, 1) . " heures / " . $total_minutes . " min)";
            }
        }
    }

    echo "<tr>";
    echo "<td>" . $booking['id'] . "</td>";
    echo "<td>" . htmlspecialchars($booking['name']) . "</td>";
    echo "<td>" . $booking['email'] . "</td>";
    echo "<td>" . ($booking['reminder_3h_sent'] ? "OUI ✅" : "NON ❌") . "</td>";
    echo "<td>" . $status . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
