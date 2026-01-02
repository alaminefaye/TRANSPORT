@extends('layouts.app')

@section('title', 'Création de Colis')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Création de Colis</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('parcels.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Colonne de gauche - Informations expéditeur -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-primary">📤 Informations de l'expéditeur</h6>
                    
                    <div class="mb-3">
                        <label for="sender_phone" class="form-label">Téléphone de l'expéditeur *</label>
                        <input type="text" class="form-control @error('sender_phone') is-invalid @enderror" 
                               id="sender_phone" name="sender_phone" value="{{ old('sender_phone') }}" required>
                        @error('sender_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Le client sera automatiquement enregistré ou trouvé dans la base de données.</small>
                        <div id="sender-search-message" class="mt-2" style="display: none;"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sender_name" class="form-label">Nom de l'expéditeur *</label>
                        <input type="text" class="form-control @error('sender_name') is-invalid @enderror" 
                               id="sender_name" name="sender_name" value="{{ old('sender_name') }}" required>
                        @error('sender_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Colonne de droite - Informations bénéficiaire -->
                <div class="col-md-6">
                    <h6 class="mb-3 text-success">📥 Informations du bénéficiaire</h6>
                    
                    <div class="mb-3">
                        <label for="recipient_phone" class="form-label">Téléphone du bénéficiaire *</label>
                        <input type="text" class="form-control @error('recipient_phone') is-invalid @enderror" 
                               id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone') }}" required>
                        @error('recipient_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Le client sera automatiquement enregistré ou trouvé dans la base de données.</small>
                        <div id="recipient-search-message" class="mt-2" style="display: none;"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label">Nom du bénéficiaire *</label>
                        <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" 
                               id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" required>
                        @error('recipient_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <h6 class="mb-3">📦 Informations du colis</h6>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="destination_id" class="form-label">Destination *</label>
                        <select class="form-select @error('destination_id') is-invalid @enderror" id="destination_id" name="destination_id" required>
                            <option value="">Sélectionner une destination</option>
                            @foreach($destinations as $destination)
                                <option value="{{ $destination->id }}" {{ old('destination_id') == $destination->id ? 'selected' : '' }}>
                                    {{ $destination->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('destination_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="reception_agency_id" class="form-label">Agence de réception *</label>
                        <select class="form-select @error('reception_agency_id') is-invalid @enderror" id="reception_agency_id" name="reception_agency_id" required>
                            <option value="">Sélectionner une agence</option>
                            @foreach($receptionAgencies as $agency)
                                <option value="{{ $agency->id }}" {{ old('reception_agency_id') == $agency->id ? 'selected' : '' }}>
                                    {{ $agency->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('reception_agency_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="parcel_type" class="form-label">Type de colis *</label>
                        <select class="form-select @error('parcel_type') is-invalid @enderror" id="parcel_type" name="parcel_type" required>
                            <option value="">Sélectionner un type</option>
                            <option value="Pli/enveloppe" {{ old('parcel_type') == 'Pli/enveloppe' ? 'selected' : '' }}>Pli/enveloppe</option>
                            <option value="Carton" {{ old('parcel_type') == 'Carton' ? 'selected' : '' }}>Carton</option>
                            <option value="Paquet" {{ old('parcel_type') == 'Paquet' ? 'selected' : '' }}>Paquet</option>
                            <option value="Sac" {{ old('parcel_type') == 'Sac' ? 'selected' : '' }}>Sac</option>
                            <option value="Sachet" {{ old('parcel_type') == 'Sachet' ? 'selected' : '' }}>Sachet</option>
                            <option value="Colis" {{ old('parcel_type') == 'Colis' ? 'selected' : '' }}>Colis</option>
                            <option value="Déménagement" {{ old('parcel_type') == 'Déménagement' ? 'selected' : '' }}>Déménagement</option>
                            <option value="Déménagement complet" {{ old('parcel_type') == 'Déménagement complet' ? 'selected' : '' }}>Déménagement complet</option>
                            <option value="Bazart" {{ old('parcel_type') == 'Bazart' ? 'selected' : '' }}>Bazart</option>
                            <option value="Salon complet" {{ old('parcel_type') == 'Salon complet' ? 'selected' : '' }}>Salon complet</option>
                        </select>
                        @error('parcel_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Montant</label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                               id="amount" name="amount" value="{{ old('amount') }}">
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="parcel_value" class="form-label">Valeur du colis</label>
                        <input type="number" step="0.01" class="form-control @error('parcel_value') is-invalid @enderror" 
                               id="parcel_value" name="parcel_value" value="{{ old('parcel_value') }}">
                        @error('parcel_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo du colis</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                               id="photo" name="photo" accept="image/*">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Format accepté: JPG, PNG, GIF (max 20 MB)</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('parcels.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@push('page-js')
<script>
// Recherche automatique du client expéditeur par téléphone
let senderSearchTimeout;
document.getElementById('sender_phone').addEventListener('input', function() {
    const phone = this.value.trim();
    const nameInput = document.getElementById('sender_name');
    const messageDiv = document.getElementById('sender-search-message');
    
    // Réinitialiser le message
    messageDiv.style.display = 'none';
    messageDiv.className = 'mt-2';
    messageDiv.innerHTML = '';
    
    // Si le champ nom est déjà rempli manuellement, ne pas le modifier
    if (nameInput.value.trim() && nameInput.dataset.autoFilled !== 'true') {
        return;
    }
    
    // Attendre un peu avant de faire la recherche (debounce)
    clearTimeout(senderSearchTimeout);
    senderSearchTimeout = setTimeout(function() {
        if (phone.length >= 8) { // Minimum 8 caractères pour rechercher
            fetch(`/clients/search-by-phone?phone=${encodeURIComponent(phone)}`)
                .then(response => {
                    if (response.status === 404) {
                        // Client non trouvé, ne rien faire
                        nameInput.dataset.autoFilled = 'false';
                        return null;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.found && data.client) {
                        // Client trouvé, remplir le nom
                        nameInput.value = data.client.name;
                        nameInput.dataset.autoFilled = 'true';
                        messageDiv.style.display = 'block';
                        messageDiv.className = 'mt-2 text-success';
                        messageDiv.innerHTML = '<i class="bx bx-check-circle"></i> Client trouvé : ' + data.client.name;
                    } else {
                        // Client non trouvé
                        if (nameInput.dataset.autoFilled === 'true') {
                            nameInput.value = '';
                        }
                        nameInput.dataset.autoFilled = 'false';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche du client expéditeur:', error);
                    nameInput.dataset.autoFilled = 'false';
                });
        } else {
            // Numéro trop court, réinitialiser si c'était auto-rempli
            if (nameInput.dataset.autoFilled === 'true') {
                nameInput.value = '';
                nameInput.dataset.autoFilled = 'false';
            }
        }
    }, 500); // Attendre 500ms après la dernière frappe
});

// Réinitialiser le flag autoFilled si l'utilisateur modifie manuellement le nom de l'expéditeur
document.getElementById('sender_name').addEventListener('input', function() {
    if (this.dataset.autoFilled === 'true') {
        this.dataset.autoFilled = 'false';
    }
});

// Recherche automatique du client bénéficiaire par téléphone
let recipientSearchTimeout;
document.getElementById('recipient_phone').addEventListener('input', function() {
    const phone = this.value.trim();
    const nameInput = document.getElementById('recipient_name');
    const messageDiv = document.getElementById('recipient-search-message');
    
    // Réinitialiser le message
    messageDiv.style.display = 'none';
    messageDiv.className = 'mt-2';
    messageDiv.innerHTML = '';
    
    // Si le champ nom est déjà rempli manuellement, ne pas le modifier
    if (nameInput.value.trim() && nameInput.dataset.autoFilled !== 'true') {
        return;
    }
    
    // Attendre un peu avant de faire la recherche (debounce)
    clearTimeout(recipientSearchTimeout);
    recipientSearchTimeout = setTimeout(function() {
        if (phone.length >= 8) { // Minimum 8 caractères pour rechercher
            fetch(`/clients/search-by-phone?phone=${encodeURIComponent(phone)}`)
                .then(response => {
                    if (response.status === 404) {
                        // Client non trouvé, ne rien faire
                        nameInput.dataset.autoFilled = 'false';
                        return null;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.found && data.client) {
                        // Client trouvé, remplir le nom
                        nameInput.value = data.client.name;
                        nameInput.dataset.autoFilled = 'true';
                        messageDiv.style.display = 'block';
                        messageDiv.className = 'mt-2 text-success';
                        messageDiv.innerHTML = '<i class="bx bx-check-circle"></i> Client trouvé : ' + data.client.name;
                    } else {
                        // Client non trouvé
                        if (nameInput.dataset.autoFilled === 'true') {
                            nameInput.value = '';
                        }
                        nameInput.dataset.autoFilled = 'false';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche du client bénéficiaire:', error);
                    nameInput.dataset.autoFilled = 'false';
                });
        } else {
            // Numéro trop court, réinitialiser si c'était auto-rempli
            if (nameInput.dataset.autoFilled === 'true') {
                nameInput.value = '';
                nameInput.dataset.autoFilled = 'false';
            }
        }
    }, 500); // Attendre 500ms après la dernière frappe
});

// Réinitialiser le flag autoFilled si l'utilisateur modifie manuellement le nom du bénéficiaire
document.getElementById('recipient_name').addEventListener('input', function() {
    if (this.dataset.autoFilled === 'true') {
        this.dataset.autoFilled = 'false';
    }
});
</script>
@endpush
@endsection

