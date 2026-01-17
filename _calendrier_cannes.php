<?php
/**
 * Page de Calendrier locale pour Cannes, Mandelieu, Vallauris
 */

require '_settings.php';

// Déterminer le centre (Cannes par défaut)
$center_id = isset($_GET['center']) ? (int)$_GET['center'] : 305;
$centers_info = [
    305 => ['city' => 'Cannes', 'addr' => '60 avenue du Docteur Raymond Picaud', 'tel' => '04 93 93 05 65'],
    347 => ['city' => 'Mandelieu', 'addr' => 'Avenue de Fréjus', 'tel' => '04 93 93 05 65'],
    349 => ['city' => 'Vallauris', 'addr' => 'Chemin de Saint-Bernard', 'tel' => '04 93 93 05 65']
];

$current_center = isset($centers_info[$center_id]) ? $centers_info[$center_id] : $centers_info[305];

// Configuration des créneaux (Transition 9h45)
$creneaux_semaine = ['09:45', '11:00', '12:15', '13:30', '14:45', '16:00', '17:15', '18:30'];
$creneaux_samedi  = ['09:45', '11:00', '12:15', '13:30'];

$special_activities = [
    'Lundi'    => ['13:30' => 'AQUAGYM'],
    'Mardi'    => ['13:30' => 'AQUABOXING', '16:00' => 'AQUAGYM'],
    'Mercredi' => ['14:45' => 'AQUAGYM'],
    'Jeudi'    => ['14:45' => 'AQUAGYM'],
    'Vendredi' => ['17:15' => 'AQUAGYM'],
    'Samedi'   => ['13:30' => 'AQUAGYM'],
];

// Récupérer les réservations pour les centres qui partagent le même planning (Cannes, Mandelieu, Vallauris)
$centers_shared = [305, 347, 349];
if (in_array($center_id, $centers_shared)) {
    $bookings_query = $database->prepare("SELECT name FROM am_free WHERE center_id IN (305, 347, 349) AND name LIKE '%(RDV:%'");
    $bookings_query->execute();
} else {
    $bookings_query = $database->prepare("SELECT name FROM am_free WHERE center_id = ? AND name LIKE '%(RDV:%'");
    $bookings_query->execute([$center_id]);
}
$existing_bookings = $bookings_query->fetchAll(PDO::FETCH_COLUMN);

function isSlotTaken($date, $hour, $existing_bookings) {
    // On cherche la date et l'heure dans la chaîne (ex: "20/01/2026 à 13:30")
    $search = $date . " à " . $hour;
    foreach ($existing_bookings as $booking) {
        if (strpos($booking, $search) !== false) return true;
    }
    return false;
}

$calendar = [];
$today = new DateTime();
for ($i = 0; $i < 14; $i++) {
    $date = clone $today;
    $date->modify("+$i day");
    $day_num = $date->format('N');
    if ($day_num <= 6) {
        $calendar[] = [
            'full_date' => $date->format('d/m/Y'),
            'day_name'  => ($day_num == 6) ? 'Samedi' : ($day_num == 1 ? 'Lundi' : ($day_num == 2 ? 'Mardi' : ($day_num == 3 ? 'Mercredi' : ($day_num == 4 ? 'Jeudi' : 'Vendredi')))),
            'slots'     => ($day_num == 6) ? $creneaux_samedi : $creneaux_semaine
        ];
    }
}
?>

<section class="content-area brightText" data-bg="images/content/about-v2-title-bg.jpg" data-topspace="30" data-btmspace="20" style="min-height: 150px;">
  <div class="container">
    <h1 style="color: white; text-align: center; font-size: 1.5rem; margin: 20px 0;">
      📅 Planning Aquavelo <?= $current_center['city'] ?>
    </h1>
    <p style="color: white; text-align: center;">Cliquez simplement sur le créneau de votre choix</p>
  </div>
</section>

<section class="content-area bg1" style="padding: 20px 0 100px 0;">
  <div class="container">
    <div style="max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.1);">
      
      <div id="loading-overlay" style="display: none; text-align: center; margin-bottom: 20px; padding: 20px; background: #e8f8fc; border-radius: 10px;">
        <h3 style="color: #00a8cc;"><i class="fa fa-spinner fa-spin"></i> Enregistrement de votre séance...</h3>
      </div>

      <div style="display: flex; overflow-x: auto; gap: 15px; padding-bottom: 20px;" class="date-selector">
        <?php foreach ($calendar as $day) : ?>
          <div class="day-card" style="min-width: 140px; border: 1px solid #eee; border-radius: 10px; padding: 10px; background: #f9f9f9;">
            <div style="text-align: center; font-weight: bold; color: #00a8cc; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px;">
                <?= $day['day_name'] ?> <br> <small style="color: #666;"><?= $day['full_date'] ?></small>
            </div>
            <div class="slots">
              <?php foreach ($day['slots'] as $slot) : 
                $taken = isSlotTaken($day['full_date'], $slot, $existing_bookings);
                $activity = isset($special_activities[$day['day_name']][$slot]) ? $special_activities[$day['day_name']][$slot] : 'AQUAVELO';
                $act_color = ($activity == 'AQUABOXING') ? '#e91e63' : (($activity == 'AQUAGYM') ? '#9c27b0' : '#00a8cc');
              ?>
                <button type="button" 
                        class="slot-btn <?= $taken ? 'taken' : '' ?>" 
                        <?= $taken ? 'disabled' : '' ?>
                        data-date="<?= $day['full_date'] ?>" 
                        data-dayname="<?= $day['day_name'] ?>"
                        data-hour="<?= $slot ?>"
                        data-activity="<?= $activity ?>"
                        onclick="confirmBooking(this)"
                        style="width: 100%; margin-bottom: 5px; padding: 8px 5px; border: 1px solid <?= $taken ? '#ccc' : '#00a8cc' ?>; background: <?= $taken ? '#eee' : 'white' ?>; color: <?= $taken ? '#999' : '#00a8cc' ?>; border-radius: 5px; cursor: <?= $taken ? 'not-allowed' : 'pointer' ?>; font-size: 0.9rem;">
                  <?= $slot ?> <?= $taken ? '(Complet)' : '' ?>
                  <br><span style="font-size: 10px; font-weight: bold; color: <?= $taken ? '#999' : $act_color ?>;"><?= $activity ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <form role="form" id="calendrierForm" method="POST" action="index.php?p=free">
        <input type="hidden" name="reason" value=""> <!-- Ajout du champ reason pour valider le traitement -->
        <input type="hidden" name="center" value="<?= $center_id ?>">
        <input type="hidden" name="segment" value="calendrier-cannes">
        <input type="hidden" id="selected_date_heure" name="date_heure" value="">
        <input type="hidden" name="nom" value="<?= htmlspecialchars($_GET['nom'] ?? 'Client Web') ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
        <input type="hidden" name="phone" value="<?= htmlspecialchars($_GET['phone'] ?? '') ?>">
      </form>

    </div>
  </div>
</section>

<script>
function confirmBooking(btn) {
    if (btn.classList.contains('taken')) return;
    document.getElementById('loading-overlay').style.display = 'block';
    document.getElementById('loading-overlay').scrollIntoView({ behavior: 'smooth' });
    document.querySelectorAll('.slot-btn').forEach(b => b.disabled = true);
    const date = btn.getAttribute('data-date');
    const dayName = btn.getAttribute('data-dayname');
    const hour = btn.getAttribute('data-hour');
    const activity = btn.getAttribute('data-activity');
    
    // Remplir le champ caché : format "Mardi 20/01/2026 à 13:30 (AQUABOXING)"
    document.getElementById('selected_date_heure').value = dayName + " " + date + " à " + hour + " (" + activity + ")";
    
    // Envoyer le formulaire automatiquement
    setTimeout(function() {
        document.getElementById('calendrierForm').submit();
    }, 500);
}
</script>

<style>
.slot-btn:not(.taken):hover { background: #e8f8fc !important; transform: translateY(-2px); box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.date-selector::-webkit-scrollbar { height: 8px; }
.date-selector::-webkit-scrollbar-thumb { background: #00a8cc; border-radius: 10px; }
</style>
