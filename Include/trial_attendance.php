<?php
/**
 * Présence séances d'essai + ingestion Google Agenda (site & Calendly) → am_free.
 */
declare(strict_types=1);

require_once __DIR__ . '/../google_calendar_rdv_helpers.php';

const AQUAVELO_TRIAL_CALENDAR_ID = 'aqua.cannes@gmail.com';
const AQUAVELO_TRIAL_CENTER_ID = 305;

/**
 * Ajoute les colonnes présence si absentes.
 */
function aquavelo_trial_ensure_schema(PDO $database): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $existing = [];
    try {
        $st = $database->query('SHOW COLUMNS FROM am_free');
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $existing[$row['Field']] = true;
        }
    } catch (Throwable $e) {
        return;
    }

    $alters = [
        'attendance_status' => "ALTER TABLE am_free ADD COLUMN attendance_status VARCHAR(20) NOT NULL DEFAULT 'pending'",
        'attended_at'       => 'ALTER TABLE am_free ADD COLUMN attended_at DATETIME DEFAULT NULL',
        'trial_source'      => "ALTER TABLE am_free ADD COLUMN trial_source VARCHAR(20) DEFAULT NULL",
    ];
    foreach ($alters as $col => $sql) {
        if (empty($existing[$col])) {
            try {
                $database->exec($sql);
            } catch (Throwable $e) {
                error_log('aquavelo_trial_ensure_schema: ' . $e->getMessage());
            }
        }
    }
}

function aquavelo_trial_normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function aquavelo_trial_normalize_phone(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if (str_starts_with($digits, '33') && strlen($digits) >= 11) {
        $digits = '0' . substr($digits, 2);
    }
    if (str_starts_with($digits, '0033') && strlen($digits) >= 13) {
        $digits = '0' . substr($digits, 4);
    }

    return $digits;
}

/**
 * @return array{email:string,phone:string,name:string,source:string}
 */
