<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Trip;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Client;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index(Request $request)
    {
        $query = Ticket::with(['trip.route', 'fromStop', 'toStop', 'passenger', 'soldBy']);

        // Filtre par numéro de ticket
        if ($request->has('ticket_number') && !empty(trim($request->ticket_number))) {
            $query->where('ticket_number', 'like', '%' . trim($request->ticket_number) . '%');
        }

        // Filtre par nom du passager
        if ($request->has('passenger_name') && !empty(trim($request->passenger_name))) {
            $query->where('passenger_name', 'like', '%' . trim($request->passenger_name) . '%');
        }

        // Filtre par téléphone du passager
        if ($request->has('passenger_phone') && !empty(trim($request->passenger_phone))) {
            $query->where('passenger_phone', 'like', '%' . trim($request->passenger_phone) . '%');
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par route (trajet)
        if ($request->filled('route_id')) {
            $query->whereHas('trip', function ($q) use ($request) {
                $q->where('route_id', $request->route_id);
            });
        }

        // Filtre par date de création
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtre par arrêt de départ
        if ($request->filled('from_stop_id')) {
            $query->where('from_stop_id', $request->from_stop_id);
        }

        // Filtre par arrêt d'arrivée
        if ($request->filled('to_stop_id')) {
            $query->where('to_stop_id', $request->to_stop_id);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        // Charger les données pour les filtres
        $routes = Route::where('is_active', true)->get();
        $stops = Stop::all();
        $statuses = ['En attente', 'Embarqué', 'Terminé', 'Annulé'];

        return view('tickets.index', compact('tickets', 'routes', 'stops', 'statuses'));
    }

    public function create()
    {
        $trips = Trip::with(['route.routeStops.stop', 'bus'])
            ->where('status', 'Scheduled')
            ->where('departure_time', '>', now())
            ->latest('departure_time')
            ->get();
        
        return view('tickets.create', compact('trips'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'from_stop_id' => 'required|exists:stops,id',
            'to_stop_id' => 'required|exists:stops,id|different:from_stop_id',
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:20',
            'passenger_id' => 'nullable|exists:users,id',
            'seat_number' => 'nullable|integer|min:1',
            'selected_seats' => 'nullable|string', // Nouveau champ pour les sièges multiples
            'price' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:Espèce,Carte bancaire,Mobile Money,Virement',
        ]);

        try {
            DB::beginTransaction();

            // Rechercher ou créer le client basé sur le numéro de téléphone
            $phone = trim($validated['passenger_phone']);
            $client = Client::where('phone', $phone)->first();
            
            if (!$client) {
                // Créer un nouveau client si il n'existe pas
                $client = Client::create([
                    'name' => $validated['passenger_name'],
                    'phone' => $phone,
                    'email' => null, // Peut être ajouté plus tard si nécessaire
                ]);
            } else {
                // Mettre à jour le nom si nécessaire (au cas où le nom a changé)
                if ($client->name !== $validated['passenger_name']) {
                    $client->update(['name' => $validated['passenger_name']]);
                }
            }

            // Déterminer les sièges à utiliser
            $seatsToUse = [];
            if (!empty($validated['selected_seats'])) {
                // Si des sièges sont sélectionnés via la grille
                $seatsArray = array_map('trim', explode(',', $validated['selected_seats']));
                $seatsToUse = array_filter(array_map('intval', $seatsArray), function($seat) {
                    return $seat > 0;
                });
            } elseif (!empty($validated['seat_number'])) {
                // Sinon, utiliser le siège unique (ancien système)
                $seatsToUse = [(int)$validated['seat_number']];
            }

            // Si aucun siège n'est spécifié, le système choisira automatiquement
            if (empty($seatsToUse)) {
                $seatsToUse = [null]; // Un seul ticket avec siège automatique
            }

            $createdTickets = [];
            $totalAmount = 0;
            
            // Calculer le prix unitaire une seule fois
            $unitPrice = $validated['price'] ?? null;
            if ($unitPrice === null || $unitPrice <= 0) {
                $trip = Trip::with('route')->findOrFail($validated['trip_id']);
                $unitPrice = $this->ticketService->calculatePrice(
                    $trip,
                    $validated['from_stop_id'],
                    $validated['to_stop_id']
                );
            }

            // Créer un ticket pour chaque siège sélectionné
            foreach ($seatsToUse as $seatNumber) {
                $ticket = $this->ticketService->createTicket([
                    'trip_id' => $validated['trip_id'],
                    'from_stop_id' => $validated['from_stop_id'],
                    'to_stop_id' => $validated['to_stop_id'],
                    'passenger_name' => $validated['passenger_name'],
                    'passenger_phone' => $phone,
                    'passenger_id' => $validated['passenger_id'] ?? null,
                    'client_id' => $client->id,
                    'seat_number' => $seatNumber,
                    'price' => $unitPrice, // Utiliser le prix unitaire calculé
                    'sold_by' => Auth::id(),
                ]);

                $createdTickets[] = $ticket;
                $totalAmount += $ticket->price;

                // Créer le paiement pour ce ticket
                Payment::create([
                    'ticket_id' => $ticket->id,
                    'amount' => $ticket->price,
                    'payment_method' => $validated['payment_method'],
                    'status' => 'Completed',
                    'received_by' => Auth::id(),
                ]);
            }

            DB::commit();

            // Rediriger vers le premier ticket créé ou la liste si plusieurs
            if (count($createdTickets) === 1) {
                return redirect()->route('tickets.show', $createdTickets[0])
                    ->with('success', 'Ticket créé avec succès.');
            } else {
                return redirect()->route('tickets.index')
                    ->with('success', count($createdTickets) . ' tickets créés avec succès. Montant total: ' . number_format($totalAmount, 0, ',', ' ') . ' FCFA');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la création du ticket: ' . $e->getMessage());
        }
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['trip.route.stops', 'trip.bus', 'fromStop', 'toStop', 'passenger', 'soldBy', 'payment']);
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Recherche de ticket par QR code
     */
    public function searchByQrCode(Request $request)
    {
        if ($request->isMethod('get') && !$request->has('qr_code')) {
            return view('tickets.search-qr');
        }

        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $ticket = Ticket::where('qr_code', $request->qr_code)
            ->with(['trip.route', 'fromStop', 'toStop'])
            ->first();

        if (!$ticket) {
            return back()->with('error', 'Ticket non trouvé.');
        }

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Embarquement d'un passager
     */
    public function board(Request $request, Ticket $ticket)
    {
        if ($ticket->status !== 'En attente') {
            return back()->with('error', 'Ce ticket ne peut pas être embarqué.');
        }

        $ticket->update([
            'status' => 'Embarqué',
            'boarding_time' => now(),
        ]);

        return back()->with('success', 'Passager embarqué avec succès.');
    }

    /**
     * Débarquement d'un passager
     */
    public function disembark(Request $request, Ticket $ticket)
    {
        if ($ticket->status !== 'Embarqué') {
            return back()->with('error', 'Ce ticket n\'est pas en état d\'embarquement.');
        }

        DB::transaction(function () use ($ticket) {
            $ticket->update([
                'status' => 'Terminé',
                'disembarkation_time' => now(),
            ]);

            // Libérer les segments
            $ticket->seatSegments()->delete();
        });

        return back()->with('success', 'Passager débarqué avec succès.');
    }

    /**
     * Annulation d'un ticket
     */
    public function cancel(Ticket $ticket)
    {
        if ($ticket->status === 'Terminé') {
            return back()->with('error', 'Impossible d\'annuler un ticket terminé.');
        }

        DB::transaction(function () use ($ticket) {
            $ticket->update(['status' => 'Annulé']);
            
            // Libérer les segments
            $ticket->seatSegments()->delete();

            // Rembourser le paiement si nécessaire
            if ($ticket->payment) {
                $ticket->payment->update(['status' => 'Refunded']);
            }
        });

        return back()->with('success', 'Ticket annulé avec succès.');
    }

    /**
     * Affiche les tickets récupérables (annulés)
     */
    public function retrieve(Request $request)
    {
        $query = Ticket::with(['trip.route', 'fromStop', 'toStop', 'passenger', 'soldBy'])
            ->where('status', 'Annulé');

        // Filtre par numéro de ticket
        if ($request->has('ticket_number') && !empty(trim($request->ticket_number))) {
            $query->where('ticket_number', 'like', '%' . trim($request->ticket_number) . '%');
        }

        // Filtre par nom du passager
        if ($request->has('passenger_name') && !empty(trim($request->passenger_name))) {
            $query->where('passenger_name', 'like', '%' . trim($request->passenger_name) . '%');
        }

        // Filtre par téléphone du passager
        if ($request->has('passenger_phone') && !empty(trim($request->passenger_phone))) {
            $query->where('passenger_phone', 'like', '%' . trim($request->passenger_phone) . '%');
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        // Charger les données pour les filtres
        $routes = Route::where('is_active', true)->get();
        $stops = Stop::all();
        $statuses = ['En attente', 'Embarqué', 'Terminé', 'Annulé'];

        return view('tickets.retrieve', compact('tickets', 'routes', 'stops', 'statuses'));
    }

    /**
     * Récupère les sièges disponibles pour un segment
     */
    public function getAvailableSeats(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'from_stop_id' => 'required|exists:stops,id',
            'to_stop_id' => 'required|exists:stops,id',
        ]);

        $trip = Trip::with('route.routeStops', 'bus')->findOrFail($validated['trip_id']);
        $availableSeats = $this->ticketService->getAvailableSeats(
            $trip,
            $validated['from_stop_id'],
            $validated['to_stop_id']
        );

        // Calculer les sièges occupés
        $capacity = $trip->bus->capacity ?? 0;
        $allSeats = range(1, $capacity);
        $occupiedSeats = array_diff($allSeats, $availableSeats);

        return response()->json([
            'available_seats' => $availableSeats,
            'occupied_seats' => array_values($occupiedSeats), // Réindexer le tableau
            'total_capacity' => $capacity
        ]);
    }

    /**
     * Calcule le prix entre deux arrêts
     * IMPORTANT: Les prix sont liés à la route (trajet), pas au voyage.
     * Les prix définis pour une route sont automatiquement utilisés pour tous les voyages de cette route,
     * peu importe la date du voyage.
     */
    public function calculatePrice(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'from_stop_id' => 'required|exists:stops,id',
            'to_stop_id' => 'required|exists:stops,id',
        ]);

        try {
            $trip = Trip::with('route')->findOrFail($validated['trip_id']);
            
            if (!$trip->route) {
                return response()->json([
                    'price' => 0, 
                    'error' => 'Route non trouvée pour ce voyage. Vérifiez que le voyage est bien associé à un trajet.',
                    'missing_segments' => [],
                    'route_id' => null
                ], 400);
            }
            
            $result = $this->ticketService->calculatePriceWithDetails(
                $trip,
                (int) $validated['from_stop_id'],
                (int) $validated['to_stop_id']
            );

            if ($result['price'] == 0 && $result['error']) {
                return response()->json([
                    'price' => 0,
                    'error' => $result['error'],
                    'missing_segments' => $result['missing_segments'],
                    'route_id' => $result['route_id']
                ], 400);
            }

            return response()->json([
                'price' => (float) $result['price'],
                'error' => null,
                'missing_segments' => [],
                'route_id' => $result['route_id']
            ]);
        } catch (\Exception $e) {
            \Log::error('Error calculating price', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'trip_id' => $validated['trip_id'] ?? null,
                'from_stop_id' => $validated['from_stop_id'] ?? null,
                'to_stop_id' => $validated['to_stop_id'] ?? null,
            ]);
            
            return response()->json([
                'price' => 0, 
                'error' => 'Erreur lors du calcul du prix: ' . $e->getMessage(),
                'missing_segments' => [],
                'route_id' => null
            ], 500);
        }
    }
}
