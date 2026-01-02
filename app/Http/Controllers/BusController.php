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

    /**
     * Affiche la page de configuration de la disposition des sièges
     */
    public function configureSeats(Bus $bus)
    {
        return view('buses.configure-seats', compact('bus'));
    }

    /**
     * Sauvegarde la disposition des sièges
     */
    public function saveSeatLayout(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'seat_layout' => 'required|array',
            'seat_layout.*' => 'required|integer|min:1',
        ]);

        // Convertir tous les éléments en entiers
        $layout = array_map('intval', $validated['seat_layout']);
        $expectedSeats = range(1, $bus->capacity);
        
        // Vérifier le nombre de sièges
        if (count($layout) !== count($expectedSeats)) {
            return redirect()->back()
                ->with('error', 'Le nombre de sièges dans la disposition (' . count($layout) . ') ne correspond pas à la capacité du bus (' . $bus->capacity . ').')
                ->withInput();
        }

        // Vérifier que tous les numéros de sièges sont uniques
        $uniqueLayout = array_unique($layout);
        if (count($layout) !== count($uniqueLayout)) {
            $duplicates = array_diff_assoc($layout, $uniqueLayout);
            $duplicateValues = array_unique(array_values($duplicates));
            return redirect()->back()
                ->with('error', 'Les numéros de sièges suivants sont en double: ' . implode(', ', $duplicateValues) . '. Chaque siège doit avoir un numéro unique.')
                ->withInput();
        }

        // Vérifier que tous les sièges de 1 à capacity sont présents
        $sortedLayout = $layout;
        sort($sortedLayout);
        
        if ($sortedLayout !== $expectedSeats) {
            $missing = array_diff($expectedSeats, $sortedLayout);
            $extra = array_diff($sortedLayout, $expectedSeats);
            
            $errorMsg = 'La disposition doit contenir tous les sièges de 1 à ' . $bus->capacity . ' exactement une fois.';
            
            if (!empty($missing)) {
                $errorMsg .= ' Sièges manquants: ' . implode(', ', $missing) . '.';
            }
            
            if (!empty($extra)) {
                $errorMsg .= ' Sièges en trop: ' . implode(', ', $extra) . '.';
            }
            
            return redirect()->back()
                ->with('error', $errorMsg)
                ->withInput();
        }

        $bus->update([
            'seat_layout' => $layout
        ]);

        return redirect()->route('buses.show', $bus)
            ->with('success', 'Disposition des sièges sauvegardée avec succès.');
    }
}
