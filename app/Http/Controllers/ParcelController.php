<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\Destination;
use App\Models\ReceptionAgency;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ParcelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Parcel::with(['createdBy', 'destination', 'receptionAgency'])
            ->where('status', '!=', 'Récupéré');

        // Filtre par numéro de courrier
        if ($request->has('mail_number') && !empty(trim($request->mail_number))) {
            $query->where('mail_number', 'like', '%' . trim($request->mail_number) . '%');
        }

        // Filtre par nom de l'expéditeur
        if ($request->has('sender_name') && !empty(trim($request->sender_name))) {
            $query->where('sender_name', 'like', '%' . trim($request->sender_name) . '%');
        }

        // Filtre par nom du bénéficiaire
        if ($request->has('recipient_name') && !empty(trim($request->recipient_name))) {
            $query->where('recipient_name', 'like', '%' . trim($request->recipient_name) . '%');
        }

        // Filtre par type de colis
        if ($request->filled('parcel_type')) {
            $query->where('parcel_type', $request->parcel_type);
        }

        // Filtre par destination
        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par agence de réception
        if ($request->filled('reception_agency_id')) {
            $query->where('reception_agency_id', $request->reception_agency_id);
        }

        $parcels = $query->latest()->paginate(10)->withQueryString();

        // Charger les données pour les filtres
        $statuses = ['En attente', 'En transit', 'Arrivé'];
        $destinations = Destination::orderBy('name')->get();
        $receptionAgencies = ReceptionAgency::orderBy('name')->get();

        return view('parcels.index', compact('parcels', 'statuses', 'destinations', 'receptionAgencies'));
    }

    /**
     * Liste des colis récupérés
     */
    public function retrieved(Request $request)
    {
        $query = Parcel::with(['createdBy', 'destination', 'receptionAgency', 'retrievedByUser'])
            ->where('status', 'Récupéré');

        // Filtre par numéro de courrier
        if ($request->has('mail_number') && !empty(trim($request->mail_number))) {
            $query->where('mail_number', 'like', '%' . trim($request->mail_number) . '%');
        }

        // Filtre par nom du bénéficiaire
        if ($request->has('recipient_name') && !empty(trim($request->recipient_name))) {
            $query->where('recipient_name', 'like', '%' . trim($request->recipient_name) . '%');
        }

        // Filtre par nom de la personne qui a récupéré
        if ($request->has('retrieved_by_name') && !empty(trim($request->retrieved_by_name))) {
            $query->where('retrieved_by_name', 'like', '%' . trim($request->retrieved_by_name) . '%');
        }

        // Filtre par date de récupération (de)
        if ($request->filled('date_from')) {
            $query->whereDate('retrieved_at', '>=', $request->date_from);
        }

        // Filtre par date de récupération (à)
        if ($request->filled('date_to')) {
            $query->whereDate('retrieved_at', '<=', $request->date_to);
        }

        $parcels = $query->latest('retrieved_at')->paginate(10)->withQueryString();

        return view('parcels.retrieved', compact('parcels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        $receptionAgencies = ReceptionAgency::orderBy('name')->get();
        return view('parcels.create', compact('destinations', 'receptionAgencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'parcel_type' => 'required|in:Pli/enveloppe,Carton,Paquet,Sac,Sachet,Colis,Déménagement,Déménagement complet,Bazart,Salon complet',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'parcel_value' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:20480', // 20 MB = 20480 KB
            'destination_id' => 'required|exists:destinations,id',
            'reception_agency_id' => 'required|exists:reception_agencies,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'En attente';

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('parcels/photos', 'public');
            $validated['photo'] = $photoPath;
        }

        DB::beginTransaction();
        try {
            // Rechercher ou créer le client expéditeur basé sur le numéro de téléphone
            $senderPhone = trim($validated['sender_phone']);
            $senderClient = Client::where('phone', $senderPhone)->first();
            
            if (!$senderClient) {
                // Créer un nouveau client expéditeur si il n'existe pas
                $senderClient = Client::create([
                    'name' => $validated['sender_name'],
                    'phone' => $senderPhone,
                    'email' => null,
                ]);
            } else {
                // Mettre à jour le nom si nécessaire
                if ($senderClient->name !== $validated['sender_name']) {
                    $senderClient->update(['name' => $validated['sender_name']]);
                }
            }

            // Rechercher ou créer le client bénéficiaire basé sur le numéro de téléphone
            $recipientPhone = trim($validated['recipient_phone']);
            $recipientClient = Client::where('phone', $recipientPhone)->first();
            
            if (!$recipientClient) {
                // Créer un nouveau client bénéficiaire si il n'existe pas
                $recipientClient = Client::create([
                    'name' => $validated['recipient_name'],
                    'phone' => $recipientPhone,
                    'email' => null,
                ]);
            } else {
                // Mettre à jour le nom si nécessaire
                if ($recipientClient->name !== $validated['recipient_name']) {
                    $recipientClient->update(['name' => $validated['recipient_name']]);
                }
            }

            // Ajouter les IDs des clients au tableau validé
            $validated['sender_client_id'] = $senderClient->id;
            $validated['recipient_client_id'] = $recipientClient->id;

            Parcel::create($validated);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la création du colis: ' . $e->getMessage());
        }

        return redirect()->route('parcels.index')
            ->with('success', 'Colis créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Parcel $parcel)
    {
        $parcel->load(['createdBy', 'destination', 'receptionAgency', 'retrievedByUser', 'senderClient', 'recipientClient']);
        return view('parcels.show', compact('parcel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parcel $parcel)
    {
        $destinations = Destination::orderBy('name')->get();
        $receptionAgencies = ReceptionAgency::orderBy('name')->get();
        return view('parcels.edit', compact('parcel', 'destinations', 'receptionAgencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Parcel $parcel)
    {
        $validated = $request->validate([
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'parcel_type' => 'required|in:Pli/enveloppe,Carton,Paquet,Sac,Sachet,Colis,Déménagement,Déménagement complet,Bazart,Salon complet',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'parcel_value' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:20480', // 20 MB = 20480 KB
            'destination_id' => 'required|exists:destinations,id',
            'reception_agency_id' => 'required|exists:reception_agencies,id',
            'status' => 'required|in:En attente,En transit,Arrivé,Récupéré',
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($parcel->photo && Storage::disk('public')->exists($parcel->photo)) {
                Storage::disk('public')->delete($parcel->photo);
            }
            $photoPath = $request->file('photo')->store('parcels/photos', 'public');
            $validated['photo'] = $photoPath;
        } else {
            // Garder l'ancienne photo si aucune nouvelle n'est uploadée
            unset($validated['photo']);
        }

        DB::beginTransaction();
        try {
            // Rechercher ou créer le client expéditeur basé sur le numéro de téléphone
            $senderPhone = trim($validated['sender_phone']);
            $senderClient = Client::where('phone', $senderPhone)->first();
            
            if (!$senderClient) {
                // Créer un nouveau client expéditeur si il n'existe pas
                $senderClient = Client::create([
                    'name' => $validated['sender_name'],
                    'phone' => $senderPhone,
                    'email' => null,
                ]);
            } else {
                // Mettre à jour le nom si nécessaire
                if ($senderClient->name !== $validated['sender_name']) {
                    $senderClient->update(['name' => $validated['sender_name']]);
                }
            }

            // Rechercher ou créer le client bénéficiaire basé sur le numéro de téléphone
            $recipientPhone = trim($validated['recipient_phone']);
            $recipientClient = Client::where('phone', $recipientPhone)->first();
            
            if (!$recipientClient) {
                // Créer un nouveau client bénéficiaire si il n'existe pas
                $recipientClient = Client::create([
                    'name' => $validated['recipient_name'],
                    'phone' => $recipientPhone,
                    'email' => null,
                ]);
            } else {
                // Mettre à jour le nom si nécessaire
                if ($recipientClient->name !== $validated['recipient_name']) {
                    $recipientClient->update(['name' => $validated['recipient_name']]);
                }
            }

            // Ajouter les IDs des clients au tableau validé
            $validated['sender_client_id'] = $senderClient->id;
            $validated['recipient_client_id'] = $recipientClient->id;

            $parcel->update($validated);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la modification du colis: ' . $e->getMessage());
        }

        return redirect()->route('parcels.index')
            ->with('success', 'Colis modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parcel $parcel)
    {
        // Supprimer la photo si elle existe
        if ($parcel->photo && Storage::disk('public')->exists($parcel->photo)) {
            Storage::disk('public')->delete($parcel->photo);
        }

        $parcel->delete();

        return redirect()->route('parcels.index')
            ->with('success', 'Colis supprimé avec succès.');
    }

    /**
     * Marquer un colis comme récupéré
     */
    public function markAsRetrieved(Request $request, Parcel $parcel)
    {
        $validated = $request->validate([
            'retrieved_by_name' => 'required|string|max:255',
            'retrieved_by_phone' => 'nullable|string|max:20',
            'retrieved_by_cni' => 'nullable|string|max:255',
            'signature' => 'nullable|string', // Base64 signature
        ]);

        $parcel->update([
            'status' => 'Récupéré',
            'retrieved_at' => now(),
            'retrieved_by_name' => $validated['retrieved_by_name'],
            'retrieved_by_phone' => $validated['retrieved_by_phone'] ?? null,
            'retrieved_by_cni' => $validated['retrieved_by_cni'] ?? null,
            'retrieved_by_user_id' => Auth::id(),
            'signature' => $validated['signature'] ?? null,
        ]);

        return redirect()->route('parcels.retrieved')
            ->with('success', 'Colis marqué comme récupéré avec succès.');
    }
}
