@extends('layouts.app')

@section('title', 'Détails du Voyage')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations du Voyage</h5>
                <div>
                    <a href="{{ route('trips.edit', $trip) }}" class="btn btn-sm btn-primary">Modifier</a>
                    <a href="{{ route('tickets.create', ['trip_id' => $trip->id]) }}" class="btn btn-sm btn-success">Vendre un ticket</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Trajet:</th>
                        <td>{{ $trip->route->departure_city }} → {{ $trip->route->arrival_city }}</td>
                    </tr>
                    <tr>
                        <th>Bus:</th>
                        <td>{{ $trip->bus->immatriculation }} ({{ $trip->bus->capacity }} places - {{ $trip->bus->type }})</td>
                    </tr>
                    <tr>
                        <th>Date & Heure de départ:</th>
                        <td>{{ $trip->departure_time->format('d/m/Y H:i') }}</td>
                    </tr>
                    @if($trip->arrival_time)
                    <tr>
                        <th>Date & Heure d'arrivée:</th>
                        <td>{{ $trip->arrival_time->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Chauffeur:</th>
                        <td>{{ $trip->driver ? $trip->driver->name : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
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
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Tickets vendus</h5>
            </div>
            <div class="card-body">
                @if($trip->tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N° Ticket</th>
                                    <th>Passager</th>
                                    <th>De → À</th>
                                    <th>Siège</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trip->tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->ticket_number }}</td>
                                    <td>{{ $ticket->passenger_name }}</td>
                                    <td>{{ $ticket->fromStop->name }} → {{ $ticket->toStop->name }}</td>
                                    <td>{{ $ticket->seat_number }}</td>
                                    <td>{{ number_format($ticket->price, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge bg-{{ $ticket->status === 'Terminé' ? 'success' : ($ticket->status === 'Embarqué' ? 'warning' : 'info') }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-info">Voir</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Aucun ticket vendu pour ce voyage.</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Arrêts</h5>
            </div>
            <div class="card-body">
                <ol class="list-group list-group-numbered">
                    @foreach($trip->route->stops as $stop)
                    <li class="list-group-item">
                        {{ $stop->name }} ({{ $stop->city }})
                        @if($stop->pivot->estimated_time)
                            <br><small class="text-muted">{{ substr($stop->pivot->estimated_time, 0, 5) }}</small>
                        @endif
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('trips.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

