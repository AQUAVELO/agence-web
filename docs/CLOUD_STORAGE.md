# Stockage Cloud (FS Bucket)

## C'est quoi ?
Un disque réseau partagé monté sur `/cloud` pour stocker les images des centres.
Sans ça, les images seraient perdues à chaque déploiement (infrastructure immuable Clever Cloud).

## ⚠️ RÈGLE IMPORTANTE
**Ne JAMAIS créer de dossier `/cloud` dans le projet Git.**
Si ce dossier existe, le montage échoue silencieusement et les images sont perdues.

```bash
# Vérifier que /cloud n'existe pas dans le repo
git ls-files | grep "^cloud/"
# Si résultat → SUPPRIMER immédiatement !
```

---

## Architecture multi-repo

### Qui écrit ? Qui lit ?

| Repo | Type | Accès FS Bucket | Rôle |
|------|------|-----------------|------|
| **alesiaminceur** | Intranet franchise | **Read/Write** | Upload + miniatures |
| aquavelo | Site vitrine | Read-only | Affichage des images |
| hyperminceur | Site vitrine | Read-only | Affichage des images |
| autres sites... | Site vitrine | Read-only | Affichage des images |

### Pourquoi cette architecture ?

1. **Point d'entrée unique** : Seul l'intranet (alesiaminceur) permet aux franchisés d'uploader leurs photos
2. **Génération des miniatures** : L'intranet crée automatiquement les thumbnails (300px)
3. **Sites vitrines en lecture seule** : Ils ne font qu'afficher les images, pas besoin d'écriture
4. **Sécurité** : Moins de risques d'écrasement ou corruption des fichiers
5. **Cohérence** : Une seule source de vérité pour les images

---

## Structure des fichiers

```
/cloud/
├── center_{id}/           # Photos du centre
│   ├── 1.jpg              # Devanture
│   ├── 2.jpg              # Accueil
│   ├── 3.jpg              # Cabine 1
│   ├── 4.jpg              # Cabine 2
│   └── 5.jpg              # Divers
└── thumbnail/center_{id}/ # Miniatures 300px
    ├── 1.jpg
    ├── 2.jpg
    ├── 3.jpg
    ├── 4.jpg
    └── 5.jpg
```

### Convention de nommage

| Numéro | Description | Usage |
|--------|-------------|-------|
| 1.jpg | Devanture/Façade | Photo principale du centre |
| 2.jpg | Accueil/Réception | Zone d'accueil clients |
| 3.jpg | Cabine 1 | Équipements aquabike |
| 4.jpg | Cabine 2 | Autres équipements |
| 5.jpg | Divers | Photo complémentaire |

---

## Config Clever Cloud

### Intranet (alesiaminceur) - Read/Write :
```
CC_FS_BUCKET=/cloud:bucket-xxx.services.clever-cloud.com
```

### Sites vitrines - Read-only :
```
CC_FS_BUCKET=/cloud:bucket-xxx.services.clever-cloud.com:ro
```
> Le `:ro` rend le montage en lecture seule

### Configuration via Console Clever Cloud

