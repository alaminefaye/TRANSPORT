@extends('layouts.app')

@section('title', 'Modifier Trajet')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Modifier le Trajet</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('routes.update', $route) }}" method="POST" id="routeForm">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="route_number" class="form-label">Numéro de route *</label>
                <input type="number" class="form-control @error('route_number') is-invalid @enderror" 
                       id="route_number" name="route_number" value="{{ old('route_number', $route->route_number) }}" min="1" required>
                @error('route_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Numéro unique pour identifier cette route (ex: 1, 2, 3...)</small>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="departure_city_id" class="form-label">Ville de départ *</label>
                    <select class="form-select @error('departure_city_id') is-invalid @enderror" 
                            id="departure_city_id" name="departure_city_id" required>
                        <option value="">Sélectionner une ville</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->id }}" {{ old('departure_city_id', $route->departure_city_id) == $ville->id ? 'selected' : '' }}>
                                {{ $ville->name }}@if($ville->code) ({{ $ville->code }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('departure_city_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="arrival_city_id" class="form-label">Ville d'arrivée *</label>
                    <select class="form-select @error('arrival_city_id') is-invalid @enderror" 
                            id="arrival_city_id" name="arrival_city_id" required>
                        <option value="">Sélectionner une ville</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->id }}" {{ old('arrival_city_id', $route->arrival_city_id) == $ville->id ? 'selected' : '' }}>
                                {{ $ville->name }}@if($ville->code) ({{ $ville->code }})@endif
                            </option>
                        @endforeach
                    </select>
                    @error('arrival_city_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="distance" class="form-label">Distance (km) *</label>
                    <input type="number" step="0.01" class="form-control @error('distance') is-invalid @enderror" 
                           id="distance" name="distance" value="{{ old('distance', $route->distance) }}" min="0" required>
                    @error('distance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="estimated_duration" class="form-label">Durée estimée (minutes) *</label>
                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $route->estimated_duration) }}" min="0" required>
                    @error('estimated_duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $route->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Actif
                    </label>
                </div>
            </div>
            
            <hr>
            
            <h5 class="mb-3">Arrêts du trajet *</h5>
            <div id="stops-container">
                <!-- Les arrêts seront ajoutés dynamiquement ici -->
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-stop-btn">
                <i class="bx bx-plus"></i> Ajouter un arrêt
            </button>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('routes.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

@push('page-js')
<script>
@php
$stopsData = $stops->toArray();
$routeStopsData = [];
foreach ($route->stops as $stop) {
    $estimatedTime = '';
    if ($stop->pivot->estimated_time) {
        $estimatedTime = substr($stop->pivot->estimated_time, 0, 5);
    }
    $routeStopsData[] = [
        'stop_id' => $stop->id,
        'order' => $stop->pivot->order,
        'estimated_time' => $estimatedTime
    ];
}
@endphp

const stops = @json($stopsData);
const routeStops = @json($routeStopsData);
let stopIndex = 0;

document.getElementById('add-stop-btn').addEventListener('click', function() {
    addStopRow();
});

function addStopRow(stopId = '', order = null, time = '') {
    const container = document.getElementById('stops-container');
    const index = order !== null ? order - 1 : stopIndex++;
    
    const stopRow = document.createElement('div');
    stopRow.className = 'row mb-3 stop-row';
    stopRow.innerHTML = `
        <div class="col-md-5">
            <select class="form-select stop-select" name="stops[${index}][stop_id]" required>
                <option value="">Sélectionner un arrêt</option>
                ${stops.map(s => `<option value="${s.id}" ${s.id == stopId ? 'selected' : ''}>${s.name} - ${s.city}</option>`).join('')}
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control" name="stops[${index}][order]" value="${order !== null ? order : index + 1}" placeholder="Ordre" min="1" required>
        </div>
        <div class="col-md-3">
            <input type="time" class="form-control" name="stops[${index}][estimated_time]" value="${time}" placeholder="Heure estimée">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger remove-stop-btn">
                <i class="bx bx-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(stopRow);
    
    stopRow.querySelector('.remove-stop-btn').addEventListener('click', function() {
        stopRow.remove();
        updateStopOrders();
    });
}

function updateStopOrders() {
    const rows = document.querySelectorAll('.stop-row');
    rows.forEach((row, index) => {
        row.querySelector('input[name*="[order]"]').value = index + 1;
    });
}

// Charger les arrêts existants
routeStops.forEach(function(rs) {
    addStopRow(rs.stop_id, rs.order, rs.estimated_time);
});
</script>
@endpush
@endsection

