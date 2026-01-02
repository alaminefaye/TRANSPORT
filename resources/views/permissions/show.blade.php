@extends('layouts.app')

@section('title', 'Détails de la permission')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Détails de la permission</h5>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Retour
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted">Informations générales</h6>
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Nom :</th>
                        <td><span class="badge bg-primary">{{ $permission->name }}</span></td>
                    </tr>
                    <tr>
                        <th>Créée le :</th>
                        <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Modifiée le :</th>
                        <td>{{ $permission->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h6 class="text-muted mb-3">Rôles ayant cette permission</h6>
                @forelse($permission->roles as $role)
                    <a href="{{ route('roles.show', $role) }}" class="badge bg-info me-2 mb-2 text-decoration-none">
                        {{ $role->name }}
                    </a>
                @empty
                    <p class="text-muted">Aucun rôle n'a cette permission.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection


