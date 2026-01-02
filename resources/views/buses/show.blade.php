@extends('layouts.app')

@section('title', 'Détails du Bus')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations du Bus</h5>
                <a href="{{ route('buses.edit', $bus) }}" class="btn btn-sm btn-primary">Modifier</a>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Immatriculation:</th>
                        <td>{{ $bus->immatriculation }}</td>
                    </tr>
                    <tr>
                        <th>Capacité:</th>
                        <td>{{ $bus->capacity }} sièges</td>
                    </tr>
                    <tr>
                        <th>Type:</th>
                        <td>{{ $bus->type }}</td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
                        <td>
                            <span class="badge bg-{{ $bus->status === 'Disponible' ? 'success' : ($bus->status === 'En voyage' ? 'warning' : 'danger') }}">
                                {{ $bus->status }}
                            </span>
                        </td>
                    </tr>
                    @if($bus->notes)
                    <tr>
                        <th>Notes:</th>
                        <td>{{ $bus->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Voyages</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Nombre de voyages:</strong> {{ $bus->trips->count() }}</p>
                @if($bus->trips->count() > 0)
                    <a href="{{ route('trips.index', ['bus_id' => $bus->id]) }}" class="btn btn-sm btn-outline-primary mb-2">Voir les voyages</a>
                @endif
                <div class="mt-2">
                    <a href="{{ route('buses.configure-seats', $bus) }}" class="btn btn-sm btn-outline-info">
                        <i class="bx bx-chair"></i> Configurer la disposition des sièges
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('buses.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

