# Installation du Système de Gestion des Utilisateurs et Permissions

## ✅ Ce qui a été installé et configuré

### 1. Package Spatie Laravel Permission
- ✅ Installation via Composer
- ✅ Publication des migrations et configuration
- ✅ Configuration du modèle User avec le trait `HasRoles`

### 2. Base de données
- ✅ Migrations créées pour les tables :
  - `permissions`
  - `roles`
  - `model_has_permissions`
  - `model_has_roles`
  - `role_has_permissions`
- ✅ Migrations exécutées avec succès

### 3. Seeders
- ✅ `RolePermissionSeeder` : Crée 7 rôles et 120+ permissions
- ✅ `SuperAdminSeeder` : Crée un utilisateur Super Admin
- ✅ Seeders exécutés avec succès

### 4. Contrôleurs
- ✅ `UserController` : Gestion CRUD des utilisateurs
- ✅ `RoleController` : Gestion CRUD des rôles
- ✅ `PermissionController` : Consultation des permissions

### 5. Routes
- ✅ `/users` : Gestion des utilisateurs
- ✅ `/roles` : Gestion des rôles
- ✅ `/permissions` : Consultation des permissions

### 6. Vues (Blade)
#### Utilisateurs
- ✅ `users/index.blade.php` : Liste des utilisateurs
- ✅ `users/create.blade.php` : Créer un utilisateur
- ✅ `users/edit.blade.php` : Modifier un utilisateur
- ✅ `users/show.blade.php` : Détails d'un utilisateur

#### Rôles
- ✅ `roles/index.blade.php` : Liste des rôles
- ✅ `roles/create.blade.php` : Créer un rôle
- ✅ `roles/edit.blade.php` : Modifier un rôle
- ✅ `roles/show.blade.php` : Détails d'un rôle

#### Permissions
- ✅ `permissions/index.blade.php` : Liste des permissions
- ✅ `permissions/show.blade.php` : Détails d'une permission

### 7. Menu de navigation
- ✅ Nouveau menu "Utilisateurs & Permissions" ajouté dans `layouts/app.blade.php`
- ✅ Sous-menus : Utilisateurs, Rôles, Permissions
- ✅ Protection par permissions (@can, @canany)

## 🎯 Rôles créés

1. **Super Admin** - Toutes les permissions
2. **Administrateur** - Gestion complète (sauf super admin)
3. **Agent** - Vente de tickets et colis
4. **Chef Parc** - Gestion des bus et carburant
5. **Chauffeur** - Embarquement/débarquement
6. **Comptable** - Gestion financière
7. **Client** - Consultation uniquement

## 🔐 Compte Super Admin

```
Email: admin@admin.com
Mot de passe: password
```

**⚠️ IMPORTANT : Changez ce mot de passe immédiatement !**

## 📋 Permissions créées (par catégorie)

### Dashboard (1)
- view-dashboard

### Tickets (12)
- view-tickets, create-tickets, edit-tickets, delete-tickets
- cancel-tickets, board-tickets, disembark-tickets
- calculate-ticket-price, view-available-seats
- search-ticket-qr, retrieve-cancelled-tickets

### Trips (4)
- view-trips, create-trips, edit-trips, delete-trips

### Routes (4)
- view-routes, create-routes, edit-routes, delete-routes

### Villes (4)
- view-villes, create-villes, edit-villes, delete-villes

### Stops (5)
- view-stops, create-stops, edit-stops, delete-stops, view-stops-api

### Route Stop Prices (4)
- view-route-stop-prices, create-route-stop-prices
- edit-route-stop-prices, delete-route-stop-prices

### Clients (3)
- view-clients, view-client-details, search-clients-by-phone

### Employees (4)
- view-employees, create-employees, edit-employees, delete-employees

### Buses (4)
- view-buses, create-buses, edit-buses, delete-buses

### Fuel Records (4)
- view-fuel-records, create-fuel-records
- edit-fuel-records, delete-fuel-records

### Parcels (6)
- view-parcels, create-parcels, edit-parcels, delete-parcels
- mark-parcel-retrieved, view-retrieved-parcels

### Destinations (4)
- view-destinations, create-destinations
- edit-destinations, delete-destinations

### Reception Agencies (4)
- view-reception-agencies, create-reception-agencies
- edit-reception-agencies, delete-reception-agencies

