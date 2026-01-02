<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BusController extends Controller
{
    public function index(Request $request)
    {
        $query = Bus::query();

        // Filtre par immatriculation
        if ($request->has('immatriculation') && !empty(trim($request->immatriculation))) {
            $query->where('immatriculation', 'like', '%' . trim($request->immatriculation) . '%');
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $buses = $query->latest()->paginate(10)->withQueryString();

        return view('buses.index', compact('buses'));
    }

    public function create()
    {
        return view('buses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'immatriculation' => 'required|string|unique:buses,immatriculation',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:VIP,Classique,Climatisé',
            'status' => 'required|in:Disponible,En voyage,En panne',
            'notes' => 'nullable|string',
        ]);

        Bus::create($validated);

        return redirect()->route('buses.index')
            ->with('success', 'Bus créé avec succès.');
    }

    public function show(Bus $bus)
    {
        $bus->load('trips');
        return view('buses.show', compact('bus'));
    }

    public function edit(Bus $bus)
    {
        return view('buses.edit', compact('bus'));
    }

    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'immatriculation' => 'required|string|unique:buses,immatriculation,' . $bus->id,
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:VIP,Classique,Climatisé',
            'status' => 'required|in:Disponible,En voyage,En panne',
            'notes' => 'nullable|string',
        ]);

        $bus->update($validated);

        return redirect()->route('buses.index')
            ->with('success', 'Bus modifié avec succès.');
    }

    public function destroy(Bus $bus)
    {
        // Vérifier si le bus a des voyages
        if ($bus->trips()->count() > 0) {
            return redirect()->route('buses.index')
                ->with('error', 'Impossible de supprimer ce bus car il a des voyages associés.');
        }

        $bus->delete();

        return redirect()->route('buses.index')
            ->with('success', 'Bus supprimé avec succès.');
    }
}
