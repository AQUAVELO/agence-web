<?php
/**
 * Audit et test du protocole email séance d'essai.
 *
 * Audit : ?key=aquavelo123
 * Tests SMTP (vers claude@) : ?key=aquavelo123&send_tests=1
 * Simuler crons : ?key=aquavelo123&run_crons=1
 */
declare(strict_types=1);

require_once '_settings.php';
require_once __DIR__ . '/google_calendar_rdv_helpers.php';
require_once __DIR__ . '/Include/cron_email_helpers.php';
require_once __DIR__ . '/Include/rdv_confirmation_mail.php';

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Europe/Paris');

$secretKey = 'aquavelo123';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    die('Accès non autorisé.');
}

header('Content-Type: text/plain; charset=utf-8');

$sendTests = isset($_GET['send_tests']) && $_GET['send_tests'] === '1';
$runCrons = isset($_GET['run_crons']) && $_GET['run_crons'] === '1';
$testTo = trim((string) ($_GET['to'] ?? 'claude@alesiaminceur.com'));

echo "=== AUDIT PROTOCOLE EMAIL AQUAVELO ===\n";
echo 'Date : ' . date('Y-m-d H:i:s') . "\n\n";

// 1. Config Mailjet
echo "--- Configuration SMTP ---\n";
$smtpOk = !empty($settings['mjhost']) && !empty($settings['mjusername']) && !empty($settings['mjpassword']);
echo 'Host     : ' . ($settings['mjhost'] ?? '(vide)') . "\n";
echo 'Username : ' . ($settings['mjusername'] ? substr($settings['mjusername'], 0, 6) . '...' : '(vide)') . "\n";
echo 'Password : ' . (!empty($settings['mjpassword']) ? 'OK' : 'MANQUANT') . "\n";
echo 'Statut   : ' . ($smtpOk ? 'OK' : 'ERREUR') . "\n\n";

if ($smtpOk && $sendTests) {
    echo "--- Test connexion SMTP ---\n";
    try {
        $probe = new PHPMailer(true);
        $probe->isSMTP();
        $probe->Host = $settings['mjhost'];
        $probe->SMTPAuth = true;
        $probe->Username = $settings['mjusername'];
        $probe->Password = $settings['mjpassword'];
        $probe->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $probe->Port = 587;
        $probe->SMTPDebug = 0;
        $probe->setFrom('service.clients@aquavelo.com', 'Aquavelo Test');
        $probe->addAddress($testTo);
        $probe->Subject = '[TEST] Connexion SMTP Aquavelo ' . date('H:i:s');
        $probe->Body = 'Test de connexion Mailjet OK.';
        $probe->send();
        echo "Envoi test SMTP vers $testTo : OK (message_id=" . $probe->getLastMessageID() . ")\n\n";
    } catch (Exception $e) {
        echo "Envoi test SMTP : ECHEC — " . ($probe->ErrorInfo ?? $e->getMessage()) . "\n\n";
    }
}

// 2. Analyse parsing RDV récents
echo "--- Parsing RDV (50 dernières réservations) ---\n";
$stmt = $database->query(
    "SELECT id, name, email, center_id, reminder_sent, reminder_3h_sent, after_session_sent,
            followup_48h_sent, followup_2d_sent, followup_7d_sent
     FROM am_free
     WHERE name LIKE '%(RDV:%'
       AND name NOT LIKE '%BLOQUE%'
       AND COALESCE(segment_id, '') <> 'admin-lock'
     ORDER BY id DESC
     LIMIT 50"
);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$parseOk = 0;
$parseFail = 0;
$legacyRegexFail = 0;
$now = new DateTime();

foreach ($bookings as $b) {
    $parsed = aquavelo_cron_parse_rdv_start($b);
    if ($parsed) {
        $parseOk++;
    } else {
        $parseFail++;
        echo "  ECHEC parse #{$b['id']} : {$b['name']}\n";
    }

    if (!preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', (string) $b['name'])) {
        $legacyRegexFail++;
    }
}

echo "Réservations analysées : " . count($bookings) . "\n";
echo "Parse helper OK        : $parseOk\n";
echo "Parse helper ECHEC     : $parseFail\n";
echo "Ancien regex ECHEC     : $legacyRegexFail (crons avant correctif)\n\n";

// 3. Éligibilité protocole
echo "--- Éligibilité protocole (sur les 50 RDV) ---\n";
$eligible = [
    'confirmation' => 0,
    'rappel_24h' => 0,
    'rappel_3h' => 0,
    'apres_seance' => 0,
    'suivi_48h' => 0,
    'suivi_2j' => 0,
    'suivi_7j' => 0,
];

