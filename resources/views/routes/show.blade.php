@extends('layouts.app')

@section('title', 'Détails du Trajet')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations du Trajet</h5>
                <a href="{{ route('routes.edit', $route) }}" class="btn btn-sm btn-primary">Modifier</a>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Numéro de route:</th>
                        <td><strong>#{{ $route->route_number ?? $route->id }}</strong></td>
                    </tr>
                    <tr>
                        <th width="200">Trajet:</th>
                        <td>{{ $route->departure_city }} → {{ $route->arrival_city }}</td>
                    </tr>
                    <tr>
                        <th>Distance:</th>
                        <td>{{ $route->distance }} km</td>
                    </tr>
                    <tr>
                        <th>Durée estimée:</th>
                        <td>{{ $route->estimated_duration }} minutes</td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
                        <td>
                            <span class="badge bg-{{ $route->is_active ? 'success' : 'secondary' }}">
                                {{ $route->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                    </tr>
                </table>
                
                <hr>
                
                <h5 class="mb-3">Arrêts du trajet</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ordre</th>
                                <th>Nom</th>
                                <th>Ville</th>
                                <th>Type</th>
                                <th>Heure estimée</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($route->stops as $stop)
                            <tr>
                                <td>{{ $stop->pivot->order }}</td>
                                <td>{{ $stop->name }}</td>
                                <td>{{ $stop->city }}</td>
                                <td>
                                    <span class="badge bg-{{ $stop->type === 'Gare principale' ? 'primary' : 'secondary' }}">
                                        {{ $stop->type }}
                                    </span>
                                </td>
                                <td>{{ $stop->pivot->estimated_time ? substr($stop->pivot->estimated_time, 0, 5) : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Nombre d'arrêts:</strong> {{ $route->stops->count() }}</p>
                <p class="mb-2"><strong>Nombre de voyages:</strong> {{ $route->trips->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('routes.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

