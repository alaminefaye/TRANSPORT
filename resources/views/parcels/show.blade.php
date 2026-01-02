@extends('layouts.app')

@section('title', 'Détails du Colis')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations du Colis</h5>
                <div>
                    <a href="{{ route('parcels.edit', $parcel) }}" class="btn btn-sm btn-primary">Modifier</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">N° Courrier:</th>
                        <td><strong>{{ $parcel->mail_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
                        <td>
                            <span class="badge bg-{{ $parcel->status === 'Récupéré' ? 'success' : ($parcel->status === 'Arrivé' ? 'info' : ($parcel->status === 'En transit' ? 'warning' : 'secondary')) }}">
                                {{ $parcel->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Expéditeur:</th>
                        <td>{{ $parcel->sender_name }}</td>
                    </tr>
                    <tr>
                        <th>Téléphone expéditeur:</th>
                        <td>{{ $parcel->sender_phone }}</td>
                    </tr>
                    <tr>
                        <th>Bénéficiaire:</th>
                        <td>{{ $parcel->recipient_name }}</td>
                    </tr>
                    <tr>
                        <th>Téléphone bénéficiaire:</th>
                        <td>{{ $parcel->recipient_phone }}</td>
                    </tr>
                    <tr>
                        <th>Type de colis:</th>
                        <td>{{ $parcel->parcel_type }}</td>
                    </tr>
                    <tr>
                        <th>Destination:</th>
                        <td>{{ $parcel->destination->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Agence de réception:</th>
                        <td>{{ $parcel->receptionAgency->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Montant:</th>
                        <td>{{ $parcel->amount ? number_format($parcel->amount, 0, ',', ' ') . ' FCFA' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Valeur du colis:</th>
                        <td>{{ $parcel->parcel_value ? number_format($parcel->parcel_value, 0, ',', ' ') . ' FCFA' : '-' }}</td>
                    </tr>
                    @if($parcel->photo)
                    <tr>
                        <th>Photo:</th>
                        <td><img src="{{ asset('storage/' . $parcel->photo) }}" alt="Photo du colis" class="img-thumbnail" style="max-width: 300px; max-height: 300px;"></td>
                    </tr>
                    @endif
                    @if($parcel->description)
                    <tr>
                        <th>Description:</th>
                        <td>{{ $parcel->description }}</td>
                    </tr>
                    @endif
                    @if($parcel->status === 'Récupéré')
                    <tr>
                        <th>Récupéré par:</th>
                        <td>{{ $parcel->retrieved_by_name }}</td>
                    </tr>
                    <tr>
                        <th>Téléphone:</th>
                        <td>{{ $parcel->retrieved_by_phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>CNI:</th>
                        <td>{{ $parcel->retrieved_by_cni ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Date de récupération:</th>
                        <td>{{ $parcel->retrieved_at ? $parcel->retrieved_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    @if($parcel->signature)
                    <tr>
                        <th>Signature:</th>
                        <td><img src="{{ $parcel->signature }}" alt="Signature" style="max-width: 300px; border: 1px solid #ccc;"></td>
                    </tr>
                    @endif
                    <tr>
                        <th>Marqué comme récupéré par:</th>
                        <td>{{ $parcel->retrievedByUser->name ?? '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Créé par:</th>
                        <td>{{ $parcel->createdBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Date de création:</th>
                        <td>{{ $parcel->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('parcels.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

