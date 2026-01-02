# Test du Système de Paramètres

## ✅ Liste de Vérification

### Installation
- [x] Migration créée et exécutée (`settings` table)
- [x] Modèle `Setting` créé avec méthodes `get()` et `set()`
- [x] Contrôleur `SettingController` créé
- [x] Routes ajoutées (`/settings`)
- [x] Vue créée (`resources/views/settings/index.blade.php`)
- [x] Seeder créé et exécuté
- [x] Helper `setting()` créé et autoloadé
- [x] AppServiceProvider mis à jour pour partager les paramètres
- [x] Layout modifié pour utiliser les paramètres dynamiques
- [x] Lien symbolique de stockage vérifié

### Tests à Effectuer

#### 1. Test d'Accès
```bash
# Démarrer le serveur
php artisan serve
```
- [ ] Accéder à http://localhost:8000/settings
- [ ] Vérifier que la page se charge sans erreur
- [ ] Vérifier que le menu "Paramètres" est visible avec l'icône palette

#### 2. Test du Nom de l'Entreprise
- [ ] Changer le nom de l'entreprise
- [ ] Sauvegarder
- [ ] Vérifier que le nouveau nom apparaît dans :
  - Le titre de la page (onglet du navigateur)
  - Le logo dans le menu latéral
  - Le header de l'application

#### 3. Test du Logo
- [ ] Télécharger un logo (PNG, JPG, ou SVG)
- [ ] Sauvegarder
- [ ] Vérifier que le logo s'affiche dans le menu latéral
- [ ] Vérifier que l'ancien logo SVG par défaut est remplacé

#### 4. Test de la Couleur Principale
- [ ] Changer la couleur principale (ex: `#ff6b35`)
- [ ] Observer l'aperçu en temps réel
- [ ] Sauvegarder
- [ ] Vérifier que la nouvelle couleur est appliquée à :
  - Les boutons principaux
  - Les liens
  - Le menu actif
  - Les bordures de focus des formulaires

#### 5. Test de la Couleur Secondaire
- [ ] Changer la couleur secondaire (ex: `#00a86b`)
- [ ] Observer l'aperçu
- [ ] Sauvegarder
- [ ] Vérifier l'application de la couleur

#### 6. Test du Cache
```bash
# Vérifier que les paramètres sont mis en cache
php artisan tinker
```
```php
// Dans tinker
use App\Models\Setting;

// Obtenir un paramètre (devrait être mis en cache)
$name = Setting::get('company_name');
echo $name;

// Modifier un paramètre
Setting::set('company_name', 'Test Company');

// Vérifier que le cache est effacé
$name = Setting::get('company_name');
echo $name; // Devrait afficher 'Test Company'

// Effacer tout le cache
Setting::clearCache();
```

#### 7. Test du Helper
```bash
php artisan tinker
```
```php
// Tester le helper setting()
echo setting('company_name');
echo setting('primary_color');
echo setting('inexistant', 'valeur_par_defaut');
```

#### 8. Test de Validation
- [ ] Essayer de télécharger un fichier trop grand (>2MB)
- [ ] Essayer de télécharger un fichier non-image
- [ ] Vérifier que les erreurs de validation s'affichent correctement

#### 9. Test de Persistance
- [ ] Modifier tous les paramètres
- [ ] Se déconnecter
- [ ] Se reconnecter
- [ ] Vérifier que tous les paramètres sont conservés

#### 10. Test Multi-Pages
- [ ] Modifier les paramètres
- [ ] Naviguer vers différentes pages de l'application
- [ ] Vérifier que les paramètres sont appliqués partout

## 🐛 Problèmes Connus et Solutions

### Problème : Le logo ne s'affiche pas
**Solution :**
```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

### Problème : Les couleurs ne changent pas
**Solution :**
- Vider le cache du navigateur (Ctrl+F5)
- Vérifier le format de couleur (#RRGGBB)
- Inspecter l'élément pour voir si les styles CSS sont appliqués

### Problème : Erreur "Class 'Setting' not found"
**Solution :**
```bash
composer dump-autoload
```

### Problème : Erreur lors de la sauvegarde
**Solution :**
```bash
# Vérifier les permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Vérifier les logs
tail -f storage/logs/laravel.log
```

## 📊 Résultats Attendus

### Base de Données
La table `settings` devrait contenir au minimum :
- `company_name` (text)
- `primary_color` (color)
- `secondary_color` (color)
- `company_logo` (image) - optionnel

### Fichiers Créés
```
app/
  ├── Helpers/
  │   └── SettingHelper.php
  ├── Http/Controllers/
  │   └── SettingController.php
  └── Models/
      └── Setting.php

database/
  ├── migrations/
  │   └── 2026_01_02_173542_create_settings_table.php
  └── seeders/
      └── SettingsSeeder.php

resources/views/
  └── settings/
      └── index.blade.php

routes/
  └── web.php (modifié)
```

## 🎯 Critères de Succès

Le système est considéré comme fonctionnel si :
1. ✅ Tous les paramètres peuvent être modifiés via l'interface
2. ✅ Les changements sont persistants (base de données)
3. ✅ Les changements sont visibles immédiatement après sauvegarde
4. ✅ Le cache fonctionne correctement
5. ✅ Le logo peut être téléchargé et affiché
6. ✅ Les couleurs sont appliquées dynamiquement
7. ✅ Aucune erreur dans les logs
8. ✅ Le système fonctionne sur toutes les pages

## 📝 Notes

- Les paramètres sont mis en cache pendant 1 heure
- Le cache est automatiquement effacé lors de la mise à jour
- Les logos sont stockés dans `storage/app/public/logos/`
- Les couleurs utilisent le format hexadécimal (#RRGGBB)

