<?php
/**
 * Page de Calendrier locale pour Cannes, Mandelieu, Vallauris
 * Version avec restriction 9h45 (actuel) et 10h00 (février)
 */

require '_settings.php';

$center_id = isset($_GET['center']) ? (int)$_GET['center'] : 305;

// Récupérer les infos du centre dynamiquement
$stmt_center = $database->prepare("SELECT city, address, phone FROM am_centers WHERE id = ?");
$stmt_center->execute([$center_id]);
$row_c = $stmt_center->fetch();

$centers_info = [
    305 => ['city' => 'Cannes', 'addr' => '60 avenue du Docteur Raymond Picaud', 'tel' => '04 93 93 05 65'],
    347 => ['city' => 'Mandelieu', 'addr' => 'Avenue de Fréjus', 'tel' => '04 93 93 05 65'],
    349 => ['city' => 'Vallauris', 'addr' => 'Chemin de Saint-Bernard', 'tel' => '04 93 93 05 65'],
    343 => ['city' => 'Mérignac', 'addr' => $row_c['address'] ?? 'Centre Mérignac', 'tel' => $row_c['phone'] ?? '05 56 00 00 00'],
    253 => ['city' => 'Antibes', 'addr' => '1730 Chemin des Terriers', 'tel' => '04 93 74 97 99']
];
$current_center = isset($centers_info[$center_id]) ? $centers_info[$center_id] : $centers_info[305];

// --- LOGIQUE DES CRÉNEAUX ---

// 1. Planning CANNES/MANDELIEU/VALLAURIS
$old_creneaux_semaine = ['09:45', '11:00', '12:15', '13:30', '14:45', '16:00', '17:15', '18:30'];
$old_creneaux_samedi  = ['09:45', '11:00', '12:15', '13:30'];
$old_special_activities = [
    'Lundi'    => ['13:30' => 'AQUAGYM'],
    'Mardi'    => ['13:30' => 'AQUABOXING', '16:00' => 'AQUAGYM'],
    'Mercredi' => ['14:45' => 'AQUAGYM'],
    'Jeudi'    => ['14:45' => 'AQUAGYM'],
    'Vendredi' => ['17:15' => 'AQUAGYM'],
    'Samedi'   => ['13:30' => 'AQUAGYM'],
];

// 2. Planning MÉRIGNAC (ID: 343)
$merignac_creneaux_semaine = ['09:30', '10:30', '11:30', '12:30', '16:30', '17:30', '18:30', '19:30'];
$merignac_creneaux_samedi  = ['09:30', '10:30', '11:30'];

// 3. Planning ANTIBES (ID: 253)
$antibes_planning = [
    'Lundi'    => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAGYM','14:45' => 'AQUAVELO','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO','19:45' => 'AQUAVELO'],
    'Mardi'    => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUABOXING','14:45' => 'AQUAVELO','16:00' => 'AQUAGYM','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO','19:45' => 'AQUAVELO'],
    'Mercredi' => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAGYM','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO','19:45' => 'AQUAVELO'],
    'Jeudi'    => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAGYM','16:00' => 'AQUAGYM','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO','19:45' => 'AQUAVELO'],
    'Vendredi' => ['10:15' => 'AQUAGYM','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAVELO','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO','19:45' => 'AQUAVELO'],
    'Samedi'   => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:15' => 'AQUAGYM']
];

