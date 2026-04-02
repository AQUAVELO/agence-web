<?php
/**
 * RDV am_free ↔ Google Calendar (Cannes / Mandelieu / Vallauris → aqua.cannes@gmail.com)
 */
declare(strict_types=1);

use Google\Service\Calendar;
use Google\Service\Calendar\Event;

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

    preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', $name, $m);
    if (count($m) !== 3) {
        return false;
    }

    $searchStart = DateTime::createFromFormat('d/m/Y H:i', $m[1] . ' ' . $m[2], new DateTimeZone('Europe/Paris'));
    if (!$searchStart) {
        return false;
    }
    $searchEnd = clone $searchStart;
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
 * @return array{synced:int, errors:int, lines:string[]}
 */
function aquavelo_gc_sync_pending_rdvs(PDO $database): array
{
    $result = ['synced' => 0, 'errors' => 0, 'lines' => []];
    $service = aquavelo_gc_bootstrap();
    if (!$service) {
        $result['lines'][] = '❌ API Google indisponible (vendor ou google_key.json)';
        $result['errors']++;

        return $result;
    }

    $stmt = $database->prepare("SELECT * FROM am_free WHERE name LIKE '%(RDV:%' AND google_sync = 0 AND center_id IN (305, 347, 349) ORDER BY id DESC");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bookings as $booking) {
        preg_match('/(\d{2}\/\d{2}\/\d{4}) à (\d{2}:\d{2})/', (string) $booking['name'], $matches);
        $clientName = trim(explode('(RDV:', (string) $booking['name'])[0]);

        if (count($matches) !== 3) {
            $result['lines'][] = "⚠️ ID:{$booking['id']} | $clientName | Format date non parsé";
            continue;
        }

        $rdvStart = DateTime::createFromFormat('d/m/Y H:i', $matches[1] . ' ' . $matches[2], new DateTimeZone('Europe/Paris'));
        if (!$rdvStart) {
            $result['lines'][] = "⚠️ ID:{$booking['id']} | $clientName | Date non reconnue";
            continue;
        }

        try {
            $rdvEnd = clone $rdvStart;
            $rdvEnd->modify('+45 minutes');

            $stmt_c = $database->prepare('SELECT address, city FROM am_centers WHERE id = ?');
            $stmt_c->execute([(int) $booking['center_id']]);
            $cInfo = $stmt_c->fetch(PDO::FETCH_ASSOC) ?: [];

            $location = $cInfo['address'] ?? '60 Avenue du Dr Raymond Picaud, 06150 Cannes';
            $city = $cInfo['city'] ?? 'Cannes';
            $calendarId = 'aqua.cannes@gmail.com';

            $existing = $service->events->listEvents($calendarId, [
                'timeMin'      => $rdvStart->format(DateTime::RFC3339),
                'timeMax'      => $rdvEnd->format(DateTime::RFC3339),
                'singleEvents' => true,
            ]);
            $linked = false;
            foreach ($existing->getItems() as $evt) {
                if ($clientName !== '' && stripos((string) $evt->getSummary(), $clientName) !== false) {
                    $database->prepare('UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?')
                        ->execute([$evt->getId(), $booking['id']]);
                    $result['lines'][] = "🔗 ID:{$booking['id']} | $clientName | relié à l'événement existant {$evt->getId()}";
                    $result['synced']++;
                    $linked = true;
                    break;
                }
            }
            if ($linked) {
                continue;
            }

            $event = new Event([
                'summary'     => '🏊 ' . $clientName . ' - ' . $city,
                'location'    => $location,
                'description' => "Client: $clientName\nEmail: {$booking['email']}\nTél: {$booking['phone']}\nID: {$booking['id']}",
                'start'       => ['dateTime' => $rdvStart->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
                'end'         => ['dateTime' => $rdvEnd->format(DateTime::RFC3339), 'timeZone' => 'Europe/Paris'],
            ]);

            $created = $service->events->insert($calendarId, $event);
            $googleEventId = $created->getId();

            $database->prepare('UPDATE am_free SET google_sync = 1, google_event_id = ? WHERE id = ?')
                ->execute([$googleEventId, $booking['id']]);

            $result['lines'][] = "✅ ID:{$booking['id']} | $clientName | {$matches[1]} à {$matches[2]} → $googleEventId";
            $result['synced']++;
        } catch (Throwable $e) {
            $result['lines'][] = "❌ ID:{$booking['id']} | $clientName | " . $e->getMessage();
            $result['errors']++;
        }
    }

    return $result;
}
