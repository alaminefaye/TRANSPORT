@extends('layouts.app')

@section('title', 'Détails du rôle')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Détails du rôle : {{ $role->name }}</h5>
        <div>
            @can('edit-roles')
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                <i class="bx bx-edit me-1"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Retour
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted">Informations générales</h6>
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Nom du rôle :</th>
                        <td>
                            {{ $role->name }}
                            @if(in_array($role->name, ['Super Admin', 'Administrateur']))
                                <span class="badge bg-danger ms-2">Système</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Créé le :</th>
                        <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Modifié le :</th>
                        <td>{{ $role->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Permissions :</th>
                        <td><span class="badge bg-info">{{ $role->permissions->count() }} permissions</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h6 class="text-muted mb-3">Permissions associées</h6>
                @php
                    $groupedPermissions = $role->permissions->groupBy(function ($permission) {
                        return explode('-', $permission->name)[1] ?? 'autres';
                    })->sortKeys();
                @endphp
                
                <div class="row">
                    @forelse($groupedPermissions as $category => $permissions)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <strong class="text-capitalize">{{ ucfirst($category) }}</strong>
                            </div>
                            <div class="card-body">
                                @foreach($permissions as $permission)
                                <span class="badge bg-primary me-1 mb-1">{{ $permission->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted">Aucune permission associée à ce rôle.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


