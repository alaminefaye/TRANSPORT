<?php

namespace App\Http\Controllers;

use App\Models\Ville;
use Illuminate\Http\Request;

class VilleController extends Controller
{
    public function index(Request $request)
    {
        $query = Ville::query();

        // Filtre par nom
        if ($request->has('name') && !empty(trim($request->name))) {
            $query->where('name', 'like', '%' . trim($request->name) . '%');
        }

        // Filtre par code
        if ($request->has('code') && !empty(trim($request->code))) {
            $query->where('code', 'like', '%' . trim($request->code) . '%');
        }

        // Filtre par statut (actif/inactif)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        $villes = $query->latest()->paginate(10)->withQueryString();

        return view('villes.index', compact('villes'));
    }

    public function create()
    {
        return view('villes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:villes,name',
            'code' => 'nullable|string|max:10|unique:villes,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Ville::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('villes.index')
            ->with('success', 'Ville créée avec succès.');
    }

    public function show(Ville $ville)
    {
        $ville->load(['departureRoutes', 'arrivalRoutes']);
        return view('villes.show', compact('ville'));
    }

    public function edit(Ville $ville)
    {
        return view('villes.edit', compact('ville'));
    }

    public function update(Request $request, Ville $ville)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:villes,name,' . $ville->id,
            'code' => 'nullable|string|max:10|unique:villes,code,' . $ville->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $ville->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('villes.index')
            ->with('success', 'Ville modifiée avec succès.');
    }

    public function destroy(Ville $ville)
    {
        // Vérifier si la ville est utilisée dans des routes
        if ($ville->departureRoutes()->count() > 0 || $ville->arrivalRoutes()->count() > 0) {
            return redirect()->route('villes.index')
                ->with('error', 'Impossible de supprimer cette ville car elle est utilisée dans des routes.');
        }

        $ville->delete();

        return redirect()->route('villes.index')
            ->with('success', 'Ville supprimée avec succès.');
    }
}
