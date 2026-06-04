<?php
/**
 * RDV am_free ↔ Google Calendar (Cannes / Mandelieu / Vallauris → aqua.cannes@gmail.com)
 */
declare(strict_types=1);

use Google\Service\Calendar;
use Google\Service\Calendar\Event;

function aquavelo_gc_normalize_rdv_text(string $text): string
{
    $s = str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', $text);

    return trim(preg_replace('/\s+/u', ' ', $s) ?? '');
}

/**
 * Extrait date/heure du libellé RDV (ex. "Lundi 31/03/2026 à 18:30 (AQUAVELO)").
 *
 * @return array{start: DateTime, date: string, time: string}|null
 */
function aquavelo_gc_parse_rdv_from_name(string $name): ?array
{
    $norm = aquavelo_gc_normalize_rdv_text($name);
    if (!preg_match('/(\d{2}\/\d{2}\/\d{4})\s*à\s*(\d{1,2})[h:](\d{2})/iu', $norm, $m)) {
        return null;
    }
    $h = str_pad((string) (int) $m[2], 2, '0', STR_PAD_LEFT);
    $min = $m[3];
    $time = $h . ':' . $min;
    $start = DateTime::createFromFormat('d/m/Y H:i', $m[1] . ' ' . $time, new DateTimeZone('Europe/Paris'));
    if (!$start) {
        return null;
    }

    return ['start' => $start, 'date' => $m[1], 'time' => $time];
}

/**
 * Date/heure du RDV : libellé name, sinon colonne SQL date (après déplacement admin).
 *
 * @param array{name?:string,date?:string|null} $booking
 * @return array{start: DateTime, date: string, time: string}|null
 */
function aquavelo_gc_resolve_rdv_datetime(array $booking): ?array
{
    $parsed = aquavelo_gc_parse_rdv_from_name((string) ($booking['name'] ?? ''));
    if ($parsed !== null) {
        return $parsed;
    }
    $dateCol = $booking['date'] ?? null;
    if ($dateCol === null || trim((string) $dateCol) === '') {
        return null;
    }
    try {
        $start = new DateTime((string) $dateCol, new DateTimeZone('Europe/Paris'));

        return [
            'start' => $start,
            'date'  => $start->format('d/m/Y'),
            'time'  => $start->format('H:i'),
        ];
    } catch (Throwable $e) {
        return null;
    }
}

/** RDV client (pas créneau bloqué admin). */
function aquavelo_gc_is_syncable_booking(array $booking): bool
{
    $name = (string) ($booking['name'] ?? '');
    if ($name === '' || stripos($name, '(RDV:') === false) {
        return false;
    }
    if (stripos($name, 'BLOQUE (ADMIN)') !== false || stripos($name, 'VERROUILLÉ') !== false) {
        return false;
    }
    $seg = (string) ($booking['segment_id'] ?? '');
    if ($seg === 'admin-lock') {
        return false;
    }

    return true;
}

function aquavelo_gc_bootstrap(): ?Calendar
{
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        return null;
    }
    require_once __DIR__ . '/vendor/autoload.php';
    if (!function_exists('generateGoogleKeyFile')) {
        require_once __DIR__ . '/load_env.php';
    }
    $keyFile = __DIR__ . '/google_key.json';
    if (!file_exists($keyFile)) {
        generateGoogleKeyFile();
    }
    if (!file_exists($keyFile)) {
        return null;
    }
    $client = new Google\Client();
    $client->setAuthConfig($keyFile);
    $client->addScope(Calendar::CALENDAR);

    return new Calendar($client);
}

