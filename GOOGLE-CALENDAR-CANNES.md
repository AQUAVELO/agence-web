# ✅ Configuration Google Calendar - Cannes, Mandelieu, Vallauris

## 📋 RÉSUMÉ

Les RDV de séance d'essai pour les centres **Cannes**, **Mandelieu** et **Vallauris** sont configurés pour se synchroniser sur le calendrier Google :

**📧 aqua.cannes@gmail.com**

---

## 🔧 CONFIGURATION ACTUELLE

### Fichier : `cron_sync_google.php` (lignes 62-64)

```php
if (in_array((int)$booking['center_id'], [305, 347, 349])) {
    // Cannes, Mandelieu, Vallauris → Agenda commun
    $targetCalendarId = 'aqua.cannes@gmail.com';
}
```

### Centres concernés :

| ID  | Centre      | Calendrier Google       |
|-----|-------------|-------------------------|
| 305 | Cannes      | aqua.cannes@gmail.com  |
| 347 | Mandelieu   | aqua.cannes@gmail.com  |
| 349 | Vallauris   | aqua.cannes@gmail.com  |

---

## 🧪 DIAGNOSTIC

### Étape 1 : Vérifier la configuration

1. **Pousser le code vers production**
   ```bash
   git push origin main
   git push production main
   ```

2. **Accéder au script de test**
   👉 https://aquavelo.com/test_google_calendar_cannes.php

### Ce que le script teste :

✅ Présence du fichier `google_key.json`  
✅ Authentification Google API  
✅ Accès au calendrier `aqua.cannes@gmail.com`  
✅ Liste des RDV non synchronisés (Cannes, Mandelieu, Vallauris)  
✅ Création d'un événement de test

---

## ⚠️ PROBLÈME POTENTIEL : Permissions Google Calendar

### Symptôme :
Erreur `403 Forbidden` ou "Access denied" lors de la synchronisation

### Cause :
Le calendrier **aqua.cannes@gmail.com** n'a pas partagé l'agenda avec le **compte de service Google**.

### ✅ SOLUTION :

#### 1. Récupérer l'email du compte de service

Ouvrir le fichier `google_key.json` et copier la valeur de `client_email` :

```json
{
  "client_email": "nom-du-compte@projet-123456.iam.gserviceaccount.com",
  ...
}
```

#### 2. Partager le calendrier

1. Se connecter à **Google Calendar** avec le compte **aqua.cannes@gmail.com**
2. Cliquer sur les **3 points** à côté du calendrier principal
3. Sélectionner **"Paramètres et partage"**
4. Dans la section **"Partager avec des personnes en particulier"**, cliquer sur **"+ Ajouter des personnes ou des groupes"**
5. Coller l'email du compte de service (de `google_key.json`)
6. Choisir les droits : **"Apporter des modifications aux événements"**
7. Cliquer sur **"Envoyer"**

---

## 🚀 LANCEMENT DE LA SYNCHRONISATION

### Automatique (Cron)

Le script `cron_sync_google.php` s'exécute automatiquement toutes les X minutes (selon configuration du serveur).

### Manuel

Pour forcer la synchronisation immédiatement :

👉 https://aquavelo.com/cron_sync_google.php

**Résultat attendu :**
```
Nombre de RDV synchronisés avec Google Calendar : X
```

---

## 📊 VÉRIFICATION

### 1. Vérifier les RDV synchronisés dans la base de données

```sql
SELECT 
    f.id, 
    c.city, 
    f.name, 
    f.email, 
    f.google_sync, 
    f.google_event_id
FROM am_free f
LEFT JOIN am_centers c ON f.center_id = c.id
WHERE f.name LIKE '%(RDV:%'
AND f.center_id IN (305, 347, 349)
ORDER BY f.id DESC
LIMIT 20;
```

**Colonnes importantes :**
- `google_sync` : doit être `1` après synchronisation
- `google_event_id` : ID de l'événement dans Google Calendar

### 2. Vérifier dans Google Calendar

1. Se connecter à **aqua.cannes@gmail.com**
2. Ouvrir **Google Calendar**
3. Les événements doivent apparaître avec :
   - 🏊 [Nom du client]
   - Date/Heure du RDV
   - Durée : 45 minutes

---

## 🔁 PROCESSUS DE SYNCHRONISATION

### Quand un RDV est pris :

1. **Client remplit le formulaire** sur `/free`
2. **Client choisit un créneau** sur le planning Calendly
3. **Webhook Calendly** enregistre le RDV dans `am_free` avec :
   - `name` : "Nom Client (RDV: 27/01/2026 à 14:00)"
   - `center_id` : 305, 347 ou 349
   - `google_sync` : 0 (non synchronisé)

4. **Cron `cron_sync_google.php`** s'exécute (toutes les X minutes)
5. Script lit les RDV avec `google_sync = 0`
6. Pour chaque RDV :
   - Extrait la date/heure
   - Crée l'événement sur `aqua.cannes@gmail.com`
   - Marque `google_sync = 1`
   - Enregistre `google_event_id`

---

## 🛠️ DÉPANNAGE

### Problème : Les RDV ne se synchronisent pas

**Checklist :**

1. ☐ Le fichier `google_key.json` existe et est valide
2. ☐ Le calendrier `aqua.cannes@gmail.com` est partagé avec le compte de service
3. ☐ Les RDV ont bien `center_id` = 305, 347 ou 349
4. ☐ Les RDV ont `name` au format "Nom (RDV: DD/MM/YYYY à HH:MM)"
5. ☐ Le cron est activé et s'exécute
6. ☐ Pas d'erreur dans les logs serveur

### Problème : Erreur "403 Forbidden"

**Solution :** Partager le calendrier avec le compte de service (voir section ci-dessus)

### Problème : Événements créés sur le mauvais calendrier

**Vérifier :**
```php
// Dans cron_sync_google.php ligne 62-64
if (in_array((int)$booking['center_id'], [305, 347, 349])) {
    $targetCalendarId = 'aqua.cannes@gmail.com'; // ← Doit être correct
}
```

---

## 📝 FICHIERS IMPLIQUÉS

```
✓ cron_sync_google.php           → Script de synchronisation
✓ test_google_calendar_cannes.php → Script de diagnostic (NOUVEAU)
✓ google_key.json                 → Clés d'authentification Google
✓ _free.php                       → Formulaire séance d'essai
✓ _calendrier_cannes.php          → Interface Calendly
```

---

## 📞 SUPPORT

En cas de problème persistant :

1. Exécuter le script de diagnostic : https://aquavelo.com/test_google_calendar_cannes.php
2. Vérifier les logs serveur
3. Contacter Claude Rodriguez : claude@alesiaminceur.com / 06 22 64 70 95

---

**Dernière mise à jour :** 27 Janvier 2026  
**Configuration validée :** ✅ Cannes, Mandelieu, Vallauris → aqua.cannes@gmail.com
