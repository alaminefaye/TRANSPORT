@extends('layouts.app')

@section('title', 'Nouvelle Dépense')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Formulaire de demande de dépense</h5>
        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-secondary">
            <i class="bx bx-arrow-back"></i> Retour
        </a>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">Remplissez le formulaire ci-dessous pour soumettre une demande de dépense qui sera examinée par l'administrateur.</p>
        
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label">Type de dépense *</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="divers" {{ old('type') == 'divers' ? 'selected' : '' }}>Divers</option>
                            <option value="ration" {{ old('type') == 'ration' ? 'selected' : '' }}>Ration</option>
                            <option value="carburant" {{ old('type') == 'carburant' ? 'selected' : '' }}>Carburant</option>
                            <option value="maintenance" {{ old('type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="motif" class="form-label">Motif de la dépense *</label>
                        <input type="text" class="form-control @error('motif') is-invalid @enderror" 
                               id="motif" name="motif" value="{{ old('motif') }}" 
                               placeholder="Décrivez le motif de la dépense" required>
                        @error('motif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant (FCFA) *</label>
                        <input type="number" step="0.01" class="form-control @error('montant') is-invalid @enderror" 
                               id="montant" name="montant" value="{{ old('montant') }}" 
                               placeholder="Montant en FCFA" required>
                        @error('montant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="invoice_photo" class="form-label">Photo de la facture</label>
                        <input type="file" class="form-control @error('invoice_photo') is-invalid @enderror" 
                               id="invoice_photo" name="invoice_photo" accept="image/jpeg,image/png,image/jpg">
                        @error('invoice_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Format accepté : JPG, PNG. Taille max : 10MB</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="5" placeholder="Notes ou commentaires">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="bx bx-info-circle"></i> 
                Votre demande sera soumise à validation par l'administrateur avant d'être approuvée.
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Soumettre la demande</button>
            </div>
        </form>
    </div>
</div>
@endsection

