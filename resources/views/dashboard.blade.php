@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistiques -->
    <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <i class="bx bx-receipt text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Tickets Aujourd'hui</span>
                <h3 class="card-title mb-2">{{ $stats['today_tickets'] }}</h3>
                <small class="text-muted">Total: {{ $stats['total_tickets'] }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <i class="bx bx-money text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Recettes Aujourd'hui</span>
                <h3 class="card-title mb-2">{{ number_format($stats['today_revenue'], 0, ',', ' ') }} FCFA</h3>
                <small class="text-muted">Total: {{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                    <div class="avatar flex-shrink-0">
                        <i class="bx bx-package text-info" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="mb-0">Gestion des Colis</h5>
                </div>
                <div class="row">
                    <div class="col-4 mb-3">
                        <span class="fw-semibold d-block mb-1 text-muted">Colis Créés</span>
                        <h4 class="mb-0">{{ $stats['parcels_created'] }}</h4>
                    </div>
                    <div class="col-4 mb-3">
                        <span class="fw-semibold d-block mb-1 text-muted">Colis Récupérés</span>
                        <h4 class="mb-0">{{ $stats['parcels_retrieved'] }}</h4>
                    </div>
                    <div class="col-4 mb-3">
                        <span class="fw-semibold d-block mb-1 text-muted">Montant Total</span>
                        <h4 class="mb-0">{{ number_format($stats['parcels_amount'], 0, ',', ' ') }} FCFA</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Voyages du jour -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Voyages du Jour</h5>
                <a href="{{ route('trips.create') }}" class="btn btn-sm btn-primary">Nouveau Voyage</a>
            </div>
            <div class="card-body">
                @if($todayTrips->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Trajet</th>
                                    <th>Bus</th>
                                    <th>Départ</th>
                                    <th>Statut</th>
                                    <th>Taux de Chargement</th>
                                    <th>Taux de Remplissage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayTrips as $trip)
                                <tr>
                                    <td>{{ $trip->route->departure_city }} → {{ $trip->route->arrival_city }}</td>
                                    <td>{{ $trip->bus->immatriculation }}</td>
                                    <td>{{ $trip->departure_time->format('H:i') }}</td>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                'Scheduled' => 'Programmé',
                                                'In Progress' => 'En cours',
                                                'Completed' => 'Terminé',
                                                'Cancelled' => 'Annulé'
                                            ];
                                            $statusLabel = $statusLabels[$trip->status] ?? $trip->status;
                                        @endphp
                                        <span class="badge bg-{{ $trip->status === 'Completed' ? 'success' : ($trip->status === 'In Progress' ? 'warning' : ($trip->status === 'Cancelled' ? 'danger' : 'info')) }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $fillRate = $trip->fill_rate ?? 0;
                                            $capacity = $trip->bus->capacity ?? 0;
                                            $soldTickets = $trip->sold_tickets ?? 0;
                                            $progressColor = $fillRate >= 80 ? 'success' : ($fillRate >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $progressColor }}" role="progressbar" 
                                                     style="width: {{ min($fillRate, 100) }}%" 
                                                     aria-valuenow="{{ $fillRate }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $fillRate }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $soldTickets }} / {{ $capacity }} places
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Aucun voyage prévu aujourd'hui.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Graphique du chiffre d'affaires -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Chiffre d'Affaires des Tickets (12 derniers mois)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Statistiques par trajet -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top 5 Trajets (30 derniers jours)</h5>
            </div>
            <div class="card-body">
                @if($routeStats->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Trajet</th>
                                    <th>Nombre de Voyages</th>
                                    <th>Distance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routeStats as $route)
                                <tr>
                                    <td>{{ $route->departure_city }} → {{ $route->arrival_city }}</td>
                                    <td>{{ $route->trips_count }}</td>
                                    <td>{{ $route->distance }} km</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Aucune statistique disponible.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('page-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');
        if (ctx) {
            const monthlyRevenue = @json($monthlyRevenue);
            const monthlyLabels = @json($monthlyLabels);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Chiffre d\'affaires (FCFA)',
                        data: monthlyRevenue,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'CA: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
