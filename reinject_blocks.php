<?php
/**
 * Script de ré-injection des blocages demandés hier soir
 */

require '_settings.php';

$manual_blocks = [
    '19/01/2026' => ['12:15', '14:45', '16:00', '17:15', '18:30'],
    '20/01/2026' => ['11:00', '13:30', '14:45'],
    '21/01/2026' => ['17:15'],
    '22/01/2026' => ['12:15'],
    '23/01/2026' => ['09:45', '11:00', '14:45', '18:30'],
    '24/01/2026' => ['09:45', '11:00', '12:15'],
    '26/01/2026' => ['17:15'],
    '27/01/2026' => ['13:30'],
    '30/01/2026' => ['17:15'],
    '31/01/2026' => ['09:45', '11:00', '12:15'],
];

$count = 0;
foreach ($manual_blocks as $date => $hours) {
    foreach ($hours as $h) {
        // Vérification si déjà présent
        $search = "%(RDV: $date à $h%";
        $check = $database->prepare("SELECT id FROM am_free WHERE name LIKE ?");
        $check->execute([$search]);
        
        if (!$check->fetch()) {
            $name = "RÉSERVÉ (RDV: $date à $h (AQUAVELO))";
            $ins = $database->prepare("INSERT INTO am_free (reference, center_id, name, email, phone, free) VALUES (?, 305, ?, 'deja@reserve.com', '0000', 3)");
            $ins->execute(['RESTORE'.rand(100,999), $name]);
            $count++;
        }
    }
}

echo "Nombre de blocages ré-injectés : $count";
?>
