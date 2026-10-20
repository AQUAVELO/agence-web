<?php
/**
 * WhatsApp automatique ~1 h après le début du RDV.
 * Fenêtre : 50–80 minutes (cron toutes les 15 min).
 */

if (file_exists(__DIR__ . '/load_env.php')) {
    require_once __DIR__ . '/load_env.php';
}
require_once '_settings.php';
require_once __DIR__ . '/Include/cron_email_helpers.php';
date_default_timezone_set('Europe/Paris');

try {
    $chk = $database->query("SHOW COLUMNS FROM am_free LIKE 'whatsapp_1h_sent'");
    if ($chk->rowCount() === 0) {
        $database->exec('ALTER TABLE am_free ADD COLUMN whatsapp_1h_sent TINYINT(1) NOT NULL DEFAULT 0');
    }
} catch (Throwable $e) {
    error_log('cron_whatsapp_1h colonne : ' . $e->getMessage());
    echo 'Erreur colonne whatsapp_1h_sent';
    return;
}

$stmt = $database->prepare(
    "SELECT * FROM am_free
     WHERE name LIKE '%(RDV:%'
       AND name NOT LIKE '%BLOQUE%'
       AND COALESCE(segment_id, '') <> 'admin-lock'
       AND whatsapp_1h_sent = 0"
);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$now = new DateTime();
$count = 0;
$eligibleMissingApi = 0;
$apiReady = function_exists('sendWhatsAppConfigured') && sendWhatsAppConfigured();

foreach ($bookings as $booking) {
    $rdvStart = aquavelo_cron_parse_rdv_start($booking);
    if (!$rdvStart) {
        continue;
    }

    $minutesPassed = (int) floor(($now->getTimestamp() - $rdvStart->getTimestamp()) / 60);
    $forceThis = isset($_GET['force_email']) && $_GET['force_email'] === $booking['email'];
    $inWindow = ($minutesPassed >= 50 && $minutesPassed <= 80);

    if (!$forceThis && !$inWindow) {
        continue;
    }

    $phone = trim((string) ($booking['phone'] ?? ''));
    if ($phone === '') {
        continue;
    }

    if (!$apiReady) {
        $eligibleMissingApi++;
        continue;
    }

    $centerId = (int) ($booking['center_id'] ?: 305);
    $centerInfo = aquavelo_cron_lookup_center($database, $centerId);
    $prenom = aquavelo_cron_client_first_name($booking);
    if ($prenom === '') {
        $prenom = 'Bonjour';
    }

    if (in_array($centerId, [305, 347, 349], true)) {
        $message = "Bonjour $prenom, merci d’être venu(e) découvrir Aquavelo ! J’espère que la séance vous a plu. Pour toute question ou pour vous inscrire : https://www.aquavelo.com/vente\nClaude — 06 22 64 70 95";
        $signature = 'Claude — 06 22 64 70 95';
    } else {
        $ville = (string) ($centerInfo['city'] ?? '');
        $tel = (string) ($centerInfo['phone'] ?? '');
        $message = "Bonjour $prenom, merci d’être venu(e) découvrir Aquavelo $ville ! J’espère que la séance vous a plu. Pour toute question : $tel";
        $signature = 'Aquavelo ' . $ville . ($tel !== '' ? ' — ' . $tel : '');
    }

    $result = sendWhatsApp($phone, $message, [
        'template_params' => [$prenom, $signature],
    ]);

    if (!empty($result['ok'])) {
        $database->prepare('UPDATE am_free SET whatsapp_1h_sent = 1 WHERE id = ?')->execute([$booking['id']]);
        $count++;
    } elseif (empty($result['skipped'])) {
        error_log('Erreur Cron WhatsApp 1h pour ' . $booking['email'] . ' : ' . ($result['error'] ?? 'inconnu'));
    }
}

if ($eligibleMissingApi > 0 && function_exists('sendTelegram')) {
    sendTelegram(
        '<b>WhatsApp 1h après RDV</b> : ' . $eligibleMissingApi
        . " prospect(s) dans la fenêtre, mais l’API n’est pas configurée (WHATSAPP_TOKEN / WHATSAPP_PHONE_NUMBER_ID)."
    );
}

echo "WhatsApp 1h envoyés : $count";
