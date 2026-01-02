@extends('layouts.app')

@section('title', 'Gestion des Voyages')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Voyages</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('trips.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouveau Voyage</span>
                <span class="d-sm-none">Nouveau</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['route_id', 'bus_id', 'status', 'date_from', 'date_to']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('trips.index') }}" class="row g-3">
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
                    <label for="bus_id" class="form-label">Bus</label>
                    <select class="form-select" id="bus_id" name="bus_id">
                        <option value="">Tous les bus</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->id }}" {{ request('bus_id') == $bus->id ? 'selected' : '' }}>
                                {{ $bus->immatriculation }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date de début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['route_id', 'bus_id', 'status', 'date_from', 'date_to']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $trips->total() }} voyage(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('trips.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Trajet</th>
                        <th>Bus</th>
                        <th>Date & Heure de départ</th>
                        <th>Chauffeur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trips as $trip)
                    <tr>
                        <td>{{ $trip->route->departure_city }} → {{ $trip->route->arrival_city }}</td>
                        <td>{{ $trip->bus->immatriculation }}</td>
                        <td>{{ $trip->departure_time->format('d/m/Y H:i') }}</td>
                        <td>{{ $trip->driver ? $trip->driver->name : '-' }}</td>
                        <td>
                            @php
                                $statusLabels = [
                                    'Scheduled' => 'Programmé',
                                    'In Progress' => 'En cours',
                                    'Completed' => 'Terminé',
                                    'Cancelled' => 'Annulé'
                                ];
                                $statusLabel = $statusLabels[$trip->status] ?? $trip->status;
                            @endphp
                            <span class="badge bg-{{ $trip->status === 'Completed' ? 'success' : ($trip->status === 'In Progress' ? 'warning' : ($trip->status === 'Cancelled' ? 'danger' : 'info')) }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-info">Voir</a>
                            <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            <form action="{{ route('trips.destroy', $trip) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce voyage ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            @if(request()->hasAny(['route_id', 'bus_id', 'status', 'date_from', 'date_to']))
                                Aucun voyage trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('trips.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun voyage enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $trips->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

