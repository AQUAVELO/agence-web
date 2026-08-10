<?php
/**
 * Check-in accueil — validation présence séances d'essai (Cannes / Agenda).
 */
$authenticated = $authenticated ?? false;
$login_error = $login_error ?? null;
$dayParam = $dayParam ?? date('Y-m-d');
$checkin_day = $checkin_day ?? new DateTime('now', new DateTimeZone('Europe/Paris'));
$checkin_board = $checkin_board ?? [];

$flash = $_SESSION['admin_checkin_flash'] ?? '';
$flashType = $_SESSION['admin_checkin_flash_type'] ?? 'ok';
unset($_SESSION['admin_checkin_flash'], $_SESSION['admin_checkin_flash_type']);

if (!$authenticated): ?>
<section class="content-area bg1" style="padding: 100px 0;">
  <div class="container">
    <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center;">
      <h2 style="color: #00a8cc;">Check-in séances d'essai</h2>
      <?php if ($login_error): ?>
        <div style="color: #d32f2f; margin-bottom: 15px; font-weight: bold;"><?= htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
      <form method="POST" action="<?= htmlspecialchars(aquavelo_admin_checkin_url(['day' => $dayParam]), ENT_QUOTES, 'UTF-8') ?>" autocomplete="on">
        <input type="text" name="username" value="admin" autocomplete="username" style="display:none;">
        <input type="password" name="login_pass" placeholder="Mot de passe" required autocomplete="current-password" style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <button type="submit" class="btn btn-primary" style="width: 100%; background: #00a8cc; border: none; padding: 12px; color: white; font-weight: bold;">CONNEXION</button>
      </form>
      <p style="margin-top: 18px; margin-bottom: 0; font-size: 0.9rem;">
        <a href="<?= htmlspecialchars(aquavelo_admin_planning_url(), ENT_QUOTES, 'UTF-8') ?>" style="color: #104e8b;">→ Admin planning</a>
      </p>
    </div>
  </div>
</section>
<?php return; endif;

