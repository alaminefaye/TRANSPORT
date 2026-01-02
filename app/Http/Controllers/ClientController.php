<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::withCount(['tickets', 'sentParcels', 'receivedParcels']);

        // Filtre par nom
        if ($request->has('name') && !empty(trim($request->name))) {
            $query->where('name', 'like', '%' . trim($request->name) . '%');
        }

        // Filtre par téléphone
        if ($request->has('phone') && !empty(trim($request->phone))) {
            $query->where('phone', 'like', '%' . trim($request->phone) . '%');
        }

        // Filtre par email
        if ($request->has('email') && !empty(trim($request->email))) {
            $query->where('email', 'like', '%' . trim($request->email) . '%');
        }

        $clients = $query->latest()->paginate(10)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    /**
     * Rechercher un client par numéro de téléphone
     */
    public function searchByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // Nettoyer le numéro de téléphone (retirer espaces, tirets, etc.)
        $phone = trim($request->phone);
        $cleanPhone = preg_replace('/[\s\-\(\)\.]/', '', $phone);
        
        // Rechercher d'abord avec le numéro exact
        $client = Client::where('phone', $phone)->first();
        
        // Si pas trouvé, essayer avec le numéro nettoyé
        if (!$client && $cleanPhone !== $phone) {
            $client = Client::where(function($query) use ($cleanPhone) {
                $query->where('phone', $cleanPhone)
                      ->orWhere('phone', 'like', '%' . $cleanPhone . '%');
            })->first();
        }
        
        // Si toujours pas trouvé, essayer une recherche plus flexible
        if (!$client) {
            $client = Client::where('phone', 'like', '%' . $cleanPhone . '%')->first();
        }

        if (!$client) {
            return response()->json([
                'found' => false,
                'message' => 'Aucun client trouvé avec ce numéro de téléphone'
            ], 404);
        }

        return response()->json([
            'found' => true,
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'email' => $client->email,
                'loyalty_points' => $client->loyalty_points ?? 0,
                'can_use_free_ticket' => ($client->loyalty_points ?? 0) >= 10,
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client, Request $request)
    {
        // Paginer les tickets (5 par page)
        $ticketsPage = $request->get('tickets_page', 1);
        $tickets = $client->tickets()
            ->with(['trip.route', 'fromStop', 'toStop'])
            ->latest()
            ->paginate(5, ['*'], 'tickets_page')
            ->withQueryString();

        // Paginer les colis envoyés (5 par page)
        $sentParcelsPage = $request->get('sent_page', 1);
        $sentParcels = $client->sentParcels()
            ->with(['destination', 'receptionAgency'])
            ->latest()
            ->paginate(5, ['*'], 'sent_page')
            ->withQueryString();

        // Paginer les colis reçus (5 par page)
        $receivedParcelsPage = $request->get('received_page', 1);
        $receivedParcels = $client->receivedParcels()
            ->with(['destination', 'receptionAgency'])
            ->latest()
            ->paginate(5, ['*'], 'received_page')
            ->withQueryString();

        return view('clients.show', compact('client', 'tickets', 'sentParcels', 'receivedParcels'));
    }
}
