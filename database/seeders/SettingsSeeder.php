<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'company_name',
                'value' => config('app.name', 'Gestion Transport'),
                'type' => 'text',
                'description' => 'Nom de l\'entreprise affiché dans l\'application'
            ],
            [
                'key' => 'primary_color',
                'value' => '#696cff',
                'type' => 'color',
                'description' => 'Couleur principale de l\'application (boutons, liens, etc.)'
            ],
            [
                'key' => 'secondary_color',
                'value' => '#8592a3',
                'type' => 'color',
                'description' => 'Couleur secondaire de l\'application'
            ],
            [
                'key' => 'sidebar_text_color',
                'value' => '#697a8d',
                'type' => 'color',
                'description' => 'Couleur du texte dans le menu latéral (sidebar)'
            ],
            [
                'key' => 'sidebar_active_bg_color',
                'value' => '#696cff',
                'type' => 'color',
                'description' => 'Couleur de fond de l\'élément actif du menu'
            ],
            [
                'key' => 'sidebar_active_text_color',
                'value' => '#ffffff',
                'type' => 'color',
                'description' => 'Couleur du texte de l\'élément actif du menu'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
