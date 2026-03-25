-- Liens de paiement personnalisés (impayés / règlements)
-- À exécuter sur la base MySQL de production (phpMyAdmin / Clever Cloud).

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
