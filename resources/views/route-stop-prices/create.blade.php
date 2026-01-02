@extends('layouts.app')

@section('title', 'Nouveau Tarif')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Ajouter un Nouveau Tarif</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <strong>ℹ️ NOUVEAU :</strong> Vous pouvez maintenant créer des <strong>prix globaux</strong> réutilisables pour tous les trajets ! 
            Par exemple, le prix Abidjan → Yamoussoukro sera le même pour toutes les routes qui passent par ces deux villes.
        </div>
        
        <form action="{{ route('route-stop-prices.store') }}" method="POST" id="priceForm">
            @csrf
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_global" name="is_global" value="1" {{ old('is_global', true) ? 'checked' : '' }} onchange="toggleRouteField()">
                    <label class="form-check-label" for="is_global">
                        <strong>Prix global (réutilisable pour tous les trajets)</strong>
                    </label>
                </div>
                <small class="text-muted">
                    ✅ Recommandé : Créez un prix global qui sera automatiquement utilisé par tous les trajets qui passent par ces arrêts.
                </small>
            </div>
            
            <div class="mb-3" id="route_field">
                <label for="route_id" class="form-label">Trajet (optionnel si prix global)</label>
                <select class="form-select @error('route_id') is-invalid @enderror" id="route_id" name="route_id" required>
                    <option value="">Sélectionner un trajet</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}
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
                       id="price" name="price" value="{{ old('price') }}" min="0" required>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Actif
                    </label>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('route-stop-prices.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@push('page-js')
<script>
function toggleRouteField() {
    const isGlobal = document.getElementById('is_global').checked;
    const routeField = document.getElementById('route_field');
    const routeSelect = document.getElementById('route_id');
    
    if (isGlobal) {
        routeField.style.display = 'none';
        routeSelect.removeAttribute('required');
        // Charger tous les arrêts disponibles
        loadAllStops();
    } else {
        routeField.style.display = 'block';
        routeSelect.setAttribute('required', 'required');
    }
}

function loadAllStops() {
    // Pour un prix global, charger tous les arrêts disponibles
    fetch('/stops/api')
        .then(response => response.json())
        .then(stops => {
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
        })
        .catch(error => {
            console.error('Error loading stops:', error);
        });
}

document.getElementById('route_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
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
});

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    toggleRouteField();
});
</script>
@endpush
@endsection

