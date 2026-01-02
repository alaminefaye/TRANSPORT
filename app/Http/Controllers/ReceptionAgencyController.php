<?php

namespace App\Http\Controllers;

use App\Models\ReceptionAgency;
use Illuminate\Http\Request;

class ReceptionAgencyController extends Controller
{
    public function index(Request $request)
    {
        $query = ReceptionAgency::query();

        // Filtre par nom
        if ($request->has('name') && !empty(trim($request->name))) {
            $query->where('name', 'like', '%' . trim($request->name) . '%');
        }

        $agencies = $query->latest()->paginate(10)->withQueryString();

        return view('reception-agencies.index', compact('agencies'));
    }

    public function create()
    {
        return view('reception-agencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:reception_agencies,name',
        ]);

        ReceptionAgency::create($validated);

        return redirect()->route('reception-agencies.index')
            ->with('success', 'Agence de réception créée avec succès.');
    }

    public function show(ReceptionAgency $receptionAgency)
    {
        $receptionAgency->loadCount('parcels');
        return view('reception-agencies.show', compact('receptionAgency'));
    }

    public function edit(ReceptionAgency $receptionAgency)
    {
        return view('reception-agencies.edit', compact('receptionAgency'));
    }

    public function update(Request $request, ReceptionAgency $receptionAgency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:reception_agencies,name,' . $receptionAgency->id,
        ]);

        $receptionAgency->update($validated);

        return redirect()->route('reception-agencies.index')
            ->with('success', 'Agence de réception modifiée avec succès.');
    }

    public function destroy(ReceptionAgency $receptionAgency)
    {
        // Vérifier si l'agence est utilisée dans des colis
        if ($receptionAgency->parcels()->count() > 0) {
            return redirect()->route('reception-agencies.index')
                ->with('error', 'Impossible de supprimer cette agence car elle est utilisée dans des colis.');
        }

        $receptionAgency->delete();

        return redirect()->route('reception-agencies.index')
            ->with('success', 'Agence de réception supprimée avec succès.');
    }
}