### Expenses (4)
- view-expenses, create-expenses, edit-expenses, delete-expenses

### Users (5)
- view-users, create-users, edit-users, delete-users, assign-roles

### Roles (4)
- view-roles, create-roles, edit-roles, delete-roles

### Permissions (2)
- view-permissions, assign-permissions

### Diagnostic (2)
- view-diagnostic, view-reports

**Total : 120+ permissions**

## 🚀 Comment utiliser

### 1. Se connecter
```
URL: http://votre-domaine/login
Email: admin@admin.com
Mot de passe: password
```

### 2. Accéder au menu
- Dans la barre latérale, cliquez sur "Utilisateurs & Permissions"
- Vous verrez 3 sous-menus : Utilisateurs, Rôles, Permissions

### 3. Créer un utilisateur
1. Allez dans "Utilisateurs"
2. Cliquez sur "Nouvel utilisateur"
3. Remplissez le formulaire
4. Sélectionnez un ou plusieurs rôles
5. Cliquez sur "Créer l'utilisateur"

### 4. Créer un rôle personnalisé
1. Allez dans "Rôles"
2. Cliquez sur "Nouveau rôle"
3. Donnez un nom au rôle
4. Sélectionnez les permissions souhaitées
5. Cliquez sur "Créer le rôle"

### 5. Modifier les permissions d'un rôle
1. Allez dans "Rôles"
2. Cliquez sur "Modifier" pour le rôle souhaité
3. Cochez/décochez les permissions
4. Cliquez sur "Mettre à jour"

## 🔧 Commandes utiles

```bash
# Réinitialiser le cache des permissions
php artisan permission:cache-reset

# Recréer les rôles et permissions
php artisan db:seed --class=RolePermissionSeeder --force

# Recréer le Super Admin
php artisan db:seed --class=SuperAdminSeeder --force

# Voir le statut des migrations
php artisan migrate:status
```

## 📚 Documentation

Consultez le fichier `PERMISSIONS_GUIDE.md` pour une documentation complète sur :
- L'utilisation des permissions dans le code
- Les bonnes pratiques
- Le dépannage
- Les exemples de code

## 🎨 Interface utilisateur

Toutes les vues utilisent le template Sneat existant avec :
- Design moderne et responsive
- Icônes Boxicons
- Badges colorés pour les rôles et permissions
- Tables paginées
- Formulaires validés
- Messages de confirmation
- Protection CSRF

## 🛡️ Sécurité

- ✅ Middleware de permissions sur tous les contrôleurs
- ✅ Protection des rôles système (Super Admin, Administrateur)
- ✅ Empêche la suppression du dernier Super Admin
- ✅ Validation des entrées côté serveur
- ✅ Protection CSRF sur tous les formulaires
- ✅ Hachage des mots de passe avec bcrypt

## ✨ Fonctionnalités

### Gestion des utilisateurs
- Liste paginée avec recherche
- Création avec validation
- Modification avec préservation du mot de passe
- Suppression avec confirmation
- Affichage des statistiques (tickets, voyages, etc.)
- Double système de rôles (legacy + Spatie)

### Gestion des rôles
- Liste avec nombre de permissions
- Création avec sélection de permissions
- Modification avec permissions groupées par catégorie
- Suppression (sauf rôles système)
- Affichage des permissions par catégorie

### Gestion des permissions
- Liste groupée par catégorie
- Affichage des rôles ayant chaque permission
- Interface en lecture seule (les permissions sont gérées via les rôles)

## 🎯 Prochaines étapes recommandées

1. **Changer le mot de passe du Super Admin**
2. **Créer vos utilisateurs réels**
3. **Tester les permissions** en vous connectant avec différents rôles
4. **Personnaliser les rôles** selon vos besoins
5. **Configurer les sauvegardes** de la base de données
6. **Former les utilisateurs** à l'utilisation du système

## 📞 Support

Pour toute question ou problème :
1. Consultez `PERMISSIONS_GUIDE.md`
2. Vérifiez les logs Laravel : `storage/logs/laravel.log`
3. Réinitialisez le cache : `php artisan cache:clear`
4. Documentation Spatie : https://spatie.be/docs/laravel-permission

---

**Installation terminée avec succès ! 🎉**

