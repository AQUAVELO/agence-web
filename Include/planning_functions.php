<?php
/**
 * Données des plannings par centre
 */

function getPlanningData($center_id) {
    $plannings = [
        // Centre d'Antibes (ou Cannes selon l'image, on adapte pour Antibes ici)
        253 => [
            'title' => 'PLANNING AQUAVÉLO ANTIBES',
            'phone' => '04 93 93 95 65',
            'duration' => '45min',
            'coachs_count' => '9 Coachs à votre disposition',
            'days' => ['LUNDI', 'MARDI', 'MERCREDI', 'JEUDI', 'VENDREDI', 'SAMEDI', 'DIMANCHE'],
            'hours' => ['8h30', '9h45', '11h00', '12h15', '13h30', '14h45', '16h00', '17h15', '18h30', '19h45'],
            'slots' => [
                // [Heure][Jour] => ['coach' => 'NOM', 'class' => 'coach-nom', 'activity' => 'NOM']
                '8h30' => [
                    'LUNDI' => ['coach' => 'JEROME', 'class' => 'coach-jerome'],
                    'MARDI' => ['coach' => 'CORALINE', 'class' => 'coach-coraline'],
                    'MERCREDI' => ['coach' => 'JUSTINE', 'class' => 'coach-justine'],
                    'JEUDI' => ['coach' => 'THOMAS', 'class' => 'coach-thomas'],
                    'VENDREDI' => ['coach' => 'JUSTINE', 'class' => 'coach-justine'],
                    'SAMEDI' => ['coach' => 'KEVIN', 'class' => 'coach-kevin'],
                    'DIMANCHE' => ['coach' => 'FREDERIC', 'class' => 'coach-frederic'],
                ],
                '9h45' => [
                    'LUNDI' => ['coach' => 'JEROME', 'class' => 'coach-jerome'],
                    'MARDI' => ['coach' => 'CORALINE', 'class' => 'coach-coraline'],
                    'MERCREDI' => ['coach' => 'JUSTINE', 'class' => 'coach-justine'],
                    'JEUDI' => ['coach' => 'THOMAS', 'class' => 'coach-thomas'],
                    'VENDREDI' => ['coach' => 'JUSTINE', 'class' => 'coach-justine'],
                    'SAMEDI' => ['coach' => 'KEVIN', 'class' => 'coach-kevin'],
                    'DIMANCHE' => ['coach' => 'FREDERIC', 'class' => 'coach-frederic'],
                ],
                '13h30' => [
                    'LUNDI' => ['coach' => 'JUSTINE', 'class' => 'coach-justine', 'activity' => 'AQUAGYM'],
                    'MARDI' => ['coach' => 'MYLENE', 'class' => 'coach-mylene', 'activity' => 'AQUABOXING'],
                    'MERCREDI' => ['coach' => 'JUSTINE', 'class' => 'coach-justine'],
                    'JEUDI' => ['coach' => 'RACHEL', 'class' => 'coach-rachel'],
                    'VENDREDI' => ['coach' => 'JUSTINE', 'class' => 'coach-justine'],
                    'SAMEDI' => ['coach' => 'RACHEL', 'class' => 'coach-rachel', 'activity' => 'AQUAGYM'],
                ],
                // ... les autres créneaux seront ajoutés de la même manière
            ]
        ]
    ];

    return isset($plannings[$center_id]) ? $plannings[$center_id] : null;
}

function renderPlanningTable($center_id) {
    $data = getPlanningData($center_id);
    if (!$data) return '';

    ob_start();
    ?>
    <div class="planning-container">
        <div class="planning-header">
            <div>
                <p class="planning-info">Durée des cours : <?= $data['duration'] ?></p>
                <p class="planning-info"><?= $data['coachs_count'] ?></p>
            </div>
            <div class="text-center">
                <h2 class="planning-title-main"><?= $data['title'] ?></h2>
                <p style="color: #00a8cc; font-weight: bold; margin-top: 5px;">PLANNING DES COURS</p>
            </div>
            <div class="text-right">
                <p class="planning-info">Tél : <?= $data['phone'] ?></p>
            </div>
        </div>

        <table class="planning-table">
            <thead>
                <tr>
                    <th class="planning-time"></th>
                    <?php foreach ($data['days'] as $day) : ?>
                        <th><?= $day ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['hours'] as $hour) : ?>
                <tr>
                    <td class="planning-time"><?= $hour ?></td>
                    <?php foreach ($data['days'] as $day) : ?>
                        <td>
                            <?php if (isset($data['slots'][$hour][$day])) : 
                                $slot = $data['slots'][$hour][$day];
                            ?>
                                <?php if (isset($slot['activity'])) : ?>
                                    <span class="slot-activity"><?= $slot['activity'] ?></span>
                                <?php endif; ?>
                                <span class="slot-coach <?= $slot['class'] ?>"><?= $slot['coach'] ?></span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="planning-footer text-center">
            <p>Pour votre séance prévoir votre maillot de bain, serviette de bain, gel douche, cadenas, bouteille d'eau, les chaussures de piscine et prenez la douche obligatoire avant de rentrer dans la piscine.</p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
