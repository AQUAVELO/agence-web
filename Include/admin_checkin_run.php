<?php
/**
 * Auth + actions check-in séances d'essai (avant HTML).
 */
require_once __DIR__ . '/../_settings.php';
require_once __DIR__ . '/trial_attendance.php';

if (!function_exists('aquavelo_admin_checkin_url')) {
    function aquavelo_admin_checkin_url(array $query = []): string
    {
        $base = aquavelo_canonical_base_url() . 'index.php?p=admin_checkin';
        if ($query === []) {
            return $base;
        }

        return $base . '&' . http_build_query($query);
    }
}

if (!function_exists('aquavelo_admin_checkin_redirect')) {
    function aquavelo_admin_checkin_redirect(string $flash = '', string $type = 'ok', array $query = []): void
    {
        if ($flash !== '') {
            $_SESSION['admin_checkin_flash'] = $flash;
            $_SESSION['admin_checkin_flash_type'] = $type;
        }
        $url = aquavelo_admin_checkin_url($query);
        while (ob_get_level() > 1) {
            ob_end_clean();
        }
        if (ob_get_level()) {
            ob_clean();
        }
        if (!headers_sent()) {
            header('Location: ' . $url, true, 303);
        }
        $safe = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $urlJs = json_encode($url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8">'
            . '<meta http-equiv="refresh" content="0;url=' . $safe . '">'
            . '<title>Redirection…</title></head><body style="font-family:sans-serif;padding:2rem">'
            . '<p>Redirection…</p><p><a href="' . $safe . '">Continuer</a></p>'
            . '<script>location.replace(' . $urlJs . ');</script></body></html>';
        exit;
    }
}

if (isset($_GET['logout'])) {
    $_SESSION['admin_auth'] = false;
    unset($_SESSION['admin_auth'], $_SESSION['csrf_token']);
    aquavelo_admin_checkin_redirect();
}

$password_secret = 'aquavelo2026';
$authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;
$login_error = null;

$dayParam = isset($_GET['day']) ? trim((string) $_GET['day']) : date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayParam)) {
    $dayParam = date('Y-m-d');
}
$checkin_day = DateTime::createFromFormat('Y-m-d', $dayParam, new DateTimeZone('Europe/Paris')) ?: new DateTime('now', new DateTimeZone('Europe/Paris'));
$checkin_day->setTime(0, 0, 0);

if (isset($_POST['login_pass'])) {
    if ($_POST['login_pass'] === $password_secret) {
        $_SESSION['admin_auth'] = true;
        $authenticated = true;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        aquavelo_admin_checkin_redirect('Connexion réussie.', 'ok', ['day' => $dayParam]);
    }
    sleep(1);
    $login_error = 'Mot de passe incorrect';
}

$checkin_ingest = null;
$checkin_board = [];

if ($authenticated) {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    aquavelo_trial_ensure_schema($database);

    $token_ok = isset($_POST['token']) && hash_equals((string) $_SESSION['csrf_token'], (string) $_POST['token']);

    if ($token_ok && isset($_POST['action_ingest']) && $_POST['action_ingest'] === '1') {
        $dayPost = isset($_POST['day']) ? trim((string) $_POST['day']) : $dayParam;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayPost)) {
            $dayPost = $dayParam;
        }
        $d = DateTime::createFromFormat('Y-m-d', $dayPost, new DateTimeZone('Europe/Paris')) ?: $checkin_day;
        $out = aquavelo_trial_ingest_day($database, $d);
        $msg = 'Agenda → am_free : ' . $out['imported'] . ' créé(s), ' . $out['linked'] . ' relié(s), ' . $out['skipped'] . ' ignoré(s)';
        aquavelo_admin_checkin_redirect($msg, 'ok', ['day' => $dayPost]);
    }

    if ($token_ok && isset($_POST['action_attendance'], $_POST['booking_id'])) {
        $bookingId = (int) $_POST['booking_id'];
        $status = (string) $_POST['action_attendance'];
        $dayPost = isset($_POST['day']) ? trim((string) $_POST['day']) : $dayParam;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayPost)) {
            $dayPost = $dayParam;
        }
        $res = aquavelo_trial_set_attendance($database, $bookingId, $status);
        aquavelo_admin_checkin_redirect(
            $res['message'],
            $res['ok'] ? (($res['previous'] ?? []) !== [] ? 'warn' : 'ok') : 'error',
            ['day' => $dayPost]
        );
    }

    $checkin_board = aquavelo_trial_day_board($database, $checkin_day);
}
