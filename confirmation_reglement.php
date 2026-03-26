<?php
/**
 * Retour serveur Monetico (CGI2) — règlements personnalisés (reglement_lien).
 */
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/_settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');

function reglement_validateMAC(array $params, string $keyHex): bool
{
    $recognizedKeys = [
        'TPE', 'contexte_commande', 'date', 'montant', 'reference', 'texte-libre',
        'code-retour', 'cvx', 'vld', 'brand', 'status3ds', 'numauto', 'originecb',
        'bincb', 'hpancb', 'ipclient', 'originetr', 'cbmasquee', 'modepaiement',
        'authentification', 'usage', 'typecompte', 'ecard', 'version', 'MAC',
    ];
    $macFields = [];
    foreach ($recognizedKeys as $k) {
        if (isset($params[$k])) {
            $macFields[$k] = mb_convert_encoding((string) $params[$k], 'UTF-8', 'auto');
        }
    }
    ksort($macFields, SORT_STRING);
    $chaine = '';
    foreach ($macFields as $k => $v) {
        if ($k !== 'MAC') {
            $chaine .= "$k=$v*";
        }
    }
    $chaine = rtrim($chaine, '*');
    $macCalc = strtoupper(hash_hmac('sha1', $chaine, pack('H*', $keyHex)));
    return isset($params['MAC']) && hash_equals($macCalc, strtoupper((string) $params['MAC']));
}

function reglement_parse_montant_monetico(string $s): ?float
{
    $s = trim($s);
    if (preg_match('/(\d+\.\d{2})EUR$/', $s, $m)) {
        return (float) $m[1];
    }
    return null;
}

function reglement_send_emails(
    array $settings,
    string $toEmail,
    string $prenom,
    string $nom,
    string $telephone,
    string $libelle,
    string $motif,
    float $montant,
    string $codeValidation
): void {
    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        return;
    }
    $identite = trim($prenom . ' ' . $nom);
    $montantFmt = number_format($montant, 2, ',', ' ') . ' €';
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $settings['mjhost'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['mjusername'];
        $mail->Password = $settings['mjpassword'];
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('service.clients@aquavelo.com', 'Aquavelo');
        $mail->addAddress($toEmail);
        $mail->addReplyTo('claude@alesiaminceur.com', 'Aquavelo');
        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de votre règlement Aquavelo';
        $mail->Body = '<p>Bonjour <strong>' . htmlspecialchars($identite) . '</strong>,</p>'
            . '<p>Nous avons bien reçu votre règlement de <strong>' . htmlspecialchars($montantFmt) . '</strong>.</p>'
            . '<p><strong>Détail :</strong> ' . htmlspecialchars($libelle) . '<br>'
            . htmlspecialchars($motif) . '</p>'
            . '<p>Code de référence : <strong>' . htmlspecialchars($codeValidation) . '</strong></p>'
            . '<p>Pour toute question : <strong>04 93 93 05 65</strong></p>'
            . '<p>Cordialement,<br>L\'équipe Aquavelo</p>';
        $mail->send();

        $admin = new PHPMailer(true);
        $admin->isSMTP();
        $admin->Host = $settings['mjhost'];
        $admin->SMTPAuth = true;
        $admin->Username = $settings['mjusername'];
        $admin->Password = $settings['mjpassword'];
        $admin->Port = 587;
        $admin->CharSet = 'UTF-8';
        $admin->setFrom('service.clients@aquavelo.com', 'Aquavelo');
        $admin->addAddress('aqua.cannes@gmail.com');
        $admin->isHTML(true);
        $admin->Subject = 'Règlement en ligne — ' . $identite;
        $admin->Body = '<ul>'
            . '<li>Nom et prénom : ' . htmlspecialchars($identite) . '</li>'
            . '<li>Email : ' . htmlspecialchars($toEmail) . '</li>'
            . '<li>Tél : ' . htmlspecialchars($telephone) . '</li>'
            . '<li>Montant : ' . htmlspecialchars($montantFmt) . '</li>'
            . '<li>Libellé : ' . htmlspecialchars($libelle) . '</li>'
            . '<li>Motif : ' . htmlspecialchars($motif) . '</li>'
            . '<li>Code : ' . htmlspecialchars($codeValidation) . '</li>'
            . '</ul>';
        $admin->send();
    } catch (Exception $e) {
        error_log('confirmation_reglement email: ' . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: https://www.aquavelo.com/');
    exit;
}

$macOk = reglement_validateMAC($_POST, MONETICO_KEY);
$cdr = '1';

if ($macOk) {
    parse_str(str_replace(';', '&', $_POST['texte-libre'] ?? ''), $infos);
    $reglementId = isset($infos['reglement_id']) ? (int) $infos['reglement_id'] : 0;
    $token = preg_replace('/[^a-f0-9]/i', '', (string) ($infos['token'] ?? ''));

    $codeRetour = $_POST['code-retour'] ?? '';
    $montantPost = reglement_parse_montant_monetico((string) ($_POST['montant'] ?? ''));

    if ($reglementId > 0 && $codeRetour === 'paiement' && $montantPost !== null) {
        $stmt = $conn->prepare('SELECT * FROM reglement_lien WHERE id = ? LIMIT 1');
        $stmt->execute([$reglementId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && ($token === '' || hash_equals((string) $row['token'], $token))) {
            $attendu = round((float) $row['montant'], 2);
            if (abs($montantPost - $attendu) >= 0.01) {
                // montant ne correspond pas
            } elseif ($row['statut'] === 'paye') {
                $cdr = '0';
            } elseif ($row['statut'] === 'en_attente') {
                $codeValidation = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                $upd = $conn->prepare("UPDATE reglement_lien SET statut = 'paye', paid_at = NOW() WHERE id = ? AND statut = 'en_attente'");
                $upd->execute([$reglementId]);
                if ($upd->rowCount() > 0) {
                    $email = trim($infos['email'] ?? '');
                    $prenom = trim($infos['prenom'] ?? '');
                    $nom = trim($infos['nom'] ?? '');
                    $tel = trim($infos['telephone'] ?? '');
                    reglement_send_emails(
                        $settings,
                        $email,
                        $prenom,
                        $nom,
                        $tel,
                        (string) $row['libelle_client'],
                        (string) $row['motif'],
                        $attendu,
                        $codeValidation
                    );
                }
                $cdr = '0';
            }
        }
    }
}

header('Content-Type: text/plain; charset=utf-8');
echo "version=2\ncdr={$cdr}\n";
exit;
