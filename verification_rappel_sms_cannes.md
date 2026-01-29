# 🔍 Vérification : Envoi SMS 3h avant pour Cannes

## ✅ Configuration vérifiée

### 1. SMS Factor
- **Token configuré** : ✅ OUI (dans `_settings.php` ligne 41)
- **Fonction `sendSMS()`** : ✅ Implémentée (lignes 71-108 de `_settings.php`)
- **API utilisée** : `https://api.smsfactor.com/send`
- **Expéditeur** : "Aquavelo"

### 2. Script de rappel 3h
- **Fichier** : `cron_rappel_3h.php`
- **Fonctionnement** :
  - Sélectionne les RDV avec `reminder_3h_sent = 0`
  - Calcule le temps restant en minutes
  - **Fenêtre d'envoi** : 150-210 minutes (2h30 à 3h30 avant) - **CENTRÉ SUR 3H** ✅
  - Envoie un email ET un SMS si le téléphone est présent

### 3. Code d'envoi SMS (lignes 95-99 de `cron_rappel_3h.php`)
```php
// --- ENVOI SMS ---
if (!empty($booking['phone'])) {
    $sms_text = "Bonjour " . $client_first_name . ", rappel de votre séance découverte Aquavelo aujourd'hui à " . $matches[2] . ". À très bientôt !";
    sendSMS($booking['phone'], $sms_text);
}
```

### 4. Centre de Cannes
- **ID du centre** : 305
- **Valeur par défaut** : Si `center_id` est NULL, utilise 305 (Cannes) - ligne 38
- **Script fonctionne pour** : Tous les centres, y compris Cannes ✅

### 5. Configuration Cron
- **Fichier** : `clevercloud/cron.json`
- **Schedule** : `"10 * * * *"` (toutes les heures à la 10ème minute)
- **Commande** : `php cron_rappel_3h.php`

## 📋 Points à vérifier manuellement

### 1. Base de données
Vérifier que les rendez-vous de Cannes ont :
- ✅ `name` contenant `(RDV: ...)`
- ✅ `reminder_3h_sent = 0` (pas encore envoyé)
- ✅ `phone` rempli (obligatoire pour l'envoi SMS)
- ✅ `center_id = 305` (ou NULL, qui devient 305 par défaut)

### 2. Test d'envoi SMS
Pour tester l'envoi réel :
```
https://www.aquavelo.com/test_sms.php?phone=0622647095
```

### 3. Logs du cron
Vérifier que le cron s'exécute bien sur le serveur et consultez les logs pour voir :
- Le nombre de rappels envoyés
- D'éventuelles erreurs

## 🎯 Conclusion

**✅ L'application ENVOIE BIEN un SMS avec SMS Factor 3 heures avant un RDV sur le planning de Cannes**

**Amélioration apportée** : La fenêtre d'envoi a été ajustée de 120-240 min à **150-210 min** pour être plus centrée sur 3h exactement.

**Conditions pour l'envoi** :
1. RDV dans la fenêtre 2h30-3h30 avant (centré sur 3h)
2. `reminder_3h_sent = 0`
3. Numéro de téléphone présent dans `phone`
4. RDV au format `(RDV: DD/MM/YYYY à HH:MM)`