1. Aller sur [console.clever-cloud.com](https://console.clever-cloud.com)
2. Sélectionner l'application
3. **Environment variables** → Ajouter `CC_FS_BUCKET`
4. Redéployer l'application

---

## Fichiers concernés

### Repo alesiaminceur (écriture)

| Fichier | Rôle |
|---------|------|
| `_settings.php` | Définit `$clouddir = '/cloud'` |
| `controller/picmanager.php` | Upload des photos originales |
| `min/controller/picmanager.php` | Upload + génération thumbnails 300px |

### Repo aquavelo (lecture seule)

| Fichier | Rôle |
|---------|------|
| `.htaccess` | Route `/cloud/` vers le FS Bucket |
| `_centers.php` | Liste des centres avec thumbnails |
| `_page.php` | Page centre + Schema.org JSON-LD |
| `articles.php` | Galerie photos du centre |

---

## Utilisation dans aquavelo

### Configuration .htaccess

Le fichier `.htaccess` exclut `/cloud/` du routing pour servir directement les fichiers :

```apache
# Exclusion du dossier FS Bucket
RewriteRule ^(css|js|images|fonts|uploads|cloud|vendor)/ - [L]
```

### Liste des centres (`_centers.php`)

Affiche le thumbnail principal de chaque centre :

```php
<img src="/cloud/thumbnail/center_<?= $row_centers_list['id']; ?>/1.jpg" 
     alt="Centre Aquavélo <?= $row_centers_list['city']; ?>"
     style="width: 100%; height: 250px; object-fit: cover;">
```

### Page centre (`_page.php`)

Utilisé dans le Schema.org JSON-LD pour le SEO :

```php
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SportsActivityLocation",
  "name": "Aquavélo <?= htmlspecialchars($city); ?>",
  "image": [
    "https://www.aquavelo.com/cloud/thumbnail/center_<?= $row_center['id']; ?>/1.jpg",
    "https://www.aquavelo.com/cloud/thumbnail/center_<?= $row_center['id']; ?>/2.jpg"
  ]
}
</script>
```

### Galerie photos (`articles.php`)

Affiche 3 photos du centre :

```php
<!-- Photo principale -->
<img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/1.jpg" alt="Photo principale">

<!-- Photo secondaire -->
<img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/2.jpg" alt="Photo secondaire">

<!-- Photo supplémentaire -->
<img src="/cloud/thumbnail/center_<?= $row_center['id']; ?>/3.jpg" alt="Photo supplémentaire">
```

### URLs utilisées

| Type | Format URL |
|------|------------|
| Thumbnail local | `/cloud/thumbnail/center_{id}/1.jpg` |
| URL absolue (SEO) | `https://www.aquavelo.com/cloud/thumbnail/center_{id}/1.jpg` |

---

## URL publique

### Format des URLs
```
https://cache.alesiaminceur.com/cloud/center_{id}/{1-5}.jpg
https://cache.alesiaminceur.com/cloud/thumbnail/center_{id}/{1-5}.jpg
```

### Exemples
```php
// Photo principale du centre 42
$url = "https://cache.alesiaminceur.com/cloud/center_42/1.jpg";

// Thumbnail du centre 42
$thumb = "https://cache.alesiaminceur.com/cloud/thumbnail/center_42/1.jpg";
```

---

## Vérification du montage

### En PHP
```php
<?php
// Vérifier que le bucket est monté
if (is_dir('/cloud')) {
    echo "✅ FS Bucket monté correctement";
    
    // Lister les centres
    $dirs = glob('/cloud/center_*', GLOB_ONLYDIR);
    echo "Centres trouvés : " . count($dirs);
} else {
    echo "❌ FS Bucket NON monté !";
}
```

### En ligne de commande (SSH Clever Cloud)
```bash
# Vérifier le montage
df -h | grep cloud

# Lister les fichiers
ls -la /cloud/

# Vérifier les permissions
stat /cloud/
```

---

## Troubleshooting

### ❌ Images non affichées

| Symptôme | Cause probable | Solution |
|----------|----------------|----------|
| 404 sur toutes les images | Bucket non monté | Vérifier `CC_FS_BUCKET` |
| 404 sur nouvelles images | Dossier `/cloud` dans Git | Supprimer et redéployer |
| Images anciennes OK | Sync en cours | Attendre quelques minutes |

### ❌ Upload échoue (intranet)

| Symptôme | Cause probable | Solution |
|----------|----------------|----------|
| Permission denied | Montage en `:ro` | Retirer `:ro` de la variable |
| Disk quota exceeded | Bucket plein | Nettoyer les anciens fichiers |
| No space left | Même problème | Contacter support Clever Cloud |

### ❌ Dossier /cloud dans le Git

```bash
# 1. Supprimer du repo (garder les fichiers locaux)
git rm -r --cached cloud/

# 2. Ajouter au .gitignore
echo "/cloud/" >> .gitignore

# 3. Commit et push
git add .gitignore
git commit -m "fix: remove /cloud from git, add to gitignore"
git push

# 4. Redéployer sur Clever Cloud
clever restart
```

---

## Développement local

### Option 1 : Dossier local (recommandé)

Créer un dossier `cloud/` **localement** (hors Git) :
```bash
# Créer le dossier (ignoré par Git grâce au .gitignore)
mkdir -p cloud/center_1 cloud/thumbnail/center_1

# Copier des images de test
cp test-images/*.jpg cloud/center_1/
```

### Option 2 : Symlink vers dossier partagé
```bash
ln -s /chemin/vers/images/partagees /cloud
```

### Option 3 : Variable d'environnement
```php
// _settings.php
$clouddir = getenv('CLOUD_DIR') ?: '/cloud';
```

```bash
# .env local
CLOUD_DIR=/Users/dev/images-test
```

---

## Limitations Clever Cloud FS Bucket

| Limitation | Détail | Impact |
|------------|--------|--------|
| Backups | Toutes les 24h, rétention 72h | Sauvegardes externes recommandées |
| Cache | Non optimisé haute performance | Utiliser CDN (cache.alesiaminceur.com) |
| Docker | Non disponible | Utiliser volumes Docker à la place |
| Cross-region | Pas de montage multi-région | Un bucket par région |
| Taille max fichier | ~5GB | OK pour nos images |
| Latence | ~10-50ms | Acceptable pour images |

---

## Bonnes pratiques

### ✅ À faire

- [ ] Toujours vérifier que `/cloud` n'est PAS dans le repo Git
- [ ] Utiliser les thumbnails pour les listes (économie bande passante)
- [ ] Mettre en cache côté CDN (cache.alesiaminceur.com)
- [ ] Compresser les images avant upload (max 500KB recommandé)
- [ ] Utiliser des noms de fichiers simples (1.jpg à 5.jpg)

### ❌ À éviter

- Ne jamais commit `/cloud/` dans Git
- Ne pas uploader depuis les sites vitrines
- Ne pas stocker de fichiers temporaires dans `/cloud`
- Ne pas dépasser 5 photos par centre
- Ne pas utiliser de caractères spéciaux dans les noms

---

## Sauvegarde externe (recommandé)

Le backup Clever Cloud n'étant que de 72h, prévoir une sauvegarde externe :

```bash
# Script de backup (à exécuter via cron externe)
#!/bin/bash
DATE=$(date +%Y%m%d)
rsync -avz user@ssh.clever-cloud.com:/cloud/ /backup/cloud_$DATE/

# Ou via S3
aws s3 sync /cloud/ s3://backup-bucket/cloud/
```

---

## Contacts & Support

- **Clever Cloud** : support@clever-cloud.com
- **Documentation** : https://www.clever-cloud.com/doc/deploy/addon/fs-bucket/
- **Console** : https://console.clever-cloud.com

---

*Dernière mise à jour : Février 2026*