function aquavelo_gc_calendar_id_for_center(PDO $database, int $centerId): string
{
    if (in_array($centerId, [305, 347, 349], true)) {
        return 'aqua.cannes@gmail.com';
    }
    $stmt = $database->prepare('SELECT email FROM am_centers WHERE id = ?');
    $stmt->execute([$centerId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return !empty($row['email']) ? $row['email'] : 'aqua.cannes@gmail.com';
}

/**
 * Supprime l'événement Google (par google_event_id, sinon recherche créneau + nom pour le groupe Cannes).
 *
 * @param array{name?:string,google_event_id?:string|null,center_id?:int|string|null} $booking
 */
function aquavelo_gc_delete_booking_event(Calendar $service, PDO $database, array $booking): bool
{
    $centerId = (int) ($booking['center_id'] ?? 0);
    $calendarId = aquavelo_gc_calendar_id_for_center($database, $centerId);
    $isCannesGroup = in_array($centerId, [305, 347, 349], true);

    $eventId = isset($booking['google_event_id']) ? trim((string) $booking['google_event_id']) : '';
    if ($eventId !== '') {
        try {
            $service->events->delete($calendarId, $eventId);

            return true;
        } catch (Throwable $e) {
            error_log('aquavelo_gc_delete_booking_event: delete by id failed: ' . $e->getMessage());
        }
    }

    if (!$isCannesGroup) {
        return false;
    }

    $name = (string) ($booking['name'] ?? '');
    if ($name === '' || stripos($name, '(RDV:') === false) {
        return false;
    }

    $parsed = aquavelo_gc_parse_rdv_from_name($name);
    if ($parsed === null) {
        return false;
    }

    $searchStart = clone $parsed['start'];
    $searchEnd = clone $parsed['start'];
    $searchEnd->modify('+1 hour');

    try {
        $gcEvents = $service->events->listEvents($calendarId, [
            'timeMin'      => $searchStart->format(DateTime::RFC3339),
            'timeMax'      => $searchEnd->format(DateTime::RFC3339),
            'singleEvents' => true,
        ]);
        $clientName = trim(explode('(RDV:', $name)[0]);
        foreach ($gcEvents->getItems() as $evt) {
            if ($clientName !== '' && stripos((string) $evt->getSummary(), $clientName) !== false) {
                $service->events->delete($calendarId, $evt->getId());
                error_log('aquavelo_gc_delete_booking_event: fallback deleted ' . $evt->getId() . ' for ' . $clientName);

                return true;
            }
        }
    } catch (Throwable $e) {
        error_log('aquavelo_gc_delete_booking_event: listEvents failed: ' . $e->getMessage());
    }

    return false;
}

/**
 * Crée ou relie un RDV sur Google Calendar.
 *
 * @return array{ok:bool, line:string}
 */
function aquavelo_gc_push_booking_to_calendar(Calendar $service, PDO $database, array $booking): array
{
    $id = (int) ($booking['id'] ?? 0);
    $clientName = trim(explode('(RDV:', (string) ($booking['name'] ?? ''))[0]);
    $parsed = aquavelo_gc_resolve_rdv_datetime($booking);

    if ($parsed === null) {
        return ['ok' => false, 'line' => "⚠️ ID:$id | $clientName | Date/heure introuvable (nom + colonne date)"];
    }

    $rdvStart = $parsed['start'];
    $matchesDate = $parsed['date'];
    $matchesTime = $parsed['time'];

    try {
        $rdvEnd = clone $rdvStart;
        $rdvEnd->modify('+45 minutes');

        $stmt_c = $database->prepare('SELECT address, city FROM am_centers WHERE id = ?');
        $stmt_c->execute([(int) ($booking['center_id'] ?? 0)]);
        $cInfo = $stmt_c->fetch(PDO::FETCH_ASSOC) ?: [];

        $location = $cInfo['address'] ?? '60 Avenue du Dr Raymond Picaud, 06150 Cannes';
        $city = $cInfo['city'] ?? 'Cannes';
        $calendarId = aquavelo_gc_calendar_id_for_center($database, (int) ($booking['center_id'] ?? 305));

        $windowStart = (clone $rdvStart)->modify('-30 minutes');
        $windowEnd = (clone $rdvStart)->modify('+90 minutes');

        $existing = $service->events->listEvents($calendarId, [
            'timeMin'      => $windowStart->format(DateTime::RFC3339),
            'timeMax'      => $windowEnd->format(DateTime::RFC3339),
            'singleEvents' => true,
        ]);
        foreach ($existing->getItems() as $evt) {
            if ($clientName !== '' && stripos((string) $evt->getSummary(), $clientName) !== false) {
                $database->prepare('UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?')
                    ->execute([$evt->getId(), $id]);

                return [
                    'ok'   => true,
                    'line' => "🔗 ID:$id | $clientName | relié à l'événement existant {$evt->getId()}",
                ];
            }
        }

        $event = new Event([
            'summary'     => '🏊 ' . $clientName . ' - ' . $city,
            'location'    => $location,
            'description' => "Client: $clientName\nEmail: {$booking['email']}\nTél: {$booking['phone']}\nID: $id",
            'start'       => ['dateTime' => $rdvStart->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
            'end'         => ['dateTime' => $rdvEnd->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
        ]);

        $created = $service->events->insert($calendarId, $event);
        $googleEventId = $created->getId();

        $database->prepare('UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?')
            ->execute([$googleEventId, $id]);

        return [
            'ok'   => true,
            'line' => "✅ ID:$id | $clientName | {$matchesDate} à {$matchesTime} → $googleEventId",
        ];
    } catch (Throwable $e) {
        return ['ok' => false, 'line' => "❌ ID:$id | $clientName | " . $e->getMessage()];
    }
}

/**
 * Synchronise un RDV précis (ex. après déplacement admin).
 *
 * @return array{ok:bool, line:string}
 */
function aquavelo_gc_sync_booking_by_id(PDO $database, int $bookingId, ?array $bookingBeforeUpdate = null): array
{
    if ($bookingId <= 0) {
        return ['ok' => false, 'line' => '❌ ID réservation invalide'];
    }

    $service = aquavelo_gc_bootstrap();
    if (!$service) {
        return ['ok' => false, 'line' => '❌ API Google indisponible'];
    }

    if ($bookingBeforeUpdate !== null && aquavelo_gc_is_syncable_booking($bookingBeforeUpdate)) {
        aquavelo_gc_delete_booking_event($service, $database, $bookingBeforeUpdate);
    }

    $stmt = $database->prepare('SELECT * FROM am_free WHERE id = ?');
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$booking) {
        return ['ok' => false, 'line' => "❌ ID:$bookingId introuvable"];
    }
    if (!aquavelo_gc_is_syncable_booking($booking)) {
        return ['ok' => false, 'line' => "⚠️ ID:$bookingId | non synchronisable (blocage ou format)"];
    }
    if (!in_array((int) ($booking['center_id'] ?? 0), [305, 347, 349], true)) {
        return ['ok' => false, 'line' => "⚠️ ID:$bookingId | hors centres Cannes/Mandelieu/Vallauris"];
    }

    return aquavelo_gc_push_booking_to_calendar($service, $database, $booking);
}

/**
 * @return array{synced:int, errors:int, lines:string[]}
 */
function aquavelo_gc_sync_pending_rdvs(PDO $database): array
{
    $result = ['synced' => 0, 'errors' => 0, 'lines' => []];
    $service = aquavelo_gc_bootstrap();
    if (!$service) {
        $vendorOk = file_exists(__DIR__ . '/vendor/autoload.php');
        $keyPath = __DIR__ . '/google_key.json';
        $b64Len = 0;
        if (function_exists('aquavelo_env')) {
            $b64Len = strlen(preg_replace('/\s+/', '', aquavelo_env('GOOGLE_CALENDAR_KEY_JSON_BASE64')));
        }
        $result['lines'][] = '❌ API Google indisponible — vendor: ' . ($vendorOk ? 'OK' : 'manquant')
            . ' | google_key.json: ' . (file_exists($keyPath) ? 'OK' : 'absent')
            . ' | env BASE64 (longueur): ' . ($b64Len > 0 ? (string) $b64Len : '0');
        $result['errors']++;

        return $result;
    }

    // RDV jamais poussés OU marqués synchro sans google_event_id (ex. déplacement admin, échec résa)
    $stmt = $database->prepare(
        "SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND center_id IN (305, 347, 349)
         AND COALESCE(segment_id, '') <> 'admin-lock'
         AND name NOT LIKE '%BLOQUE (ADMIN)%'
         AND (
            google_sync = 0
            OR google_sync IS NULL
            OR google_event_id IS NULL
            OR TRIM(COALESCE(google_event_id, '')) = ''
         )
         ORDER BY id DESC"
    );
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bookings as $booking) {
        if (!aquavelo_gc_is_syncable_booking($booking)) {
            continue;
        }
        $push = aquavelo_gc_push_booking_to_calendar($service, $database, $booking);
        $result['lines'][] = $push['line'];
        if ($push['ok']) {
            $result['synced']++;
        } else {
            $result['errors']++;
        }
    }

    return $result;
}
