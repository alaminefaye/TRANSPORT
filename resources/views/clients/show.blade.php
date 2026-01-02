@extends('layouts.app')

@section('title', 'Détails du Client')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations du Client</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nom:</dt>
                    <dd class="col-sm-8">{{ $client->name }}</dd>
                    
                    <dt class="col-sm-4">Téléphone:</dt>
                    <dd class="col-sm-8">{{ $client->phone }}</dd>
                    
                    <dt class="col-sm-4">Email:</dt>
                    <dd class="col-sm-8">{{ $client->email ?? '-' }}</dd>
                    
                    <dt class="col-sm-4">Date d'enregistrement:</dt>
                    <dd class="col-sm-8">{{ $client->created_at->format('d/m/Y H:i') }}</dd>
                    
                    <dt class="col-sm-4">Nombre de tickets:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-info">{{ $client->tickets()->count() }}</span>
                    </dd>
                    
                    <dt class="col-sm-4">Nombre de courriers:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-success">{{ $client->sentParcels()->count() + $client->receivedParcels()->count() }}</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Historique des Tickets</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>N° Ticket</th>
                                <th>Trajet</th>
                                <th>De → À</th>
                                <th>Siège</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_number }}</td>
                                <td>{{ $ticket->trip->route->departure_city }} → {{ $ticket->trip->route->arrival_city }}</td>
                                <td>{{ $ticket->fromStop->name }} → {{ $ticket->toStop->name }}</td>
                                <td>{{ $ticket->seat_number }}</td>
                                <td>{{ number_format($ticket->price, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="badge bg-{{ $ticket->status === 'Terminé' ? 'success' : ($ticket->status === 'Embarqué' ? 'warning' : ($ticket->status === 'Annulé' ? 'danger' : 'info')) }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-info">Voir</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Aucun ticket pour ce client.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($tickets->hasPages())
                <div class="mt-3">
                    {{ $tickets->links('vendor.pagination.custom') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Historique des Courriers</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="parcelsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab" aria-controls="sent" aria-selected="true">
                            Colis Envoyés ({{ $client->sentParcels()->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="received-tab" data-bs-toggle="tab" data-bs-target="#received" type="button" role="tab" aria-controls="received" aria-selected="false">
                            Colis Reçus ({{ $client->receivedParcels()->count() }})
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="parcelsTabContent">
                    <!-- Colis Envoyés -->
                    <div class="tab-pane fade show active" id="sent" role="tabpanel" aria-labelledby="sent-tab">
                        <div class="table-responsive mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>N° Courrier</th>
                                        <th>Bénéficiaire</th>
                                        <th>Destination</th>
                                        <th>Type</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sentParcels as $parcel)
                                    <tr>
                                        <td>{{ $parcel->mail_number }}</td>
                                        <td>{{ $parcel->recipient_name }}<br><small class="text-muted">{{ $parcel->recipient_phone }}</small></td>
                                        <td>{{ $parcel->destination->name ?? '-' }}</td>
                                        <td>{{ $parcel->parcel_type }}</td>
                                        <td>{{ number_format($parcel->amount ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <span class="badge bg-{{ $parcel->status === 'Récupéré' ? 'success' : ($parcel->status === 'Arrivé' ? 'info' : ($parcel->status === 'En transit' ? 'warning' : 'secondary')) }}">
                                                {{ $parcel->status }}
                                            </span>
                                        </td>
                                        <td>{{ $parcel->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('parcels.show', $parcel) }}" class="btn btn-sm btn-outline-info">Voir</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun colis envoyé par ce client.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($sentParcels->hasPages())
                        <div class="mt-3">
                            {{ $sentParcels->links('vendor.pagination.custom') }}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Colis Reçus -->
                    <div class="tab-pane fade" id="received" role="tabpanel" aria-labelledby="received-tab">
                        <div class="table-responsive mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>N° Courrier</th>
                                        <th>Expéditeur</th>
                                        <th>Destination</th>
                                        <th>Type</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($receivedParcels as $parcel)
                                    <tr>
                                        <td>{{ $parcel->mail_number }}</td>
                                        <td>{{ $parcel->sender_name }}<br><small class="text-muted">{{ $parcel->sender_phone }}</small></td>
                                        <td>{{ $parcel->destination->name ?? '-' }}</td>
                                        <td>{{ $parcel->parcel_type }}</td>
                                        <td>{{ number_format($parcel->amount ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <span class="badge bg-{{ $parcel->status === 'Récupéré' ? 'success' : ($parcel->status === 'Arrivé' ? 'info' : ($parcel->status === 'En transit' ? 'warning' : 'secondary')) }}">
                                                {{ $parcel->status }}
                                            </span>
                                        </td>
                                        <td>{{ $parcel->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('parcels.show', $parcel) }}" class="btn btn-sm btn-outline-info">Voir</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun colis reçu par ce client.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($receivedParcels->hasPages())
                        <div class="mt-3">
                            {{ $receivedParcels->links('vendor.pagination.custom') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

