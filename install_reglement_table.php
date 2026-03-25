<?php
/**
 * Création one-shot de la table reglement_lien.
 * Ouvrir UNE FOIS : https://www.aquavelo.com/install_reglement_table.php?key=aquavelo-reglement-sql-2026
 * Puis SUPPRIMER ce fichier du serveur.
 */

$expected = getenv('AQUAVELO_INSTALL_REGLEMENT_KEY') ?: 'aquavelo-reglement-sql-2026';
if (!isset($_GET['key']) || $_GET['key'] !== $expected) {
    http_response_code(403);
    die('Accès non autorisé.');
}

require_once __DIR__ . '/_settings.php';

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Install reglement_lien</title></head><body style="font-family:sans-serif;max-width:640px;margin:40px auto;padding:20px;">';

$sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS reglement_lien (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  token VARCHAR(64) NOT NULL,
  libelle_client VARCHAR(255) NOT NULL,
  motif TEXT NOT NULL,
  montant DECIMAL(10,2) NOT NULL,
  email_client VARCHAR(255) DEFAULT NULL,
  telephone_client VARCHAR(50) DEFAULT NULL,
  statut ENUM('en_attente','paye','expire','annule') NOT NULL DEFAULT 'en_attente',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  paid_at DATETIME DEFAULT NULL,
  monetico_reference VARCHAR(64) DEFAULT NULL,
  expires_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_token (token),
  KEY idx_statut (statut),
  KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;

try {
    $conn->exec($sql);
    echo '<h1 style="color:green;">OK</h1><p>La table <code>reglement_lien</code> a été créée (ou existait déjà).</p>';
    echo '<p><a href="index.php?p=admin_reglements">Ouvrir l’admin liens de règlement</a></p>';
} catch (PDOException $e) {
    http_response_code(500);
    echo '<h1 style="color:red;">Erreur</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}

echo '<hr><p style="color:#c62828;font-weight:bold;">Supprimez le fichier <code>install_reglement_table.php</code> du serveur maintenant.</p></body></html>';