foreach ($bookings as $b) {
    $rdv = aquavelo_cron_parse_rdv_start($b);
    if (!$rdv) {
        continue;
    }

    $centerId = (int) ($b['center_id'] ?? 305);
    $diff = $now->diff($rdv);
    $hoursUntil = ($diff->invert === 0) ? (($diff->days * 24) + $diff->h) : -1 * (($diff->days * 24) + $diff->h + 1);
    $hoursPassed = ($diff->invert === 1) ? (($diff->days * 24) + $diff->h) : -1;
    $minsUntil = ($rdv > $now) ? (int) (($rdv->getTimestamp() - $now->getTimestamp()) / 60) : 0;

    if ($rdv > $now) {
        if ($hoursUntil >= 18 && $hoursUntil <= 30 && !(int) $b['reminder_sent']) {
            $eligible['rappel_24h']++;
        }
        if ($minsUntil >= 150 && $minsUntil <= 210 && !(int) $b['reminder_3h_sent']) {
            $eligible['rappel_3h']++;
        }
    } else {
        if ($hoursPassed >= 3 && $hoursPassed <= 6 && !(int) $b['after_session_sent']) {
            $eligible['apres_seance']++;
        }
        if ($hoursPassed >= 44 && $hoursPassed <= 52 && !(int) $b['followup_48h_sent']) {
            $eligible['suivi_48h']++;
        }
        if ($hoursPassed >= 44 && $hoursPassed <= 60 && !(int) $b['followup_2d_sent'] && !in_array($centerId, [305, 347, 349], true)) {
            $eligible['suivi_2j']++;
        }
        if ($hoursPassed >= 160 && $hoursPassed <= 184 && !(int) $b['followup_7d_sent']) {
            $eligible['suivi_7j']++;
        }
    }
}

$eligible['confirmation'] = count($bookings);

foreach ($eligible as $step => $count) {
    echo sprintf("  %-14s : %d en attente / éligible(s)\n", $step, $count);
}
echo "\n";

// 4. Logs cron récents
echo "--- Dernières lignes cron_log.txt ---\n";
$logFile = __DIR__ . '/cron_log.txt';
if (is_file($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES) ?: [];
    $tail = array_slice($lines, -15);
    foreach ($tail as $line) {
        echo $line . "\n";
    }
} else {
    echo "(fichier absent — les crons tournent peut-être uniquement via Clever Cloud CLI)\n";
}
echo "\n";

// 5. Tests templates email
if ($sendTests) {
    echo "--- Envoi des modèles de test vers $testTo ---\n";
    $centerInfo = aquavelo_cron_lookup_center($database, 305);
    $templates = [
        'confirmation' => [
            'from' => 'claude@alesiaminceur.com',
            'subject' => '[TEST] Confirmation séance Aquavelo Cannes',
            'body' => 'Modèle confirmation — test protocole.',
        ],
        'rappel_24h' => [
            'subject' => '[TEST] Votre séance Aquavelo demain !',
            'body' => 'Modèle rappel 24h — test protocole.',
        ],
        'rappel_3h' => [
            'subject' => '[TEST] Rappel de séance découverte',
            'body' => 'Modèle rappel 3h — test protocole.',
        ],
        'apres_seance' => [
            'subject' => '[TEST] Merci de votre visite chez Aquavelo !',
            'body' => 'Modèle après séance — test protocole.',
        ],
        'suivi_48h' => [
            'subject' => '[TEST] Suite à votre séance découverte Aquavelo',
            'body' => 'Modèle suivi 48h — test protocole.',
        ],
        'suivi_7j' => [
            'subject' => '[TEST] Des nouvelles de votre séance découverte',
            'body' => 'Modèle suivi J+7 — test protocole.',
        ],
    ];

    foreach ($templates as $name => $tpl) {
        try {
            if ($name === 'confirmation') {
                $fakeBooking = [
                    'id' => 1,
                    'name' => 'Test Protocole (RDV: Jeudi 17/07/2026 à 10:15 (AQUAVELO))',
                    'email' => $testTo,
                    'phone' => '0600000000',
                    'center_id' => 305,
                ];
                $res = aquavelo_send_rdv_confirmation_to_client($database, $settings, $fakeBooking);
                echo $name . ' : ' . ($res['ok'] ? 'OK — ' . $res['message'] : 'ECHEC — ' . $res['message']) . "\n";
                continue;
            }

            $mail = aquavelo_cron_create_mailer($settings, $centerInfo);
            $mail->clearAddresses();
            $mail->addAddress($testTo);
            $mail->Subject = $tpl['subject'];
            $mail->Body = $tpl['body'];
            $mail->send();
            echo "$name : OK\n";
        } catch (Exception $e) {
            echo "$name : ECHEC — " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

// 6. Exécution dry-run des crons
if ($runCrons) {
    echo "--- Exécution des crons (production) ---\n";
    $cronFiles = [
        'cron_rappel_24h.php',
        'cron_rappel_3h.php',
        'cron_apres_seance.php',
        'cron_suivi_48h.php',
        'cron_suivi_2j.php',
        'cron_suivi_7j.php',
    ];
    foreach ($cronFiles as $file) {
        echo ">> $file : ";
        ob_start();
        try {
            include __DIR__ . '/' . $file;
            echo trim((string) ob_get_clean()) . "\n";
        } catch (Throwable $e) {
            ob_end_clean();
            echo 'ERREUR — ' . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

echo "=== FIN AUDIT ===\n";
echo "Options : &send_tests=1  &run_crons=1  &to=email@domaine.com\n";
