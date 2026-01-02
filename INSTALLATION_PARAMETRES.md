# Installation du Système de Paramètres

## 📦 Ce qui a été installé

Un système complet de gestion des paramètres de l'application permettant de personnaliser :
- Le nom de l'entreprise
- Le logo de l'entreprise
- Les couleurs de l'interface (couleur principale et secondaire)

## 🚀 Installation Automatique Effectuée

### 1. Base de Données
```bash
# Migration créée et exécutée
php artisan migrate --force

# Seeder exécuté pour les valeurs par défaut
php artisan db:seed --class=SettingsSeeder --force
```

### 2. Fichiers Créés

#### Modèle
- `app/Models/Setting.php` - Modèle avec cache et méthodes helper

#### Contrôleur
- `app/Http/Controllers/SettingController.php` - Gestion des paramètres

#### Vues
- `resources/views/settings/index.blade.php` - Interface de gestion

#### Helper
- `app/Helpers/SettingHelper.php` - Fonction globale `setting()`

#### Seeder
- `database/seeders/SettingsSeeder.php` - Valeurs par défaut

#### Migration
- `database/migrations/2026_01_02_173542_create_settings_table.php`

### 3. Fichiers Modifiés

#### Routes
- `routes/web.php` - Routes pour `/settings`

#### Layout
- `resources/views/layouts/app.blade.php` - Utilisation des paramètres dynamiques

#### Composer
- `composer.json` - Autoload du helper

#### Service Provider
- `app/Providers/AppServiceProvider.php` - Partage des paramètres avec les vues

## 📋 Structure de la Table `settings`

```sql
CREATE TABLE settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT,
    type VARCHAR(255) DEFAULT 'text',
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🎨 Paramètres par Défaut

| Clé | Valeur | Type | Description |
|-----|--------|------|-------------|
| `company_name` | Nom de l'app | text | Nom de l'entreprise |
| `primary_color` | #696cff | color | Couleur principale |
| `secondary_color` | #8592a3 | color | Couleur secondaire |
| `company_logo` | null | image | Logo (optionnel) |

## 🔧 Configuration Requise

### Permissions
```bash
# Assurez-vous que les permissions sont correctes
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Lien Symbolique
```bash
# Le lien symbolique doit exister (déjà créé)
php artisan storage:link
```

### Autoload
```bash
# L'autoload a été régénéré
composer dump-autoload
```

## 📖 Utilisation

### Accès à l'Interface
1. Connectez-vous à l'application
2. Cliquez sur "Paramètres" dans le menu (icône palette)
3. Modifiez les paramètres
4. Cliquez sur "Enregistrer"

### Utilisation dans le Code

#### Dans les Vues Blade
```blade
<!-- Utiliser le helper -->
{{ setting('company_name') }}
{{ setting('primary_color', '#696cff') }}

<!-- Utiliser la variable partagée -->
{{ $appSettings['company_name'] }}

<!-- Afficher le logo -->
@if(setting('company_logo'))
    <img src="{{ asset('storage/' . setting('company_logo')) }}" alt="Logo">
@endif
```

#### Dans les Contrôleurs
```php
use App\Models\Setting;

// Obtenir une valeur
$name = Setting::get('company_name', 'Défaut');

// Définir une valeur
Setting::set('company_name', 'Ma Compagnie');

// Effacer le cache
Setting::clearCache();
```

#### Utiliser le Helper
```php
// N'importe où dans le code
$companyName = setting('company_name');
$primaryColor = setting('primary_color', '#696cff');
```

## 🎯 Fonctionnalités

### Cache Automatique
- Les paramètres sont mis en cache pendant 1 heure
- Le cache est automatiquement effacé lors des mises à jour
- Améliore les performances en réduisant les requêtes DB

### Validation
- Nom de l'entreprise : max 255 caractères
- Couleurs : format hexadécimal (#RRGGBB)
- Logo : JPG, PNG, GIF, SVG (max 2MB)

### Aperçu en Temps Réel
- Les changements de couleur sont visibles avant sauvegarde
- Synchronisation entre le sélecteur de couleur et le champ texte

### Application Dynamique
Les couleurs sont appliquées automatiquement à :
- Boutons principaux
- Liens
- Menu actif
- Bordures de focus
- Badges et indicateurs
- Barres de progression

## 🔄 Mise à Jour

Si vous avez déjà une installation existante :

```bash
# 1. Exécuter la migration
php artisan migrate --force

# 2. Exécuter le seeder
php artisan db:seed --class=SettingsSeeder --force

# 3. Régénérer l'autoload
composer dump-autoload

# 4. Vider le cache (optionnel)
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📚 Documentation

- `PARAMETRES_GUIDE.md` - Guide complet d'utilisation
- `QUICK_START_PARAMETRES.md` - Démarrage rapide
- `TEST_PARAMETRES.md` - Tests et vérification

## 🆘 Support

### Logs
En cas de problème, consultez les logs :
```bash
tail -f storage/logs/laravel.log
```

### Réinitialisation
Pour réinitialiser les paramètres par défaut :
```bash
php artisan db:seed --class=SettingsSeeder --force
```

### Vider le Cache
```bash
php artisan cache:clear
```

## ✅ Vérification de l'Installation

Pour vérifier que tout fonctionne :

```bash
php artisan tinker
```

```php
// Dans tinker
use App\Models\Setting;

// Vérifier les paramètres
Setting::all();

// Tester le helper
echo setting('company_name');

// Tester la mise à jour
Setting::set('company_name', 'Test');
echo setting('company_name');
```

## 🎉 Installation Terminée !

Le système de paramètres est maintenant opérationnel. Vous pouvez :
1. Accéder à `/settings` pour gérer les paramètres
2. Personnaliser le nom, le logo et les couleurs
3. Les changements seront appliqués immédiatement

Pour plus d'informations, consultez `QUICK_START_PARAMETRES.md`.

