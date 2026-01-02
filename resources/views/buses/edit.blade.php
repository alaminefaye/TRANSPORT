@extends('layouts.app')

@section('title', 'Modifier Bus')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Modifier le Bus</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('buses.update', $bus) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="immatriculation" class="form-label">Immatriculation *</label>
                <input type="text" class="form-control @error('immatriculation') is-invalid @enderror" 
                       id="immatriculation" name="immatriculation" value="{{ old('immatriculation', $bus->immatriculation) }}" required>
                @error('immatriculation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacité (nombre de sièges) *</label>
                <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                       id="capacity" name="capacity" value="{{ old('capacity', $bus->capacity) }}" min="1" required>
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="type" class="form-label">Type *</label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">Sélectionner un type</option>
                    <option value="VIP" {{ old('type', $bus->type) == 'VIP' ? 'selected' : '' }}>VIP</option>
                    <option value="Classique" {{ old('type', $bus->type) == 'Classique' ? 'selected' : '' }}>Classique</option>
                    <option value="Climatisé" {{ old('type', $bus->type) == 'Climatisé' ? 'selected' : '' }}>Climatisé</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Statut *</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="">Sélectionner un statut</option>
                    <option value="Disponible" {{ old('status', $bus->status) == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="En voyage" {{ old('status', $bus->status) == 'En voyage' ? 'selected' : '' }}>En voyage</option>
                    <option value="En panne" {{ old('status', $bus->status) == 'En panne' ? 'selected' : '' }}>En panne</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                          id="notes" name="notes" rows="3">{{ old('notes', $bus->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('buses.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection

