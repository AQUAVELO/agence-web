<?php
/**
 * 8h (Europe/Paris) : RDV Google Agenda du jour → prospects sandbox Resamania → Telegram.
 */
declare(strict_types=1);

require __DIR__ . '/load_env.php';
putenv('AQUAVELO_SKIP_DB=1');
$_ENV['AQUAVELO_SKIP_DB'] = '1';
require __DIR__ . '/_settings.php';
date_default_timezone_set('Europe/Paris');
require_once __DIR__ . '/Include/trial_attendance.php';
require_once __DIR__ . '/Include/resamania_sandbox.php';

$forced = (PHP_SAPI === 'cli' && in_array('--force', $argv ?? [], true))
    || (isset($_GET['force']) && (string) $_GET['force'] === '1');

$now = new DateTime('now', new DateTimeZone('Europe/Paris'));
if (!$forced && (int) $now->format('G') !== 8) {
    echo "skip: pas 8h Europe/Paris\n";
    return;
}

if (!headers_sent()) {
    header('Content-Type: text/plain; charset=utf-8');
}

if (!aquavelo_resa_sandbox_configured()) {
    $msg = 'Sandbox Resamania : configuration manquante (variables RESAMANIA_*).';
    echo $msg . "\n";
    if (function_exists('sendTelegram')) {
        sendTelegram(htmlspecialchars($msg, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    return;
}

$out = aquavelo_resa_import_agenda_day($now);
$dateFr = $now->format('d/m/Y');

$lines = [
    '<b>Sandbox Resamania</b> — RDV du ' . htmlspecialchars($dateFr, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    'Créés : ' . $out['created'] . ' · déjà présents : ' . $out['existing'] . ' · ignorés : ' . $out['skipped'] . ' · échecs : ' . $out['failed'],
];
foreach ($out['lines'] as $line) {
    $lines[] = '• ' . htmlspecialchars($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
if ($out['lines'] === []) {
    $lines[] = 'Aucun RDV Agenda aujourd’hui.';
}
$html = implode("\n", $lines);

echo "=== AGENDA → SANDBOX RESAMANIA " . $out['date'] . " ===\n";
echo implode("\n", $out['lines']) . "\n";
echo 'Créés=' . $out['created'] . ' existants=' . $out['existing'] . ' ignorés=' . $out['skipped'] . ' échecs=' . $out['failed'] . "\n";

if (function_exists('sendTelegram')) {
    $tg = sendTelegram($html);
    echo $tg ? "Telegram: envoyé\n" : "Telegram: échec\n";
}
