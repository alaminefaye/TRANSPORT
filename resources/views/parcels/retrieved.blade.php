@extends('layouts.app')

@section('title', 'Colis Récupérés')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Colis Récupérés</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('parcels.index') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-list"></i> <span class="d-none d-sm-inline">Liste des Colis</span>
                <span class="d-sm-none">Liste</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['mail_number', 'recipient_name', 'retrieved_by_name', 'date_from', 'date_to']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('parcels.retrieved') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="mail_number" class="form-label">N° Courrier</label>
                    <input type="text" class="form-control" id="mail_number" name="mail_number" 
                           value="{{ request('mail_number') }}" placeholder="Ex: MAIL-20251231-0200">
                </div>
                
                <div class="col-md-3">
                    <label for="recipient_name" class="form-label">Nom du bénéficiaire</label>
                    <input type="text" class="form-control" id="recipient_name" name="recipient_name" 
                           value="{{ request('recipient_name') }}" placeholder="Nom du bénéficiaire">
                </div>
                
                <div class="col-md-3">
                    <label for="retrieved_by_name" class="form-label">Nom de la personne qui a récupéré</label>
                    <input type="text" class="form-control" id="retrieved_by_name" name="retrieved_by_name" 
                           value="{{ request('retrieved_by_name') }}" placeholder="Nom">
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date de récupération (de)</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date de récupération (à)</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('parcels.retrieved') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['mail_number', 'recipient_name', 'retrieved_by_name', 'date_from', 'date_to']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $parcels->total() }} colis récupéré(s) trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('parcels.retrieved') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>N° Courrier</th>
                        <th>Bénéficiaire</th>
                        <th>Récupéré par</th>
                        <th>Téléphone</th>
                        <th>CNI</th>
                        <th>Date de récupération</th>
                        <th>Marqué par</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parcels as $parcel)
                    <tr>
                        <td><small class="text-muted">{{ $parcel->mail_number }}</small></td>
                        <td>{{ $parcel->recipient_name }}</td>
                        <td>{{ $parcel->retrieved_by_name ?? '-' }}</td>
                        <td>{{ $parcel->retrieved_by_phone ?? '-' }}</td>
                        <td>{{ $parcel->retrieved_by_cni ?? '-' }}</td>
                        <td>{{ $parcel->retrieved_at ? $parcel->retrieved_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $parcel->retrievedByUser->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('parcels.show', $parcel) }}" class="btn btn-sm btn-outline-info">Voir</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            @if(request()->hasAny(['mail_number', 'recipient_name', 'retrieved_by_name', 'date_from', 'date_to']))
                                Aucun colis récupéré trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('parcels.retrieved') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun colis récupéré.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $parcels->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

