@extends('layouts.app')

@section('title', 'Configurer la disposition des sièges')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Configurer la disposition des sièges - {{ $bus->immatriculation }}</h5>
        <a href="{{ route('buses.show', $bus) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Retour
        </a>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="alert alert-info">
            <i class="bx bx-info-circle"></i>
            <strong>Instructions :</strong> Cliquez sur les numéros pour les modifier. La disposition doit contenir tous les sièges de 1 à {{ $bus->capacity }}.
        </div>

        <form id="seatLayoutForm" method="POST" action="{{ route('buses.save-seat-layout', $bus) }}">
            @csrf
            
            <div class="mb-4">
                <label class="form-label fw-bold">Disposition des sièges ({{ $bus->capacity }} sièges)</label>
                <div id="seat-grid-container" class="seat-grid-container">
                    <div id="seat-grid" class="seat-grid">
                        <!-- Les sièges seront générés par JavaScript -->
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="resetLayout()">
                    <i class="bx bx-reset"></i> Réinitialiser
                </button>
                <div>
                    <a href="{{ route('buses.show', $bus) }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Enregistrer la disposition
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('page-css')
<style>
.seat-grid-container {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.seat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    row-gap: 8px;
    column-gap: 8px;
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

/* Ajouter un espace au milieu entre les colonnes 2 et 3 */
.seat-item-config:nth-child(4n+1),
.seat-item-config:nth-child(4n+2) {
    margin-right: 12px;
}

.seat-item-config:nth-child(4n+3),
.seat-item-config:nth-child(4n+4) {
    margin-left: 12px;
}

.seat-item-config {
    aspect-ratio: 1;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 70px;
    padding: 10px;
    background-color: #ffffff;
    color: #333;
}

.seat-item-config:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    border-color: var(--primary-color, #696cff);
}

.seat-item-config i {
    font-size: 20px;
    margin-bottom: 5px;
    color: #6c757d;
}

.seat-item-config .seat-number-input {
    width: 100%;
    border: none;
    background: transparent;
    text-align: center;
    font-size: 20px;
    font-weight: 700;
    color: #333;
    padding: 0;
    cursor: pointer;
    outline: none;
}

.seat-item-config .seat-number-input:focus {
    outline: 2px solid var(--primary-color, #696cff);
    outline-offset: -2px;
    border-radius: 4px;
}

.seat-item-config .seat-number-input::-webkit-inner-spin-button,
.seat-item-config .seat-number-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.seat-item-config .seat-number-input[type=number] {
    -moz-appearance: textfield;
}

@media (max-width: 768px) {
    .seat-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        padding: 15px;
        max-width: 400px;
    }
    
    .seat-item-config {
        min-height: 60px;
        border-radius: 6px;
        padding: 8px;
    }
    
    .seat-item-config i {
        font-size: 18px;
    }
    
    .seat-item-config .seat-number-input {
        font-size: 18px;
    }
}
</style>
@endpush

<script>
const capacity = {{ $bus->capacity }};
const currentLayout = @json($bus->seat_layout ?? null);

// Générer la disposition initiale
let seatLayout = [];
if (currentLayout && Array.isArray(currentLayout) && currentLayout.length === capacity) {
    seatLayout = [...currentLayout];
} else {
    // Disposition par défaut : sièges numérotés de 1 à capacity
    seatLayout = Array.from({ length: capacity }, (_, i) => i + 1);
}

function initializeGrid() {
    const grid = document.getElementById('seat-grid');
    grid.innerHTML = '';
    
    seatLayout.forEach((seatNumber, index) => {
        const seatItem = document.createElement('div');
        seatItem.className = 'seat-item-config';
        
        // Créer l'icône de chaise
        const seatIcon = document.createElement('i');
        seatIcon.className = 'bx bx-chair';
        
        // Créer l'input pour le numéro
        const seatInput = document.createElement('input');
        seatInput.type = 'number';
        seatInput.className = 'seat-number-input';
        seatInput.name = 'seat_layout[]';
        seatInput.value = seatNumber;
        seatInput.min = 1;
        seatInput.max = capacity;
        seatInput.setAttribute('data-index', index);
        seatInput.required = true;
        
        seatItem.appendChild(seatIcon);
        seatItem.appendChild(seatInput);
        
        // Gestion des événements
        seatInput.addEventListener('focus', function() {
            this.select();
        });
        
        // Permettre la modification libre sans validation en temps réel
        seatInput.addEventListener('change', function() {
            const newValue = parseInt(this.value);
            // Validation basique uniquement (valeur entre 1 et capacity)
            if (isNaN(newValue) || newValue < 1 || newValue > capacity) {
                alert(`Le numéro doit être entre 1 et ${capacity}`);
                this.value = seatLayout[this.dataset.index];
                return;
            }
            
            // Mettre à jour la valeur sans vérifier les doublons (validation à l'enregistrement)
            const currentIndex = parseInt(this.dataset.index);
            seatLayout[currentIndex] = newValue;
        });
        
        // Permettre aussi la modification avec les flèches du clavier
        seatInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                e.preventDefault();
                const currentValue = parseInt(this.value) || 1;
                const newValue = e.key === 'ArrowUp' 
                    ? Math.min(currentValue + 1, capacity)
                    : Math.max(currentValue - 1, 1);
                this.value = newValue;
                const currentIndex = parseInt(this.dataset.index);
                seatLayout[currentIndex] = newValue;
            }
        });
        
        grid.appendChild(seatItem);
    });
}

// Fonction supprimée - plus besoin de validation en temps réel

function resetLayout() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser la disposition ?')) {
        seatLayout = Array.from({ length: capacity }, (_, i) => i + 1);
        initializeGrid();
    }
}

// Initialiser la grille au chargement
document.addEventListener('DOMContentLoaded', function() {
    initializeGrid();
    
    // Validation uniquement lors de la soumission du formulaire
    document.getElementById('seatLayoutForm').addEventListener('submit', function(e) {
        // Mettre à jour seatLayout avec les valeurs actuelles des inputs
        const inputs = this.querySelectorAll('input[name="seat_layout[]"]');
        const values = [];
        
        inputs.forEach((input, index) => {
            const value = parseInt(input.value);
            if (isNaN(value) || value < 1 || value > capacity) {
                e.preventDefault();
                alert(`Erreur: Le siège à la position ${index + 1} a une valeur invalide (${input.value}). Les valeurs doivent être entre 1 et ${capacity}.`);
                input.focus();
                return false;
            }
            values.push(value);
            seatLayout[index] = value;
        });
        
        // Vérifier le nombre de sièges
        if (values.length !== capacity) {
            e.preventDefault();
            alert(`Erreur: Le nombre de sièges (${values.length}) ne correspond pas à la capacité du bus (${capacity}).`);
            return false;
        }
        
        // Vérifier que tous les sièges de 1 à capacity sont présents
        const sorted = [...values].sort((a, b) => a - b);
        const expected = Array.from({ length: capacity }, (_, i) => i + 1);
        
        if (JSON.stringify(sorted) !== JSON.stringify(expected)) {
            e.preventDefault();
            const missing = expected.filter(x => !sorted.includes(x));
            const duplicates = values.filter((val, idx) => values.indexOf(val) !== idx);
            let errorMsg = 'Erreur dans la disposition des sièges:\n\n';
            
            if (duplicates.length > 0) {
                const uniqueDuplicates = [...new Set(duplicates)];
                errorMsg += `- Les sièges suivants sont en double: ${uniqueDuplicates.join(', ')}\n`;
            }
            
            if (missing.length > 0) {
                errorMsg += `- Les sièges suivants sont manquants: ${missing.join(', ')}\n`;
            }
            
            // Vérifier les valeurs en trop
            const extra = sorted.filter(x => x > capacity || x < 1);
            if (extra.length > 0) {
                errorMsg += `- Les valeurs suivantes sont invalides: ${extra.join(', ')}\n`;
            }
            
            errorMsg += `\nLa disposition doit contenir tous les sièges de 1 à ${capacity} exactement une fois.`;
            
            alert(errorMsg);
            return false;
        }
        
        // Vérifier les doublons (double vérification)
        if (new Set(values).size !== values.length) {
            e.preventDefault();
            const duplicates = values.filter((val, idx) => values.indexOf(val) !== idx);
            const uniqueDuplicates = [...new Set(duplicates)];
            alert(`Les numéros de sièges suivants sont en double: ${uniqueDuplicates.join(', ')}\n\nChaque siège doit avoir un numéro unique.`);
            return false;
        }
        
        // S'assurer que tous les inputs ont des valeurs valides avant l'envoi
        inputs.forEach(input => {
            const value = parseInt(input.value);
            if (isNaN(value) || value < 1 || value > capacity) {
                input.value = seatLayout[parseInt(input.dataset.index)] || 1;
            }
        });
    });
});
</script>
@endsection

