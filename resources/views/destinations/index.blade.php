@extends('layouts.app')

@section('title', 'Gestion des Destinations')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Destinations</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('destinations.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouvelle Destination</span>
                <span class="d-sm-none">Nouvelle</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->has('name') ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('destinations.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ request('name') }}" placeholder="Nom de la destination">
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->has('name'))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $destinations->total() }} destination(s) trouvée(s) avec les critères sélectionnés.
                <a href="{{ route('destinations.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $destination)
                    <tr>
                        <td>{{ $destination->name }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('destinations.show', $destination) }}" class="btn btn-sm btn-outline-info">Voir</a>
                                <a href="{{ route('destinations.edit', $destination) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                                <form action="{{ route('destinations.destroy', $destination) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette destination ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center">
                            @if(request()->has('name'))
                                Aucune destination trouvée avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('destinations.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucune destination enregistrée.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $destinations->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

