@extends('layouts.app')

@section('title', 'Gestion des Bus')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Bus</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('buses.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouveau Bus</span>
                <span class="d-sm-none">Nouveau</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['immatriculation', 'type', 'status']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('buses.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="immatriculation" class="form-label">Immatriculation</label>
                    <input type="text" class="form-control" id="immatriculation" name="immatriculation" 
                           value="{{ request('immatriculation') }}" placeholder="Immatriculation">
                </div>
                
                <div class="col-md-4">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        <option value="VIP" {{ request('type') == 'VIP' ? 'selected' : '' }}>VIP</option>
                        <option value="Classique" {{ request('type') == 'Classique' ? 'selected' : '' }}>Classique</option>
                        <option value="Climatisé" {{ request('type') == 'Climatisé' ? 'selected' : '' }}>Climatisé</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="Disponible" {{ request('status') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="En voyage" {{ request('status') == 'En voyage' ? 'selected' : '' }}>En voyage</option>
                        <option value="En panne" {{ request('status') == 'En panne' ? 'selected' : '' }}>En panne</option>
                    </select>
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('buses.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['immatriculation', 'type', 'status']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $buses->total() }} bus trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('buses.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Immatriculation</th>
                        <th>Capacité</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buses as $bus)
                    <tr>
                        <td>{{ $bus->immatriculation }}</td>
                        <td>{{ $bus->capacity }} sièges</td>
                        <td>{{ $bus->type }}</td>
                        <td>
                            <span class="badge bg-{{ $bus->status === 'Disponible' ? 'success' : ($bus->status === 'En voyage' ? 'warning' : 'danger') }}">
                                {{ $bus->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('buses.show', $bus) }}" class="btn btn-sm btn-outline-info">Voir</a>
                            <a href="{{ route('buses.edit', $bus) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form action="{{ route('buses.destroy', $bus) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bus ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            @if(request()->hasAny(['immatriculation', 'type', 'status']))
                                Aucun bus trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('buses.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun bus enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $buses->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

