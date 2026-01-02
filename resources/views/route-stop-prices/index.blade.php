@extends('layouts.app')

@section('title', 'Gestion des Tarifs')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Tarifs</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('route-stop-prices.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouveau Tarif</span>
                <span class="d-sm-none">Nouveau</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['route_id', 'from_stop_id', 'to_stop_id', 'is_active']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('route-stop-prices.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="route_id" class="form-label">Trajet</label>
                    <select class="form-select" id="route_id" name="route_id">
                        <option value="">Tous les trajets</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->departure_city }} → {{ $route->arrival_city }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="from_stop_id" class="form-label">Arrêt de départ</label>
                    <select class="form-select" id="from_stop_id" name="from_stop_id">
                        <option value="">Tous les arrêts</option>
                        @foreach($stops as $stop)
                            <option value="{{ $stop->id }}" {{ request('from_stop_id') == $stop->id ? 'selected' : '' }}>
                                {{ $stop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="to_stop_id" class="form-label">Arrêt d'arrivée</label>
                    <select class="form-select" id="to_stop_id" name="to_stop_id">
                        <option value="">Tous les arrêts</option>
                        @foreach($stops as $stop)
                            <option value="{{ $stop->id }}" {{ request('to_stop_id') == $stop->id ? 'selected' : '' }}>
                                {{ $stop->name }}
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
                    <a href="{{ route('route-stop-prices.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['route_id', 'from_stop_id', 'to_stop_id', 'is_active']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $prices->total() }} tarif(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('route-stop-prices.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Trajet</th>
                        <th>De</th>
                        <th>À</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices as $price)
                    <tr>
                        <td>
                            @if($price->route_id)
                                <span class="badge bg-info">Spécifique</span>
                            @else
                                <span class="badge bg-success">✨ Global</span>
                            @endif
                        </td>
                        <td>
                            @if($price->route)
                                {{ $price->route->departure_city }} → {{ $price->route->arrival_city }}
                            @else
                                <em class="text-muted">Tous les trajets</em>
                            @endif
                        </td>
                        <td>{{ $price->fromStop->name }}</td>
                        <td>{{ $price->toStop->name }}</td>
                        <td>{{ number_format($price->price, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge bg-{{ $price->is_active ? 'success' : 'secondary' }}">
                                {{ $price->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('route-stop-prices.edit', $price) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form action="{{ route('route-stop-prices.destroy', $price) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce tarif ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            @if(request()->hasAny(['route_id', 'from_stop_id', 'to_stop_id', 'is_active']))
                                Aucun tarif trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('route-stop-prices.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun tarif enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $prices->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

