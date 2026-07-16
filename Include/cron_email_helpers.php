<?php
/**
 * Helpers partagés pour les crons email du protocole séance d'essai.
 */
declare(strict_types=1);

require_once __DIR__ . '/../google_calendar_rdv_helpers.php';

if (!function_exists('aquavelo_cron_parse_rdv_start')) {
    /**
     * @param array{name?:string,date?:string|null} $booking
     */
    function aquavelo_cron_parse_rdv_start(array $booking): ?DateTime
    {
        $parsed = aquavelo_gc_resolve_rdv_datetime($booking);
        if ($parsed === null) {
            return null;
        }

        $start = clone $parsed['start'];
        $start->setTimezone(new DateTimeZone('Europe/Paris'));

        return $start;
    }
}

if (!function_exists('aquavelo_cron_client_first_name')) {
    function aquavelo_cron_client_first_name(array $booking): string
    {
        $name = (string) ($booking['name'] ?? '');
        $client = trim(explode('(RDV:', $name)[0]);

        if ($client === '') {
            return 'Bonjour';
        }

        $parts = preg_split('/\s+/u', $client) ?: [];

        return $parts[0] ?? $client;
    }
}

if (!function_exists('aquavelo_cron_rdv_label')) {
    function aquavelo_cron_rdv_label(array $booking): string
    {
        $name = (string) ($booking['name'] ?? '');
        $pos = strpos($name, '(RDV:');
        if ($pos === false) {
            return '';
        }

        $raw = substr($name, $pos + 6);
        $raw = trim(str_replace([')', '(', 'RDV:'], ['', '', ''], $raw));

        return str_replace(['(', ')'], ['', ''], $raw);
    }
}

if (!function_exists('aquavelo_cron_lookup_center')) {
    /**
     * @return array{city:string,address:string,phone:string,email:string}
     */
    function aquavelo_cron_lookup_center(PDO $database, int $centerId): array
    {
        $lookupId = in_array($centerId, [305, 347, 349], true) ? 305 : $centerId;
        $stmt = $database->prepare('SELECT city, address, phone, email FROM am_centers WHERE id = ?');
        $stmt->execute([$lookupId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                'city' => (string) ($row['city'] ?? 'Cannes'),
                'address' => (string) ($row['address'] ?? ''),
                'phone' => (string) ($row['phone'] ?? ''),
                'email' => (string) ($row['email'] ?? ''),
            ];
        }

        return [
            'city' => 'Cannes',
            'address' => '60 avenue du Docteur Raymond Picaud, Cannes',
            'phone' => '04 93 93 05 65',
            'email' => 'aqua.cannes@gmail.com',
        ];
    }
}

if (!function_exists('aquavelo_cron_create_mailer')) {
    function aquavelo_cron_create_mailer(array $settings, array $centerInfo): PHPMailer\PHPMailer\PHPMailer
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $settings['mjhost'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['mjusername'];
        $mail->Password = $settings['mjpassword'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo ' . ($centerInfo['city'] ?? ''));
        $mail->isHTML(true);

        $reply = trim((string) ($centerInfo['email'] ?? ''));
        if ($reply !== '' && filter_var($reply, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($reply, 'Aquavelo ' . ($centerInfo['city'] ?? ''));
        }

        return $mail;
    }
}

if (!function_exists('aquavelo_cron_signature')) {
    function aquavelo_cron_signature(int $centerId, array $centerInfo): string
    {
        return in_array($centerId, [305, 347, 349], true)
            ? 'Cordialement Claude'
            : 'Cordialement,<br>Aquavelo ' . ($centerInfo['city'] ?? '');
    }
}
