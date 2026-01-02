<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'sidebar_text_color' => 'nullable|string|max:7',
            'sidebar_active_bg_color' => 'nullable|string|max:7',
            'sidebar_active_text_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Update company name
        if ($request->has('company_name')) {
            Setting::set('company_name', $request->company_name, 'text', 'Nom de l\'entreprise');
        }

        // Update primary color
        if ($request->has('primary_color')) {
            Setting::set('primary_color', $request->primary_color, 'color', 'Couleur principale');
    }

        // Update secondary color
        if ($request->has('secondary_color')) {
            Setting::set('secondary_color', $request->secondary_color, 'color', 'Couleur secondaire');
        }

        // Update sidebar text color
        if ($request->has('sidebar_text_color')) {
            Setting::set('sidebar_text_color', $request->sidebar_text_color, 'color', 'Couleur du texte du menu (sidebar)');
    }

        // Update sidebar active background color
        if ($request->has('sidebar_active_bg_color')) {
            Setting::set('sidebar_active_bg_color', $request->sidebar_active_bg_color, 'color', 'Couleur de fond de l\'élément actif du menu');
    }

        // Update sidebar active text color
        if ($request->has('sidebar_active_text_color')) {
            Setting::set('sidebar_active_text_color', $request->sidebar_active_text_color, 'color', 'Couleur du texte de l\'élément actif du menu');
        }

        // Update logo
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
    }

            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            Setting::set('company_logo', $logoPath, 'image', 'Logo de l\'entreprise');
        }

        // Clear all settings cache
        Setting::clearCache();

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres mis à jour avec succès!');
    }
}
