@extends('layouts.app')

@section('title', 'Modifier un Plein de Carburant')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Modifier un plein de carburant</h5>
        <a href="{{ route('fuel-records.index') }}" class="btn btn-sm btn-secondary">
            <i class="bx bx-arrow-back"></i> Retour
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('fuel-records.update', $fuelRecord) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="bus_id" class="form-label">Bus *</label>
                        <select class="form-select @error('bus_id') is-invalid @enderror" id="bus_id" name="bus_id" required>
                            <option value="">Sélectionnez un bus</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}" {{ old('bus_id', $fuelRecord->bus_id) == $bus->id ? 'selected' : '' }}>
                                    {{ $bus->immatriculation }} - {{ $bus->type }}
                                </option>
                            @endforeach
                        </select>
                        @error('bus_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Montant *</label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                               id="amount" name="amount" value="{{ old('amount', $fuelRecord->amount) }}" placeholder="Montant" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="refill_date" class="form-label">Date et heure du plein *</label>
                        <input type="datetime-local" class="form-control @error('refill_date') is-invalid @enderror" 
                               id="refill_date" name="refill_date" 
                               value="{{ old('refill_date', $fuelRecord->refill_date->format('Y-m-d\TH:i')) }}" required>
                        @error('refill_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="invoice_photo" class="form-label">Photo de la facture</label>
                        @if($fuelRecord->invoice_photo)
                            <div class="mb-2">
                                <img src="{{ Storage::url($fuelRecord->invoice_photo) }}" alt="Photo facture" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted small mt-1">Photo actuelle</p>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('invoice_photo') is-invalid @enderror" 
                               id="invoice_photo" name="invoice_photo" accept="image/jpeg,image/png,image/jpg">
                        @error('invoice_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Format accepté : JPG, PNG. Taille max : 10MB. Laisser vide pour conserver la photo actuelle.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="5" placeholder="Notes ou commentaires">{{ old('notes', $fuelRecord->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('fuel-records.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

