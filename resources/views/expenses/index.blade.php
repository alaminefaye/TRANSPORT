@extends('layouts.app')

@section('title', 'Liste des Dépenses')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Dépenses</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouvelle dépense</span>
                <span class="d-sm-none">Nouvelle</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['type', 'status', 'date_from', 'date_to', 'search']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date de début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-6">
                    <label for="search" class="form-label">Recherche (motif)</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Rechercher par motif">
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['type', 'status', 'date_from', 'date_to', 'search']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $expenses->total() }} dépense(s) trouvée(s) avec les critères sélectionnés.
                <a href="{{ route('expenses.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Motif</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Créé par</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">{{ $types[$expense->type] ?? $expense->type }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($expense->motif, 50) }}</td>
                        <td class="text-nowrap"><strong>{{ number_format($expense->montant, 0, ',', ' ') }} FCFA</strong></td>
                        <td>
                            <span class="badge bg-{{ $expense->status === 'validee' ? 'success' : ($expense->status === 'rejetee' ? 'danger' : 'warning') }}">
                                {{ $statuses[$expense->status] ?? $expense->status }}
                            </span>
                        </td>
                        <td>{{ $expense->createdBy->name ?? '-' }}</td>
                        <td>{{ $expense->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-info">Voir</a>
                                @if($expense->status === 'en_attente')
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                                    <form action="{{ route('expenses.validate', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir valider cette dépense ?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">Valider</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $expense->id }}">Rejeter</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Modal pour rejeter -->
                    @if($expense->status === 'en_attente')
                    <div class="modal fade" id="rejectModal{{ $expense->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $expense->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel{{ $expense->id }}">Rejeter la dépense</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="rejection_reason{{ $expense->id }}" class="form-label">Raison du rejet *</label>
                                            <textarea class="form-control" id="rejection_reason{{ $expense->id }}" name="rejection_reason" rows="3" required placeholder="Expliquez pourquoi cette dépense est rejetée"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-danger">Rejeter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            @if(request()->hasAny(['type', 'status', 'date_from', 'date_to', 'search']))
                                Aucune dépense trouvée avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucune dépense enregistrée.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $expenses->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

