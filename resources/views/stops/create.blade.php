@extends('layouts.app')

@section('title', 'Nouvel Arrêt')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Ajouter un Nouvel Arrêt</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('stops.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nom de l'arrêt *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="city" class="form-label">Ville *</label>
                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                       id="city" name="city" value="{{ old('city') }}" required>
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="type" class="form-label">Type *</label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">Sélectionner un type</option>
                    <option value="Gare principale" {{ old('type') == 'Gare principale' ? 'selected' : '' }}>Gare principale</option>
                    <option value="Arrêt secondaire" {{ old('type') == 'Arrêt secondaire' ? 'selected' : '' }}>Arrêt secondaire</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="2">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('stops.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

