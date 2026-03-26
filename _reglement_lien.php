<?php
/**
 * Paiement personnalisé (impayé / règlement) — montant défini en BDD, accès par token.
 * URL : index.php?p=reglement_lien&t=<token>
 */

if (!defined('MONETICO_TPE')) {
    define('MONETICO_TPE', '6684349');
    define('MONETICO_KEY', 'AB477436DAE9200BF71E755208720A3CD5280594');
    define('MONETICO_COMPANY', 'AQUACANNES');
    define('MONETICO_URL', 'https://p.monetico-services.com/paiement.cgi');
    define('MONETICO_RETURN_URL', 'https://www.aquavelo.com/confirmation_reglement.php');
    define('MONETICO_CANCEL_URL', 'https://www.aquavelo.com/annulation_reglement.php');
}

if (!function_exists('reglement_parse_libelle')) {
    /**
     * @return array{prenom: string, nom: string}
     */
    function reglement_parse_libelle(string $libelle): array
    {
        $libelle = trim(preg_replace('/\s+/u', ' ', $libelle));
        $prenom = '';
        $nom = '';
        if ($libelle === '') {
            return ['prenom' => $prenom, 'nom' => $nom];
        }
        $rest = preg_replace('/^(M\.|Mme|Mr|Mrs|Ms|Mlle|Monsieur|Madame|Dr)\.?\s+/iu', '', $libelle);
        $rest = trim((string) $rest);
        if ($rest === '') {
            return ['prenom' => $prenom, 'nom' => $nom];
        }
        $parts = preg_split('/\s+/u', $rest);
        if (count($parts) === 1) {
            return ['prenom' => $prenom, 'nom' => $parts[0]];
        }
        $nom = (string) array_pop($parts);

        return ['prenom' => implode(' ', $parts), 'nom' => $nom];
    }
}

if (!function_exists('reglement_libelle_nom_complet')) {
    /** Pré-remplit « Nom et prénom » (civilité retirée, reste du libellé admin). */
    function reglement_libelle_nom_complet(string $libelle): string
    {
        $p = reglement_parse_libelle($libelle);

        return trim($p['prenom'] . ' ' . $p['nom']);
    }
}

if (!function_exists('calculateMAC_reglement')) {
    function calculateMAC_reglement(array $fields, string $keyHex): string
    {
        $recognizedKeys = [
            'TPE', 'contexte_commande', 'date', 'lgue', 'mail', 'montant', 'reference',
            'societe', 'texte-libre', 'url_retour_err', 'url_retour_ok', 'version',
        ];
        $macFields = [];
        foreach ($recognizedKeys as $key) {
            $macFields[$key] = isset($fields[$key]) ? mb_convert_encoding((string) $fields[$key], 'UTF-8', 'auto') : '';
        }
        ksort($macFields, SORT_STRING);
        $chaine = '';
        foreach ($macFields as $k => $v) {
            $chaine .= "$k=$v*";
        }
        $chaine = rtrim($chaine, '*');
        $binaryKey = pack('H*', $keyHex);
        return strtoupper(hash_hmac('sha1', $chaine, $binaryKey));
    }
}

$token = '';
if (isset($_GET['t'])) {
    $token = preg_replace('/[^a-f0-9]/i', '', (string) $_GET['t']);
}
if ($token === '' && isset($_POST['reglement_token'])) {
    $token = preg_replace('/[^a-f0-9]/i', '', (string) $_POST['reglement_token']);
}

$row = null;
$db_error = '';
if ($token !== '') {
    try {
        $stmt = $conn->prepare('SELECT * FROM reglement_lien WHERE token = ? LIMIT 1');
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $db_error = 'Service temporairement indisponible. Réessayez plus tard.';
        error_log('reglement_lien: ' . $e->getMessage());
    }
}

$dateCommande = date('d/m/Y:H:i:s');
$contexteCommande = base64_encode(json_encode([
    'billing' => [
        'addressLine1' => '60 avenue du Docteur Raymond Picaud',
        'city' => 'Cannes',
        'postalCode' => '06150',
        'country' => 'FR',
    ],
], JSON_UNESCAPED_UNICODE));

$error = '';
$blocked_message = '';

if ($db_error !== '') {
    $blocked_message = $db_error;
} elseif ($token === '') {
    $blocked_message = 'Lien incomplet. Utilisez le lien reçu par votre centre Aquavelo.';
} elseif (!$row) {
    $blocked_message = 'Ce lien de paiement est invalide ou a expiré. Contactez votre centre Aquavelo.';
} elseif ($row['statut'] === 'paye') {
    $blocked_message = 'Ce règlement a déjà été effectué. Merci.';
} elseif ($row['statut'] === 'annule') {
    $blocked_message = 'Ce lien de paiement n\'est plus actif.';
} elseif ($row['statut'] === 'expire' || (!empty($row['expires_at']) && strtotime($row['expires_at']) < time())) {
    $blocked_message = 'Ce lien de paiement a expiré. Contactez votre centre Aquavelo.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payer_reglement'])) {
    $nomComplet = trim($_POST['nom_complet'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['telephone'] ?? '');
    $emailDb = trim((string) ($row['email_client'] ?? ''));
    $emailFieldShown = $emailDb !== '';

    if ($nomComplet === '' || !preg_match('/^[0-9\s\-\+\(\)]+$/', $tel)) {
        $error = 'Veuillez remplir correctement tous les champs.';
    } elseif ($emailFieldShown && $email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse e-mail invalide.';
    } elseif ($emailFieldShown && $emailDb !== '' && $email === '') {
        $error = 'L’e-mail est requis pour ce lien.';
    } else {
        $montant = (float) $row['montant'];
        if ($montant <= 0) {
            $error = 'Montant invalide.';
        } else {
            $reference = 'REG' . $row['id'] . date('YmdHis') . rand(100, 999);
            $detail = $row['motif'];
            $achatLabel = 'Règlement : ' . $row['libelle_client'] . ' — ' . $row['motif'];

            $emailPourClient = '';
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailPourClient = $email;
            } elseif ($emailDb !== '' && filter_var($emailDb, FILTER_VALIDATE_EMAIL)) {
                $emailPourClient = $emailDb;
            }
            // Monetico attend un e-mail valide : adresse générique si le client n’en fournit pas.
            $mailMonetico = $emailPourClient !== '' ? $emailPourClient : 'service.clients@aquavelo.com';

            $texteLibreInfos = [
                'reglement_id' => (string) $row['id'],
                'token'        => $token,
                'email'        => $emailPourClient,
                'nom'          => $nomComplet,
                'prenom'       => '',
                'telephone'    => $tel,
                'achat'        => $achatLabel,
                'detail'       => $detail,
                'montant'      => number_format($montant, 2, '.', '') . 'EUR',
                'libelle'      => $row['libelle_client'],
                'motif'        => $row['motif'],
            ];

            $fields = [
                'TPE'               => MONETICO_TPE,
                'contexte_commande' => $contexteCommande,
                'date'              => $dateCommande,
                'montant'           => sprintf('%012.2f', $montant) . 'EUR',
                'reference'         => $reference,
                'texte-libre'       => http_build_query($texteLibreInfos, '', ';'),
                'version'           => '3.0',
                'lgue'              => 'FR',
                'societe'           => MONETICO_COMPANY,
                'mail'              => $mailMonetico,
                'url_retour_ok'     => MONETICO_RETURN_URL,
                'url_retour_err'    => MONETICO_CANCEL_URL,
            ];
            $fields['MAC'] = calculateMAC_reglement($fields, MONETICO_KEY);

            $upd = $conn->prepare('UPDATE reglement_lien SET monetico_reference = ? WHERE id = ? AND token = ?');
            $upd->execute([$reference, $row['id'], $token]);

            echo '<div style="text-align:center; font-family:sans-serif; margin-top:30px; color:green; padding:50px;">Redirection vers le paiement sécurisé…</div>';
            echo '<form id="form-monetico-reglement" action="' . htmlspecialchars(MONETICO_URL) . '" method="post">';
            foreach ($fields as $name => $value) {
                echo '<input type="hidden" name="' . htmlspecialchars((string) $name) . '" value="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '">';
            }
            echo '</form>';
            echo '<script>document.getElementById("form-monetico-reglement").submit();</script>';
            return;
        }
    }
}

