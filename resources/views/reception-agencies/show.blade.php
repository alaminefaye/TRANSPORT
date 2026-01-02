@extends('layouts.app')

@section('title', 'Détails de l\'Agence de Réception')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Informations de l'Agence de Réception</h5>
        <div>
            <a href="{{ route('reception-agencies.edit', $receptionAgency) }}" class="btn btn-sm btn-primary">Modifier</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th width="200">Nom:</th>
                <td><strong>{{ $receptionAgency->name }}</strong></td>
            </tr>
            <tr>
                <th>Nombre de colis:</th>
                <td>{{ $receptionAgency->parcels_count }}</td>
            </tr>
            <tr>
                <th>Date de création:</th>
                <td>{{ $receptionAgency->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('reception-agencies.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

