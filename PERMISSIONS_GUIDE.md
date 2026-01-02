# Guide de Gestion des Utilisateurs, Rôles et Permissions

## 📋 Vue d'ensemble

Ce système utilise **Spatie Laravel Permission** pour gérer les utilisateurs, rôles et permissions de manière granulaire et sécurisée.

## 🚀 Installation et Configuration

### 1. Migrations
Les tables suivantes ont été créées :
- `permissions` - Stocke toutes les permissions du système
- `roles` - Stocke les rôles (Super Admin, Administrateur, Agent, etc.)
- `model_has_permissions` - Associe les permissions directement aux utilisateurs
- `model_has_roles` - Associe les rôles aux utilisateurs
- `role_has_permissions` - Associe les permissions aux rôles

### 2. Seeders
Deux seeders ont été créés :
- `RolePermissionSeeder` - Crée tous les rôles et permissions
- `SuperAdminSeeder` - Crée un utilisateur Super Admin

## 👥 Rôles Prédéfinis

### 1. Super Admin
- **Accès** : Toutes les permissions du système
- **Usage** : Gestion complète de l'application

### 2. Administrateur
- **Accès** : Presque toutes les permissions sauf gestion des Super Admins
- **Usage** : Gestion quotidienne de l'entreprise

### 3. Agent
- **Accès** : Vente de tickets, gestion des clients, création de colis
- **Usage** : Personnel de guichet

### 4. Chef Parc
- **Accès** : Gestion des bus, carburant, trips, dépenses
- **Usage** : Responsable du parc automobile

### 5. Chauffeur
- **Accès** : Embarquement/débarquement, consultation des trips
- **Usage** : Conducteurs de bus

### 6. Comptable
- **Accès** : Gestion financière, dépenses, rapports
- **Usage** : Service comptabilité

### 7. Client
- **Accès** : Consultation de ses propres tickets
- **Usage** : Clients de la compagnie

## 🔐 Permissions Disponibles

### Dashboard
- `view-dashboard` - Accès au tableau de bord

### Gestion des Tickets
- `view-tickets` - Voir les tickets
- `create-tickets` - Créer des tickets
- `edit-tickets` - Modifier des tickets
- `delete-tickets` - Supprimer des tickets
- `cancel-tickets` - Annuler des tickets
- `board-tickets` - Embarquer des passagers
- `disembark-tickets` - Débarquer des passagers
- `calculate-ticket-price` - Calculer le prix des tickets
- `view-available-seats` - Voir les sièges disponibles
- `search-ticket-qr` - Rechercher par QR code
- `retrieve-cancelled-tickets` - Récupérer les tickets annulés

### Gestion des Trajets (Trips)
- `view-trips` - Voir les départs
- `create-trips` - Créer des départs
- `edit-trips` - Modifier des départs
- `delete-trips` - Supprimer des départs

### Gestion des Routes
- `view-routes` - Voir les trajets
- `create-routes` - Créer des trajets
- `edit-routes` - Modifier des trajets
- `delete-routes` - Supprimer des trajets

### Gestion des Villes
- `view-villes` - Voir les villes
- `create-villes` - Créer des villes
- `edit-villes` - Modifier des villes
- `delete-villes` - Supprimer des villes

### Gestion des Arrêts
- `view-stops` - Voir les arrêts
- `create-stops` - Créer des arrêts
- `edit-stops` - Modifier des arrêts
- `delete-stops` - Supprimer des arrêts
- `view-stops-api` - API des arrêts

### Configuration des Tarifs
- `view-route-stop-prices` - Voir les tarifs
- `create-route-stop-prices` - Créer des tarifs
- `edit-route-stop-prices` - Modifier des tarifs
- `delete-route-stop-prices` - Supprimer des tarifs

### Gestion des Clients
- `view-clients` - Voir les clients
- `view-client-details` - Voir les détails des clients
- `search-clients-by-phone` - Rechercher par téléphone

### Gestion des Employés
- `view-employees` - Voir les employés
- `create-employees` - Créer des employés
- `edit-employees` - Modifier des employés
- `delete-employees` - Supprimer des employés

### Gestion des Bus
- `view-buses` - Voir les bus
- `create-buses` - Créer des bus
- `edit-buses` - Modifier des bus
- `delete-buses` - Supprimer des bus

### Gestion du Carburant
- `view-fuel-records` - Voir les enregistrements
- `create-fuel-records` - Créer des enregistrements
- `edit-fuel-records` - Modifier des enregistrements
- `delete-fuel-records` - Supprimer des enregistrements

### Gestion des Colis
- `view-parcels` - Voir les colis
- `create-parcels` - Créer des colis
- `edit-parcels` - Modifier des colis
- `delete-parcels` - Supprimer des colis
- `mark-parcel-retrieved` - Marquer comme récupéré
- `view-retrieved-parcels` - Voir les colis récupérés

### Gestion des Destinations
- `view-destinations` - Voir les destinations
- `create-destinations` - Créer des destinations
- `edit-destinations` - Modifier des destinations
- `delete-destinations` - Supprimer des destinations

### Gestion des Agences de Réception
- `view-reception-agencies` - Voir les agences
- `create-reception-agencies` - Créer des agences
- `edit-reception-agencies` - Modifier des agences
- `delete-reception-agencies` - Supprimer des agences

### Gestion des Dépenses
- `view-expenses` - Voir les dépenses
- `create-expenses` - Créer des dépenses
- `edit-expenses` - Modifier des dépenses
- `delete-expenses` - Supprimer des dépenses

