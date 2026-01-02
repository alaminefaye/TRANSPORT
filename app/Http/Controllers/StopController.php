<?php

namespace App\Http\Controllers;

use App\Models\Stop;
use Illuminate\Http\Request;

class StopController extends Controller
{
    /**
     * API pour récupérer tous les arrêts (pour les prix globaux)
     */
    public function api()
    {
        $stops = Stop::select('id', 'name', 'city')->orderBy('name')->get();
        return response()->json($stops);
    }
    
    public function index(Request $request)
    {
        $query = Stop::query();

        // Filtre par nom
        if ($request->has('name') && !empty(trim($request->name))) {
            $query->where('name', 'like', '%' . trim($request->name) . '%');
        }

        // Filtre par ville
        if ($request->has('city') && !empty(trim($request->city))) {
            $query->where('city', 'like', '%' . trim($request->city) . '%');
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $stops = $query->latest()->paginate(10)->withQueryString();

        return view('stops.index', compact('stops'));
    }

    public function create()
    {
        return view('stops.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'type' => 'required|in:Gare principale,Arrêt secondaire',
            'address' => 'nullable|string',
        ]);

        Stop::create($validated);

        return redirect()->route('stops.index')
            ->with('success', 'Arrêt créé avec succès.');
    }

    public function show(Stop $stop)
    {
        $stop->load('routes');
        return view('stops.show', compact('stop'));
    }

    public function edit(Stop $stop)
    {
        return view('stops.edit', compact('stop'));
    }

    public function update(Request $request, Stop $stop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'type' => 'required|in:Gare principale,Arrêt secondaire',
            'address' => 'nullable|string',
        ]);

        $stop->update($validated);

        return redirect()->route('stops.index')
            ->with('success', 'Arrêt modifié avec succès.');
    }

    public function destroy(Stop $stop)
    {
        // Vérifier si l'arrêt est utilisé dans des routes
        if ($stop->routes()->count() > 0) {
            return redirect()->route('stops.index')
                ->with('error', 'Impossible de supprimer cet arrêt car il est utilisé dans des routes.');
        }

        $stop->delete();

        return redirect()->route('stops.index')
            ->with('success', 'Arrêt supprimé avec succès.');
    }
}
