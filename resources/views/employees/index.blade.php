@extends('layouts.app')

@section('title', 'Gestion des Employés')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des Employés</h5>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">Nouvel Employé</a>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('employees.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="{{ request('name') }}" placeholder="Nom de l'employé">
            </div>
            
            <div class="col-md-3">
                <label for="matricule" class="form-label">Matricule</label>
                <input type="text" class="form-control" id="matricule" name="matricule" 
                       value="{{ request('matricule') }}" placeholder="Matricule">
            </div>
            
            <div class="col-md-3">
                <label for="position" class="form-label">Poste</label>
                <input type="text" class="form-control" id="position" name="position" 
                       value="{{ request('position') }}" placeholder="Poste">
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="Actif" {{ request('status') == 'Actif' ? 'selected' : '' }}>Actif</option>
                    <option value="Inactif" {{ request('status') == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                    <option value="Congé" {{ request('status') == 'Congé' ? 'selected' : '' }}>Congé</option>
                    <option value="Démissionné" {{ request('status') == 'Démissionné' ? 'selected' : '' }}>Démissionné</option>
                </select>
            </div>
            
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search"></i> Rechercher
                </button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-x"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['name', 'matricule', 'position', 'status']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $employees->total() }} employé(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('employees.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Poste</th>
                        <th>Statut</th>
                        <th>Date d'embauche</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->matricule ?? '-' }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
                        <td>{{ $employee->email ?? '-' }}</td>
                        <td>{{ $employee->position ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $employee->status === 'Actif' ? 'success' : ($employee->status === 'Inactif' ? 'secondary' : ($employee->status === 'Congé' ? 'warning' : 'danger')) }}">
                                {{ $employee->status }}
                            </span>
                        </td>
                        <td>{{ $employee->hire_date ? $employee->hire_date->format('d/m/Y') : '-' }}</td>
                        <td>
                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-info">Voir</a>
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            @if(request()->hasAny(['name', 'matricule', 'position', 'status']))
                                Aucun employé trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun employé enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $employees->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

