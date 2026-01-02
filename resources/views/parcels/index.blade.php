@extends('layouts.app')

@section('title', 'Liste des Colis')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0">Liste des Colis</h5>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters">
                <i class="bx bx-filter"></i> <span class="d-none d-sm-inline">Filtres</span>
            </button>
            <a href="{{ route('parcels.retrieved') }}" class="btn btn-info btn-sm">
                <i class="bx bx-check"></i> <span class="d-none d-sm-inline">Colis récupérés</span>
                <span class="d-sm-none">Récupérés</span>
            </a>
            <a href="{{ route('parcels.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> <span class="d-none d-sm-inline">Nouveau Colis</span>
                <span class="d-sm-none">Nouveau</span>
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche/filtres -->
    <div class="collapse {{ request()->hasAny(['mail_number', 'sender_name', 'recipient_name', 'parcel_type', 'destination_id', 'status', 'reception_agency_id']) ? 'show' : '' }}" id="searchFilters">
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('parcels.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="mail_number" class="form-label">N° Courrier</label>
                    <input type="text" class="form-control" id="mail_number" name="mail_number" 
                           value="{{ request('mail_number') }}" placeholder="Ex: MAIL-20251231-0200">
                </div>
                
                <div class="col-md-3">
                    <label for="sender_name" class="form-label">Nom de l'expéditeur</label>
                    <input type="text" class="form-control" id="sender_name" name="sender_name" 
                           value="{{ request('sender_name') }}" placeholder="Nom de l'expéditeur">
                </div>
                
                <div class="col-md-3">
                    <label for="recipient_name" class="form-label">Nom du bénéficiaire</label>
                    <input type="text" class="form-control" id="recipient_name" name="recipient_name" 
                           value="{{ request('recipient_name') }}" placeholder="Nom du bénéficiaire">
                </div>
                
                <div class="col-md-3">
                    <label for="parcel_type" class="form-label">Type de colis</label>
                    <input type="text" class="form-control" id="parcel_type" name="parcel_type" 
                           value="{{ request('parcel_type') }}" placeholder="Type de colis">
                </div>
                
                <div class="col-md-3">
                    <label for="destination_id" class="form-label">Destination</label>
                    <select class="form-select" id="destination_id" name="destination_id">
                        <option value="">Toutes les destinations</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" {{ request('destination_id') == $destination->id ? 'selected' : '' }}>
                                {{ $destination->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="reception_agency_id" class="form-label">Agence de réception</label>
                    <select class="form-select" id="reception_agency_id" name="reception_agency_id">
                        <option value="">Toutes les agences</option>
                        @foreach($receptionAgencies as $agency)
                            <option value="{{ $agency->id }}" {{ request('reception_agency_id') == $agency->id ? 'selected' : '' }}>
                                {{ $agency->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search"></i> Rechercher
                    </button>
                    <a href="{{ route('parcels.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->hasAny(['mail_number', 'sender_name', 'recipient_name', 'parcel_type', 'destination_id', 'status', 'reception_agency_id']))
            <div class="alert alert-info mb-3">
                <i class="bx bx-info-circle"></i> 
                <strong>Recherche active:</strong> 
                {{ $parcels->total() }} colis trouvé(s) avec les critères sélectionnés.
                <a href="{{ route('parcels.index') }}" class="alert-link ms-2">Réinitialiser les filtres</a>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="min-width: 80px;">Photo</th>
                        <th style="min-width: 150px;">N° Courrier</th>
                        <th style="min-width: 120px;">Expéditeur</th>
                        <th style="min-width: 120px;">Bénéficiaire</th>
                        <th style="min-width: 100px;">Type</th>
                        <th style="min-width: 120px;">Destination</th>
                        <th style="min-width: 100px;">Statut</th>
                        <th style="min-width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parcels as $parcel)
                    <tr>
                        <td>
                            @if($parcel->photo)
                                <img src="{{ asset('storage/' . $parcel->photo) }}" alt="Photo du colis" 
                                     class="img-thumbnail" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $parcel->mail_number }}</small></td>
                        <td>{{ $parcel->sender_name }}</td>
                        <td>{{ $parcel->recipient_name }}</td>
                        <td>{{ $parcel->parcel_type }}</td>
                        <td>{{ $parcel->destination->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $parcel->status === 'Récupéré' ? 'success' : ($parcel->status === 'Arrivé' ? 'info' : ($parcel->status === 'En transit' ? 'warning' : 'secondary')) }}">
                                {{ $parcel->status }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('parcels.show', $parcel) }}" class="btn btn-sm btn-outline-info">Voir</a>
                                <a href="{{ route('parcels.edit', $parcel) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                                @if($parcel->status !== 'Récupéré')
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#retrieveModal{{ $parcel->id }}">
                                        Récupérer
                                    </button>
                                @endif
                                <form action="{{ route('parcels.destroy', $parcel) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce colis ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Modal pour marquer comme récupéré -->
                    @if($parcel->status !== 'Récupéré')
                    <div class="modal fade" id="retrieveModal{{ $parcel->id }}" tabindex="-1" aria-labelledby="retrieveModalLabel{{ $parcel->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="retrieveModalLabel{{ $parcel->id }}">Marquer le colis comme récupéré</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('parcels.mark-retrieved', $parcel) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="retrieved_by_name{{ $parcel->id }}" class="form-label">Nom de la personne qui récupère *</label>
                                            <input type="text" class="form-control" id="retrieved_by_name{{ $parcel->id }}" 
                                                   name="retrieved_by_name" value="{{ $parcel->recipient_name }}" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="retrieved_by_phone{{ $parcel->id }}" class="form-label">Téléphone de la personne qui récupère</label>
                                            <input type="text" class="form-control" id="retrieved_by_phone{{ $parcel->id }}" 
                                                   name="retrieved_by_phone" value="{{ $parcel->recipient_phone }}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="retrieved_by_cni{{ $parcel->id }}" class="form-label">Numéro de CNI</label>
                                            <input type="text" class="form-control" id="retrieved_by_cni{{ $parcel->id }}" 
                                                   name="retrieved_by_cni">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="signature{{ $parcel->id }}" class="form-label">Signature</label>
                                            <canvas id="signatureCanvas{{ $parcel->id }}" width="400" height="200" style="border: 1px solid #ccc; cursor: crosshair;"></canvas>
                                            <input type="hidden" name="signature" id="signatureInput{{ $parcel->id }}">
                                            <button type="button" class="btn btn-sm btn-secondary mt-2 clear-signature-btn" data-canvas-id="{{ $parcel->id }}">Effacer</button>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Confirmer la récupération</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            @if(request()->hasAny(['mail_number', 'sender_name', 'recipient_name', 'parcel_type', 'destination_id', 'status', 'reception_agency_id']))
                                Aucun colis trouvé avec les critères de recherche spécifiés.
                                <br>
                                <a href="{{ route('parcels.index') }}" class="btn btn-sm btn-outline-primary mt-2">Réinitialiser les filtres</a>
                            @else
                                Aucun colis enregistré.
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

@push('page-js')
<script>
// Gestion de la signature pour chaque modal
document.addEventListener('DOMContentLoaded', function() {
    const signatureCanvases = document.querySelectorAll('[id^="signatureCanvas"]');
    
    signatureCanvases.forEach(canvas => {
        const ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        let isDrawing = false;
        
        // Récupérer l'ID du canvas
        const canvasId = canvas.id.replace('signatureCanvas', '');
        const inputId = 'signatureInput' + canvasId;
        const canvasCtx = ctx;
        const canvasElement = canvas;
        const inputElement = document.getElementById(inputId);
        
        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: e.clientX - rect.left,
                y: e.clientY - rect.top
            };
        }
        
        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            const pos = getMousePos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        });
        
        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const pos = getMousePos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        });
        
        canvas.addEventListener('mouseup', () => {
            isDrawing = false;
        });
        
        canvas.addEventListener('mouseout', () => {
            isDrawing = false;
        });
        
        // Sauvegarder la signature avant soumission du formulaire
        const form = canvas.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                const signature = canvas.toDataURL('image/png');
                const input = document.getElementById(inputId);
                if (input) input.value = signature;
            });
        }
    });
    
    // Gérer les boutons "Effacer"
    document.querySelectorAll('.clear-signature-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const canvasId = this.getAttribute('data-canvas-id');
            const canvas = document.getElementById('signatureCanvas' + canvasId);
            const input = document.getElementById('signatureInput' + canvasId);
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
            if (input) {
                input.value = '';
            }
        });
    });
});
</script>
@endpush
@endsection

