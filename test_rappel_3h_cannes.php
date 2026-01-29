<?php
/**
 * Script de test pour vérifier l'envoi de SMS 3h avant pour Cannes
 */
require '_settings.php';
date_default_timezone_set('Europe/Paris');

echo "<h2>🔍 Vérification des rappels SMS 3h pour Cannes</h2>";
echo "<pre>";

// 1. Vérifier la configuration SMS Factor
echo "1. Configuration SMS Factor :\n";
echo "   Token configuré : " . (empty($settings['smsfactor_token']) ? "❌ NON" : "✅ OUI") . "\n\n";

// 2. Vérifier les rendez-vous de Cannes à venir
$center_id_cannes = 305;
echo "2. Rendez-vous de Cannes (center_id = $center_id_cannes) à venir :\n";
$stmt = $database->prepare("
    SELECT id, name, email, phone, center_id, reminder_3h_sent 
    FROM am_free 
    WHERE name LIKE '%(RDV:%' 
    AND (center_id = ? OR center_id IS NULL)
    AND reminder_3h_sent = 0
    ORDER BY id DESC 
    LIMIT 10
");
$stmt->execute([$center_id_cannes]);
$bookings = $stmt->fetchAll();

if (empty($bookings)) {
    echo "   ⚠️ Aucun rendez-vous trouvé pour Cannes avec reminder_3h_sent = 0\n";
} else {
    echo "   ✅ " . count($bookings) . " rendez-vous trouvés\n\n";
    
    $now = new DateTime();
    echo "3. Analyse des rendez-vous (heure actuelle : " . $now->format('d/m/Y H:i:s') . ") :\n\n";
    
    foreach ($bookings as $booking) {
        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $booking['name'], $matches);
        
        if (count($matches) === 3) {
            $rdv_date = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2]);
            
            if ($rdv_date) {
                $diff = $now->diff($rdv_date);
                $total_minutes_until = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                $is_future = ($rdv_date > $now);
                
                $center_id = $booking['center_id'] ?: 305;
                $client_name = trim(explode('(RDV:', $booking['name'])[0]);
                
                echo "   📅 RDV #{$booking['id']} : {$client_name}\n";
                echo "      Date/heure : " . $rdv_date->format('d/m/Y H:i') . "\n";
                echo "      Center ID : $center_id " . ($center_id == 305 ? "(Cannes ✅)" : "(⚠️ Autre centre)") . "\n";
                echo "      Téléphone : " . ($booking['phone'] ? $booking['phone'] . " ✅" : "❌ MANQUANT") . "\n";
                echo "      Temps restant : " . ($is_future ? $total_minutes_until . " minutes (" . round($total_minutes_until/60, 1) . "h)" : "PASSÉ") . "\n";
                
                // Vérifier si dans la fenêtre d'envoi (120-240 min)
                if ($is_future && $total_minutes_until >= 120 && $total_minutes_until <= 240) {
                    echo "      Statut : ✅ DANS LA FENÊTRE D'ENVOI (2h-4h avant)\n";
                    if ($total_minutes_until >= 170 && $total_minutes_until <= 190) {
                        echo "      ⭐ PROCHE DE 3H (entre 2h50 et 3h10)\n";
                    }
                } elseif ($is_future && $total_minutes_until < 120) {
                    echo "      Statut : ⏳ TROP TÔT (< 2h avant)\n";
                } elseif ($is_future && $total_minutes_until > 240) {
                    echo "      Statut : ⏳ TROP TARD (> 4h avant)\n";
                } else {
                    echo "      Statut : ❌ RDV PASSÉ\n";
                }
                echo "\n";
            }
        }
    }
}

// 4. Test d'envoi SMS (optionnel - décommenter pour tester)
echo "4. Test d'envoi SMS :\n";
echo "   Pour tester l'envoi réel, utilisez : test_sms.php?phone=VOTRE_NUMERO\n";
echo "   Exemple : test_sms.php?phone=0622647095\n\n";

// 5. Vérifier le cron
echo "5. Configuration du cron :\n";
if (file_exists('clevercloud/cron.json')) {
    $cron_config = json_decode(file_get_contents('clevercloud/cron.json'), true);
    foreach ($cron_config as $cron) {
        if ($cron['id'] === 'rappel-3h') {
            echo "   ✅ Cron configuré : {$cron['schedule']} (toutes les heures à la 10ème minute)\n";
            echo "   Commande : {$cron['command']}\n";
        }
    }
} else {
    echo "   ⚠️ Fichier cron.json non trouvé\n";
}

echo "\n✅ Vérification terminée\n";
echo "</pre>";
?>
