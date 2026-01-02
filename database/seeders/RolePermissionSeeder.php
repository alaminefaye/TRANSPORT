<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer toutes les permissions
        $permissions = [
            // Dashboard
            'view-dashboard',

            // Gestion des tickets
            'view-tickets',
            'create-tickets',
            'edit-tickets',
            'delete-tickets',
            'cancel-tickets',
            'board-tickets',
            'disembark-tickets',
            'calculate-ticket-price',
            'view-available-seats',
            'search-ticket-qr',
            'retrieve-cancelled-tickets',

            // Gestion des trajets (Trips)
            'view-trips',
            'create-trips',
            'edit-trips',
            'delete-trips',

            // Gestion des routes (Trajets)
            'view-routes',
            'create-routes',
            'edit-routes',
            'delete-routes',

            // Gestion des villes
            'view-villes',
            'create-villes',
            'edit-villes',
            'delete-villes',

            // Gestion des arrêts (Stops)
            'view-stops',
            'create-stops',
            'edit-stops',
            'delete-stops',
            'view-stops-api',

            // Configuration des tarifs (Route Stop Prices)
            'view-route-stop-prices',
            'create-route-stop-prices',
            'edit-route-stop-prices',
            'delete-route-stop-prices',

            // Gestion des clients (Programme de fidélité)
            'view-clients',
            'view-client-details',
            'search-clients-by-phone',

            // Gestion d'administration
            'view-employees',
            'create-employees',
            'edit-employees',
            'delete-employees',

            // Gestion des bus
            'view-buses',
            'create-buses',
            'edit-buses',
            'delete-buses',

            // Gestion du carburant
            'view-fuel-records',
            'create-fuel-records',
            'edit-fuel-records',
            'delete-fuel-records',

            // Gestion des colis
            'view-parcels',
            'create-parcels',
            'edit-parcels',
            'delete-parcels',
            'mark-parcel-retrieved',
            'view-retrieved-parcels',

            // Gestion des destinations
            'view-destinations',
            'create-destinations',
            'edit-destinations',
            'delete-destinations',

            // Gestion des agences de réception
            'view-reception-agencies',
            'create-reception-agencies',
            'edit-reception-agencies',
            'delete-reception-agencies',

            // Gestion des dépenses
            'view-expenses',
            'create-expenses',
            'edit-expenses',
            'delete-expenses',

            // Gestion des utilisateurs
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'assign-roles',

            // Gestion des rôles
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // Gestion des permissions
            'view-permissions',
            'assign-permissions',

            // Diagnostic et rapports
            'view-diagnostic',
            'view-reports',
        ];

        // Créer toutes les permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles et leur assigner des permissions
        
        // Rôle: Super Admin (tous les droits)
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Rôle: Administrateur (presque tous les droits sauf gestion des super admins)
        $adminRole = Role::create(['name' => 'Administrateur']);
        $adminRole->givePermissionTo([
            'view-dashboard',
            
            // Tickets
            'view-tickets', 'create-tickets', 'edit-tickets', 'delete-tickets',
            'cancel-tickets', 'board-tickets', 'disembark-tickets',
            'calculate-ticket-price', 'view-available-seats', 'search-ticket-qr',
            'retrieve-cancelled-tickets',
            
            // Trips
            'view-trips', 'create-trips', 'edit-trips', 'delete-trips',
            
            // Routes
            'view-routes', 'create-routes', 'edit-routes', 'delete-routes',
            
            // Villes
            'view-villes', 'create-villes', 'edit-villes', 'delete-villes',
            
            // Stops
            'view-stops', 'create-stops', 'edit-stops', 'delete-stops', 'view-stops-api',
            
            // Route Stop Prices
            'view-route-stop-prices', 'create-route-stop-prices',
            'edit-route-stop-prices', 'delete-route-stop-prices',
            
            // Clients
            'view-clients', 'view-client-details', 'search-clients-by-phone',
            
            // Employees
            'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
            
            // Buses
            'view-buses', 'create-buses', 'edit-buses', 'delete-buses',
            
            // Fuel Records
            'view-fuel-records', 'create-fuel-records', 'edit-fuel-records', 'delete-fuel-records',
            
            // Parcels
            'view-parcels', 'create-parcels', 'edit-parcels', 'delete-parcels',
            'mark-parcel-retrieved', 'view-retrieved-parcels',
            
            // Destinations
            'view-destinations', 'create-destinations', 'edit-destinations', 'delete-destinations',
            
            // Reception Agencies
            'view-reception-agencies', 'create-reception-agencies',
            'edit-reception-agencies', 'delete-reception-agencies',
            
            // Expenses
            'view-expenses', 'create-expenses', 'edit-expenses', 'delete-expenses',
            
            // Users
            'view-users', 'create-users', 'edit-users',
            
            // Roles
            'view-roles',
            
            // Permissions
            'view-permissions',
            
            // Diagnostic
            'view-diagnostic', 'view-reports',
        ]);

        // Rôle: Agent de vente (vente de tickets et gestion des clients)
        $agentRole = Role::create(['name' => 'Agent']);
        $agentRole->givePermissionTo([
            'view-dashboard',
            
            // Tickets
            'view-tickets', 'create-tickets', 'cancel-tickets',
            'calculate-ticket-price', 'view-available-seats',
            'search-ticket-qr', 'retrieve-cancelled-tickets',
            
            // Trips
            'view-trips',
            
            // Routes
            'view-routes',
            
            // Villes
            'view-villes',
            
            // Stops
            'view-stops', 'view-stops-api',
            
            // Route Stop Prices
            'view-route-stop-prices',
            
            // Clients
            'view-clients', 'view-client-details', 'search-clients-by-phone',
            
            // Parcels
            'view-parcels', 'create-parcels', 'edit-parcels',
            'mark-parcel-retrieved', 'view-retrieved-parcels',
        ]);

        // Rôle: Chef Parc (gestion des bus et carburant)
        $chefParcRole = Role::create(['name' => 'Chef Parc']);
        $chefParcRole->givePermissionTo([
            'view-dashboard',
            
            // Buses
            'view-buses', 'create-buses', 'edit-buses', 'delete-buses',
            
            // Fuel Records
            'view-fuel-records', 'create-fuel-records', 'edit-fuel-records', 'delete-fuel-records',
            
            // Trips
            'view-trips', 'create-trips', 'edit-trips',
            
            // Expenses
            'view-expenses', 'create-expenses',
            
            // Diagnostic
            'view-diagnostic', 'view-reports',
        ]);

        // Rôle: Chauffeur (gestion des trips et embarquement)
        $chauffeurRole = Role::create(['name' => 'Chauffeur']);
        $chauffeurRole->givePermissionTo([
            'view-dashboard',
            
            // Tickets
            'view-tickets', 'board-tickets', 'disembark-tickets', 'search-ticket-qr',
            
            // Trips
            'view-trips',
            
            // Routes
            'view-routes',
            
            // Fuel Records
            'view-fuel-records',
            
            // Parcels
            'view-parcels',
        ]);

        // Rôle: Comptable (gestion financière)
        $comptableRole = Role::create(['name' => 'Comptable']);
        $comptableRole->givePermissionTo([
            'view-dashboard',
            
            // Tickets
            'view-tickets',
            
            // Expenses
            'view-expenses', 'create-expenses', 'edit-expenses', 'delete-expenses',
            
            // Fuel Records
            'view-fuel-records',
            
            // Parcels
            'view-parcels',
            
            // Clients
            'view-clients', 'view-client-details',
            
            // Diagnostic
            'view-diagnostic', 'view-reports',
        ]);

        // Rôle: Client (accès minimal)
        $clientRole = Role::create(['name' => 'Client']);
        $clientRole->givePermissionTo([
            'view-dashboard',
            'view-tickets',
        ]);

        $this->command->info('Rôles et permissions créés avec succès!');
        $this->command->info('');
        $this->command->info('Rôles créés:');
        $this->command->info('- Super Admin (toutes les permissions)');
        $this->command->info('- Administrateur (gestion complète sauf super admin)');
        $this->command->info('- Agent (vente de tickets et colis)');
        $this->command->info('- Chef Parc (gestion des bus et carburant)');
        $this->command->info('- Chauffeur (embarquement et débarquement)');
        $this->command->info('- Comptable (gestion financière)');
        $this->command->info('- Client (consultation uniquement)');
    }
}


