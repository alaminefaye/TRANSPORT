@extends('layouts.app')

@section('title', 'Rechercher Ticket par QR Code')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Rechercher un Ticket par QR Code</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tickets.search.qr') }}" method="GET">
            <div class="mb-3">
                <label for="qr_code" class="form-label">Code QR *</label>
                <input type="text" class="form-control @error('qr_code') is-invalid @enderror" 
                       id="qr_code" name="qr_code" value="{{ old('qr_code') }}" 
                       placeholder="Saisir ou scanner le QR code" required autofocus>
                @error('qr_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>
    </div>
</div>
@endsection

