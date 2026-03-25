-- Prénom / nom pour pré-remplissage page de paiement (exécuter une fois sur la BDD existante).
ALTER TABLE reglement_lien
  ADD COLUMN prenom_client VARCHAR(100) DEFAULT NULL AFTER libelle_client,
  ADD COLUMN nom_client VARCHAR(100) DEFAULT NULL AFTER prenom_client;
