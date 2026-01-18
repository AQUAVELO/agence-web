<?php
/**
 * Page de Calendrier locale pour Cannes, Mandelieu, Vallauris
 * Version avec bascule automatique au 1er Février 2026
 */

require '_settings.php';

$center_id = isset($_GET['center']) ? (int)$_GET['center'] : 305;
$centers_info = [
    305 => ['city' => 'Cannes', 'addr' => '60 avenue du Docteur Raymond Picaud', 'tel' => '04 93 93 05 65'],
    347 => ['city' => 'Mandelieu', 'addr' => 'Avenue de Fréjus', 'tel' => '04 93 93 05 65'],
    349 => ['city' => 'Vallauris', 'addr' => 'Chemin de Saint-Bernard', 'tel' => '04 93 93 05 65']
];
$current_center = isset($centers_info[$center_id]) ? $centers_info[$center_id] : $centers_info[305];

// --- LOGIQUE DES CRÉNEAUX ---

// 1. Planning ACTUEL (jusqu'au 31 janvier)
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

// 2. NOUVEAU PLANNING (à partir du 1er Février)
$new_planning = [
    'Lundi' => [
        '08:30' => 'AQUAVELO', '09:45' => 'AQUAVELO', '11:00' => 'AQUAVELO', '12:15' => 'AQUAVELO', 
        '13:30' => 'AQUAGYM', '14:45' => 'AQUAVELO', '16:00' => 'AQUAVELO', '17:15' => 'AQUAVELO', 
        '18:30' => 'AQUAVELO', '19:45' => 'AQUAGYM'
    ],
    'Mardi' => [
        '08:30' => 'AQUAVELO', '09:45' => 'AQUAVELO', '11:00' => 'AQUAVELO', '12:15' => 'AQUAVELO', 
        '13:30' => 'AQUABOXING', '14:45' => 'AQUAVELO', '16:00' => 'AQUAGYM', '17:15' => 'AQUAVELO', 
        '18:30' => 'AQUAVELO', '19:45' => 'AQUAVELO'
    ],
    'Mercredi' => [
        '08:15' => 'AQUAVELO', '09:15' => 'AQUAGYM', '10:15' => 'AQUAVELO', '11:15' => 'AQUAVELO', 
        '12:15' => 'AQUAVELO', '13:30' => 'AQUAVELO', '14:45' => 'AQUAGYM', '16:00' => 'AQUAVELO', 
        '17:15' => 'AQUAVELO', '18:30' => 'AQUAVELO', '19:45' => 'AQUAVELO'
    ],
    'Jeudi' => [
        '08:30' => 'AQUAVELO', '09:45' => 'AQUAVELO', '11:00' => 'AQUAVELO', '12:15' => 'AQUAVELO', 
        '13:30' => 'AQUAVELO', '14:45' => 'AQUAGYM', '16:00' => 'AQUAVELO', '17:15' => 'AQUAVELO', 
        '18:30' => 'AQUAVELO', '19:45' => 'AQUAVELO'
    ],
    'Vendredi' => [
        '08:15' => 'AQUAVELO', '09:15' => 'AQUAGYM', '10:15' => 'AQUAVELO', '11:15' => 'AQUAVELO', 
        '12:15' => 'AQUAVELO', '13:30' => 'AQUAVELO', '14:45' => 'AQUAVELO', '16:00' => 'AQUAVELO', 
        '17:15' => 'AQUAGYM', '18:30' => 'AQUAVELO', '19:45' => 'AQUABOXING'
    ],
    'Samedi' => [
        '08:15' => 'AQUAVELO', '09:15' => 'AQUAGYM', '10:15' => 'AQUAVELO', '11:15' => 'AQUAVELO', 
        '12:15' => 'AQUAVELO', '13:15' => 'AQUAGYM'
    ],
    'Dimanche' => [
        '08:00' => 'AQUAVELO', '09:00' => 'AQUAGYM', '10:00' => 'AQUAVELO', '11:00' => 'AQUAVELO', 
        '12:00' => 'AQUAVELO'
    ]
];

// Récupérer les réservations
$bookings_query = $database->prepare("SELECT name FROM am_free WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%'");
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

for ($i = 0; $i < 14; $i++) {
    $date = clone $today;
    $date->modify("+$i day");
    $day_name_en = $date->format('l');
    $day_num = $date->format('N');
    
    // Traduction jour
    $days_fr = ['Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'];
    $day_fr = $days_fr[$day_name_en];

    // Choix du planning selon la date
    $current_slots = [];
    if ($date >= $switch_date) {
        // NOUVEAU PLANNING
        foreach ($new_planning[$day_fr] as $h => $act) {
            $current_slots[] = ['time' => $h, 'activity' => $act];
        }
    } else {
        // ANCIEN PLANNING (Pas de Dimanche)
        if ($day_num <= 6) {
            $times = ($day_num == 6) ? $old_creneaux_samedi : $old_creneaux_semaine;
            foreach ($times as $t) {
                $act = $old_special_activities[$day_fr][$t] ?? 'AQUAVELO';
                $current_slots[] = ['time' => $t, 'activity' => $act];
            }
        }
    }

    if (!empty($current_slots)) {
        $calendar[] = [
            'full_date' => $date->format('d/m/Y'),
            'day_name'  => $day_fr,
            'slots'     => $current_slots
        ];
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

      <form id="calendrierForm" method="POST" action="index.php?p=free">
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
