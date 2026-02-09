# 🔐 Migration Google Credentials vers .env

## 🎯 Objectif

Déplacer les credentials Google Cloud du fichier `google_key.json` vers des **variables d'environnement** (`.env`) pour :
- ✅ **Sécurité** : Ne plus commiter les secrets dans Git
- ✅ **Flexibilité** : Gérer différentes clés selon l'environnement (dev/prod)
- ✅ **Clever Cloud** : Utiliser les variables d'environnement natives

---

## 📋 ÉTAPES DE MIGRATION

### 1️⃣ **Générer le fichier .env local** (EN LOCAL)

Exécutez ce script **en local** pour créer votre `.env` :

👉 **http://localhost/aquavelo/generate_env_from_google_key.php**

Le script va :
1. ✅ Lire `google_key.json`
2. ✅ Créer `.env` avec le JSON encodé en base64
3. ✅ Afficher les instructions pour Clever Cloud

---

### 2️⃣ **Configurer Clever Cloud** (PRODUCTION)

#### A. Récupérer la valeur base64

Ouvrez le fichier `.env` généré et copiez la valeur de :
```
GOOGLE_CALENDAR_KEY_JSON_BASE64=eyJ0eXBlIjoic2VydmljZV...
```

#### B. Ajouter dans Clever Cloud

1. **Clever Cloud Console** → Votre application
2. Section **Environment variables**
3. Cliquer sur **"Add a variable"**
4. **Name** : `GOOGLE_CALENDAR_KEY_JSON_BASE64`
5. **Value** : Coller la longue chaîne base64
6. **Save**
7. **Redémarrer l'application** (obligatoire)

---

### 3️⃣ **Déployer le code**

```bash
git add .env.example .gitignore load_env.php generate_env_from_google_key.php
git add cron_sync_google.php test_google_calendar_cannes.php .htaccess
git commit -m "Security: migration Google credentials vers .env"
git push origin main
git push production main --force
```

---

### 4️⃣ **Tester en production**

Après le déploiement et redémarrage Clever Cloud :

👉 **https://aquavelo.com/test_google_calendar_cannes.php**

**Résultat attendu :**
```
✅ Fichier google_key.json trouvé (généré dynamiquement)
✅ Authentification Google réussie
✅ Accès au calendrier aqua.cannes@gmail.com réussi
```

---

## 🔧 FONCTIONNEMENT

### En Local (développement)

```
1. Fichier google_key.json existe physiquement
   ↓
2. load_env.php détecte sa présence
   ↓
3. Utilise le fichier directement
```

### En Production (Clever Cloud)

```
1. Variable d'environnement GOOGLE_CALENDAR_KEY_JSON_BASE64 existe
   ↓
2. load_env.php charge la variable
   ↓
3. Décode le base64
   ↓
4. Crée google_key.json temporairement
   ↓
5. Scripts l'utilisent normalement
```

---

## 📂 FICHIERS CRÉÉS/MODIFIÉS

### Nouveaux fichiers :
```
✓ .env.example                    → Template pour .env
✓ load_env.php                    → Chargeur de variables d'environnement
✓ generate_env_from_google_key.php → Générateur .env (usage unique)
```

### Fichiers modifiés :
```
✓ .gitignore                      → Ajout de .env
✓ cron_sync_google.php            → require 'load_env.php'
✓ test_google_calendar_cannes.php → require 'load_env.php'
✓ .htaccess                       → Autoriser generate_env_from_google_key.php
```

---

## ✅ AVANTAGES

### Avant (google_key.json commité) :
```
❌ Secrets dans Git
❌ Détection GitHub Secret Scanning
❌ Push bloqués régulièrement
❌ Risque de fuite de credentials
```

### Après (.env) :
```
✅ Secrets hors de Git
✅ Pas de blocage GitHub
✅ Configuration par environnement
✅ Sécurité renforcée
✅ Conforme aux bonnes pratiques
```

---

## 🔐 SÉCURITÉ

### Fichiers à ne JAMAIS commiter :
```
.env                 ← Dans .gitignore ✅
google_key.json      ← Dans .gitignore ✅
```

### Fichiers OK pour Git :
```
.env.example         ← Template sans valeurs sensibles ✅
load_env.php         ← Code public ✅
```

---

## 🧹 NETTOYAGE (Optionnel)

### Supprimer google_key.json de l'historique Git

Si vous voulez nettoyer complètement l'historique Git :

```bash
# ⚠️ ATTENTION : Modifie l'historique Git
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch google_key.json" \
  --prune-empty --tag-name-filter cat -- --all

# Force push sur tous les remotes
git push origin main --force
git push production main --force
```

**Note :** Cette opération est irréversible et modifie l'historique Git.

---

## 📞 SUPPORT

En cas de problème :
- 📧 claude@alesiaminceur.com
- 📱 06 22 64 70 95

---

**Date de création :** 27 Janvier 2026  
**Statut :** Migration en cours
