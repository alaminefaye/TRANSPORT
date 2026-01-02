@extends('layouts.app')

@section('title', 'Diagnostic des Prix')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🔍 Diagnostic des Prix de Route</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>ℹ️ À quoi sert cette page ?</strong><br>
            Cette page vous permet de diagnostiquer les problèmes de prix pour une route. 
            Elle affiche tous les arrêts de la route, tous les prix définis, et identifie les segments manquants.
        </div>
        
        <form method="GET" action="{{ route('diagnostic.route-prices') }}">
            <div class="mb-3">
                <label for="route_id" class="form-label">Sélectionner une route</label>
                <select class="form-select" id="route_id" name="route_id" onchange="this.form.submit()">
                    <option value="">-- Choisir une route --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ $routeId == $route->id ? 'selected' : '' }}>
                            #{{ $route->route_number ?? $route->id }} - {{ $route->departure_city }} → {{ $route->arrival_city }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
        
        @if($diagnostic)
            <hr>
            
            <h6 class="mb-3">📊 Résumé</h6>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $diagnostic['total_stops'] }}</h3>
                            <small class="text-muted">Arrêts dans la route</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $diagnostic['expected_segments'] }}</h3>
                            <small class="text-muted">Segments attendus (consécutifs)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card {{ $diagnostic['total_prices'] > 0 ? 'bg-success text-white' : 'bg-warning' }}">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $diagnostic['total_prices'] }}</h3>
                            <small>Prix définis (actifs)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card {{ empty($diagnostic['missing_segments']) ? 'bg-success text-white' : 'bg-danger text-white' }}">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ count($diagnostic['missing_segments']) }}</h3>
                            <small>Segments manquants</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <h6 class="mb-3">🗺️ Arrêts de la route (dans l'ordre)</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Ordre</th>
                            <th>ID Arrêt</th>
                            <th>Nom de l'arrêt</th>
                            <th>Ville</th>
                            <th>Heure estimée</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diagnostic['route_stops'] as $routeStop)
                            <tr>
                                <td><strong>{{ $routeStop->order }}</strong></td>
                                <td><code>{{ $routeStop->stop_id }}</code></td>
                                <td>{{ $routeStop->stop->name ?? 'N/A' }}</td>
                                <td>{{ $routeStop->stop->city ?? 'N/A' }}</td>
                                <td>{{ $routeStop->estimated_time ?? 'Non définie' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <h6 class="mb-3">💰 Prix définis pour cette route</h6>
            @if($diagnostic['prices']->isEmpty())
                <div class="alert alert-warning">
                    ⚠️ Aucun prix actif défini pour cette route
                </div>
            @else
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>De (ID)</th>
                                <th>De (Nom)</th>
                                <th>À (ID)</th>
                                <th>À (Nom)</th>
                                <th>Prix</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diagnostic['prices'] as $price)
                                <tr>
                                    <td><code>{{ $price->from_stop_id }}</code></td>
                                    <td>{{ $price->fromStop->name ?? 'N/A' }} ({{ $price->fromStop->city ?? 'N/A' }})</td>
                                    <td><code>{{ $price->to_stop_id }}</code></td>
                                    <td>{{ $price->toStop->name ?? 'N/A' }} ({{ $price->toStop->city ?? 'N/A' }})</td>
                                    <td><strong>{{ number_format($price->price, 0, ',', ' ') }} FCFA</strong></td>
                                    <td>
                                        <span class="badge bg-success">Actif</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <h6 class="mb-3">🚨 Segments consécutifs manquants</h6>
            @if(empty($diagnostic['missing_segments']))
                <div class="alert alert-success">
                    ✅ Tous les segments consécutifs ont des prix définis ! Le système devrait fonctionner correctement.
                </div>
            @else
                <div class="alert alert-danger">
                    <strong>⚠️ Attention !</strong> Les segments suivants n'ont pas de prix définis. 
                    Vous devez créer des prix pour ces segments dans la section "Configurations des tarifs".
                </div>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-danger">
                            <tr>
                                <th>Segment</th>
                                <th>De (ID)</th>
                                <th>De (Nom)</th>
                                <th>À (ID)</th>
                                <th>À (Nom)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diagnostic['missing_segments'] as $segment)
                                <tr>
                                    <td><strong>{{ $segment['order'] }}</strong></td>
                                    <td><code>{{ $segment['from_stop_id'] }}</code></td>
                                    <td>{{ $segment['from_stop_name'] }} ({{ $segment['from_stop_city'] }})</td>
                                    <td><code>{{ $segment['to_stop_id'] }}</code></td>
                                    <td>{{ $segment['to_stop_name'] }} ({{ $segment['to_stop_city'] }})</td>
                                    <td>
                                        <a href="{{ route('route-stop-prices.create') }}" class="btn btn-sm btn-primary">
                                            Créer ce prix
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <div class="alert alert-info mt-4">
                <strong>💡 Comment corriger les segments manquants ?</strong><br>
                1. Allez dans "Configurations des tarifs" (menu de gauche)<br>
                2. Cliquez sur "Nouveau Tarif"<br>
                3. Sélectionnez la route : <strong>#{{ $diagnostic['route']->route_number ?? $diagnostic['route']->id }} - {{ $diagnostic['route']->departure_city }} → {{ $diagnostic['route']->arrival_city }}</strong><br>
                4. Pour chaque segment manquant ci-dessus, créez un prix en utilisant les IDs exacts affichés
            </div>
        @endif
    </div>
</div>
@endsection


