@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Détails de l'utilisateur</h5>
        <div>
            @can('edit-users')
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="bx bx-edit me-1"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Retour
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted">Informations personnelles</h6>
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Nom :</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email :</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Téléphone :</th>
                        <td>{{ $user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Rôle (Legacy) :</th>
                        <td>
                            @if($user->role)
                                <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Créé le :</th>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h6 class="text-muted">Rôles et permissions</h6>
                <div class="mb-3">
                    <strong>Rôles :</strong>
                    <div class="mt-2">
                        @forelse($user->roles as $role)
                            <span class="badge bg-primary me-1 mb-1">{{ $role->name }}</span>
                        @empty
                            <span class="text-muted">Aucun rôle assigné</span>
                        @endforelse
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Permissions via rôles :</strong>
                    <div class="mt-2" style="max-height: 300px; overflow-y: auto;">
                        @php
                            $allPermissions = collect();
                            foreach($user->roles as $role) {
                                $allPermissions = $allPermissions->merge($role->permissions);
                            }
                            $allPermissions = $allPermissions->unique('id');
                        @endphp
                        @forelse($allPermissions as $permission)
                            <span class="badge bg-info me-1 mb-1">{{ $permission->name }}</span>
                        @empty
                            <span class="text-muted">Aucune permission</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @if($user->tickets()->exists() || $user->soldTickets()->exists() || $user->trips()->exists())
        <div class="row">
            <div class="col-12">
                <h6 class="text-muted">Statistiques</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h4 class="mb-0">{{ $user->tickets()->count() }}</h4>
                                <small class="text-muted">Tickets achetés</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h4 class="mb-0">{{ $user->soldTickets()->count() }}</h4>
                                <small class="text-muted">Tickets vendus</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h4 class="mb-0">{{ $user->trips()->count() }}</h4>
                                <small class="text-muted">Voyages effectués</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection


