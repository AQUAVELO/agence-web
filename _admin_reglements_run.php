<?php
/**
 * Actions admin règlements : doit s'exécuter AVANT tout envoi HTML (index.php),
 * sinon header(Location) échoue → page blanche + risque de double POST au refresh.
 */

require_once __DIR__ . '/_settings.php';

if (!function_exists('admin_reglements_envoyer_sms')) {
    function admin_reglements_envoyer_sms(string $telephone, string $message): array
    {
        if ($telephone === '') {
            return ['ok' => false, 'detail' => 'Numéro de téléphone vide.'];
        }
        if (!function_exists('sendSMS')) {
            return ['ok' => false, 'detail' => 'Fonction SMS indisponible.'];
        }
        $raw = sendSMS($telephone, $message);
        if ($raw === false) {
            return ['ok' => false, 'detail' => 'Token SMSFactor manquant ou erreur réseau.'];
        }
        $j = json_decode($raw, true);
        if (is_array($j)) {
            $st = $j['status'] ?? null;
            if ($st === 1 || $st === '1' || (!empty($j['success']) && $j['success'])) {
                return ['ok' => true, 'detail' => 'SMS envoyé.'];
            }
            $msg = $j['message'] ?? $j['error'] ?? json_encode($j);
            return ['ok' => false, 'detail' => is_string($msg) ? $msg : 'Réponse API inattendue.'];
        }
        if (stripos($raw, 'error') !== false || stripos($raw, 'fail') !== false) {
            return ['ok' => false, 'detail' => 'Réponse : ' . mb_substr($raw, 0, 200)];
        }
        return ['ok' => true, 'detail' => 'SMS envoyé.'];
    }
}

function admin_reglements_redirect(): void
{
    $base = defined('BASE_PATH') ? (string) BASE_PATH : '/';
    if ($base === '') {
        $base = '/';
    }
    header('Location: ' . $base . 'index.php?p=admin_reglements', true, 303);
    exit;
}

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_reglements_auth'], $_SESSION['csrf_reglements']);
    admin_reglements_redirect();
}

$authenticated = !empty($_SESSION['admin_reglements_auth']);

if ($authenticated) {
    if (empty($_SESSION['csrf_reglements'])) {
        $_SESSION['csrf_reglements'] = bin2hex(random_bytes(32));
    }
    $csrf = $_SESSION['csrf_reglements'];

    if (!empty($_GET['resend_sms']) && isset($_GET['token_csrf'])) {
        $rid = (int) $_GET['resend_sms'];
        if (hash_equals($csrf, (string) $_GET['token_csrf']) && $rid > 0) {
            try {
                $st = $conn->prepare('SELECT id, token, libelle_client, motif, montant, statut, telephone_client FROM reglement_lien WHERE id = ? LIMIT 1');
                $st->execute([$rid]);
                $rw = $st->fetch(PDO::FETCH_ASSOC);
                if ($rw && $rw['statut'] === 'en_attente' && !empty($rw['telephone_client'])) {
                    $u = 'https://www.aquavelo.com/index.php?p=reglement_lien&t=' . $rw['token'];
                    $mf = number_format((float) $rw['montant'], 2, ',', ' ');
                    $txt = 'Bonjour, pour régler votre situation Aquavelo (' . $rw['libelle_client'] . ', ' . $mf . ' €) : ' . $u;
                    $r = admin_reglements_envoyer_sms($rw['telephone_client'], $txt);
                    $_SESSION['admin_reglements_flash'] = $r['ok']
                        ? ('✅ ' . $r['detail'])
                        : ('❌ SMS non envoyé : ' . $r['detail']);
                } else {
                    $_SESSION['admin_reglements_flash'] = '❌ Lien introuvable, déjà payé ou sans numéro.';
                }
            } catch (PDOException $e) {
                $_SESSION['admin_reglements_flash'] = '❌ ' . $e->getMessage();
            }
        }
        admin_reglements_redirect();
    }

    if (!empty($_GET['annuler']) && isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $tok = $_GET['token_csrf'] ?? '';
        if (hash_equals($csrf, $tok)) {
            try {
                $conn->prepare("UPDATE reglement_lien SET statut = 'annule' WHERE id = ? AND statut = 'en_attente'")->execute([$id]);
            } catch (PDOException $e) {
                $_SESSION['admin_reglements_flash'] = '❌ ' . $e->getMessage();
            }
        }
        admin_reglements_redirect();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_lien'])) {
        if (!hash_equals($csrf, (string) ($_POST['csrf'] ?? ''))) {
            $_SESSION['admin_reglements_creer_feedback'] = [
                'error' => 'Session expirée, rechargez la page.',
            ];
            admin_reglements_redirect();
        }

        $libelle = trim($_POST['libelle_client'] ?? '');
        $motif = trim($_POST['motif'] ?? '');
        $montantStr = str_replace(',', '.', trim($_POST['montant'] ?? ''));
        $montant = round((float) $montantStr, 2);
        $email_c = null;
        $tel_c = trim($_POST['telephone_client'] ?? '') ?: null;
        $envoyer_sms = !empty($_POST['envoyer_sms']);

        if ($libelle === '' || $motif === '' || $montant <= 0) {
            $_SESSION['admin_reglements_creer_feedback'] = [
                'error' => 'Libellé, motif et montant valide sont obligatoires.',
            ];
            admin_reglements_redirect();
        }

        $token = bin2hex(random_bytes(16));
        try {
            $ins = $conn->prepare('INSERT INTO reglement_lien (token, libelle_client, motif, montant, email_client, telephone_client, statut) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $ins->execute([$token, $libelle, $motif, $montant, $email_c, $tel_c, 'en_attente']);
            $ok_url = 'https://www.aquavelo.com/index.php?p=reglement_lien&t=' . $token;
            $montantFmt = number_format($montant, 2, ',', ' ');
            $ok_sms = "Bonjour, pour régler votre situation Aquavelo ({$libelle}, {$montantFmt} €) : {$ok_url}";

            $pcSmsMsg = '';
            $pcSmsOk = false;
            if ($envoyer_sms && $tel_c) {
                $sr = admin_reglements_envoyer_sms($tel_c, $ok_sms);
                $pcSmsOk = $sr['ok'];
                $pcSmsMsg = ($sr['ok'] ? '✅ ' : '❌ ') . $sr['detail'];
            } elseif ($envoyer_sms && !$tel_c) {
                $pcSmsOk = false;
                $pcSmsMsg = '❌ Cochez « Envoyer le SMS » uniquement si un numéro de mobile est renseigné.';
            }

            if ($pcSmsMsg !== '') {
                $_SESSION['admin_reglements_post_create'] = [
                    'sms_msg' => $pcSmsMsg,
                    'sms_ok'  => $pcSmsOk,
                ];
            }
            admin_reglements_redirect();
        } catch (PDOException $e) {
            $_SESSION['admin_reglements_creer_feedback'] = [
                'error' => 'Erreur base : ' . $e->getMessage() . ' — Exécutez sql/reglement_lien.sql ou vérifiez la base utilisée par le site.',
            ];
            admin_reglements_redirect();
        }
    }
}
