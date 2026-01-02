@extends('layouts.app')

@section('title', 'Gestion des rôles')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des rôles</h5>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Nouveau rôle
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom du rôle</th>
                        <th>Nombre de permissions</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>
                            <strong>{{ $role->name }}</strong>
                            @if(in_array($role->name, ['Super Admin', 'Administrateur']))
                                <span class="badge bg-danger ms-2">Système</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                        </td>
                        <td>{{ $role->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @if(!in_array($role->name, ['Super Admin', 'Administrateur']))
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Aucun rôle trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection

