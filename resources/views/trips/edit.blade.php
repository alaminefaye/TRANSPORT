@extends('layouts.app')

@section('title', 'Modifier Voyage')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Modifier le Voyage</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('trips.update', $trip) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="route_id" class="form-label">Trajet *</label>
                <select class="form-select @error('route_id') is-invalid @enderror" id="route_id" name="route_id" required>
                    <option value="">Sélectionner un trajet</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id', $trip->route_id) == $route->id ? 'selected' : '' }}>
                            #{{ $route->route_number ?? $route->id }} - {{ $route->departure_city }} → {{ $route->arrival_city }}
                        </option>
                    @endforeach
                </select>
                @error('route_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="bus_id" class="form-label">Bus *</label>
                <select class="form-select @error('bus_id') is-invalid @enderror" id="bus_id" name="bus_id" required>
                    <option value="">Sélectionner un bus</option>
                    @foreach($buses as $bus)
                        <option value="{{ $bus->id }}" {{ old('bus_id', $trip->bus_id) == $bus->id ? 'selected' : '' }}>
                            {{ $bus->immatriculation }} ({{ $bus->capacity }} places - {{ $bus->type }})
                        </option>
                    @endforeach
                </select>
                @error('bus_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="departure_time" class="form-label">Date & Heure de départ *</label>
                    <input type="datetime-local" class="form-control @error('departure_time') is-invalid @enderror" 
                           id="departure_time" name="departure_time" 
                           value="{{ old('departure_time', $trip->departure_time->format('Y-m-d\TH:i')) }}" required>
                    @error('departure_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="arrival_time" class="form-label">Date & Heure d'arrivée</label>
                    <input type="datetime-local" class="form-control @error('arrival_time') is-invalid @enderror" 
                           id="arrival_time" name="arrival_time" 
                           value="{{ old('arrival_time', $trip->arrival_time ? $trip->arrival_time->format('Y-m-d\TH:i') : '') }}">
                    @error('arrival_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="driver_id" class="form-label">Chauffeur</label>
                <select class="form-select @error('driver_id') is-invalid @enderror" id="driver_id" name="driver_id">
                    <option value="">Sélectionner un chauffeur</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('driver_id', $trip->driver_id) == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
                @error('driver_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Statut *</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="Scheduled" {{ old('status', $trip->status) == 'Scheduled' ? 'selected' : '' }}>Programmé</option>
                    <option value="In Progress" {{ old('status', $trip->status) == 'In Progress' ? 'selected' : '' }}>En cours</option>
                    <option value="Completed" {{ old('status', $trip->status) == 'Completed' ? 'selected' : '' }}>Terminé</option>
                    <option value="Cancelled" {{ old('status', $trip->status) == 'Cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('trips.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection

