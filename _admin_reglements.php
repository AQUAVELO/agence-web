<?php
/**
 * Mini-admin : création de liens de règlement personnalisés (impayés).
 * URL : index.php?p=admin_reglements
 * Les actions avec redirection sont dans _admin_reglements_run.php (avant le HTML dans index.php).
 */

require_once '_settings.php';

$password_secret = getenv('AQUAVELO_ADMIN_REGLEMENTS_PASS') ?: 'aquavelo2026';

$authenticated = !empty($_SESSION['admin_reglements_auth']);

if (isset($_POST['login_pass'])) {
    if ($_POST['login_pass'] === $password_secret) {
        $_SESSION['admin_reglements_auth'] = true;
        $_SESSION['csrf_reglements'] = bin2hex(random_bytes(32));
        $authenticated = true;
    } else {
        sleep(1);
        $login_error = 'Mot de passe incorrect';
    }
}

if (!$authenticated) {
    ?>
    <section class="content-area bg1" style="padding: 100px 0;">
      <div class="container">
        <div style="max-width: 420px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
          <h2 style="color: #00a8cc;">Admin — Liens de règlement</h2>
          <?php if (!empty($login_error)): ?>
            <div style="color: #d32f2f; margin-bottom: 15px; font-weight: bold;"><?= htmlspecialchars($login_error) ?></div>
          <?php endif; ?>
          <form method="POST" action="index.php?p=admin_reglements" autocomplete="on">
            <input type="password" name="login_pass" placeholder="Mot de passe" required autocomplete="current-password" style="width: 100%; padding: 12px; margin-bottom: 16px; border: 1px solid #ddd; border-radius: 5px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">Connexion</button>
          </form>
        </div>
      </div>
    </section>
    <?php
    return;
}

