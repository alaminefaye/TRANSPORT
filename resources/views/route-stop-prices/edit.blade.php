@extends('layouts.app')

@section('title', 'Modifier Tarif')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Modifier le Tarif</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <strong>ℹ️ Information importante:</strong> Les prix sont définis pour le <strong>trajet (route)</strong>, pas pour chaque voyage. 
            Une fois que vous avez défini les prix d'un trajet, ils sont automatiquement utilisés pour <strong>tous les voyages</strong> de ce trajet, 
            peu importe la date du voyage. Vous n'avez donc pas besoin de redéfinir les prix à chaque fois que vous créez un nouveau voyage.
        </div>
        
        <form action="{{ route('route-stop-prices.update', $routeStopPrice) }}" method="POST" id="priceForm">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="route_id" class="form-label">Trajet *</label>
                <select class="form-select @error('route_id') is-invalid @enderror" id="route_id" name="route_id" required>
                    <option value="">Sélectionner un trajet</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id', $routeStopPrice->route_id) == $route->id ? 'selected' : '' }}
                            data-stops="{{ $route->stops->toJson() }}">
                            #{{ $route->route_number ?? $route->id }} - {{ $route->departure_city }} → {{ $route->arrival_city }}
                        </option>
                    @endforeach
                </select>
                @error('route_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="from_stop_id" class="form-label">Arrêt de départ *</label>
                    <select class="form-select @error('from_stop_id') is-invalid @enderror" id="from_stop_id" name="from_stop_id" required>
                        <option value="">Sélectionner un arrêt</option>
                    </select>
                    @error('from_stop_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="to_stop_id" class="form-label">Arrêt d'arrivée *</label>
                    <select class="form-select @error('to_stop_id') is-invalid @enderror" id="to_stop_id" name="to_stop_id" required>
                        <option value="">Sélectionner un arrêt</option>
                    </select>
                    @error('to_stop_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">Prix (FCFA) *</label>
                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                       id="price" name="price" value="{{ old('price', $routeStopPrice->price) }}" min="0" required>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $routeStopPrice->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Actif
                    </label>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('route-stop-prices.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

@push('page-js')
<script>
function loadStops() {
    const routeSelect = document.getElementById('route_id');
    const selectedOption = routeSelect.options[routeSelect.selectedIndex];
    const stops = selectedOption ? JSON.parse(selectedOption.dataset.stops || '[]') : [];
    
    const fromSelect = document.getElementById('from_stop_id');
    const toSelect = document.getElementById('to_stop_id');
    
    fromSelect.innerHTML = '<option value="">Sélectionner un arrêt</option>';
    toSelect.innerHTML = '<option value="">Sélectionner un arrêt</option>';
    
    stops.forEach(function(stop) {
        const option1 = new Option(stop.name + ' (' + stop.city + ')', stop.id);
        const option2 = new Option(stop.name + ' (' + stop.city + ')', stop.id);
        fromSelect.add(option1);
        toSelect.add(option2);
    });
    
    // Sélectionner les valeurs actuelles
    @if(old('from_stop_id', $routeStopPrice->from_stop_id))
        fromSelect.value = {{ old('from_stop_id', $routeStopPrice->from_stop_id) }};
    @endif
    @if(old('to_stop_id', $routeStopPrice->to_stop_id))
        toSelect.value = {{ old('to_stop_id', $routeStopPrice->to_stop_id) }};
    @endif
}

document.getElementById('route_id').addEventListener('change', loadStops);

// Charger les arrêts au chargement de la page
document.addEventListener('DOMContentLoaded', loadStops);
</script>
@endpush
@endsection

