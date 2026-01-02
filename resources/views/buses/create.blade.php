@extends('layouts.app')

@section('title', 'Nouveau Bus')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Ajouter un Nouveau Bus</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('buses.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="immatriculation" class="form-label">Immatriculation *</label>
                <input type="text" class="form-control @error('immatriculation') is-invalid @enderror" 
                       id="immatriculation" name="immatriculation" value="{{ old('immatriculation') }}" required>
                @error('immatriculation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacité (nombre de sièges) *</label>
                <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                       id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" required>
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="type" class="form-label">Type *</label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">Sélectionner un type</option>
                    <option value="VIP" {{ old('type') == 'VIP' ? 'selected' : '' }}>VIP</option>
                    <option value="Classique" {{ old('type') == 'Classique' ? 'selected' : '' }}>Classique</option>
                    <option value="Climatisé" {{ old('type') == 'Climatisé' ? 'selected' : '' }}>Climatisé</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Statut *</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="">Sélectionner un statut</option>
                    <option value="Disponible" {{ old('status') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="En voyage" {{ old('status') == 'En voyage' ? 'selected' : '' }}>En voyage</option>
                    <option value="En panne" {{ old('status') == 'En panne' ? 'selected' : '' }}>En panne</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                          id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('buses.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

