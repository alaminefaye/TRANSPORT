@extends('layouts.app')

@section('title', 'Liste des Clients')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Clients</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['name', 'phone', 'email']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="{{ request('name') }}" placeholder="Nom du client">
            </div>
            
            <div class="col-md-4">
                <label for="phone" class="form-label">Téléphone</label>
                <input type="text" class="form-control" id="phone" name="phone" 
                       value="{{ request('phone') }}" placeholder="Téléphone">
            </div>
            
            <div class="col-md-4">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" 
                       value="{{ request('email') }}" placeholder="Email">
            </div>
            
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search"></i> Rechercher
                </button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-x"></i> Réinitialiser
                </a>
            </div>
        </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['name', 'phone', 'email']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $clients->total() }} client(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('clients.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Points de fidélité</th>
                        <th>Nombre de tickets</th>
                        <th>Nombre de courriers</th>
                        <th>Date d'enregistrement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->phone }}</td>
                        <td>{{ $client->email ?? '-' }}</td>
                        <td>
                            @php
                                $points = $client->loyalty_points ?? 0;
                                $canUseFreeTicket = $points >= 10;
                            @endphp
                            <span class="badge {{ $canUseFreeTicket ? 'bg-success' : 'bg-warning' }}" title="{{ $canUseFreeTicket ? 'Voyage gratuit disponible' : 'Voyage gratuit disponible à partir de 10 points' }}">
                                <i class="bx bx-star"></i> {{ $points }} point{{ $points > 1 ? 's' : '' }}
                                @if($canUseFreeTicket)
                                    <i class="bx bx-check-circle ms-1" title="Voyage gratuit disponible"></i>
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $client->tickets_count }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success">{{ $client->sent_parcels_count + $client->received_parcels_count }}</span>
                        </td>
                        <td>{{ $client->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-info">
                                <i class="bx bx-show"></i> Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            @if(request()->hasAny(['name', 'phone', 'email']))
                                Aucun client trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun client enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $clients->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