// 4. NOUVEAU PLANNING (à partir du 1er Février) - Uniquement pour Cannes Group pour l'instant
$new_planning = [
    'Lundi' => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAGYM','14:45' => 'AQUAVELO','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
    'Mardi' => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUABOXING','14:45' => 'AQUAVELO','16:00' => 'AQUAGYM','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
    'Mercredi' => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAGYM','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
    'Jeudi' => ['09:45' => 'AQUAVELO','11:00' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAGYM','16:00' => 'AQUAVELO','17:15' => 'AQUAVELO','18:30' => 'AQUAVELO'],
    'Vendredi' => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:30' => 'AQUAVELO','14:45' => 'AQUAVELO','16:00' => 'AQUAVELO','17:15' => 'AQUAGYM','18:30' => 'AQUAVELO'],
    'Samedi' => ['10:15' => 'AQUAVELO','11:15' => 'AQUAVELO','12:15' => 'AQUAVELO','13:15' => 'AQUAGYM']
];

// Récupérer les réservations
$bookings_query = $database->prepare("SELECT name FROM am_free WHERE center_id IN (305, 347, 349, 343, 253) AND name LIKE '%(RDV:%'");
$bookings_query->execute();
$existing_bookings = $bookings_query->fetchAll(PDO::FETCH_COLUMN);

function isSlotTaken($date, $hour, $existing_bookings) {
    $search = $date . " à " . $hour;
    foreach ($existing_bookings as $booking) {
        if (strpos($booking, $search) !== false) return true;
    }
    return false;
}

$calendar = [];
$today = new DateTime();
$switch_date = new DateTime('2026-02-01');

for ($i = 0; $i < 21; $i++) {
    $date = clone $today;
    $date->modify("+$i day");
    $day_name_en = $date->format('l');
    $day_num = (int)$date->format('N'); // 1 (Lundi) à 7 (Dimanche)
    
    // On saute les dimanches (Pas de RDV le dimanche)
    if ($day_num === 7) continue;

    $days_fr = ['Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'];
    $day_fr = $days_fr[$day_name_en];

    $current_slots = [];
    if ($center_id == 343) {
        // ⭐ MÉRIGNAC
        $times = ($day_num == 6) ? $merignac_creneaux_samedi : $merignac_creneaux_semaine;
        foreach ($times as $t) {
            $current_slots[] = ['time' => $t, 'activity' => 'AQUAVELO'];
        }
    } elseif ($center_id == 253) {
        // ⭐ ANTIBES
        if (isset($antibes_planning[$day_fr])) {
            foreach ($antibes_planning[$day_fr] as $h => $act) {
                $current_slots[] = ['time' => $h, 'activity' => $act];
            }
        }
    } elseif ($date >= $switch_date) {
        // ⭐ FUTUR (Février) : Uniquement Cannes Group
        if (isset($new_planning[$day_fr])) {
            foreach ($new_planning[$day_fr] as $h => $act) {
                $current_slots[] = ['time' => $h, 'activity' => $act];
            }
        }
    } else {
        // ⭐ ACTUEL : Restriction 9h45
        $times = ($day_num == 6) ? $old_creneaux_samedi : $old_creneaux_semaine;
        foreach ($times as $t) {
            if ($t >= '09:45') {
                $act = $old_special_activities[$day_fr][$t] ?? 'AQUAVELO';
                $current_slots[] = ['time' => $t, 'activity' => $act];
            }
        }
    }

    if (!empty($current_slots)) {
        $calendar[] = ['full_date' => $date->format('d/m/Y'), 'day_name' => $day_fr, 'slots' => $current_slots];
    }
}
?>

<section class="content-area brightText" data-bg="images/content/about-v2-title-bg.jpg" style="min-height: 150px; padding: 30px 0;">
  <div class="container">
    <h1 style="color: white; text-align: center; font-size: 1.8rem;">📅 Réservation Aquavelo <?= $current_center['city'] ?></h1>
    <p style="color: white; text-align: center; font-size: 1.1rem;">Cliquez sur le créneau de votre choix pour réserver votre séance gratuite</p>
  </div>
</section>

<section class="content-area bg1" style="padding: 30px 0 100px 0;">
  <div class="container">
    <div style="max-width: 1100px; margin: 0 auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      <div id="loading-overlay" style="display: none; text-align: center; margin-bottom: 20px; padding: 20px; background: #e8f8fc; border-radius: 10px;">
        <h3 style="color: #00a8cc;"><i class="fa fa-spinner fa-spin"></i> Enregistrement en cours...</h3>
      </div>

      <div style="display: flex; overflow-x: auto; gap: 15px; padding-bottom: 25px;" class="date-selector">
        <?php foreach ($calendar as $day) : ?>
          <div style="min-width: 160px; border: 1px solid #eee; border-radius: 12px; padding: 12px; background: #fafafa;">
            <div style="text-align: center; font-weight: bold; color: #00a8cc; border-bottom: 2px solid #00a8cc; padding-bottom: 8px; margin-bottom: 12px;">
                <?= $day['day_name'] ?> <br> <small style="color: #666;"><?= $day['full_date'] ?></small>
            </div>
            <div class="slots">
              <?php foreach ($day['slots'] as $s) : 
                $taken = isSlotTaken($day['full_date'], $s['time'], $existing_bookings);
                $act_color = ($s['activity'] == 'AQUABOXING') ? '#e91e63' : (($s['activity'] == 'AQUAGYM') ? '#9c27b0' : '#00a8cc');
              ?>
                <button type="button" class="slot-btn" <?= $taken ? 'disabled' : '' ?>
                        data-date="<?= $day['full_date'] ?>" data-dayname="<?= $day['day_name'] ?>"
                        data-hour="<?= $s['time'] ?>" data-activity="<?= $s['activity'] ?>"
                        onclick="confirmBooking(this)"
                        style="width: 100%; margin-bottom: 8px; padding: 10px 5px; border: 1px solid <?= $taken ? '#ddd' : '#00a8cc' ?>; background: <?= $taken ? '#f0f0f0' : 'white' ?>; color: <?= $taken ? '#bbb' : '#00a8cc' ?>; border-radius: 8px; cursor: <?= $taken ? 'not-allowed' : 'pointer' ?>; font-size: 0.95rem; font-weight: bold;">
                  <?= $s['time'] ?> <?= $taken ? '<br><small>(Réservé)</small>' : '' ?>
                  <?php if (!$taken): ?>
                    <div style="font-size: 9px; color: <?= $act_color ?>; margin-top: 3px;"><?= $s['activity'] ?></div>
                  <?php endif; ?>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <form id="calendrierForm" method="POST" action="<?= BASE_PATH ?>index.php?p=free">
        <input type="hidden" name="reason" value=""><input type="hidden" name="center" value="<?= $center_id ?>">
        <input type="hidden" name="segment" value="calendrier-cannes"><input type="hidden" id="selected_date_heure" name="date_heure">
        <input type="hidden" name="nom" value="<?= htmlspecialchars($_GET['nom'] ?? 'Client Web') ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
        <input type="hidden" name="phone" value="<?= htmlspecialchars($_GET['phone'] ?? '') ?>">
        <input type="hidden" name="old_rdv" value="<?= htmlspecialchars($_GET['old_rdv'] ?? '') ?>">
      </form>
    </div>
  </div>
</section>

<script>
function confirmBooking(btn) {
    document.getElementById('loading-overlay').style.display = 'block';
    document.getElementById('loading-overlay').scrollIntoView({ behavior: 'smooth' });
    document.querySelectorAll('.slot-btn').forEach(b => b.disabled = true);
    document.getElementById('selected_date_heure').value = btn.getAttribute('data-dayname') + " " + btn.getAttribute('data-date') + " à " + btn.getAttribute('data-hour') + " (" + btn.getAttribute('data-activity') + ")";
    setTimeout(() => { document.getElementById('calendrierForm').submit(); }, 500);
}
</script>

<style>
.slot-btn:not(:disabled):hover { background: #e8f8fc !important; transform: scale(1.02); }
.date-selector::-webkit-scrollbar { height: 8px; }
.date-selector::-webkit-scrollbar-thumb { background: #00a8cc; border-radius: 10px; }
</style>
