<?php
/**
 * API Resamania SANDBOX uniquement (resa2-staging / demoapi).
 * Ne jamais pointer vers la production (resa2-mfr).
 */
declare(strict_types=1);

function aquavelo_resa_sandbox_configured(): bool
{
    $base = aquavelo_env('RESAMANIA_BASE_URL');
    $token = aquavelo_env('RESAMANIA_CLIENT_TOKEN');
    if ($base === '' || $token === '' || aquavelo_env('RESAMANIA_CLIENT_ID') === '') {
        return false;
    }
    if (stripos($base, 'resa2-mfr') !== false || strcasecmp($token, 'demoapi') !== 0) {
        return false;
    }
    if (stripos($base, 'resa2-staging') === false && aquavelo_env('RESAMANIA_ENV') !== 'sandbox') {
        return false;
    }

    return true;
}

function aquavelo_resa_sandbox_club_id(): string
{
    $club = aquavelo_env('RESAMANIA_CLUB_CANNES_ID');
    if ($club === '') {
        $club = aquavelo_env('RESAMANIA_CLUB_ID');
    }

    return $club;
}

function aquavelo_resa_sandbox_network_id(): string
{
    $node = aquavelo_env('RESAMANIA_NETWORK_CANNES_ID');
    if ($node === '') {
        $node = aquavelo_env('RESAMANIA_NETWORK_NODE_ID');
    }

    return $node;
}

/**
 * @return array{code:int, json:?array}
 */
function aquavelo_resa_sandbox_curl(string $method, string $url, array $headers, ?array $body = null): array
{
    $ch = curl_init($url);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_FOLLOWLOCATION => true,
    ];
    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE);
    }
    curl_setopt_array($ch, $opts);
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = null;
    if (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true);
        $json = is_array($decoded) ? $decoded : ['raw' => substr($raw, 0, 400)];
    }

    return ['code' => $code, 'json' => $json];
}

function aquavelo_resa_sandbox_token(): ?string
{
    static $cached = null;
    if (is_string($cached) && $cached !== '') {
        return $cached;
    }
    $base = rtrim(aquavelo_env('RESAMANIA_BASE_URL'), '/');
    $ct = aquavelo_env('RESAMANIA_CLIENT_TOKEN');
    $ch = curl_init($base . '/' . $ct . '/oauth/v2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'x-gravitee-api-key: ' . aquavelo_env('RESAMANIA_API_KEY'),
        ],
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => aquavelo_env('RESAMANIA_GRANT_TYPE'),
            'client_id' => aquavelo_env('RESAMANIA_CLIENT_ID'),
            'client_secret' => aquavelo_env('RESAMANIA_CLIENT_SECRET'),
            'clubId' => aquavelo_resa_sandbox_club_id(),
        ]),
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 25,
    ]);
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    if ($code !== 200 || !is_array($decoded) || empty($decoded['access_token'])) {
        error_log('resamania sandbox token HTTP ' . $code);

        return null;
    }
    $cached = (string) $decoded['access_token'];

    return $cached;
}

/**
 * @return array{code:int, json:?array}
 */
function aquavelo_resa_sandbox_api(string $method, string $path, ?array $body = null): array
{
    $tok = aquavelo_resa_sandbox_token();
    if ($tok === null) {
        return ['code' => 0, 'json' => ['error' => 'no_token']];
    }
    $base = rtrim(aquavelo_env('RESAMANIA_BASE_URL'), '/');
    $ct = aquavelo_env('RESAMANIA_CLIENT_TOKEN');
    $headers = [
        'Authorization: Bearer ' . $tok,
        'x-gravitee-api-key: ' . aquavelo_env('RESAMANIA_API_KEY'),
        'x-user-club-id: ' . aquavelo_resa_sandbox_club_id(),
        'x-user-network-node-id: ' . aquavelo_resa_sandbox_network_id(),
        'Accept: application/ld+json',
    ];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/ld+json';
    }

    return aquavelo_resa_sandbox_curl($method, $base . '/' . $ct . $path, $headers, $body);
}

function aquavelo_resa_phone_e164(string $phone): string
{
    $digits = aquavelo_trial_normalize_phone($phone);
    if ($digits === '') {
        return '';
    }
    if (str_starts_with($digits, '0') && strlen($digits) === 10) {
        return '+33' . substr($digits, 1);
    }

    return $digits[0] === '+' ? $digits : '+' . $digits;
}

/**
 * @return array{givenName:string, familyName:string}
 */
function aquavelo_resa_split_name(string $name): array
{
    $name = trim(preg_replace('/\s+/u', ' ', $name) ?? $name);
    $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if ($parts === []) {
        return ['givenName' => 'Prospect', 'familyName' => 'AGENDA'];
    }
    $given = $parts[0];
    $family = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : $parts[0];

    return [
        'givenName' => mb_convert_case($given, MB_CASE_TITLE, 'UTF-8'),
        'familyName' => mb_strtoupper($family, 'UTF-8'),
    ];
}

