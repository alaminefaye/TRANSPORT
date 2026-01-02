@extends('layouts.app')

@section('title', 'Détails du Plein de Carburant')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Détails du Plein de Carburant</h5>
                <a href="{{ route('fuel-records.edit', $fuelRecord) }}" class="btn btn-sm btn-primary">Modifier</a>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Bus:</th>
                        <td>{{ $fuelRecord->bus->immatriculation }} - {{ $fuelRecord->bus->type }}</td>
                    </tr>
                    <tr>
                        <th>Montant:</th>
                        <td><strong>{{ number_format($fuelRecord->amount, 0, ',', ' ') }} FCFA</strong></td>
                    </tr>
                    <tr>
                        <th>Date et heure du plein:</th>
                        <td>{{ $fuelRecord->refill_date->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Photo de la facture:</th>
                        <td>
                            @if($fuelRecord->invoice_photo)
                                <a href="{{ asset('storage/' . $fuelRecord->invoice_photo) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bx bx-image"></i> Voir la photo
                                </a>
                            @else
                                <span class="text-muted">Aucune photo</span>
                            @endif
                        </td>
                    </tr>
                    @if($fuelRecord->notes)
                    <tr>
                        <th>Notes:</th>
                        <td>{{ $fuelRecord->notes }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Créé par:</th>
                        <td>{{ $fuelRecord->createdBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Date de création:</th>
                        <td>{{ $fuelRecord->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    @if($fuelRecord->invoice_photo)
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Photo de la facture</h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $fuelRecord->invoice_photo) }}" alt="Photo facture" class="img-fluid rounded">
            </div>
        </div>
    </div>
    @endif
</div>

<div class="mt-3">
    <a href="{{ route('fuel-records.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

