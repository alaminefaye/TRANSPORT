<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Destination::query();

        // Filtre par nom
        if ($request->has('name') && !empty(trim($request->name))) {
            $query->where('name', 'like', '%' . trim($request->name) . '%');
        }

        $destinations = $query->latest()->paginate(10)->withQueryString();

        return view('destinations.index', compact('destinations'));
    }

    public function create()
    {
        return view('destinations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:destinations,name',
        ]);

        Destination::create($validated);

        return redirect()->route('destinations.index')
            ->with('success', 'Destination créée avec succès.');
    }

    public function show(Destination $destination)
    {
        $destination->loadCount('parcels');
        return view('destinations.show', compact('destination'));
    }

    public function edit(Destination $destination)
    {
        return view('destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:destinations,name,' . $destination->id,
        ]);

        $destination->update($validated);

        return redirect()->route('destinations.index')
            ->with('success', 'Destination modifiée avec succès.');
    }

    public function destroy(Destination $destination)
    {
        // Vérifier si la destination est utilisée dans des colis
        if ($destination->parcels()->count() > 0) {
            return redirect()->route('destinations.index')
                ->with('error', 'Impossible de supprimer cette destination car elle est utilisée dans des colis.');
        }

        $destination->delete();

        return redirect()->route('destinations.index')
            ->with('success', 'Destination supprimée avec succès.');
    }
}
