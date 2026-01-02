<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignRolesToExistingUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Assignation des rôles Spatie aux utilisateurs existants...');

        // Mapping des rôles legacy vers les rôles Spatie
        $roleMapping = [
            'admin' => 'Administrateur',
            'agent' => 'Agent',
            'chef_parc' => 'Chef Parc',
            'chauffeur' => 'Chauffeur',
            'client' => 'Client',
        ];

        $users = User::all();
        $updated = 0;

        foreach ($users as $user) {
            // Si l'utilisateur a déjà des rôles Spatie, on ne fait rien
            if ($user->roles->count() > 0) {
                $this->command->info("✓ {$user->name} ({$user->email}) a déjà des rôles assignés");
                continue;
            }

            // Assigner le rôle Spatie correspondant au rôle legacy
            if ($user->role && isset($roleMapping[$user->role])) {
                $spathieRole = $roleMapping[$user->role];
                $user->assignRole($spathieRole);
                $updated++;
                $this->command->info("✓ {$user->name} ({$user->email}) → Rôle '{$spathieRole}' assigné");
            } else {
                // Si pas de rôle legacy, assigner le rôle Client par défaut
                $user->assignRole('Client');
                $updated++;
                $this->command->info("✓ {$user->name} ({$user->email}) → Rôle 'Client' assigné par défaut");
            }
        }

        $this->command->info('');
        $this->command->info("✅ {$updated} utilisateur(s) mis à jour avec succès!");
        $this->command->info('Les utilisateurs peuvent maintenant accéder aux fonctionnalités selon leurs rôles.');
    }
}


