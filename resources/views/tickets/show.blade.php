@extends('layouts.app')

@section('title', 'Détails du Ticket')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Détails du Ticket</h5>
                <div>
                    @if($ticket->status === 'En attente')
                        <form action="{{ route('tickets.board', $ticket) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Embarquer</button>
                        </form>
                        <form action="{{ route('tickets.cancel', $ticket) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce ticket ?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                        </form>
                    @endif
                    @if($ticket->status === 'Embarqué')
                        <form action="{{ route('tickets.disembark', $ticket) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">Débarquer</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 text-center">
                        <h4 class="mb-2">N° Ticket</h4>
                        <h2 class="text-primary">{{ $ticket->ticket_number }}</h2>
                        <div class="mt-3">
                            <div id="qrcode"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Voyage:</th>
                                <td>{{ $ticket->trip->route->departure_city }} → {{ $ticket->trip->route->arrival_city }}</td>
                            </tr>
                            <tr>
                                <th>Date de départ:</th>
                                <td>{{ $ticket->trip->departure_time->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Bus:</th>
                                <td>{{ $ticket->trip->bus->immatriculation }}</td>
                            </tr>
                            <tr>
                                <th>Montée:</th>
                                <td>{{ $ticket->fromStop->name }} ({{ $ticket->fromStop->city }})</td>
                            </tr>
                            <tr>
                                <th>Descente:</th>
                                <td>{{ $ticket->toStop->name }} ({{ $ticket->toStop->city }})</td>
                            </tr>
                            <tr>
                                <th>Siège:</th>
                                <td><strong class="text-primary">N° {{ $ticket->seat_number }}</strong></td>
                            </tr>
                            <tr>
                                <th>Prix:</th>
                                <td><strong>{{ number_format($ticket->price, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            <tr>
                                <th>Statut:</th>
                                <td>
                                    <span class="badge bg-{{ $ticket->status === 'Terminé' ? 'success' : ($ticket->status === 'Embarqué' ? 'warning' : ($ticket->status === 'Annulé' ? 'danger' : 'info')) }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Informations du passager</h5>
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Nom:</th>
                        <td>{{ $ticket->passenger_name }}</td>
                    </tr>
                    @if($ticket->passenger_phone)
                    <tr>
                        <th>Téléphone:</th>
                        <td>{{ $ticket->passenger_phone }}</td>
                    </tr>
                    @endif
                    @if($ticket->boarding_time)
                    <tr>
                        <th>Embarquement:</th>
                        <td>{{ $ticket->boarding_time->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    @if($ticket->disembarkation_time)
                    <tr>
                        <th>Débarquement:</th>
                        <td>{{ $ticket->disembarkation_time->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Vendu par:</th>
                        <td>{{ $ticket->soldBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Date de création:</th>
                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
                
                @if($ticket->payment)
                <hr>
                <h5 class="mb-3">Informations de paiement</h5>
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Montant:</th>
                        <td>{{ number_format($ticket->payment->amount, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <th>Méthode:</th>
                        <td>{{ $ticket->payment->payment_method }}</td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
                        <td>
                            <span class="badge bg-{{ $ticket->payment->status === 'Completed' ? 'success' : 'warning' }}">
                                {{ $ticket->payment->status }}
                            </span>
                        </td>
                    </tr>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Retour à la liste</a>
    <button onclick="window.print()" class="btn btn-primary">Imprimer</button>
</div>

@push('vendor-js')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
@endpush

@push('page-js')
<script>
QRCode.toCanvas(document.getElementById('qrcode'), '{{ $ticket->qr_code }}', {
    width: 200,
    margin: 2
}, function (error) {
    if (error) console.error(error);
});
</script>
@endpush
@endsection

