<?php
/**
 * Périodes de fermeture partagées Cannes / Mandelieu / Vallauris (même planning Google Calendar).
 */
declare(strict_types=1);

const CANNES_GROUP_CENTER_IDS = [305, 347, 349];

const CANNES_GROUP_CLOSURES = [
    [
        'start' => '2026-07-27',
        'end'   => '2026-08-01',
        'label' => 'Fermeture estivale',
    ],
];

function is_cannes_group_center(int $center_id): bool
{
    return in_array($center_id, CANNES_GROUP_CENTER_IDS, true);
}

function is_cannes_group_closed(int $center_id, DateTimeInterface|string $date): bool
{
    if (!is_cannes_group_center($center_id)) {
        return false;
    }

    if (is_string($date)) {
        $dt = DateTime::createFromFormat('d/m/Y', $date, new DateTimeZone('Europe/Paris'))
            ?: DateTime::createFromFormat('Y-m-d', $date, new DateTimeZone('Europe/Paris'));
        if (!$dt) {
            return false;
        }
    } else {
        $dt = DateTime::createFromInterface($date);
        $dt->setTimezone(new DateTimeZone('Europe/Paris'));
    }

    $dt->setTime(0, 0, 0);

    foreach (CANNES_GROUP_CLOSURES as $closure) {
        $start = new DateTime($closure['start'], new DateTimeZone('Europe/Paris'));
        $end = new DateTime($closure['end'], new DateTimeZone('Europe/Paris'));
        $start->setTime(0, 0, 0);
        $end->setTime(23, 59, 59);

        if ($dt >= $start && $dt <= $end) {
            return true;
        }
    }

    return false;
}

function is_cannes_group_rdv_closed(int $center_id, string $date_heure): bool
{
    if (!is_cannes_group_center($center_id)) {
        return false;
    }

    if (!function_exists('aquavelo_gc_parse_rdv_from_name')) {
        require_once __DIR__ . '/../google_calendar_rdv_helpers.php';
    }

    $parsed = aquavelo_gc_parse_rdv_from_name($date_heure);
    if ($parsed !== null) {
        return is_cannes_group_closed($center_id, $parsed['date']);
    }

    if (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $date_heure, $m)) {
        return is_cannes_group_closed($center_id, $m[1]);
    }

    return false;
}

function cannes_group_closure_notice(int $center_id): ?string
{
    if (!is_cannes_group_center($center_id)) {
        return null;
    }

    $today = new DateTime('today', new DateTimeZone('Europe/Paris'));

    foreach (CANNES_GROUP_CLOSURES as $closure) {
        $start = new DateTime($closure['start'], new DateTimeZone('Europe/Paris'));
        $end = new DateTime($closure['end'], new DateTimeZone('Europe/Paris'));

        if ($today <= $end) {
            return sprintf(
                'Le centre est fermé du %s au %s (%s). Les réservations ne sont pas disponibles sur cette période.',
                $start->format('d/m/Y'),
                $end->format('d/m/Y'),
                $closure['label']
            );
        }
    }

    return null;
}
