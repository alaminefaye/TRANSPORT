@extends('layouts.app')

@section('title', 'Gestion du Carburant')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Pleins de Carburant</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('fuel-records.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Ajouter un plein</span>
                <span class="d-sm-none">Ajouter</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['bus_id', 'date_from', 'date_to']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('fuel-records.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="bus_id" class="form-label">Bus</label>
                    <select class="form-select" id="bus_id" name="bus_id">
                        <option value="">Tous les bus</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->id }}" {{ request('bus_id') == $bus->id ? 'selected' : '' }}>
                                {{ $bus->immatriculation }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="date_from" class="form-label">Date de début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-4">
                    <label for="date_to" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('fuel-records.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['bus_id', 'date_from', 'date_to']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $fuelRecords->total() }} plein(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('fuel-records.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Bus</th>
                        <th>Montant</th>
                        <th>Date et heure</th>
                        <th>Photo facture</th>
                        <th>Créé par</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fuelRecords as $record)
                    <tr>
                        <td>{{ $record->bus->immatriculation }}</td>
                        <td class="text-nowrap">{{ number_format($record->amount, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $record->refill_date->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($record->invoice_photo)
                                <a href="{{ asset('storage/' . $record->invoice_photo) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bx bx-image"></i> Voir
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $record->createdBy->name ?? '-' }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('fuel-records.show', $record) }}" class="btn btn-sm btn-outline-info">Voir</a>
                                <a href="{{ route('fuel-records.edit', $record) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                                <form action="{{ route('fuel-records.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce plein de carburant ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            @if(request()->hasAny(['bus_id', 'date_from', 'date_to']))
                                Aucun plein trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('fuel-records.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun plein de carburant enregistré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $fuelRecords->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

