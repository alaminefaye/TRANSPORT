<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['createdBy', 'validatedBy']);

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date de début
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filtre par date de fin
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtre par recherche (motif)
        if ($request->has('search') && !empty(trim($request->search))) {
            $query->where('motif', 'like', '%' . trim($request->search) . '%');
        }

        $expenses = $query->latest()->paginate(10)->withQueryString();

        $types = ['divers' => 'Divers', 'ration' => 'Ration', 'carburant' => 'Carburant', 'maintenance' => 'Maintenance', 'autre' => 'Autre'];
        $statuses = ['en_attente' => 'En attente', 'validee' => 'Validée', 'rejetee' => 'Rejetée'];

        return view('expenses.index', compact('expenses', 'types', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:divers,ration,carburant,maintenance,autre',
            'motif' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'invoice_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // 10MB
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'en_attente';

        // Gérer l'upload de la photo
        if ($request->hasFile('invoice_photo')) {
            $photoPath = $request->file('invoice_photo')->store('expenses/invoices', 'public');
            $validated['invoice_photo'] = $photoPath;
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Demande de dépense créée avec succès. Elle est en attente de validation.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $expense->load(['createdBy', 'validatedBy']);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        // Ne permettre la modification que si la dépense est en attente
        if ($expense->status !== 'en_attente') {
            return redirect()->route('expenses.index')
                ->with('error', 'Impossible de modifier une dépense qui a déjà été validée ou rejetée.');
        }

        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        // Ne permettre la modification que si la dépense est en attente
        if ($expense->status !== 'en_attente') {
            return redirect()->route('expenses.index')
                ->with('error', 'Impossible de modifier une dépense qui a déjà été validée ou rejetée.');
        }

        $validated = $request->validate([
            'type' => 'required|in:divers,ration,carburant,maintenance,autre',
            'motif' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'invoice_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // 10MB
            'notes' => 'nullable|string',
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('invoice_photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($expense->invoice_photo && Storage::disk('public')->exists($expense->invoice_photo)) {
                Storage::disk('public')->delete($expense->invoice_photo);
            }
            $photoPath = $request->file('invoice_photo')->store('expenses/invoices', 'public');
            $validated['invoice_photo'] = $photoPath;
        } else {
            // Garder l'ancienne photo si aucune nouvelle n'est uploadée
            unset($validated['invoice_photo']);
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Ne permettre la suppression que si la dépense est en attente
        if ($expense->status !== 'en_attente') {
            return redirect()->route('expenses.index')
                ->with('error', 'Impossible de supprimer une dépense qui a déjà été validée ou rejetée.');
        }

        // Supprimer la photo si elle existe
        if ($expense->invoice_photo && Storage::disk('public')->exists($expense->invoice_photo)) {
            Storage::disk('public')->delete($expense->invoice_photo);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense supprimée avec succès.');
    }

    /**
     * Valider une dépense
     */
    public function validateExpense(Request $request, Expense $expense)
    {
        if ($expense->status !== 'en_attente') {
            return back()->with('error', 'Cette dépense a déjà été traitée.');
        }

        $expense->update([
            'status' => 'validee',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Dépense validée avec succès.');
    }

    /**
     * Rejeter une dépense
     */
    public function rejectExpense(Request $request, Expense $expense)
    {
        if ($expense->status !== 'en_attente') {
            return back()->with('error', 'Cette dépense a déjà été traitée.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $expense->update([
            'status' => 'rejetee',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Dépense rejetée avec succès.');
    }
}
