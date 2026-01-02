<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Ticket;
use App\Models\SeatSegment;
use App\Models\RouteStopPrice;
use App\Models\RouteStop;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Vérifie si un siège est disponible pour un segment donné
     */
    public function isSeatAvailable(Trip $trip, int $seatNumber, int $fromStopId, int $toStopId): bool
    {
        // Charger la relation route avec routeStops
        if (!$trip->relationLoaded('route')) {
            $trip->load('route.routeStops');
        }

        // Récupérer tous les segments occupés pour ce siège sur ce voyage
        // Exclure les segments des tickets "Terminé" ou "Annulé" pour libérer les sièges
        $occupiedSegments = SeatSegment::where('trip_id', $trip->id)
            ->where('seat_number', $seatNumber)
            ->whereHas('ticket', function ($query) {
                $query->whereNotIn('status', ['Terminé', 'Annulé']);
            })
            ->get();

        // Pour chaque segment occupé, vérifier s'il y a un conflit
        foreach ($occupiedSegments as $segment) {
            if ($this->segmentsOverlap($trip, $fromStopId, $toStopId, $segment->from_stop_id, $segment->to_stop_id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifie si deux segments se chevauchent
     */
    private function segmentsOverlap(Trip $trip, int $from1, int $to1, int $from2, int $to2): bool
    {
        // Récupérer l'ordre des arrêts depuis la route
        if (!$trip->route) {
            return false;
        }

        $routeStops = $trip->route->routeStops()->orderBy('order')->get();
        $stopOrders = [];
        
        foreach ($routeStops as $rs) {
            $stopOrders[$rs->stop_id] = $rs->order;
        }

        // Si les arrêts ne sont pas dans la route, pas de conflit
        if (!isset($stopOrders[$from1]) || !isset($stopOrders[$to1]) || 
            !isset($stopOrders[$from2]) || !isset($stopOrders[$to2])) {
            return false;
        }

        $from1Order = $stopOrders[$from1];
        $to1Order = $stopOrders[$to1];
        $from2Order = $stopOrders[$from2];
        $to2Order = $stopOrders[$to2];

        // Vérifier si les segments se chevauchent
        // Deux segments se chevauchent si l'un commence avant que l'autre ne se termine
        return ($from1Order < $to2Order && $from2Order < $to1Order);
    }

    /**
     * Trouve un siège disponible pour un segment donné
     */
    public function findAvailableSeat(Trip $trip, int $fromStopId, int $toStopId): ?int
    {
        $bus = $trip->bus;
        $capacity = $bus->capacity;

        // Essayer chaque siège
        for ($seat = 1; $seat <= $capacity; $seat++) {
            if ($this->isSeatAvailable($trip, $seat, $fromStopId, $toStopId)) {
                return $seat;
            }
        }

        return null;
    }

    /**
     * Calcule le prix entre deux arrêts avec détails
     * Les prix sont liés à la route, pas au voyage, donc ils sont réutilisés pour tous les voyages de cette route
     * 
     * @return array ['price' => float, 'missing_segments' => array, 'error' => string|null, 'route_id' => int]
     */
    public function calculatePriceWithDetails(Trip $trip, int $fromStopId, int $toStopId): array
    {
        $route = $trip->route;
        
        if (!$route) {
            return [
                'price' => 0.0,
                'missing_segments' => [],
                'error' => 'Route non trouvée pour ce voyage',
                'route_id' => null
            ];
        }
        
        // Chercher un tarif direct dans cet ordre de priorité :
        // 1. Prix global (sans route spécifique) - NOUVEAU : prix réutilisable
        // 2. Prix spécifique à cette route
        
        $priceRecord = RouteStopPrice::where('from_stop_id', $fromStopId)
            ->where('to_stop_id', $toStopId)
            ->where('is_active', true)
            ->where(function($query) use ($route) {
                $query->whereNull('route_id')  // Prix global
                      ->orWhere('route_id', $route->id); // Ou prix spécifique à cette route
            })
            ->orderByRaw('CASE WHEN route_id IS NULL THEN 0 ELSE 1 END') // Priorité aux prix globaux
            ->first();

        if ($priceRecord) {
            return [
                'price' => (float) $priceRecord->price,
                'missing_segments' => [],
                'error' => null,
                'route_id' => $route->id,
                'price_type' => $priceRecord->route_id ? 'specific' : 'global'
            ];
        }

        // Si pas de tarif direct, essayer de calculer le prix cumulé des segments
        $routeStops = $route->routeStops()->orderBy('order')->get();
        
        if ($routeStops->isEmpty()) {
            return [
                'price' => 0.0,
                'missing_segments' => [],
                'error' => 'Aucun arrêt trouvé pour cette route',
                'route_id' => $route->id
            ];
        }
        
        $stopOrders = [];
        $stopNames = [];
        
        foreach ($routeStops as $rs) {
            $stopOrders[$rs->stop_id] = $rs->order;
            $stopNames[$rs->stop_id] = $rs->stop->name ?? 'Arrêt ' . $rs->stop_id;
        }

        if (!isset($stopOrders[$fromStopId]) || !isset($stopOrders[$toStopId])) {
            return [
                'price' => 0.0,
                'missing_segments' => [],
                'error' => 'Les arrêts sélectionnés ne font pas partie de cette route',
                'route_id' => $route->id
            ];
        }

        $fromOrder = $stopOrders[$fromStopId];
        $toOrder = $stopOrders[$toStopId];

        if ($fromOrder >= $toOrder) {
            return [
                'price' => 0.0,
                'missing_segments' => [],
                'error' => 'L\'arrêt de départ doit être avant l\'arrêt d\'arrivée',
                'route_id' => $route->id
            ];
        }

        // Vérifier si tous les prix nécessaires existent
        $missingPrices = [];
        $totalPrice = 0.0;
        
        for ($i = $fromOrder; $i < $toOrder; $i++) {
            $currentStop = $routeStops->where('order', $i)->first();
            $nextStop = $routeStops->where('order', $i + 1)->first();

            if ($currentStop && $nextStop) {
                // Chercher prix global d'abord, puis prix spécifique à la route
                $segmentPrice = RouteStopPrice::where('from_stop_id', $currentStop->stop_id)
                    ->where('to_stop_id', $nextStop->stop_id)
                    ->where('is_active', true)
                    ->where(function($query) use ($route) {
                        $query->whereNull('route_id')
                              ->orWhere('route_id', $route->id);
                    })
                    ->orderByRaw('CASE WHEN route_id IS NULL THEN 0 ELSE 1 END')
                    ->value('price');

                if ($segmentPrice !== null) {
                    $totalPrice += (float) $segmentPrice;
                } else {
                    // Enregistrer les segments manquants avec les noms des arrêts
                    $missingPrices[] = [
                        'from_stop_id' => $currentStop->stop_id,
                        'to_stop_id' => $nextStop->stop_id,
                        'from_stop_name' => $stopNames[$currentStop->stop_id] ?? 'Arrêt ' . $currentStop->stop_id,
                        'to_stop_name' => $stopNames[$nextStop->stop_id] ?? 'Arrêt ' . $nextStop->stop_id,
                        'order' => $i . ' -> ' . ($i + 1)
                    ];
                }
            }
        }

        // Si des prix manquent
        if (!empty($missingPrices)) {
            $missingList = implode(', ', array_map(function($seg) {
                return $seg['from_stop_name'] . ' → ' . $seg['to_stop_name'];
            }, $missingPrices));
            
            return [
                'price' => 0.0,
                'missing_segments' => $missingPrices,
                'error' => 'Prix manquants pour les segments: ' . $missingList . '. Veuillez définir les tarifs dans "Configurations des tarifs".',
                'route_id' => $route->id
            ];
        }

        return [
            'price' => $totalPrice,
            'missing_segments' => [],
            'error' => null,
            'route_id' => $route->id
        ];
    }

    /**
     * Calcule le prix entre deux arrêts (méthode de compatibilité)
     */
    public function calculatePrice(Trip $trip, int $fromStopId, int $toStopId): float
    {
        $result = $this->calculatePriceWithDetails($trip, $fromStopId, $toStopId);
        return $result['price'];
    }

    /**
     * Crée un ticket avec gestion des segments
     */
    public function createTicket(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $trip = Trip::with('route.routeStops', 'bus')->findOrFail($data['trip_id']);
            
            // Vérifier la disponibilité
            $seatNumber = $data['seat_number'] ?? $this->findAvailableSeat(
                $trip,
                $data['from_stop_id'],
                $data['to_stop_id']
            );

            if (!$seatNumber) {
                throw new \Exception('Aucun siège disponible pour ce segment.');
            }

            // Vérifier que le siège est disponible
            if (!$this->isSeatAvailable($trip, $seatNumber, $data['from_stop_id'], $data['to_stop_id'])) {
                throw new \Exception('Le siège sélectionné n\'est pas disponible pour ce segment.');
            }

            // Calculer le prix si non fourni, sinon utiliser le prix fourni
            $price = isset($data['price']) && $data['price'] > 0 
                ? (float) $data['price'] 
                : $this->calculatePrice($trip, $data['from_stop_id'], $data['to_stop_id']);

            // Créer le ticket
            $ticket = Ticket::create([
                'trip_id' => $trip->id,
                'from_stop_id' => $data['from_stop_id'],
                'to_stop_id' => $data['to_stop_id'],
                'seat_number' => $seatNumber,
                'passenger_name' => $data['passenger_name'],
                'passenger_phone' => $data['passenger_phone'] ?? null,
                'passenger_id' => $data['passenger_id'] ?? null,
                'client_id' => $data['client_id'] ?? null,
                'price' => $price,
                'sold_by' => $data['sold_by'],
                'status' => 'En attente',
            ]);

            // Créer les segments pour ce ticket
            $this->createSegments($trip, $ticket, $seatNumber, $data['from_stop_id'], $data['to_stop_id']);

            return $ticket;
        });
    }

    /**
     * Crée les segments d'occupation pour un ticket
     */
    private function createSegments(Trip $trip, Ticket $ticket, int $seatNumber, int $fromStopId, int $toStopId): void
    {
        // Récupérer tous les arrêts de la route dans l'ordre
        $routeStops = $trip->route->routeStops()->orderBy('order')->get();
        
        $fromOrder = null;
        $toOrder = null;

        foreach ($routeStops as $rs) {
            if ($rs->stop_id == $fromStopId) {
                $fromOrder = $rs->order;
            }
            if ($rs->stop_id == $toStopId) {
                $toOrder = $rs->order;
            }
        }

        if ($fromOrder === null || $toOrder === null || $fromOrder >= $toOrder) {
            throw new \Exception('Arrêts invalides pour ce trajet.');
        }

        // Créer un segment pour chaque paire d'arrêts consécutifs entre from et to
        for ($i = $fromOrder; $i < $toOrder; $i++) {
            $currentStop = $routeStops->where('order', $i)->first();
            $nextStop = $routeStops->where('order', $i + 1)->first();

            if ($currentStop && $nextStop) {
                SeatSegment::create([
                    'trip_id' => $trip->id,
                    'seat_number' => $seatNumber,
                    'from_stop_id' => $currentStop->stop_id,
                    'to_stop_id' => $nextStop->stop_id,
                    'ticket_id' => $ticket->id,
                ]);
            }
        }
    }

    /**
     * Récupère les sièges disponibles pour un segment
     */
    public function getAvailableSeats(Trip $trip, int $fromStopId, int $toStopId): array
    {
        $bus = $trip->bus;
        $capacity = $bus->capacity;
        $availableSeats = [];

        for ($seat = 1; $seat <= $capacity; $seat++) {
            if ($this->isSeatAvailable($trip, $seat, $fromStopId, $toStopId)) {
                $availableSeats[] = $seat;
            }
        }

        return $availableSeats;
    }
}

