<?php

namespace App\Http\Controllers;

use App\Models\FuelRecord;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FuelRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FuelRecord::with(['bus', 'createdBy']);

        // Filtre par bus
        if ($request->filled('bus_id')) {
            $query->where('bus_id', $request->bus_id);
        }

        // Filtre par date de début
        if ($request->filled('date_from')) {
            $query->whereDate('refill_date', '>=', $request->date_from);
        }

        // Filtre par date de fin
        if ($request->filled('date_to')) {
            $query->whereDate('refill_date', '<=', $request->date_to);
        }

        $fuelRecords = $query->latest('refill_date')->paginate(10)->withQueryString();
        $buses = Bus::orderBy('immatriculation')->get();

        return view('fuel-records.index', compact('fuelRecords', 'buses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buses = Bus::orderBy('immatriculation')->get();
        return view('fuel-records.create', compact('buses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'amount' => 'required|numeric|min:0',
            'refill_date' => 'required|date',
            'invoice_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // 10MB
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        // Gérer l'upload de la photo
        if ($request->hasFile('invoice_photo')) {
            $photoPath = $request->file('invoice_photo')->store('fuel-records/invoices', 'public');
            $validated['invoice_photo'] = $photoPath;
        }

        FuelRecord::create($validated);

        return redirect()->route('fuel-records.index')
            ->with('success', 'Plein de carburant enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FuelRecord $fuelRecord)
    {
        $fuelRecord->load(['bus', 'createdBy']);
        return view('fuel-records.show', compact('fuelRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FuelRecord $fuelRecord)
    {
        $buses = Bus::orderBy('immatriculation')->get();
        return view('fuel-records.edit', compact('fuelRecord', 'buses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FuelRecord $fuelRecord)
    {
        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'amount' => 'required|numeric|min:0',
            'refill_date' => 'required|date',
            'invoice_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // 10MB
            'notes' => 'nullable|string',
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('invoice_photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($fuelRecord->invoice_photo && Storage::disk('public')->exists($fuelRecord->invoice_photo)) {
                Storage::disk('public')->delete($fuelRecord->invoice_photo);
            }
            $photoPath = $request->file('invoice_photo')->store('fuel-records/invoices', 'public');
            $validated['invoice_photo'] = $photoPath;
        } else {
            // Garder l'ancienne photo si aucune nouvelle n'est uploadée
            unset($validated['invoice_photo']);
        }

        $fuelRecord->update($validated);

        return redirect()->route('fuel-records.index')
            ->with('success', 'Plein de carburant modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FuelRecord $fuelRecord)
    {
        // Supprimer la photo si elle existe
        if ($fuelRecord->invoice_photo && Storage::disk('public')->exists($fuelRecord->invoice_photo)) {
            Storage::disk('public')->delete($fuelRecord->invoice_photo);
        }

        $fuelRecord->delete();

        return redirect()->route('fuel-records.index')
            ->with('success', 'Plein de carburant supprimé avec succès.');
    }
}
