@extends('layouts.app')

@section('title', 'Nouvelle Destination')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Nouvelle Destination</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('destinations.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nom *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('destinations.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