$montant_display = $row ? number_format((float) $row['montant'], 2, ',', ' ') : '';

$disp_nom_complet = '';
$reglement_email_db = '';
$reglement_show_email_field = false;
$disp_email = '';
if ($row && $blocked_message === '') {
    $reglement_email_db = trim((string) ($row['email_client'] ?? ''));
    $reglement_show_email_field = $reglement_email_db !== '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['payer_reglement'])) {
        $disp_nom_complet = trim((string) ($_POST['nom_complet'] ?? ''));
        $disp_email = trim((string) ($_POST['email'] ?? ''));
    } else {
        $disp_nom_complet = reglement_libelle_nom_complet((string) ($row['libelle_client'] ?? ''));
        $disp_email = $reglement_email_db;
    }
}
?>

<style>
.reglement-section {
  max-width: 560px;
  margin: 40px auto;
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}
.reglement-section h1 {
  color: #00a8cc;
  text-align: center;
  font-size: 1.5rem;
  margin-bottom: 8px;
}
.reglement-montant {
  text-align: center;
  font-size: 2rem;
  font-weight: bold;
  color: #104e8b;
  margin: 16px 0 24px;
}
.reglement-section label { display: block; margin-top: 14px; font-weight: 600; }
.reglement-section input,
.reglement-section textarea[readonly] {
  width: 100%;
  padding: 10px 12px;
  margin-top: 6px;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-sizing: border-box;
}
.reglement-section textarea[readonly] {
  min-height: 72px;
  resize: none;
  background: #f9f9f9;
  color: #444;
}
.reglement-section button[type="submit"] {
  width: 100%;
  margin-top: 22px;
  padding: 14px;
  background: #00a8cc;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1.05rem;
  font-weight: bold;
  cursor: pointer;
}
.reglement-section button[type="submit"]:hover { background: #008fa8; }
.reglement-alert { background: #fff3cd; border: 1px solid #ffc107; padding: 16px; border-radius: 8px; margin-bottom: 20px; }
.reglement-error { color: #c62828; text-align: center; margin-bottom: 12px; }
.reglement-info { color: #555; font-size: 0.95rem; line-height: 1.5; margin-bottom: 16px; }
</style>

<div class="reglement-section">
  <h1>Règlement Aquavelo</h1>

  <?php if ($blocked_message !== ''): ?>
    <div class="reglement-alert"><?= htmlspecialchars($blocked_message) ?></div>
    <p class="text-center"><a href="<?= BASE_PATH ?>">Retour à l'accueil</a></p>
  <?php else: ?>
    <p class="reglement-info">
      <strong><?= htmlspecialchars($row['libelle_client']) ?></strong>
    </p>
    <div class="reglement-montant"><?= htmlspecialchars($montant_display) ?> €</div>

    <?php if ($error !== ''): ?>
      <p class="reglement-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="">
      <input type="hidden" name="reglement_token" value="<?= htmlspecialchars($token) ?>">
      <input type="hidden" name="payer_reglement" value="1">
      <label>Motif<textarea readonly rows="3"><?= htmlspecialchars($row['motif']) ?></textarea></label>
      <label>Nom et prénom *<input type="text" name="nom_complet" required value="<?= htmlspecialchars($disp_nom_complet) ?>" autocomplete="name"></label>
      <?php if ($reglement_show_email_field): ?>
      <label>Email *<input type="email" name="email" required value="<?= htmlspecialchars($disp_email) ?>" autocomplete="email"></label>
      <?php endif; ?>
      <label>Téléphone *<input type="tel" name="telephone" required value="<?= htmlspecialchars((string) ($_POST['telephone'] ?? $row['telephone_client'] ?? '')) ?>" autocomplete="tel"></label>
      <button type="submit">Payer <?= htmlspecialchars($montant_display) ?> € en ligne</button>
    </form>
    <p style="margin-top:20px;font-size:0.85rem;color:#888;text-align:center;">
      Paiement sécurisé par carte bancaire (Monetico / CIC).
    </p>
  <?php endif; ?>
</div>
