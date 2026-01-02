# Guide des Paramètres de l'Application

## Vue d'ensemble

Ce système de paramètres permet de personnaliser dynamiquement l'apparence de l'application sans modifier le code. Vous pouvez gérer :

- **Nom de l'entreprise** : Affiché dans tout le système
- **Logo de l'entreprise** : Remplace le logo par défaut
- **Couleur principale** : Utilisée pour les boutons, liens, éléments actifs
- **Couleur secondaire** : Utilisée pour les éléments secondaires

## Accès aux Paramètres

1. Connectez-vous à l'application
2. Dans le menu latéral, cliquez sur **"Paramètres"** (icône d'engrenage en bas du menu)
3. Modifiez les paramètres souhaités
4. Cliquez sur **"Enregistrer les paramètres"**

## Fonctionnalités

### 1. Nom de l'Entreprise

- Remplace le nom par défaut dans toute l'application
- Apparaît dans :
  - Le titre de la page (onglet du navigateur)
  - Le logo dans le menu latéral
  - Tous les endroits où le nom de l'application est affiché

### 2. Logo de l'Entreprise

- **Formats acceptés** : JPG, PNG, GIF, SVG
- **Taille maximale** : 2 MB
- **Recommandations** :
  - Utilisez une image carrée ou rectangulaire
  - Taille recommandée : 200x200 pixels ou plus
  - Fond transparent pour un meilleur rendu (PNG ou SVG)

### 3. Couleurs

#### Format des couleurs
- Utilisez le format hexadécimal : `#RRGGBB`
- Exemple : `#696cff`, `#ff5733`, `#00ff00`

#### Couleur Principale
Appliquée aux éléments suivants :
- Boutons principaux
- Liens
- Éléments de menu actifs
- Icônes principales
- Bordures de focus des formulaires
- Badges et indicateurs
- Barres de progression

#### Couleur Secondaire
Utilisée pour les éléments moins importants et les variations de couleur.

### 4. Aperçu en Temps Réel

La page des paramètres inclut un aperçu en direct des couleurs sélectionnées. Vous pouvez voir comment les boutons apparaîtront avant de sauvegarder.

## Utilisation Technique

### Dans les Vues Blade

Vous pouvez accéder aux paramètres dans n'importe quelle vue Blade :

```blade
<!-- Utiliser le helper setting() -->
{{ setting('company_name') }}
{{ setting('primary_color', '#696cff') }}

<!-- Ou utiliser la variable partagée $appSettings -->
{{ $appSettings['company_name'] }}
{{ $appSettings['primary_color'] }}

<!-- Afficher le logo -->
@if(setting('company_logo'))
    <img src="{{ asset('storage/' . setting('company_logo')) }}" alt="Logo">
@endif
```

### Dans les Contrôleurs

```php
use App\Models\Setting;

// Obtenir une valeur
$companyName = Setting::get('company_name', 'Défaut');

// Définir une valeur
Setting::set('company_name', 'Ma Compagnie', 'text', 'Description');

// Effacer le cache
Setting::clearCache();
```

### Ajouter de Nouveaux Paramètres

Pour ajouter de nouveaux paramètres personnalisés :

1. **Via le Seeder** (recommandé pour les paramètres par défaut) :
   
   Éditez `database/seeders/SettingsSeeder.php` :
   
   ```php
   [
       'key' => 'nouveau_parametre',
       'value' => 'valeur_par_defaut',
       'type' => 'text', // text, color, image, etc.
       'description' => 'Description du paramètre'
   ]
   ```

2. **Via le Code** :
   
   ```php
   Setting::set('nouveau_parametre', 'valeur', 'text', 'Description');
   ```

3. **Ajouter au Formulaire** :
   
   Éditez `resources/views/settings/index.blade.php` pour ajouter un champ dans le formulaire.

## Cache

Les paramètres sont mis en cache pour améliorer les performances :
- **Durée du cache** : 1 heure
- **Effacement automatique** : Lors de la mise à jour des paramètres
- **Effacement manuel** : `Setting::clearCache()`

## Exemples de Couleurs

Voici quelques exemples de couleurs que vous pouvez utiliser :

### Couleurs Professionnelles
- **Bleu Corporate** : `#0066cc`
- **Vert Entreprise** : `#00a86b`
- **Rouge Dynamique** : `#dc3545`
- **Orange Énergique** : `#fd7e14`
- **Violet Moderne** : `#6f42c1`

### Couleurs Tendance
- **Bleu Ciel** : `#00bcd4`
- **Vert Menthe** : `#20c997`
- **Rose Doux** : `#e83e8c`
- **Indigo** : `#6610f2`
- **Teal** : `#20c997`

## Dépannage

### Le logo ne s'affiche pas
- Vérifiez que le dossier `storage/app/public` est lié à `public/storage`
- Exécutez : `php artisan storage:link`

### Les couleurs ne changent pas
- Videz le cache du navigateur (Ctrl+F5 ou Cmd+Shift+R)
- Vérifiez que le format de couleur est correct (#RRGGBB)

### Les paramètres ne se sauvegardent pas
- Vérifiez les permissions d'écriture sur le dossier `storage`
- Consultez les logs dans `storage/logs/laravel.log`

## Support

Pour toute question ou problème, consultez la documentation Laravel ou contactez l'équipe de développement.