function aquavelo_trial_parse_google_event($event): array
{
    $summary = trim((string) $event->getSummary());
    $description = (string) ($event->getDescription() ?? '');
    $combined = $summary . "\n" . $description;

    // Emails dans la description + participants Google
    $emailCandidates = [];
    if (preg_match_all('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $combined, $all)) {
        foreach ($all[0] as $candidate) {
            $emailCandidates[] = aquavelo_trial_normalize_email($candidate);
        }
    }
    try {
        $attendees = $event->getAttendees();
        if (is_array($attendees)) {
            foreach ($attendees as $att) {
                $aEmail = aquavelo_trial_normalize_email((string) ($att->getEmail() ?? ''));
                if ($aEmail !== '') {
                    $emailCandidates[] = $aEmail;
                }
            }
        }
    } catch (Throwable $e) {
        /* ignore */
    }

    $email = '';
    foreach ($emailCandidates as $c) {
        if ($c === '' || preg_match('/@(calendly\.com|aquavelo\.com|alesiaminceur\.com|group\.calendar\.google\.com)$/i', $c)) {
            continue;
        }
        $email = $c;
        break;
    }

    $phone = '';
    if (preg_match('/(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}/', $combined, $pm)) {
        $phone = aquavelo_trial_normalize_phone($pm[0]);
    }

    $isCalendly = (bool) preg_match('/calendly\.com/i', $description)
        || (bool) preg_match('/powered by calendly/i', $description)
        || (stripos($summary, 'calendly') !== false);

    $isSite = (bool) preg_match('/^🏊/u', $summary)
        || (bool) preg_match('/\bID:\s*\d+/i', $description);

    $source = 'manual';
    if ($isCalendly) {
        $source = 'calendly';
    } elseif ($isSite) {
        $source = 'site';
    }

    $name = $summary;
    $name = preg_replace('/^🏊\s*/u', '', $name) ?? $name;
    $name = preg_replace('/\s*-\s*(Cannes|Mandelieu|Vallauris)\s*$/iu', '', $name) ?? $name;
    // Calendly: parfois "Type - Nom" ou "Nom - Type"
    if ($source === 'calendly') {
        if (preg_match('/invitee[:\s]+([^\n<]+)/i', $description, $im)) {
            $name = trim(strip_tags($im[1]));
        } elseif (preg_match('/^(.+?)\s+and\s+/i', $name, $am)) {
            $name = trim($am[1]);
        }
    }
    $name = trim(html_entity_decode(strip_tags($name), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($name === '') {
        $name = $email !== '' ? explode('@', $email)[0] : 'Prospect Agenda';
    }

    return [
        'email'  => $email,
        'phone'  => $phone,
        'name'   => $name,
        'source' => $source,
    ];
}

/**
 * @return list<array{
 *   google_event_id:string,
 *   start:DateTime,
 *   end:?DateTime,
 *   summary:string,
 *   email:string,
 *   phone:string,
 *   name:string,
 *   source:string,
 *   description:string
 * }>
 */
function aquavelo_trial_list_calendar_events(?DateTimeInterface $day = null): array
{
    $service = aquavelo_gc_bootstrap();
    if (!$service) {
        return [];
    }

    $tz = new DateTimeZone('Europe/Paris');
    $dayStart = $day ? DateTime::createFromInterface($day) : new DateTime('now', $tz);
    $dayStart->setTimezone($tz);
    $dayStart->setTime(0, 0, 0);
    $dayEnd = clone $dayStart;
    $dayEnd->setTime(23, 59, 59);

    try {
        $events = $service->events->listEvents(AQUAVELO_TRIAL_CALENDAR_ID, [
            'timeMin'      => $dayStart->format(DateTime::RFC3339),
            'timeMax'      => $dayEnd->format(DateTime::RFC3339),
            'singleEvents' => true,
            'orderBy'      => 'startTime',
        ]);
    } catch (Throwable $e) {
        error_log('aquavelo_trial_list_calendar_events: ' . $e->getMessage());

        return [];
    }

    $out = [];
    foreach ($events->getItems() as $event) {
        $startRaw = $event->getStart();
        if (!$startRaw || !$startRaw->getDateTime()) {
            // Événements journée entière : ignorer
            continue;
        }
        $start = new DateTime($startRaw->getDateTime());
        $start->setTimezone($tz);
        $end = null;
        if ($event->getEnd() && $event->getEnd()->getDateTime()) {
            $end = new DateTime($event->getEnd()->getDateTime());
            $end->setTimezone($tz);
        }
        $parsed = aquavelo_trial_parse_google_event($event);
        // Ignorer les créneaux admin / titres techniques sans prospect
        $summary = (string) $event->getSummary();
        if (preg_match('/bloque|verrouill|fermé|fermeture|congé/i', $summary)) {
            continue;
        }

        $out[] = [
            'google_event_id' => (string) $event->getId(),
            'start'           => $start,
            'end'             => $end,
            'summary'         => $summary,
            'email'           => $parsed['email'],
            'phone'           => $parsed['phone'],
            'name'            => $parsed['name'],
            'source'          => $parsed['source'],
            'description'     => (string) ($event->getDescription() ?? ''),
        ];
    }

    return $out;
}

/**
 * Cherche une fiche am_free existante pour un événement Agenda.
 *
 * @param array{google_event_id:string,email:string,phone:string,start:DateTime,name:string} $evt
 */
function aquavelo_trial_find_booking(PDO $database, array $evt): ?array
{
    $eventId = trim($evt['google_event_id']);
    if ($eventId !== '') {
        $st = $database->prepare('SELECT * FROM am_free WHERE google_event_id = ? LIMIT 1');
        $st->execute([$eventId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row;
        }
    }

    $email = aquavelo_trial_normalize_email((string) ($evt['email'] ?? ''));
    $dateLabel = $evt['start']->format('d/m/Y');
    $timeLabel = $evt['start']->format('H:i');

    if ($email !== '') {
        $st = $database->prepare(
            "SELECT * FROM am_free
             WHERE LOWER(TRIM(email)) = ?
               AND name LIKE '%(RDV:%'
               AND name LIKE ?
             ORDER BY id DESC LIMIT 5"
        );
        $st->execute([$email, '%' . $dateLabel . '%']);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $parsed = aquavelo_gc_parse_rdv_from_name((string) $row['name']);
            if ($parsed && $parsed['date'] === $dateLabel && $parsed['time'] === $timeLabel) {
                return $row;
            }
            // Tolérance ±30 min
            if ($parsed) {
                $diff = abs($parsed['start']->getTimestamp() - $evt['start']->getTimestamp());
                if ($diff <= 1800) {
                    return $row;
                }
            }
        }
    }

    $phone = aquavelo_trial_normalize_phone((string) ($evt['phone'] ?? ''));
    if (strlen($phone) >= 9) {
        $st = $database->prepare(
            "SELECT * FROM am_free
             WHERE name LIKE '%(RDV:%'
               AND name LIKE ?
               AND phone IS NOT NULL AND phone <> ''
             ORDER BY id DESC LIMIT 30"
        );
        $st->execute(['%' . $dateLabel . '%']);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            if (aquavelo_trial_normalize_phone((string) $row['phone']) !== $phone) {
                continue;
            }
            $parsed = aquavelo_gc_parse_rdv_from_name((string) $row['name']);
            if ($parsed && abs($parsed['start']->getTimestamp() - $evt['start']->getTimestamp()) <= 1800) {
                return $row;
            }
        }
    }

    return null;
}

/**
 * Crée ou met à jour am_free depuis un événement Agenda.
 *
 * @return array{booking:array,created:bool}
 */
function aquavelo_trial_upsert_from_event(PDO $database, array $evt): array
{
    aquavelo_trial_ensure_schema($database);

    $existing = aquavelo_trial_find_booking($database, $evt);
    $dateLabel = $evt['start']->format('d/m/Y');
    $timeLabel = $evt['start']->format('H:i');
    $clientName = trim((string) $evt['name']);
    $rdvName = $clientName . ' (RDV: ' . $dateLabel . ' à ' . $timeLabel . ')';
    $email = aquavelo_trial_normalize_email((string) ($evt['email'] ?? ''));
    $phone = aquavelo_trial_normalize_phone((string) ($evt['phone'] ?? ''));
    $source = (string) ($evt['source'] ?? 'manual');
    $segment = $source === 'calendly' ? 'calendly' : ($source === 'site' ? 'calendrier-cannes' : 'agenda-import');
    $eventId = (string) $evt['google_event_id'];

    if ($existing) {
        $sets = ['google_sync = 1'];
        $params = [];
        if ($eventId !== '' && empty($existing['google_event_id'])) {
            $sets[] = 'google_event_id = ?';
            $params[] = $eventId;
        }
        if ($email !== '' && trim((string) ($existing['email'] ?? '')) === '') {
            $sets[] = 'email = ?';
            $params[] = $email;
        }
        if ($phone !== '' && trim((string) ($existing['phone'] ?? '')) === '') {
            $sets[] = 'phone = ?';
            $params[] = $phone;
        }
        if (empty($existing['trial_source']) && $source !== '') {
            $sets[] = 'trial_source = ?';
            $params[] = $source;
        }
        if ($params !== [] || count($sets) > 1) {
            $params[] = (int) $existing['id'];
            $database->prepare('UPDATE am_free SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);
        }
        $st = $database->prepare('SELECT * FROM am_free WHERE id = ?');
        $st->execute([(int) $existing['id']]);

        return ['booking' => $st->fetch(PDO::FETCH_ASSOC) ?: $existing, 'created' => false];
    }

    // Ne pas créer de fiche sans email ni téléphone exploitables
    if ($email === '' && $phone === '') {
        return ['booking' => [], 'created' => false];
    }

    $reference = 'AQ' . date('dmhis') . substr((string) random_int(100, 999), -2);
    $ins = $database->prepare(
        'INSERT INTO am_free (reference, center_id, free, name, email, phone, segment_id, google_sync, google_event_id, attendance_status, trial_source)
         VALUES (?, ?, 3, ?, ?, ?, ?, 1, ?, \'pending\', ?)'
    );
    $ins->execute([
        $reference,
        AQUAVELO_TRIAL_CENTER_ID,
        $rdvName,
        $email,
        $phone,
        $segment,
        $eventId !== '' ? $eventId : null,
        $source,
    ]);
    $id = (int) $database->lastInsertId();
    $st = $database->prepare('SELECT * FROM am_free WHERE id = ?');
    $st->execute([$id]);

    return ['booking' => $st->fetch(PDO::FETCH_ASSOC) ?: [], 'created' => true];
}

/**
 * Ingère les événements Agenda du jour (ou d'une date) dans am_free.
 *
 * @return array{imported:int,linked:int,skipped:int,lines:string[]}
 */
function aquavelo_trial_ingest_day(PDO $database, ?DateTimeInterface $day = null): array
{
    aquavelo_trial_ensure_schema($database);
    $result = ['imported' => 0, 'linked' => 0, 'skipped' => 0, 'lines' => []];
    $events = aquavelo_trial_list_calendar_events($day);
    if ($events === []) {
        $result['lines'][] = 'Aucun événement Agenda trouvé (ou API indisponible).';

        return $result;
    }

    foreach ($events as $evt) {
        $label = $evt['start']->format('H:i') . ' — ' . $evt['name'];
        if ($evt['email'] === '' && $evt['phone'] === '' && $evt['source'] !== 'site') {
            // Tentative : événements site ont souvent email dans description ; sinon skip
            $result['skipped']++;
            $result['lines'][] = "⏭️ $label | contact manquant";
            continue;
        }
        $up = aquavelo_trial_upsert_from_event($database, $evt);
        if ($up['booking'] === []) {
            $result['skipped']++;
            $result['lines'][] = "⏭️ $label | non importé";
            continue;
        }
        if ($up['created']) {
            $result['imported']++;
            $result['lines'][] = '➕ ' . $label . ' | source=' . $evt['source'] . ' | am_free#' . $up['booking']['id'];
        } else {
            $result['linked']++;
            $result['lines'][] = '🔗 ' . $label . ' | am_free#' . $up['booking']['id'];
        }
    }

    return $result;
}

/**
 * Historique des passages déjà validés (hors fiche courante).
 *
 * @return list<array>
 */
function aquavelo_trial_previous_attendances(PDO $database, string $email, string $phone, int $excludeId = 0): array
{
    aquavelo_trial_ensure_schema($database);
    $email = aquavelo_trial_normalize_email($email);
    $phone = aquavelo_trial_normalize_phone($phone);

    $rows = [];
    if ($email !== '') {
        $st = $database->prepare(
            "SELECT * FROM am_free
             WHERE attendance_status = 'present'
               AND LOWER(TRIM(email)) = ?
               AND id <> ?
             ORDER BY attended_at DESC, id DESC
             LIMIT 10"
        );
        $st->execute([$email, $excludeId]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    if ($rows === [] && strlen($phone) >= 9) {
        $st = $database->prepare(
            "SELECT * FROM am_free
             WHERE attendance_status = 'present'
               AND phone IS NOT NULL AND phone <> ''
               AND id <> ?
             ORDER BY attended_at DESC, id DESC
             LIMIT 40"
        );
        $st->execute([$excludeId]);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            if (aquavelo_trial_normalize_phone((string) $row['phone']) === $phone) {
                $rows[] = $row;
            }
            if (count($rows) >= 10) {
                break;
            }
        }
    }

    return $rows;
}

/**
 * @return array{ok:bool,message:string,previous:list<array>}
 */
function aquavelo_trial_set_attendance(PDO $database, int $bookingId, string $status): array
{
    aquavelo_trial_ensure_schema($database);
    $status = strtolower(trim($status));
    if (!in_array($status, ['present', 'noshow', 'pending'], true)) {
        return ['ok' => false, 'message' => 'Statut invalide', 'previous' => []];
    }

    $st = $database->prepare('SELECT * FROM am_free WHERE id = ?');
    $st->execute([$bookingId]);
    $booking = $st->fetch(PDO::FETCH_ASSOC);
    if (!$booking) {
        return ['ok' => false, 'message' => 'Fiche introuvable', 'previous' => []];
    }

    $previous = [];
    if ($status === 'present') {
        $previous = aquavelo_trial_previous_attendances(
            $database,
            (string) ($booking['email'] ?? ''),
            (string) ($booking['phone'] ?? ''),
            $bookingId
        );
        $database->prepare(
            "UPDATE am_free SET attendance_status = 'present', attended_at = NOW() WHERE id = ?"
        )->execute([$bookingId]);
        $msg = 'Présence validée';
        if ($previous !== []) {
            $p = $previous[0];
            $when = $p['attended_at'] ?? '';
            if ($when === '' || $when === null) {
                $parsed = aquavelo_gc_parse_rdv_from_name((string) $p['name']);
                $when = $parsed ? ($parsed['date'] . ' à ' . $parsed['time']) : '';
            } else {
                try {
                    $dt = new DateTime((string) $when, new DateTimeZone('Europe/Paris'));
                    $when = $dt->format('d/m/Y à H:i');
                } catch (Throwable $e) {
                    /* keep raw */
                }
            }
            $msg .= ' — ⚠️ déjà venu' . ($when !== '' ? ' le ' . $when : '');
        }

        return ['ok' => true, 'message' => $msg, 'previous' => $previous];
    }

    if ($status === 'noshow') {
        $database->prepare(
            "UPDATE am_free SET attendance_status = 'noshow', attended_at = NULL WHERE id = ?"
        )->execute([$bookingId]);

        return ['ok' => true, 'message' => 'Marqué no-show', 'previous' => []];
    }

    $database->prepare(
        "UPDATE am_free SET attendance_status = 'pending', attended_at = NULL WHERE id = ?"
    )->execute([$bookingId]);

    return ['ok' => true, 'message' => 'Statut remis en attente', 'previous' => []];
}

/**
 * Liste unifiée du jour : Agenda + fiches am_free.
 *
 * @return list<array>
 */
function aquavelo_trial_day_board(PDO $database, ?DateTimeInterface $day = null): array
{
    aquavelo_trial_ensure_schema($database);
    $tz = new DateTimeZone('Europe/Paris');
    $dayStart = $day ? DateTime::createFromInterface($day) : new DateTime('now', $tz);
    $dayStart->setTimezone($tz);
    $dayStart->setTime(0, 0, 0);
    $dateLabel = $dayStart->format('d/m/Y');

    // Toujours tenter l'ingestion avant affichage
    aquavelo_trial_ingest_day($database, $dayStart);

    $st = $database->prepare(
        "SELECT * FROM am_free
         WHERE center_id IN (305, 347, 349)
           AND name LIKE '%(RDV:%'
           AND name LIKE ?
           AND COALESCE(segment_id, '') <> 'admin-lock'
           AND name NOT LIKE '%BLOQUE (ADMIN)%'
           AND name NOT LIKE '%VERROUILL%'
         ORDER BY id ASC"
    );
    $st->execute(['%' . $dateLabel . '%']);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $board = [];
    foreach ($rows as $row) {
        $parsed = aquavelo_gc_resolve_rdv_datetime($row);
        if (!$parsed || $parsed['date'] !== $dateLabel) {
            continue;
        }
        $previous = aquavelo_trial_previous_attendances(
            $database,
            (string) ($row['email'] ?? ''),
            (string) ($row['phone'] ?? ''),
            (int) $row['id']
        );
        $source = (string) ($row['trial_source'] ?? '');
        if ($source === '' && (string) ($row['segment_id'] ?? '') === 'calendly') {
            $source = 'calendly';
        }
        if ($source === '' && (string) ($row['segment_id'] ?? '') === 'calendrier-cannes') {
            $source = 'site';
        }
        $board[] = [
            'booking'    => $row,
            'time'       => $parsed['time'],
            'sort'       => $parsed['start']->getTimestamp(),
            'client'     => trim(explode('(RDV:', (string) $row['name'])[0]),
            'source'     => $source !== '' ? $source : 'site',
            'status'     => (string) ($row['attendance_status'] ?? 'pending'),
            'previous'   => $previous,
            'already'    => $previous !== [],
        ];
    }

    usort($board, static fn ($a, $b) => $a['sort'] <=> $b['sort']);

    return $board;
}
