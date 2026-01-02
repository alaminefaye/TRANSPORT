<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        // Filtre par nom
        if ($request->has('name') && !empty(trim($request->name))) {
            $query->where('name', 'like', '%' . trim($request->name) . '%');
        }

        // Filtre par matricule
        if ($request->has('matricule') && !empty(trim($request->matricule))) {
            $query->where('matricule', 'like', '%' . trim($request->matricule) . '%');
        }

        // Filtre par position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:Actif,Inactif,Congé,Démissionné',
            'salary' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Le matricule sera généré automatiquement par le modèle

        // Gérer l'upload de la photo si présent
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('employees', 'public');
        }

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employé créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'matricule' => 'nullable|string|unique:employees,matricule,' . $employee->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:Actif,Inactif,Congé,Démissionné',
            'salary' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Gérer l'upload de la photo si présent
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($employee->photo) {
                \Storage::disk('public')->delete($employee->photo);
            }
            $validated['photo'] = $request->file('photo')->store('employees', 'public');
        }

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employé modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Vérifier si l'employé a un compte utilisateur
        if ($employee->user) {
            return redirect()->route('employees.index')
                ->with('error', 'Impossible de supprimer cet employé car il a un compte utilisateur associé.');
        }

        // Supprimer la photo si elle existe
        if ($employee->photo) {
            \Storage::disk('public')->delete($employee->photo);
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employé supprimé avec succès.');
    }
}
