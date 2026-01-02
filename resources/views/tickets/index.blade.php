@extends('layouts.app')

@section('title', 'Gestion des Tickets')

@push('page-css')
<style>
    .pagination {
        margin-bottom: 0;
        display: flex;
        align-items: center;
    }
    .pagination .page-item {
        display: inline-block;
        margin: 0 2px;
    }
    .pagination .page-link {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        display: inline-block;
        min-width: 2.5rem;
        text-align: center;
    }
    .pagination .page-link span,
    .pagination .page-link {
        font-size: 1rem !important;
        font-weight: normal !important;
        line-height: 1.5 !important;
    }
    .pagination .page-item.disabled .page-link,
    .pagination .page-item.active .page-link {
        font-size: 1rem !important;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Tickets</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('tickets.search.qr') }}" class="btn btn-info btn-sm">
                <i class="bx bx-qr-scan"></i> <span class="d-none d-sm-inline">Rechercher par QR Code</span>
                <span class="d-sm-none">QR Code</span>
            </a>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Vendre un Ticket</span>
                <span class="d-sm-none">Vendre</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['ticket_number', 'passenger_name', 'passenger_phone', 'status', 'route_id', 'from_stop_id', 'to_stop_id', 'date_from', 'date_to']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="ticket_number" class="form-label">N° Ticket</label>
                    <input type="text" class="form-control" id="ticket_number" name="ticket_number" 
                           value="{{ request('ticket_number') }}" placeholder="Ex: TKT-RKVFDN7W">
                </div>
                
                <div class="col-md-3">
                    <label for="passenger_name" class="form-label">Nom du passager</label>
                    <input type="text" class="form-control" id="passenger_name" name="passenger_name" 
                           value="{{ request('passenger_name') }}" placeholder="Nom du passager">
                </div>
                
                <div class="col-md-3">
                    <label for="passenger_phone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="passenger_phone" name="passenger_phone" 
                           value="{{ request('passenger_phone') }}" placeholder="Téléphone">
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
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
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['ticket_number', 'passenger_name', 'passenger_phone', 'status', 'route_id', 'from_stop_id', 'to_stop_id', 'date_from', 'date_to']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $tickets->total() }} ticket(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('tickets.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="min-width: 120px;">N° Ticket</th>
                        <th style="min-width: 150px;">Trajet</th>
                        <th style="min-width: 120px;">Passager</th>
                        <th style="min-width: 150px;">De → À</th>
                        <th style="min-width: 60px;">Siège</th>
                        <th style="min-width: 100px;">Prix</th>
                        <th style="min-width: 100px;">Statut</th>
                        <th style="min-width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><small class="text-muted">{{ $ticket->ticket_number }}</small></td>
                        <td><small>{{ $ticket->trip->route->departure_city }} → {{ $ticket->trip->route->arrival_city }}</small></td>
                        <td>{{ $ticket->passenger_name }}</td>
                        <td><small>{{ $ticket->fromStop->name }} → {{ $ticket->toStop->name }}</small></td>
                        <td class="text-center">{{ $ticket->seat_number }}</td>
                        <td class="text-nowrap">{{ number_format($ticket->price, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge bg-{{ $ticket->status === 'Terminé' ? 'success' : ($ticket->status === 'Embarqué' ? 'warning' : ($ticket->status === 'Annulé' ? 'danger' : 'info')) }}">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-info">Voir</a>
                            @if($ticket->status === 'En attente')
                                <form action="{{ route('tickets.board', $ticket) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">Embarquer</button>
                                </form>
                            @endif
                            @if($ticket->status === 'Embarqué')
                                <form action="{{ route('tickets.disembark', $ticket) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning">Débarquer</button>
                                </form>
                            @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            @if(request()->hasAny(['ticket_number', 'passenger_name', 'passenger_phone', 'status', 'route_id', 'from_stop_id', 'to_stop_id', 'date_from', 'date_to']))
                                Aucun ticket trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun ticket enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $tickets->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