function aquavelo_resa_sandbox_find_by_email(string $email): ?array
{
    if ($email === '') {
        return null;
    }
    $q = rawurlencode($email);
    foreach (['/contacts?email=' . $q, '/network/contact_search?email=' . $q] as $path) {
        $res = aquavelo_resa_sandbox_api('GET', $path);
        $members = [];
        if (is_array($res['json'])) {
            $members = $res['json']['hydra:member'] ?? $res['json']['member'] ?? [];
        }
        if (is_array($members) && isset($members[0]) && is_array($members[0])) {
            return $members[0];
        }
    }

    return null;
}

/**
 * @param array{givenName:string,familyName:string,email:string,mobile:string} $person
 * @return array{ok:bool, created:bool, number:?string, id:?string, error:?string}
 */
function aquavelo_resa_sandbox_upsert_prospect(array $person): array
{
    $existing = aquavelo_resa_sandbox_find_by_email($person['email']);
    if ($existing !== null) {
        return [
            'ok' => true,
            'created' => false,
            'number' => isset($existing['number']) ? (string) $existing['number'] : null,
            'id' => isset($existing['@id']) ? (string) $existing['@id'] : null,
            'error' => null,
        ];
    }
    $body = [
        'familyName' => $person['familyName'],
        'givenName' => $person['givenName'],
        'email' => $person['email'],
        'clubId' => aquavelo_resa_sandbox_club_id(),
        'state' => 'prospect',
        'gender' => 'female',
        'birthDate' => '1990-01-01',
        'address' => [
            'addressCountry' => 'France',
            'addressLocality' => 'Cannes la Bocca',
            'postalCode' => '06150',
            'streetAddress' => 'Cannes la Bocca',
        ],
    ];
    if ($person['mobile'] !== '') {
        $body['mobile'] = $person['mobile'];
    }
    $res = aquavelo_resa_sandbox_api('POST', '/contacts', $body);
    if (!in_array($res['code'], [200, 201], true) || !is_array($res['json'])) {
        $err = is_array($res['json'])
            ? (string) ($res['json']['hydra:description'] ?? $res['json']['detail'] ?? 'HTTP ' . $res['code'])
            : 'HTTP ' . $res['code'];

        return ['ok' => false, 'created' => false, 'number' => null, 'id' => null, 'error' => $err];
    }

    return [
        'ok' => true,
        'created' => true,
        'number' => isset($res['json']['number']) ? (string) $res['json']['number'] : null,
        'id' => isset($res['json']['@id']) ? (string) $res['json']['@id'] : null,
        'error' => null,
    ];
}

/**
 * @return array{date:string, created:int, existing:int, skipped:int, failed:int, lines:list<string>}
 */
function aquavelo_resa_import_agenda_day(?DateTimeInterface $day = null): array
{
    $tz = new DateTimeZone('Europe/Paris');
    $dayStart = $day ? DateTime::createFromInterface($day) : new DateTime('now', $tz);
    $dayStart->setTimezone($tz);
    $dayStart->setTime(0, 0, 0);
    $out = [
        'date' => $dayStart->format('Y-m-d'),
        'created' => 0,
        'existing' => 0,
        'skipped' => 0,
        'failed' => 0,
        'lines' => [],
    ];
    $events = aquavelo_trial_list_calendar_events($dayStart);
    $seenEmails = [];
    foreach ($events as $evt) {
        $time = $evt['start']->format('H:i');
        $email = aquavelo_trial_normalize_email((string) ($evt['email'] ?? ''));
        $name = trim((string) ($evt['name'] ?? ''));
        if ($email === '') {
            $out['skipped']++;
            $out['lines'][] = $time . ' — ' . ($name !== '' ? $name : 'sans nom') . ' : ignoré (pas d’email)';
            continue;
        }
        if (isset($seenEmails[$email])) {
            $out['skipped']++;
            $out['lines'][] = $time . ' — ' . $name . ' : déjà traité dans le lot';
            continue;
        }
        $seenEmails[$email] = true;
        $split = aquavelo_resa_split_name($name !== '' ? $name : explode('@', $email)[0]);
        $person = [
            'givenName' => $split['givenName'],
            'familyName' => $split['familyName'],
            'email' => $email,
            'mobile' => aquavelo_resa_phone_e164((string) ($evt['phone'] ?? '')),
        ];
        $res = aquavelo_resa_sandbox_upsert_prospect($person);
        $label = $time . ' — ' . $person['givenName'] . ' ' . $person['familyName'];
        if (!$res['ok']) {
            $out['failed']++;
            $out['lines'][] = $label . ' : échec';
            error_log('resamania sandbox upsert: ' . ($res['error'] ?? ''));
            continue;
        }
        $num = $res['number'] ?? '';
        if ($res['created']) {
            $out['created']++;
            $out['lines'][] = $label . ' : créé ' . $num;
        } else {
            $out['existing']++;
            $out['lines'][] = $label . ' : déjà présent ' . $num;
        }
    }

    return $out;
}
