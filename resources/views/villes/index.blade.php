@extends('layouts.app')

@section('title', 'Gestion des Villes')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Villes</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('villes.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouvelle Ville</span>
                <span class="d-sm-none">Nouvelle</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['name', 'code', 'is_active']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('villes.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ request('name') }}" placeholder="Nom de la ville">
                </div>
                
                <div class="col-md-4">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" class="form-control" id="code" name="code" 
                           value="{{ request('code') }}" placeholder="Code">
                </div>
                
                <div class="col-md-4">
                    <label for="is_active" class="form-label">Statut</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="">Tous</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('villes.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['name', 'code', 'is_active']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $villes->total() }} ville(s) trouvée(s) avec les critères sélectionnés.
                <a href="{{ route('villes.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villes as $ville)
                    <tr>
                        <td><strong>{{ $ville->name }}</strong></td>
                        <td>{{ $ville->code ?? '-' }}</td>
                        <td>{{ Str::limit($ville->description ?? '-', 50) }}</td>
                        <td>
                            <span class="badge bg-{{ $ville->is_active ? 'success' : 'secondary' }}">
                                {{ $ville->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('villes.show', $ville) }}" class="btn btn-sm btn-outline-info">Voir</a>
                            <a href="{{ route('villes.edit', $ville) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form action="{{ route('villes.destroy', $ville) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ville ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            @if(request()->hasAny(['name', 'code', 'is_active']))
                                Aucune ville trouvée avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('villes.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucune ville enregistrée.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $villes->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

