@extends('layouts.app')

@section('title', 'Gestion des Arrêts')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Arrêts</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('stops.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouvel Arrêt</span>
                <span class="d-sm-none">Nouveau</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['name', 'city', 'type']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('stops.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ request('name') }}" placeholder="Nom de l'arrêt">
                </div>
                
                <div class="col-md-4">
                    <label for="city" class="form-label">Ville</label>
                    <input type="text" class="form-control" id="city" name="city" 
                           value="{{ request('city') }}" placeholder="Ville">
                </div>
                
                <div class="col-md-4">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        <option value="Gare principale" {{ request('type') == 'Gare principale' ? 'selected' : '' }}>Gare principale</option>
                        <option value="Arrêt secondaire" {{ request('type') == 'Arrêt secondaire' ? 'selected' : '' }}>Arrêt secondaire</option>
                    </select>
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('stops.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['name', 'city', 'type']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $stops->total() }} arrêt(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('stops.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Ville</th>
                        <th>Type</th>
                        <th>Adresse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stops as $stop)
                    <tr>
                        <td>{{ $stop->name }}</td>
                        <td>{{ $stop->city }}</td>
                        <td>
                            <span class="badge bg-{{ $stop->type === 'Gare principale' ? 'primary' : 'secondary' }}">
                                {{ $stop->type }}
                            </span>
                        </td>
                        <td>{{ $stop->address ?? '-' }}</td>
                        <td>
                            <a href="{{ route('stops.show', $stop) }}" class="btn btn-sm btn-outline-info">Voir</a>
                            <a href="{{ route('stops.edit', $stop) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form action="{{ route('stops.destroy', $stop) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet arrêt ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            @if(request()->hasAny(['name', 'city', 'type']))
                                Aucun arrêt trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('stops.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun arrêt enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $stops->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

