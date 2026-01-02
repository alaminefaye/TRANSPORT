@extends('layouts.app')

@section('title', 'Détails de la Ville')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations de la Ville</h5>
                <a href="{{ route('villes.edit', $ville) }}" class="btn btn-sm btn-primary">Modifier</a>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Nom:</th>
                        <td><strong>{{ $ville->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Code:</th>
                        <td>{{ $ville->code ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td>{{ $ville->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
                        <td>
                            <span class="badge bg-{{ $ville->is_active ? 'success' : 'secondary' }}">
                                {{ $ville->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Routes au départ:</strong> {{ $ville->departureRoutes->count() }}</p>
                <p class="mb-2"><strong>Routes à l'arrivée:</strong> {{ $ville->arrivalRoutes->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('villes.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection


