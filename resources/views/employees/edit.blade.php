@extends('layouts.app')

@section('title', 'Modifier Employé')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Modifier l'Employé</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="matricule" class="form-label">Matricule</label>
                    <input type="text" class="form-control @error('matricule') is-invalid @enderror" 
                           id="matricule" name="matricule" value="{{ old('matricule', $employee->matricule) }}">
                    @error('matricule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nom complet *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $employee->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="2">{{ old('address', $employee->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="position" class="form-label">Poste</label>
                    <input type="text" class="form-control @error('position') is-invalid @enderror" 
                           id="position" name="position" value="{{ old('position', $employee->position) }}" placeholder="Ex: Chauffeur, Agent, etc.">
                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="hire_date" class="form-label">Date d'embauche</label>
                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                           id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}">
                    @error('hire_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Statut *</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="">Sélectionner un statut</option>
                        <option value="Actif" {{ old('status', $employee->status) == 'Actif' ? 'selected' : '' }}>Actif</option>
                        <option value="Inactif" {{ old('status', $employee->status) == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                        <option value="Congé" {{ old('status', $employee->status) == 'Congé' ? 'selected' : '' }}>Congé</option>
                        <option value="Démissionné" {{ old('status', $employee->status) == 'Démissionné' ? 'selected' : '' }}>Démissionné</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="salary" class="form-label">Salaire</label>
                    <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                           id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" min="0" step="0.01">
                    @error('salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="photo" class="form-label">Photo</label>
                @if($employee->photo)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="Photo" class="img-thumbnail" style="max-width: 150px;">
                    </div>
                @endif
                <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                       id="photo" name="photo" accept="image/*">
                @error('photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Formats acceptés: JPG, PNG, GIF. Taille maximum: 2MB</small>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection

