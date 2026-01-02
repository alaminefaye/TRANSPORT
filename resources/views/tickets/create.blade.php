@extends('layouts.app')

@section('title', 'Vendre un Ticket')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Vendre un Nouveau Ticket</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tickets.store') }}" method="POST" id="ticketForm" onsubmit="return validateStops();">
            @csrf
            
            <div class="mb-3">
                <label for="trip_id" class="form-label">Voyage *</label>
                <select class="form-select @error('trip_id') is-invalid @enderror" id="trip_id" name="trip_id" required>
                    <option value="">Sélectionner un voyage</option>
                    @foreach($trips as $trip)
                        <option value="{{ $trip->id }}" {{ old('trip_id', request('trip_id')) == $trip->id ? 'selected' : '' }}
                            data-route-id="{{ $trip->route_id }}"
                            data-route-number="{{ $trip->route->route_number ?? $trip->route_id }}"
                            data-capacity="{{ $trip->bus->capacity }}">
                            @if($trip->route->route_number)
                                [{{ $trip->route->route_number }}] 
                            @endif
                            {{ $trip->route->departure_city }} → {{ $trip->route->arrival_city }} 
                            ({{ $trip->departure_time->format('d/m/Y H:i') }})
                        </option>
                    @endforeach
                </select>
                @error('trip_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="from_stop_id" class="form-label">Arrêt de montée *</label>
                    <select class="form-select @error('from_stop_id') is-invalid @enderror" id="from_stop_id" name="from_stop_id" required>
                        <option value="">Sélectionner un arrêt</option>
                    </select>
                    @error('from_stop_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="invalid-feedback" id="from_stop_error" style="display: none;">L'arrêt de montée ne peut pas être identique à l'arrêt de descente.</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="to_stop_id" class="form-label">Arrêt de descente *</label>
                    <select class="form-select @error('to_stop_id') is-invalid @enderror" id="to_stop_id" name="to_stop_id" required>
                        <option value="">Sélectionner un arrêt</option>
                    </select>
                    @error('to_stop_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="invalid-feedback" id="to_stop_error" style="display: none;">L'arrêt de descente ne peut pas être identique à l'arrêt de montée.</div>
                </div>
            </div>
            
            <!-- Sélection visuelle des sièges -->
            <div class="mb-4" id="seat-selection-container" style="display: none;">
                <label class="form-label mb-3">Sélection des sièges</label>
                
                <!-- Légende -->
                <div class="mb-3 d-flex gap-3 flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="seat-legend-item seat-free me-2">
                            <i class="bx bx-chair"></i>
                        </div>
                        <small>Libre</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="seat-legend-item seat-selected me-2">
                            <i class="bx bx-chair"></i>
                        </div>
                        <small>Sélectionné</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="seat-legend-item seat-occupied me-2">
                            <i class="bx bx-chair"></i>
                        </div>
                        <small>Occupé</small>
                    </div>
                </div>
                
                <!-- Grille des sièges -->
                <div id="seat-grid" class="seat-grid mb-3">
                    <!-- Les sièges seront générés dynamiquement -->
                </div>
                
                <!-- Sièges sélectionnés -->
                <div id="selected-seats-info" class="alert alert-info" style="display: none;">
                    <strong>Sièges sélectionnés:</strong> <span id="selected-seats-list"></span>
                </div>
                
                <input type="hidden" id="selected_seats" name="selected_seats" value="">
                <small class="text-muted d-block">Cliquez sur les sièges pour les sélectionner. Vous pouvez sélectionner plusieurs sièges pour créer plusieurs tickets.</small>
            </div>
            
            <!-- Ancien select (caché pour compatibilité) -->
            <input type="hidden" id="seat_number" name="seat_number" value="">
            
            <div class="mb-3">
                <label class="form-label">Prix</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="price_display" readonly value="0 FCFA">
                    <input type="hidden" id="calculated_price" name="price" value="0">
                </div>
                <div id="price-details" class="mt-2" style="display: none;">
                    <small class="text-muted">
                        <span id="unit-price-text"></span>
                        <span id="total-price-text" class="fw-bold text-success"></span>
                    </small>
                </div>
                <small class="text-muted">
                    <strong>Note importante:</strong> Les prix sont définis pour la route (trajet), pas pour chaque voyage. 
                    Une fois que vous avez défini les prix d'un trajet, ils sont automatiquement utilisés pour tous les voyages de ce trajet, 
                    peu importe la date. Si le prix affiche 0, vérifiez que les tarifs sont bien définis dans la section "Tarifs des tarifs".
                </small>
            </div>
            
            <!-- Points de fidélité -->
            <div class="mb-3" id="loyalty-points-section" style="display: none;">
                <div class="alert alert-info">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>Points de fidélité :</strong> 
                            <span id="client-loyalty-points" class="fw-bold text-primary">0</span> points
                        </div>
                        <div id="free-ticket-available" style="display: none;">
                            <span class="badge bg-success">Voyage gratuit disponible !</span>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="use_loyalty_points" name="use_loyalty_points" value="1">
                        <label class="form-check-label" for="use_loyalty_points">
                            Utiliser 10 points pour un voyage gratuit
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2">
                        💡 <strong>Règles :</strong> Vous gagnez 1 point par ticket acheté. Si vous prenez plusieurs tickets le même jour avec des arrêts de montée différents, vous pouvez gagner plusieurs points. Avec 10 points, vous pouvez obtenir un voyage gratuit !
                    </small>
                </div>
            </div>
            
            <hr>
            
            <h5 class="mb-3">👤 Informations du passager</h5>
            
            <div class="mb-3">
                <label for="passenger_phone" class="form-label">Téléphone *</label>
                <input type="text" class="form-control @error('passenger_phone') is-invalid @enderror" 
                       id="passenger_phone" name="passenger_phone" value="{{ old('passenger_phone') }}" required>
                @error('passenger_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Le client sera automatiquement enregistré ou trouvé dans la base de données.</small>
            </div>
            
            <div class="mb-3">
                <label for="passenger_name" class="form-label">Nom complet *</label>
                <input type="text" class="form-control @error('passenger_name') is-invalid @enderror" 
                       id="passenger_name" name="passenger_name" value="{{ old('passenger_name') }}" required>
                @error('passenger_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="payment_method" class="form-label">Méthode de paiement *</label>
                <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                    <option value="">Sélectionner une méthode</option>
                    <option value="Espèce" {{ old('payment_method', 'Espèce') == 'Espèce' ? 'selected' : '' }}>Espèce</option>
                    <option value="Carte bancaire" {{ old('payment_method') == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                    <option value="Mobile Money" {{ old('payment_method') == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                    <option value="Virement" {{ old('payment_method') == 'Virement' ? 'selected' : '' }}>Virement</option>
                    <option value="Points de fidélité" id="loyalty-payment-option" style="display: none;">Points de fidélité (Voyage gratuit)</option>
                </select>
                @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Vendre le ticket</button>
            </div>
        </form>
    </div>
</div>

@push('page-css')
<style>
    .seat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2px;
        row-gap: 2px;
        column-gap: 2px;
        max-width: 260px;
        margin: 0 auto;
        padding: 5px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    /* Ajouter un espace au milieu entre les colonnes 2 et 3 */
    .seat-button:nth-child(4n+1),
    .seat-button:nth-child(4n+2) {
        margin-right: 6px;
    }
    
    .seat-button:nth-child(4n+3),
    .seat-button:nth-child(4n+4) {
        margin-left: 6px;
    }
    
    .seat-button {
        aspect-ratio: 1;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-weight: 600;
        font-size: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 26px;
        padding: 2px;
    }
    
    .seat-button:hover:not(.seat-occupied):not(.seat-selected) {
        transform: scale(1.05);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .seat-icon {
        font-size: 10px;
        margin-bottom: 1px;
    }
    
    .seat-number {
        font-size: 11px;
        font-weight: 700;
    }
    
    /* Siège libre - Blanc */
    .seat-free {
        background-color: #ffffff;
        color: #333;
        border-color: #dee2e6;
    }
    
    .seat-free .seat-icon {
        color: #6c757d;
    }
    
    /* Siège sélectionné - Vert */
    .seat-selected {
        background-color: #28a745 !important;
        color: #ffffff !important;
        border-color: #28a745 !important;
    }
    
    .seat-selected .seat-icon,
    .seat-selected .seat-number {
        color: #ffffff !important;
    }
    
    /* Siège occupé - Rouge */
    .seat-occupied {
        background-color: #dc3545 !important;
        color: #ffffff !important;
        border-color: #dc3545 !important;
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    .seat-occupied .seat-icon,
    .seat-occupied .seat-number {
        color: #ffffff !important;
    }
    
    .seat-legend-item {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        padding: 2px;
    }
    
    .seat-legend-item i {
        margin-bottom: 1px;
    }
    
    .seat-legend-item.seat-free {
        background-color: #ffffff;
        color: #6c757d;
    }
    
    .seat-legend-item.seat-selected {
        background-color: #28a745;
        border-color: #28a745;
        color: #ffffff;
    }
    
    .seat-legend-item.seat-occupied {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #ffffff;
    }
    
    @media (max-width: 768px) {
        .seat-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 2px;
            padding: 4px;
            max-width: 100%;
        }
        
        .seat-button {
            min-height: 24px;
            border-radius: 6px;
        }
        
        .seat-icon {
            font-size: 9px;
        }
        
        .seat-number {
            font-size: 10px;
            font-weight: 700;
        }
    }
</style>
@endpush

@push('page-js')
<script>
@php
$routesData = [];
foreach ($trips as $trip) {
    if (!$trip->route || !$trip->route->routeStops) {
        $routesData[$trip->id] = ['stops' => [], 'departure_time' => '', 'departure_time_formatted' => ''];
        continue;
    }
    
    $departureTime = \Carbon\Carbon::parse($trip->departure_time);
    $stops = [];
    
    foreach ($trip->route->routeStops->sortBy('order') as $routeStop) {
        $estimatedTimeStr = '';
        if ($routeStop->estimated_time) {
            $estimatedTimeStr = substr($routeStop->estimated_time, 0, 5);
        }
        
        $stops[] = [
            'id' => $routeStop->stop->id,
            'name' => $routeStop->stop->name . ' (' . $routeStop->stop->city . ')',
            'order' => $routeStop->order,
            'estimated_time' => $estimatedTimeStr
        ];
    }
    
    $routesData[$trip->id] = [
        'stops' => $stops,
        'departure_time' => $departureTime->format('Y-m-d H:i:s'),
        'departure_time_formatted' => $departureTime->format('H:i')
    ];
}
@endphp

const routes = @json($routesData);

console.log('Routes loaded:', routes);

document.getElementById('trip_id').addEventListener('change', function() {
    const tripId = this.value;
    const fromSelect = document.getElementById('from_stop_id');
    const toSelect = document.getElementById('to_stop_id');
    const seatSelect = document.getElementById('seat_number');
    
    // Réinitialiser les sièges sélectionnés
    selectedSeats = [];
    seatStates = {};
    updateSelectedSeatsInfo();
    updateHiddenInputs();
    
    // Vider les sélections
    fromSelect.innerHTML = '<option value="">Sélectionner un arrêt</option>';
    toSelect.innerHTML = '<option value="">Sélectionner un arrêt</option>';
    seatSelect.innerHTML = '<option value="">Laisser le système choisir automatiquement</option>';
    
    // Cacher la grille de sièges
    document.getElementById('seat-selection-container').style.display = 'none';
    
    if (tripId && routes[tripId] && routes[tripId].stops && routes[tripId].stops.length > 0) {
        const tripData = routes[tripId];
        const stops = tripData.stops;
        const departureTime = new Date(tripData.departure_time);
        
        console.log('Loading stops for trip:', tripId, stops);
        
        stops.forEach(function(stop, index) {
            // Afficher l'heure de passage
            let passageTime = '';
            if (stop.estimated_time && stop.estimated_time.trim() !== '') {
                // Utiliser l'heure estimée de la route (format H:i ou H:i:s)
                // Extraire seulement H:i si c'est au format H:i:s
                passageTime = stop.estimated_time.substring(0, 5); // Prendre les 5 premiers caractères (HH:MM)
            } else {
                // Si pas d'heure estimée définie, utiliser l'heure de départ
                passageTime = tripData.departure_time_formatted;
            }
            
            // Afficher le nom de l'arrêt avec l'heure de passage
            const displayName = stop.name + ' - ' + passageTime;
            const option1 = new Option(displayName, stop.id);
            const option2 = new Option(displayName, stop.id);
            fromSelect.add(option1);
            toSelect.add(option2);
        });
        
        // Réinitialiser la validation après le chargement des arrêts
        validateStops();
        
        // Charger les sièges disponibles
        loadAvailableSeats(tripId, null, null);
    } else {
        console.warn('No stops found for trip:', tripId);
        // Afficher un message si aucun arrêt n'est trouvé
        if (tripId) {
            fromSelect.innerHTML = '<option value="">Aucun arrêt disponible pour ce voyage</option>';
            toSelect.innerHTML = '<option value="">Aucun arrêt disponible pour ce voyage</option>';
        }
    }
    
    updatePrice();
});

document.getElementById('from_stop_id').addEventListener('change', function() {
    validateStops();
    updatePrice();
    // Réinitialiser les sièges sélectionnés quand on change d'arrêt
    selectedSeats = [];
    updateSelectedSeatsInfo();
    updateHiddenInputs();
    updateSeats();
});

document.getElementById('to_stop_id').addEventListener('change', function() {
    validateStops();
    updatePrice();
    // Réinitialiser les sièges sélectionnés quand on change d'arrêt
    selectedSeats = [];
    updateSelectedSeatsInfo();
    updateHiddenInputs();
    updateSeats();
});

function validateStops() {
    const fromStopId = document.getElementById('from_stop_id').value;
    const toStopId = document.getElementById('to_stop_id').value;
    const fromSelect = document.getElementById('from_stop_id');
    const toSelect = document.getElementById('to_stop_id');
    const fromError = document.getElementById('from_stop_error');
    const toError = document.getElementById('to_stop_error');
    
    // Réinitialiser les erreurs visuelles
    fromSelect.classList.remove('is-invalid');
    toSelect.classList.remove('is-invalid');
    fromError.style.display = 'none';
    toError.style.display = 'none';
    
    // Vérifier si les deux arrêts sont identiques
    if (fromStopId && toStopId && fromStopId === toStopId) {
        fromSelect.classList.add('is-invalid');
        toSelect.classList.add('is-invalid');
        fromError.style.display = 'block';
        toError.style.display = 'block';
        return false;
    }
    
    return true;
}

function updatePrice() {
    const tripId = document.getElementById('trip_id').value;
    const fromStopId = document.getElementById('from_stop_id').value;
    const toStopId = document.getElementById('to_stop_id').value;
    
    console.log('=== UPDATE PRICE CALLED ===');
    console.log('Trip ID:', tripId);
    console.log('From Stop ID:', fromStopId);
    console.log('To Stop ID:', toStopId);
    
    if (!tripId || !fromStopId || !toStopId) {
        console.log('Missing required fields, resetting price to 0');
        document.getElementById('price_display').value = '0 FCFA';
        document.getElementById('calculated_price').value = '0';
        return;
    }
    
    // Vérifier si les arrêts sont identiques
    if (fromStopId === toStopId) {
        console.log('Same stop selected for departure and arrival, resetting price to 0');
        document.getElementById('price_display').value = '0 FCFA';
        document.getElementById('calculated_price').value = '0';
        return;
    }
    
    // Use relative URL for better compatibility
    const url = `/tickets/calculate-price?trip_id=${tripId}&from_stop_id=${fromStopId}&to_stop_id=${toStopId}`;
    console.log('Fetching price from URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response received:', response);
            console.log('Response status:', response.status);
            console.log('Response OK:', response.ok);
            
            // Essayer de lire le JSON même si la réponse n'est pas OK
            return response.json().then(data => {
                return {
                    ok: response.ok,
                    status: response.status,
                    data: data
                };
            }).catch(() => {
                // Si le JSON ne peut pas être lu, retourner une erreur
                return {
                    ok: false,
                    status: response.status,
                    data: { price: 0, error: 'Erreur de communication avec le serveur' }
                };
            });
        })
        .then(result => {
            const data = result.data;
            console.log('Price data received:', data);
            const price = data.price || 0;
            console.log('Calculated price:', price);
            
            const priceDisplay = document.getElementById('price_display');
            const priceInput = document.getElementById('calculated_price');
            
            // Réinitialiser le style
            priceDisplay.classList.remove('is-invalid');
            priceDisplay.style.color = '';
            priceDisplay.style.fontWeight = '';
            
            if (price === 0 || !result.ok) {
                if (data.error) {
                    // Afficher l'erreur de manière visible
                    priceDisplay.value = '⚠️ ' + data.error.substring(0, 50) + (data.error.length > 50 ? '...' : '');
                    priceDisplay.classList.add('is-invalid');
                    priceDisplay.style.color = '#dc3545';
                    priceDisplay.style.fontWeight = 'bold';
                    priceDisplay.style.backgroundColor = '#fff3cd';
                    console.error('Price calculation error:', data.error);
                    console.error('Missing segments:', data.missing_segments);
                    
                    // Construire le message d'erreur détaillé
                    let errorMessage = '⚠️ ATTENTION: ' + data.error;
                    
                    // Ajouter les détails des segments manquants
                    if (data.missing_segments && data.missing_segments.length > 0) {
                        errorMessage += '\n\n📋 Segments manquants:\n';
                        data.missing_segments.forEach(function(seg, index) {
                            errorMessage += `   ${index + 1}. ${seg.from_stop_name} → ${seg.to_stop_name}\n`;
                        });
                    }
                    
                    // Ajouter le lien vers les tarifs
                    const tripSelect = document.getElementById('trip_id');
                    const selectedOption = tripSelect.selectedOptions[0];
                    const routeId = data.route_id || selectedOption?.dataset?.routeId;
                    const routeNumber = selectedOption?.dataset?.routeNumber || routeId;
                    
                    if (routeId) {
                        errorMessage += `\n👉 SOLUTION: Allez dans "Configurations des tarifs" (menu de gauche) et créez les prix manquants pour la route #${routeNumber} (ID: ${routeId})`;
                    }
                    
                    errorMessage += '\n\n💡 Les prix doivent être définis pour la route (trajet), pas pour chaque voyage. Une fois définis, ils sont automatiquement utilisés pour tous les voyages de cette route.';
                    
                    alert(errorMessage);
                } else {
                    priceDisplay.value = '0 FCFA';
                    priceDisplay.style.backgroundColor = '';
                    console.warn('Prix calculé à 0 - Vérifiez que des tarifs sont définis pour cette route');
                }
            } else {
                priceDisplay.value = new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
                priceDisplay.style.backgroundColor = '';
                updatePriceDetails(price);
            }
            
            priceInput.value = price;
        })
        .catch(error => {
            console.error('Error calculating price:', error);
            console.error('Error details:', error.message);
            const priceDisplay = document.getElementById('price_display');
            priceDisplay.value = '❌ Erreur de connexion';
            priceDisplay.classList.add('is-invalid');
            priceDisplay.style.color = '#dc3545';
            priceDisplay.style.fontWeight = 'bold';
            document.getElementById('calculated_price').value = '0';
        });
}

function updateSeats() {
    const tripId = document.getElementById('trip_id').value;
    const fromStopId = document.getElementById('from_stop_id').value;
    const toStopId = document.getElementById('to_stop_id').value;
    
    if (!tripId || !fromStopId || !toStopId) {
        return;
    }
    
    // Ne pas charger les sièges si les arrêts sont identiques
    if (fromStopId === toStopId) {
        return;
    }
    
    loadAvailableSeats(tripId, fromStopId, toStopId);
}

// Variables globales pour la gestion des sièges
let selectedSeats = [];
let seatStates = {}; // {seatNumber: 'free'|'occupied'|'selected'}

function loadAvailableSeats(tripId, fromStopId, toStopId) {
    const seatContainer = document.getElementById('seat-selection-container');
    const seatGrid = document.getElementById('seat-grid');
    const selectedOption = document.getElementById('trip_id').selectedOptions[0];
    const capacity = selectedOption ? parseInt(selectedOption.dataset.capacity) : 0;
    
    // Réinitialiser
    selectedSeats = [];
    seatStates = {};
    seatGrid.innerHTML = '';
    updateSelectedSeatsInfo();
    
    if (!tripId || !capacity) {
        seatContainer.style.display = 'none';
        return;
    }
    
    if (!fromStopId || !toStopId) {
        // Afficher tous les sièges comme libres (sans vérification)
        generateSeatGrid(capacity, tripId, fromStopId, toStopId, []);
        seatContainer.style.display = 'block';
        return;
    }
    
    // Charger les sièges disponibles et leur état
    fetch(`/tickets/available-seats?trip_id=${tripId}&from_stop_id=${fromStopId}&to_stop_id=${toStopId}`)
        .then(response => response.json())
        .then(data => {
            const availableSeats = data.available_seats || [];
            const occupiedSeats = data.occupied_seats || [];
            const totalCapacity = data.total_capacity || capacity;
            
            // Marquer les sièges comme libres ou occupés
            for (let seat = 1; seat <= totalCapacity; seat++) {
                if (occupiedSeats.includes(seat)) {
                    seatStates[seat] = 'occupied';
                } else if (availableSeats.includes(seat)) {
                    seatStates[seat] = 'free';
                } else {
                    seatStates[seat] = 'free'; // Par défaut, considérer comme libre
                }
            }
            
            generateSeatGrid(totalCapacity, tripId, fromStopId, toStopId, availableSeats, occupiedSeats);
            seatContainer.style.display = 'block';
        })
        .catch(error => {
            console.error('Error loading seats:', error);
            // En cas d'erreur, afficher tous les sièges comme libres
            for (let seat = 1; seat <= capacity; seat++) {
                seatStates[seat] = 'free';
            }
            generateSeatGrid(capacity, tripId, fromStopId, toStopId, [], []);
            seatContainer.style.display = 'block';
        });
}

function generateSeatGrid(capacity, tripId, fromStopId, toStopId, availableSeats, occupiedSeats = []) {
    const seatGrid = document.getElementById('seat-grid');
    seatGrid.innerHTML = '';
    
    // Créer un bouton pour chaque siège
    for (let seat = 1; seat <= capacity; seat++) {
        const seatButton = document.createElement('button');
        seatButton.type = 'button';
        seatButton.className = 'seat-button';
        seatButton.dataset.seatNumber = seat;
        
        // Créer l'icône de chaise
        const seatIcon = document.createElement('i');
        seatIcon.className = 'bx bx-chair seat-icon';
        
        // Créer le numéro du siège
        const seatNumber = document.createElement('span');
        seatNumber.className = 'seat-number';
        seatNumber.textContent = seat;
        
        // Ajouter l'icône et le numéro au bouton
        seatButton.appendChild(seatIcon);
        seatButton.appendChild(seatNumber);
        
        // Déterminer l'état initial (utiliser seatStates si déjà défini, sinon calculer)
        let state = seatStates[seat] || 'free';
        if (occupiedSeats.length > 0 && occupiedSeats.includes(seat)) {
            state = 'occupied';
            seatStates[seat] = 'occupied';
        } else if (availableSeats.length > 0 && availableSeats.includes(seat)) {
            state = 'free';
            if (!seatStates[seat] || seatStates[seat] !== 'selected') {
                seatStates[seat] = 'free';
            }
        }
        
        // Appliquer les classes CSS
        seatButton.classList.add(`seat-${state}`);
        
        // Ajouter l'événement de clic seulement si le siège n'est pas occupé
        if (state !== 'occupied') {
            seatButton.addEventListener('click', function() {
                toggleSeatSelection(seat);
            });
        }
        
        seatGrid.appendChild(seatButton);
    }
}

function toggleSeatSelection(seatNumber) {
    const seatButton = document.querySelector(`[data-seat-number="${seatNumber}"]`);
    
    if (!seatButton) return;
    
    // Ne pas permettre la sélection d'un siège occupé
    if (seatStates[seatNumber] === 'occupied') {
        return;
    }
    
    // Toggle la sélection
    if (seatStates[seatNumber] === 'selected') {
        // Désélectionner
        seatStates[seatNumber] = 'free';
        seatButton.classList.remove('seat-selected');
        seatButton.classList.add('seat-free');
        selectedSeats = selectedSeats.filter(s => s !== seatNumber);
    } else {
        // Sélectionner
        seatStates[seatNumber] = 'selected';
        seatButton.classList.remove('seat-free');
        seatButton.classList.add('seat-selected');
        if (!selectedSeats.includes(seatNumber)) {
            selectedSeats.push(seatNumber);
        }
    }
    
    updateSelectedSeatsInfo();
    updateHiddenInputs();
}

function updateSelectedSeatsInfo() {
    const infoDiv = document.getElementById('selected-seats-info');
    const listSpan = document.getElementById('selected-seats-list');
    const priceDisplay = document.getElementById('price_display');
    const calculatedPrice = document.getElementById('calculated_price');
    
    if (selectedSeats.length === 0) {
        infoDiv.style.display = 'none';
        document.getElementById('price-details').style.display = 'none';
        // Réinitialiser le prix si aucun siège n'est sélectionné
        if (priceDisplay && calculatedPrice) {
            const singlePrice = parseFloat(calculatedPrice.value) || 0;
            priceDisplay.value = new Intl.NumberFormat('fr-FR').format(singlePrice) + ' FCFA';
            priceDisplay.style.fontWeight = '';
            priceDisplay.style.color = '';
        }
    } else {
        infoDiv.style.display = 'block';
        const sortedSeats = selectedSeats.sort((a, b) => a - b);
        listSpan.textContent = sortedSeats.join(', ');
        
        // Calculer le prix total si plusieurs sièges sont sélectionnés
        if (priceDisplay && calculatedPrice) {
            const singlePrice = parseFloat(calculatedPrice.value) || 0;
            if (selectedSeats.length > 1 && singlePrice > 0) {
                const totalPrice = singlePrice * selectedSeats.length;
                priceDisplay.value = new Intl.NumberFormat('fr-FR').format(totalPrice) + ' FCFA';
                priceDisplay.style.fontWeight = 'bold';
                priceDisplay.style.color = '#28a745';
                updatePriceDetails(singlePrice, selectedSeats.length, totalPrice);
            } else {
                priceDisplay.value = new Intl.NumberFormat('fr-FR').format(singlePrice) + ' FCFA';
                priceDisplay.style.fontWeight = '';
                priceDisplay.style.color = '';
                document.getElementById('price-details').style.display = 'none';
            }
        }
    }
}

function updatePriceDetails(unitPrice, quantity = 1, totalPrice = null) {
    const priceDetails = document.getElementById('price-details');
    const unitPriceText = document.getElementById('unit-price-text');
    const totalPriceText = document.getElementById('total-price-text');
    
    if (quantity > 1 && totalPrice !== null) {
        priceDetails.style.display = 'block';
        unitPriceText.textContent = `Prix unitaire: ${new Intl.NumberFormat('fr-FR').format(unitPrice)} FCFA × ${quantity} sièges = `;
        totalPriceText.textContent = `${new Intl.NumberFormat('fr-FR').format(totalPrice)} FCFA`;
    } else {
        priceDetails.style.display = 'none';
    }
}

function updateHiddenInputs() {
    // Mettre à jour le champ caché avec les sièges sélectionnés
    document.getElementById('selected_seats').value = selectedSeats.join(',');
    
    // Pour compatibilité, mettre le premier siège dans seat_number
    if (selectedSeats.length > 0) {
        document.getElementById('seat_number').value = selectedSeats[0];
    } else {
        document.getElementById('seat_number').value = '';
    }
}

// Initialiser si un trip_id est déjà sélectionné
document.addEventListener('DOMContentLoaded', function() {
    const tripSelect = document.getElementById('trip_id');
    if (tripSelect.value) {
        tripSelect.dispatchEvent(new Event('change'));
    }
});

// Recherche automatique du client par téléphone
let passengerSearchTimeout;
document.getElementById('passenger_phone').addEventListener('input', function() {
    const phone = this.value.trim();
    const nameInput = document.getElementById('passenger_name');
    const loyaltySection = document.getElementById('loyalty-points-section');
    const loyaltyPointsSpan = document.getElementById('client-loyalty-points');
    const freeTicketBadge = document.getElementById('free-ticket-available');
    const useLoyaltyCheckbox = document.getElementById('use_loyalty_points');
    
    // Si le champ nom est déjà rempli manuellement, ne pas le modifier
    if (nameInput.value.trim() && nameInput.dataset.autoFilled !== 'true') {
        return;
    }
    
    // Attendre un peu avant de faire la recherche (debounce)
    clearTimeout(passengerSearchTimeout);
    passengerSearchTimeout = setTimeout(function() {
        if (phone.length >= 8) { // Minimum 8 caractères pour rechercher
            fetch(`/clients/search-by-phone?phone=${encodeURIComponent(phone)}`)
                .then(response => {
                    if (response.status === 404) {
                        // Client non trouvé, cacher les points
                        nameInput.dataset.autoFilled = 'false';
                        loyaltySection.style.display = 'none';
                        return null;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.found && data.client) {
                        // Client trouvé, remplir le nom
                        nameInput.value = data.client.name;
                        nameInput.dataset.autoFilled = 'true';
                        // Afficher une notification visuelle
                        nameInput.style.backgroundColor = '#d4edda';
                        setTimeout(function() {
                            nameInput.style.backgroundColor = '';
                        }, 2000);
                        
                        // Afficher les points de fidélité
                        const points = data.client.loyalty_points || 0;
                        loyaltyPointsSpan.textContent = points;
                        loyaltySection.style.display = 'block';
                        
                        // Afficher le badge si le client peut avoir un voyage gratuit
                        if (data.client.can_use_free_ticket && points >= 10) {
                            freeTicketBadge.style.display = 'block';
                            useLoyaltyCheckbox.disabled = false;
                            // Afficher l'option de paiement par points
                            document.getElementById('loyalty-payment-option').style.display = 'block';
                        } else {
                            freeTicketBadge.style.display = 'none';
                            useLoyaltyCheckbox.disabled = true;
                            useLoyaltyCheckbox.checked = false;
                            // Cacher l'option de paiement par points
                            document.getElementById('loyalty-payment-option').style.display = 'none';
                            updatePriceWithLoyalty();
                        }
                    } else {
                        // Client non trouvé, cacher les points
                        if (nameInput.dataset.autoFilled === 'true') {
                            nameInput.value = '';
                        }
                        nameInput.dataset.autoFilled = 'false';
                        loyaltySection.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche du client:', error);
                    nameInput.dataset.autoFilled = 'false';
                    loyaltySection.style.display = 'none';
                });
        } else {
            // Numéro trop court, réinitialiser si c'était auto-rempli
            if (nameInput.dataset.autoFilled === 'true') {
                nameInput.value = '';
                nameInput.dataset.autoFilled = 'false';
            }
            loyaltySection.style.display = 'none';
            // Cacher l'option de paiement par points
            document.getElementById('loyalty-payment-option').style.display = 'none';
        }
    }, 500); // Attendre 500ms après la dernière frappe
});

// Réinitialiser le flag autoFilled si l'utilisateur modifie manuellement le nom
document.getElementById('passenger_name').addEventListener('input', function() {
    if (this.dataset.autoFilled === 'true') {
        this.dataset.autoFilled = 'false';
    }
});

// Gérer le changement de la checkbox pour utiliser les points
document.getElementById('use_loyalty_points').addEventListener('change', function() {
    updatePriceWithLoyalty();
});

// Gérer le changement de méthode de paiement
document.getElementById('payment_method').addEventListener('change', function() {
    const useLoyaltyCheckbox = document.getElementById('use_loyalty_points');
    if (this.value === 'Points de fidélité') {
        // Si l'utilisateur sélectionne "Points de fidélité", cocher la checkbox
        useLoyaltyCheckbox.checked = true;
        updatePriceWithLoyalty();
    } else if (useLoyaltyCheckbox.checked) {
        // Si l'utilisateur change la méthode de paiement alors que la checkbox est cochée
        useLoyaltyCheckbox.checked = false;
        updatePriceWithLoyalty();
    }
});

function updatePriceWithLoyalty() {
    const useLoyalty = document.getElementById('use_loyalty_points').checked;
    const priceDisplay = document.getElementById('price_display');
    const calculatedPrice = document.getElementById('calculated_price');
    const paymentMethod = document.getElementById('payment_method');
    
    if (useLoyalty) {
        // Voyage gratuit
        priceDisplay.value = '0 FCFA (Voyage gratuit avec points)';
        priceDisplay.style.color = '#28a745';
        priceDisplay.style.fontWeight = 'bold';
        calculatedPrice.value = '0';
        // Sélectionner automatiquement "Points de fidélité" comme méthode de paiement
        paymentMethod.value = 'Points de fidélité';
    } else {
        // Prix normal
        const originalPrice = calculatedPrice.value || '0';
        if (originalPrice > 0) {
            priceDisplay.value = new Intl.NumberFormat('fr-FR').format(originalPrice) + ' FCFA';
            priceDisplay.style.color = '';
            priceDisplay.style.fontWeight = '';
        } else {
            priceDisplay.value = '0 FCFA';
        }
        // Si "Points de fidélité" était sélectionné, revenir à "Espèce"
        if (paymentMethod.value === 'Points de fidélité') {
            paymentMethod.value = 'Espèce';
        }
    }
}
</script>
@endpush
@endsection