### Gestion des Utilisateurs
- `view-users` - Voir les utilisateurs
- `create-users` - Créer des utilisateurs
- `edit-users` - Modifier des utilisateurs
- `delete-users` - Supprimer des utilisateurs
- `assign-roles` - Assigner des rôles

### Gestion des Rôles
- `view-roles` - Voir les rôles
- `create-roles` - Créer des rôles
- `edit-roles` - Modifier des rôles
- `delete-roles` - Supprimer des rôles

### Gestion des Permissions
- `view-permissions` - Voir les permissions
- `assign-permissions` - Assigner des permissions

### Diagnostic et Rapports
- `view-diagnostic` - Voir les diagnostics
- `view-reports` - Voir les rapports

## 💻 Utilisation dans le Code

### Dans les Contrôleurs
```php
// Vérifier une permission
$this->middleware('permission:view-users');

// Vérifier plusieurs permissions
$this->middleware('permission:view-users|edit-users');
```

### Dans les Vues (Blade)
```php
// Vérifier une permission
@can('view-users')
    <!-- Contenu visible seulement si l'utilisateur a la permission -->
@endcan

// Vérifier plusieurs permissions (OR)
@canany(['view-users', 'edit-users'])
    <!-- Contenu visible si l'utilisateur a au moins une des permissions -->
@endcanany

// Vérifier un rôle
@role('Super Admin')
    <!-- Contenu visible seulement pour les Super Admins -->
@endrole
```

### Dans le Code PHP
```php
// Vérifier une permission
if (auth()->user()->can('view-users')) {
    // Code
}

// Vérifier un rôle
if (auth()->user()->hasRole('Super Admin')) {
    // Code
}

// Assigner un rôle
$user->assignRole('Agent');

// Assigner plusieurs rôles
$user->assignRole(['Agent', 'Comptable']);

// Retirer un rôle
$user->removeRole('Agent');

// Synchroniser les rôles (remplace tous les rôles existants)
$user->syncRoles(['Agent']);

// Donner une permission directement
$user->givePermissionTo('view-users');

// Retirer une permission
$user->revokePermissionTo('view-users');
```

## 🔧 Gestion via l'Interface Web

### Menu "Utilisateurs & Permissions"
Le menu est accessible dans la barre latérale et contient :

1. **Utilisateurs** (`/users`)
   - Liste tous les utilisateurs
   - Créer, modifier, supprimer des utilisateurs
   - Assigner des rôles aux utilisateurs
   - Voir les statistiques d'un utilisateur

2. **Rôles** (`/roles`)
   - Liste tous les rôles
   - Créer, modifier, supprimer des rôles (sauf rôles système)
   - Assigner des permissions aux rôles
   - Voir les permissions d'un rôle

3. **Permissions** (`/permissions`)
   - Liste toutes les permissions groupées par catégorie
   - Voir les rôles ayant une permission spécifique

## 🔑 Compte Super Admin par Défaut

```
Email: admin@admin.com
Mot de passe: password
```

**⚠️ Important** : Changez ce mot de passe immédiatement après la première connexion !

## 📝 Commandes Artisan Utiles

```bash
# Créer les rôles et permissions
php artisan db:seed --class=RolePermissionSeeder --force

# Créer le Super Admin
php artisan db:seed --class=SuperAdminSeeder --force

# Réinitialiser le cache des permissions
php artisan permission:cache-reset

# Voir toutes les permissions
php artisan permission:show

# Créer une nouvelle permission
php artisan permission:create-permission "nom-permission"

# Créer un nouveau rôle
php artisan permission:create-role "Nom du Rôle"
```

## 🛡️ Sécurité

1. **Rôles Système Protégés** : Les rôles "Super Admin" et "Administrateur" ne peuvent pas être supprimés
2. **Dernier Super Admin** : Le système empêche la suppression du dernier Super Admin
3. **Middleware** : Toutes les routes sont protégées par des middlewares de permissions
4. **Validation** : Toutes les entrées sont validées côté serveur

## 🎯 Bonnes Pratiques

1. **Principe du moindre privilège** : Donnez uniquement les permissions nécessaires
2. **Utilisez les rôles** : Préférez assigner des rôles plutôt que des permissions individuelles
3. **Auditez régulièrement** : Vérifiez les permissions des utilisateurs périodiquement
4. **Documentez les changements** : Gardez une trace des modifications de permissions
5. **Testez les permissions** : Vérifiez que les restrictions fonctionnent correctement

## 🆘 Dépannage

### Problème : Les permissions ne fonctionnent pas
```bash
# Réinitialiser le cache des permissions
php artisan permission:cache-reset

# Vider tous les caches
php artisan cache:clear
php artisan config:clear
```

### Problème : Un utilisateur n'a pas accès à une fonctionnalité
1. Vérifiez que l'utilisateur a le bon rôle
2. Vérifiez que le rôle a la bonne permission
3. Vérifiez que le middleware est bien appliqué dans le contrôleur
4. Réinitialisez le cache des permissions

## 📚 Documentation Spatie

Pour plus d'informations, consultez la documentation officielle :
https://spatie.be/docs/laravel-permission/v6/introduction

## ✅ Checklist de Déploiement

- [ ] Exécuter les migrations : `php artisan migrate --force`
- [ ] Exécuter les seeders : `php artisan db:seed --force`
- [ ] Changer le mot de passe du Super Admin
- [ ] Créer les utilisateurs nécessaires
- [ ] Assigner les rôles appropriés
- [ ] Tester les permissions
- [ ] Configurer les sauvegardes de la base de données