$sourceLabels = [
    'site' => 'Site',
    'calendly' => 'Calendly',
    'manual' => 'Manuel',
    'agenda-import' => 'Agenda',
];
$statusLabels = [
    'pending' => 'En attente',
    'present' => 'Présent',
    'noshow' => 'No-show',
];
?>
<section class="content-area bg1" style="padding: 28px 0 80px;">
  <div class="container" style="max-width: 820px;">
    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:12px; align-items:center; margin-bottom:18px;">
      <div>
        <h2 style="color:#00a8cc; margin:0 0 4px;">✅ Check-in essai — Cannes</h2>
        <div style="color:#666; font-size:0.92rem;">Agenda <strong>aqua.cannes@gmail.com</strong> → enregistrement dans <code>am_free</code></div>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="<?= htmlspecialchars(aquavelo_admin_planning_url(), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-default">Planning</a>
        <a href="<?= htmlspecialchars(aquavelo_admin_checkin_url(['day' => $dayParam, 'logout' => '1']), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-default">Déconnexion</a>
      </div>
    </div>

    <?php if ($flash !== ''): ?>
    <div style="margin-bottom:16px; padding:14px 16px; border-radius:8px; font-weight:600;
      <?= $flashType === 'error'
        ? 'background:#ffebee;color:#b71c1c;border:1px solid #ef9a9a;'
        : ($flashType === 'warn'
          ? 'background:#fff8e1;color:#e65100;border:1px solid #ffcc80;'
          : 'background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7;') ?>">
      <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <div style="background:#e3f2fd; border:1px solid #90caf9; border-radius:12px; padding:14px 16px; margin-bottom:18px; display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between;">
      <form method="get" action="<?= htmlspecialchars(aquavelo_canonical_base_url() . 'index.php', ENT_QUOTES, 'UTF-8') ?>" style="display:flex; gap:8px; align-items:center; margin:0;">
        <input type="hidden" name="p" value="admin_checkin">
        <label for="day" style="font-weight:600; color:#0d47a1;">Jour</label>
        <input type="date" id="day" name="day" value="<?= htmlspecialchars($dayParam, ENT_QUOTES, 'UTF-8') ?>" style="padding:8px 10px; border:1px solid #90caf9; border-radius:6px;">
        <button type="submit" class="btn btn-default" style="padding:8px 12px;">Afficher</button>
      </form>
      <form method="post" action="<?= htmlspecialchars(aquavelo_admin_checkin_url(['day' => $dayParam]), ENT_QUOTES, 'UTF-8') ?>" style="margin:0;">
        <input type="hidden" name="token" value="<?= htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="day" value="<?= htmlspecialchars($dayParam, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="action_ingest" value="1">
        <button type="submit" class="btn btn-primary" style="background:#1565c0;border:none;">↻ Synchroniser Agenda → am_free</button>
      </form>
    </div>

    <div style="background:white; border-radius:14px; box-shadow:0 5px 20px rgba(0,0,0,0.08); padding:8px 12px 18px;">
      <div style="padding:10px 6px 14px; border-bottom:1px solid #eee; margin-bottom:10px; font-weight:700;">
        <?php
          $daysFr = ['Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'];
          $dayEn = $checkin_day->format('l');
          echo htmlspecialchars(($daysFr[$dayEn] ?? $dayEn) . ' ' . $checkin_day->format('d/m/Y'), ENT_QUOTES, 'UTF-8');
        ?>
        <span style="font-weight:500; color:#777;"> — <?= count($checkin_board) ?> prospect(s)</span>
      </div>

      <?php if ($checkin_board === []): ?>
        <p style="padding:24px 8px; color:#888; text-align:center;">Aucun RDV pour ce jour. Lance une synchro Agenda si des rendez-vous Calendly / site existent.</p>
      <?php endif; ?>

      <?php foreach ($checkin_board as $item):
        $b = $item['booking'];
        $status = $item['status'] ?: 'pending';
        $src = $item['source'] ?: 'site';
        $bg = $status === 'present' ? '#e8f5e9' : ($status === 'noshow' ? '#f5f5f5' : '#fff');
        $border = $item['already'] ? '#e65100' : ($status === 'present' ? '#81c784' : '#e0e0e0');
      ?>
      <div style="border:2px solid <?= $border ?>; background:<?= $bg ?>; border-radius:12px; padding:14px; margin-bottom:12px;">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:8px; align-items:flex-start;">
          <div>
            <div style="font-size:1.35rem; font-weight:800; color:#111;"><?= htmlspecialchars($item['time'], ENT_QUOTES, 'UTF-8') ?></div>
            <div style="font-size:1.05rem; font-weight:700; margin-top:2px;"><?= htmlspecialchars($item['client'], ENT_QUOTES, 'UTF-8') ?></div>
            <div style="color:#555; font-size:0.9rem; margin-top:4px;">
              <?= htmlspecialchars((string) ($b['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
              <?php if (!empty($b['phone'])): ?>
                · <?= htmlspecialchars((string) $b['phone'], ENT_QUOTES, 'UTF-8') ?>
              <?php endif; ?>
            </div>
            <div style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;">
              <span style="font-size:0.72rem; background:#eceff1; color:#37474f; padding:3px 8px; border-radius:999px; font-weight:700;">
                <?= htmlspecialchars($sourceLabels[$src] ?? $src, ENT_QUOTES, 'UTF-8') ?>
              </span>
              <span style="font-size:0.72rem; background:#e3f2fd; color:#0d47a1; padding:3px 8px; border-radius:999px; font-weight:700;">
                <?= htmlspecialchars($statusLabels[$status] ?? $status, ENT_QUOTES, 'UTF-8') ?>
              </span>
              <span style="font-size:0.72rem; color:#90a4ae;">#<?= (int) $b['id'] ?></span>
            </div>
          </div>
          <div style="display:flex; flex-direction:column; gap:8px; min-width:140px;">
            <?php if ($status !== 'present'): ?>
            <form method="post" action="<?= htmlspecialchars(aquavelo_admin_checkin_url(['day' => $dayParam]), ENT_QUOTES, 'UTF-8') ?>" style="margin:0;">
              <input type="hidden" name="token" value="<?= htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="day" value="<?= htmlspecialchars($dayParam, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="booking_id" value="<?= (int) $b['id'] ?>">
              <input type="hidden" name="action_attendance" value="present">
              <button type="submit" style="width:100%; background:#2e7d32; color:#fff; border:none; border-radius:8px; padding:12px 14px; font-weight:800; font-size:0.95rem;">✓ PRÉSENT</button>
            </form>
            <?php endif; ?>
            <?php if ($status !== 'noshow'): ?>
            <form method="post" action="<?= htmlspecialchars(aquavelo_admin_checkin_url(['day' => $dayParam]), ENT_QUOTES, 'UTF-8') ?>" style="margin:0;">
              <input type="hidden" name="token" value="<?= htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="day" value="<?= htmlspecialchars($dayParam, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="booking_id" value="<?= (int) $b['id'] ?>">
              <input type="hidden" name="action_attendance" value="noshow">
              <button type="submit" style="width:100%; background:#fff; color:#c62828; border:1px solid #ef9a9a; border-radius:8px; padding:10px 14px; font-weight:700;">No-show</button>
            </form>
            <?php endif; ?>
            <?php if ($status !== 'pending'): ?>
            <form method="post" action="<?= htmlspecialchars(aquavelo_admin_checkin_url(['day' => $dayParam]), ENT_QUOTES, 'UTF-8') ?>" style="margin:0;">
              <input type="hidden" name="token" value="<?= htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="day" value="<?= htmlspecialchars($dayParam, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="booking_id" value="<?= (int) $b['id'] ?>">
              <input type="hidden" name="action_attendance" value="pending">
              <button type="submit" style="width:100%; background:transparent; color:#607d8b; border:none; padding:6px; font-size:0.8rem;">Remettre en attente</button>
            </form>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!empty($item['previous'])):
          $p0 = $item['previous'][0];
          $prevWhen = '';
          if (function_exists('aquavelo_gc_parse_rdv_from_name')) {
              $parsedPrev = aquavelo_gc_parse_rdv_from_name((string) ($p0['name'] ?? ''));
              if ($parsedPrev) {
                  $prevWhen = $parsedPrev['date'] . ' à ' . $parsedPrev['time'];
              }
          }
          if ($prevWhen === '') {
              $partsPrev = explode('(RDV:', (string) ($p0['name'] ?? ''), 2);
              if (isset($partsPrev[1])) {
                  $prevWhen = trim($partsPrev[1]);
                  if (substr($prevWhen, -1) === ')') {
                      $prevWhen = trim(substr($prevWhen, 0, -1));
                  }
              }
          }
        ?>
        <div style="margin-top:12px; padding:10px 12px; background:#fff3e0; border:1px solid #ffb74d; border-radius:8px; color:#e65100; font-weight:700;">
          ⚠️ Déjà venu<?= $prevWhen !== '' ? ' le ' . htmlspecialchars($prevWhen, ENT_QUOTES, 'UTF-8') : '' ?>
          <?php if (count($item['previous']) > 1): ?>
            <span style="font-weight:500;"> (<?= count($item['previous']) ?> passages validés)</span>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
