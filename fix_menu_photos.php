<?php
require_once __DIR__ . '/settings.php';

$database = $conn;
$apply = isset($_GET['apply']);

$recette_dir = __DIR__ . '/images/recette/';
$existing_files = [];
if (is_dir($recette_dir)) {
    foreach (scandir($recette_dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $existing_files[] = $f;
    }
}

$keywords_map = [];
foreach ($existing_files as $file) {
    $name = pathinfo($file, PATHINFO_FILENAME);
    $words = preg_split('/[_\-\s]+/', strtolower($name));
    $keywords_map[$file] = $words;
}

function findBestMatch($db_value, $existing_files, $keywords_map, $recette_dir) {
    $basename = basename($db_value);

    if (file_exists($recette_dir . $basename)) return $basename;

    $target_lower = strtolower($basename);
    foreach ($existing_files as $f) {
        if (strtolower($f) === $target_lower) return $f;
    }

    $target_name = pathinfo($basename, PATHINFO_FILENAME);
    $target_words = preg_split('/[_\-\s]+/', strtolower($target_name));

    $best_file = null;
    $best_score = 0;

    foreach ($keywords_map as $file => $words) {
        $common = count(array_intersect($target_words, $words));
        $total = count(array_unique(array_merge($target_words, $words)));
        $jaccard = $total > 0 ? $common / $total : 0;

        similar_text(strtolower($target_name), strtolower(pathinfo($file, PATHINFO_FILENAME)), $pct);
        $score = ($jaccard * 60) + ($pct * 0.4);

        if ($score > $best_score) {
            $best_score = $score;
            $best_file = $file;
        }
    }

    return ($best_score > 15) ? $best_file : null;
}

$photo_cols = ['photo_pet_dej', 'photo_repas_midi', 'photo_souper', 'photo_collation'];

$all_rows = $database->query("SELECT id, day_number, photo_pet_dej, photo_repas_midi, photo_souper, photo_collation FROM menu ORDER BY day_number ASC")->fetchAll(PDO::FETCH_ASSOC);

$updates = [];
foreach ($all_rows as $row) {
    foreach ($photo_cols as $col) {
        $val = $row[$col] ?? '';
        if (empty($val)) continue;

        $basename = basename($val);
        $full_path = $recette_dir . $basename;
        $exists = file_exists($full_path);

        if (!$exists) {
            foreach ($existing_files as $ef) {
                if (strtolower($ef) === strtolower($basename)) { $exists = true; break; }
            }
        }

        if (!$exists) {
            $match = findBestMatch($val, $existing_files, $keywords_map, $recette_dir);
            if ($match) {
                $updates[] = [
                    'id' => $row['id'],
                    'day' => $row['day_number'],
                    'col' => $col,
                    'old' => $val,
                    'new' => 'recette/' . $match
                ];
            } else {
                $updates[] = [
                    'id' => $row['id'],
                    'day' => $row['day_number'],
                    'col' => $col,
                    'old' => $val,
                    'new' => null
                ];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Fix Menu Photos</title>
<style>
body { font-family: monospace; background: #1a1a2e; color: #eee; padding: 30px; }
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
th, td { border: 1px solid #444; padding: 8px 12px; text-align: left; font-size: 13px; }
th { background: #16213e; color: #00d4ff; }
.old { color: #ff6b6b; }
.new { color: #51cf66; }
.none { color: #868e96; font-style: italic; }
.ok { color: #51cf66; }
h1 { color: #00d4ff; }
a.btn { display: inline-block; padding: 15px 30px; background: #51cf66; color: #000; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; margin: 20px 0; }
a.btn:hover { background: #40c057; }
.stats { background: #16213e; padding: 15px 25px; border-radius: 10px; margin: 15px 0; }
</style></head><body>
<h1>🔧 Correction Photos Menus</h1>

<div class="stats">
📁 <strong><?= count($existing_files) ?></strong> fichiers dans images/recette/<br>
📋 <strong><?= count($all_rows) ?></strong> jours dans la table menu<br>
⚠️ <strong><?= count($updates) ?></strong> photos à corriger
</div>

<?php if (empty($updates)): ?>
<p class="ok">✅ Toutes les photos sont correctes !</p>

<?php elseif ($apply): ?>
<h2>Application des corrections...</h2>
<table>
<tr><th>Jour</th><th>Champ</th><th>Ancien</th><th>Nouveau</th><th>Résultat</th></tr>
<?php
$success = 0;
$skipped = 0;
foreach ($updates as $u) {
    echo "<tr><td>{$u['day']}</td><td>{$u['col']}</td><td class='old'>{$u['old']}</td>";
    if ($u['new']) {
        $stmt = $database->prepare("UPDATE menu SET {$u['col']} = :val WHERE id = :id");
        $stmt->execute([':val' => $u['new'], ':id' => $u['id']]);
        echo "<td class='new'>{$u['new']}</td><td class='ok'>✅ OK</td>";
        $success++;
    } else {
        echo "<td class='none'>Aucun match</td><td>⏭️ Ignoré</td>";
        $skipped++;
    }
    echo "</tr>";
}
?>
</table>
<div class="stats">
✅ <strong><?= $success ?></strong> corrections appliquées<br>
⏭️ <strong><?= $skipped ?></strong> ignorées (aucun fichier similaire trouvé)
</div>

<?php else: ?>
<h2>Aperçu des corrections proposées</h2>
<table>
<tr><th>Jour</th><th>Champ</th><th>Ancien (BDD)</th><th>Nouveau proposé</th></tr>
<?php foreach ($updates as $u): ?>
<tr>
    <td><?= $u['day'] ?></td>
    <td><?= $u['col'] ?></td>
    <td class="old"><?= htmlspecialchars($u['old']) ?></td>
    <td class="<?= $u['new'] ? 'new' : 'none' ?>"><?= $u['new'] ? htmlspecialchars($u['new']) : '❌ Aucun match trouvé' ?></td>
</tr>
<?php endforeach; ?>
</table>

<p>Vérifiez les correspondances ci-dessus, puis cliquez pour appliquer :</p>
<a class="btn" href="?apply=1">✅ Appliquer les corrections</a>
<?php endif; ?>

<br><br>
<small style="color:#666;">⚠️ Supprimer ce fichier après utilisation</small>
</body></html>