if (empty($_SESSION['csrf_reglements'])) {
    $_SESSION['csrf_reglements'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_reglements'];

$table_error = '';
$created_url = '';
$created_sms = '';
$sms_status_message = '';
$sms_status_ok = null;
$form_repost = [];

if (!empty($_SESSION['admin_reglements_flash'])) {
    $sms_status_message = (string) $_SESSION['admin_reglements_flash'];
    $sms_status_ok = strpos($sms_status_message, '✅') === 0;
    unset($_SESSION['admin_reglements_flash']);
}

if (!empty($_SESSION['admin_reglements_post_create']) && is_array($_SESSION['admin_reglements_post_create'])) {
    $pc = $_SESSION['admin_reglements_post_create'];
    $created_url = (string) ($pc['url'] ?? '');
    $created_sms = (string) ($pc['sms'] ?? '');
    if (($pc['sms_msg'] ?? '') !== '') {
        $sms_status_message = (string) $pc['sms_msg'];
        $sms_status_ok = (bool) ($pc['sms_ok'] ?? false);
    }
    unset($_SESSION['admin_reglements_post_create']);
}

if (!empty($_SESSION['admin_reglements_creer_feedback']) && is_array($_SESSION['admin_reglements_creer_feedback'])) {
    $fb = $_SESSION['admin_reglements_creer_feedback'];
    if (!empty($fb['error'])) {
        $table_error = (string) $fb['error'];
    }
    unset($_SESSION['admin_reglements_creer_feedback']);
}

$liste = [];
try {
    $liste = $conn->query('SELECT id, token, libelle_client, motif, montant, statut, created_at, paid_at, telephone_client FROM reglement_lien ORDER BY id DESC LIMIT 80')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $table_error = $table_error ?: ('Table manquante : exécutez sql/reglement_lien.sql — ' . $e->getMessage());
}
?>

<section class="content-area bg1" style="padding: 40px 0 80px;">
  <div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
      <h2 style="color:#00a8cc; margin:0;">Liens de règlement personnalisés</h2>
      <a href="index.php?p=admin_reglements&logout=1" class="btn btn-default">Déconnexion</a>
    </div>

    <?php if ($table_error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($table_error) ?></div>
    <?php endif; ?>

    <?php if ($sms_status_message !== ''): ?>
      <div class="alert <?= $sms_status_ok ? 'alert-success' : 'alert-warning' ?>"><?= htmlspecialchars($sms_status_message) ?></div>
    <?php endif; ?>

    <?php if ($created_url): ?>
      <div class="alert alert-success" style="margin-bottom:24px;">
        <p><strong>Lien créé.</strong></p>
        <p><label>URL</label><br>
          <input type="text" readonly style="width:100%;max-width:100%;padding:8px;" id="copyUrl" value="<?= htmlspecialchars($created_url) ?>">
          <button type="button" class="btn btn-sm btn-info" onclick="navigator.clipboard.writeText(document.getElementById('copyUrl').value)">Copier l'URL</button>
        </p>
        <p><label>Texte SMS suggéré</label><br>
          <textarea readonly style="width:100%;min-height:100px;padding:8px;" id="copySms"><?= htmlspecialchars($created_sms) ?></textarea>
          <button type="button" class="btn btn-sm btn-info" onclick="navigator.clipboard.writeText(document.getElementById('copySms').value)">Copier le SMS</button>
        </p>
      </div>
    <?php endif; ?>

    <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);margin-bottom:32px;">
      <h3 style="margin-top:0;color:#104e8b;">Nouveau lien</h3>
      <form method="post" action="index.php?p=admin_reglements" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="creer_lien" value="1">
        <div class="form-group">
          <label>Libellé client (ex. M. Durant — page de paiement, SMS ; prénom/nom sur le formulaire sont déduits : « Jean Dupont », « Mr Durand »)</label>
          <input type="text" name="libelle_client" class="form-control" required maxlength="255" value="" autocomplete="off">
        </div>
        <div class="form-group">
          <label>Motif / détail (ex. 3 séances aquabiking — impayé)</label>
          <textarea name="motif" class="form-control" rows="3" required autocomplete="off"></textarea>
        </div>
        <div class="form-group">
          <label>Montant (€)</label>
          <input type="text" name="montant" class="form-control" required placeholder="90.00" value="" autocomplete="off">
        </div>
        <div class="form-group">
          <label>Téléphone mobile (pour envoi SMS — indicatif France 06/07)</label>
          <input type="text" name="telephone_client" class="form-control" placeholder="06 12 34 56 78" value="" autocomplete="off">
        </div>
        <div class="form-group">
          <label style="font-weight:normal;">
            <input type="checkbox" name="envoyer_sms" value="1" autocomplete="off">
            Envoyer automatiquement le SMS avec le lien de paiement (SMSFactor)
          </label>
        </div>
        <button type="submit" class="btn btn-primary" style="background:#00a8cc;border:none;">Créer le lien</button>
      </form>
    </div>

    <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
      <h3 style="margin-top:0;color:#104e8b;">Liens récents</h3>
      <div class="table-responsive">
        <table class="table table-striped table-bordered" style="font-size:0.85rem;">
          <thead><tr>
            <th>ID</th><th>Date</th><th>Libellé</th><th>Montant</th><th>Tél.</th><th>Statut</th><th>URL</th><th></th>
          </tr></thead>
          <tbody>
            <?php foreach ($liste as $r): ?>
              <?php
                $u = 'https://www.aquavelo.com/index.php?p=reglement_lien&t=' . htmlspecialchars($r['token']);
              ?>
              <tr>
                <td><?= (int) $r['id'] ?></td>
                <td><?= htmlspecialchars(substr((string) $r['created_at'], 0, 16)) ?></td>
                <td><?= htmlspecialchars($r['libelle_client']) ?><br><small class="text-muted"><?= htmlspecialchars(mb_substr($r['motif'], 0, 60)) ?><?= mb_strlen($r['motif']) > 60 ? '…' : '' ?></small></td>
                <td><?= number_format((float) $r['montant'], 2, ',', ' ') ?> €</td>
                <td><small><?= htmlspecialchars((string) ($r['telephone_client'] ?? '—')) ?></small></td>
                <td><?= htmlspecialchars($r['statut']) ?><?= $r['paid_at'] ? '<br><small>' . htmlspecialchars(substr((string) $r['paid_at'], 0, 16)) . '</small>' : '' ?></td>
                <td><input type="text" readonly class="form-control input-sm" style="min-width:220px;font-size:10px;" value="<?= $u ?>"></td>
                <td>
                  <?php if ($r['statut'] === 'en_attente' && !empty($r['telephone_client'])): ?>
                    <a href="index.php?p=admin_reglements&amp;resend_sms=<?= (int) $r['id'] ?>&amp;token_csrf=<?= urlencode($csrf) ?>" class="btn btn-xs btn-info" onclick="return confirm('Renvoyer le SMS au client ?');">SMS</a><br>
                  <?php endif; ?>
                  <?php if ($r['statut'] === 'en_attente'): ?>
                    <a href="index.php?p=admin_reglements&annuler=1&amp;id=<?= (int) $r['id'] ?>&amp;token_csrf=<?= urlencode($csrf) ?>" class="btn btn-xs btn-warning" onclick="return confirm('Annuler ce lien ?');">Annuler</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($liste) && !$table_error): ?>
              <tr><td colspan="8">Aucun lien pour l’instant.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
