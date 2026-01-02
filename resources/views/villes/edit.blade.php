@extends('layouts.app')

@section('title', 'Modifier Ville')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Modifier la Ville</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('villes.update', $ville) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="name" class="form-label">Nom de la ville *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $ville->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="code" class="form-label">Code (optionnel)</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                       id="code" name="code" value="{{ old('code', $ville->code) }}" maxlength="10">
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Code abrégé pour identifier la ville (ex: ABJ, BKE...)</small>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description (optionnel)</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $ville->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $ville->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Actif
                    </label>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('villes.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection


