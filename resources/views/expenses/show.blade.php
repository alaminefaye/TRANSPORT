@extends('layouts.app')

@section('title', 'Détails de la Dépense')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Détails de la Dépense</h5>
                <div>
                    @if($expense->status === 'en_attente')
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-primary">Modifier</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Type:</th>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($expense->type) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Motif:</th>
                        <td>{{ $expense->motif }}</td>
                    </tr>
                    <tr>
                        <th>Montant:</th>
                        <td><strong>{{ number_format($expense->montant, 0, ',', ' ') }} FCFA</strong></td>
                    </tr>
                    <tr>
                        <th>Statut:</th>
                        <td>
                            <span class="badge bg-{{ $expense->status === 'validee' ? 'success' : ($expense->status === 'rejetee' ? 'danger' : 'warning') }}">
                                @if($expense->status === 'validee')
                                    Validée
                                @elseif($expense->status === 'rejetee')
                                    Rejetée
                                @else
                                    En attente
                                @endif
                            </span>
                        </td>
                    </tr>
                    @if($expense->notes)
                    <tr>
                        <th>Notes:</th>
                        <td>{{ $expense->notes }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Créé par:</th>
                        <td>{{ $expense->createdBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Date de création:</th>
                        <td>{{ $expense->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @if($expense->validated_by)
                    <tr>
                        <th>Validé par:</th>
                        <td>{{ $expense->validatedBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Date de validation:</th>
                        <td>{{ $expense->validated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    @if($expense->rejection_reason)
                    <tr>
                        <th>Raison du rejet:</th>
                        <td class="text-danger">{{ $expense->rejection_reason }}</td>
                    </tr>
                    @endif
                </table>
                
                @if($expense->status === 'en_attente')
                <div class="mt-4">
                    <form action="{{ route('expenses.validate', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir valider cette dépense ?');">
                        @csrf
                        <button type="submit" class="btn btn-success">Valider la dépense</button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Rejeter la dépense</button>
                </div>
                
                <!-- Modal pour rejeter -->
                <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rejectModalLabel">Rejeter la dépense</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="rejection_reason" class="form-label">Raison du rejet *</label>
                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required placeholder="Expliquez pourquoi cette dépense est rejetée"></textarea>
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
            </div>
        </div>
    </div>
    
    @if($expense->invoice_photo)
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Photo de la facture</h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $expense->invoice_photo) }}" alt="Photo facture" class="img-fluid rounded">
            </div>
        </div>
    </div>
    @endif
</div>

<div class="mt-3">
    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

