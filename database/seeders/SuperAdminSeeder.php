<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Administrateur',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0000000000',
            ]
        );

        // Assigner le rôle Super Admin
        $superAdmin->assignRole('Super Admin');

        $this->command->info('Super Admin créé avec succès!');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Mot de passe: password');
    }
}

