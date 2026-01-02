@extends('layouts.app')

@section('title', 'Détails de la Destination')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Informations de la Destination</h5>
        <div>
            <a href="{{ route('destinations.edit', $destination) }}" class="btn btn-sm btn-primary">Modifier</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th width="200">Nom:</th>
                <td><strong>{{ $destination->name }}</strong></td>
            </tr>
            <tr>
                <th>Nombre de colis:</th>
                <td>{{ $destination->parcels_count }}</td>
            </tr>
            <tr>
                <th>Date de création:</th>
                <td>{{ $destination->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('destinations.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

