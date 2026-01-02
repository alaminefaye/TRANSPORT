@extends('layouts.app')

@section('title', 'Gestion des Trajets')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Trajets</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('routes.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouveau Trajet</span>
                <span class="d-sm-none">Nouveau</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['route_number', 'departure_city_id', 'arrival_city_id', 'is_active']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('routes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="route_number" class="form-label">N° Route</label>
                    <input type="text" class="form-control" id="route_number" name="route_number" 
                           value="{{ request('route_number') }}" placeholder="Ex: 1, 2, 3...">
                </div>
                
                <div class="col-md-3">
                    <label for="departure_city_id" class="form-label">Ville de départ</label>
                    <select class="form-select" id="departure_city_id" name="departure_city_id">
                        <option value="">Toutes les villes</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->id }}" {{ request('departure_city_id') == $ville->id ? 'selected' : '' }}>
                                {{ $ville->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="arrival_city_id" class="form-label">Ville d'arrivée</label>
                    <select class="form-select" id="arrival_city_id" name="arrival_city_id">
                        <option value="">Toutes les villes</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->id }}" {{ request('arrival_city_id') == $ville->id ? 'selected' : '' }}>
                                {{ $ville->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
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
                    <a href="{{ route('routes.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['route_number', 'departure_city_id', 'arrival_city_id', 'is_active']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $routes->total() }} trajet(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('routes.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Trajet</th>
                        <th>Distance</th>
                        <th>Durée</th>
                        <th>Arrêts</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $route)
                    <tr>
                        <td><strong>#{{ $route->route_number ?? $route->id }}</strong></td>
                        <td>{{ $route->departure_city }} → {{ $route->arrival_city }}</td>
                        <td>{{ $route->distance }} km</td>
                        <td>{{ $route->estimated_duration }} min</td>
                        <td>{{ $route->stops->count() }} arrêts</td>
                        <td>
                            <span class="badge bg-{{ $route->is_active ? 'success' : 'secondary' }}">
                                {{ $route->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('routes.show', $route) }}" class="btn btn-sm btn-outline-info">Voir</a>
                            <a href="{{ route('routes.edit', $route) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form action="{{ route('routes.destroy', $route) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce trajet ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            @if(request()->hasAny(['route_number', 'departure_city_id', 'arrival_city_id', 'is_active']))
                                Aucun trajet trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('routes.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun trajet enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $routes->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

