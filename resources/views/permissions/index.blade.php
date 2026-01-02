@extends('layouts.app')

@section('title', 'Liste des permissions')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des permissions</h5>
        <small class="text-muted">Toutes les permissions disponibles dans le système</small>
    </div>
    <div class="card-body">
        @forelse($permissions as $category => $categoryPermissions)
        <div class="mb-4">
            <h6 class="text-primary mb-3">
                <i class="bx bx-key me-2"></i>{{ $category }} 
                <span class="badge bg-secondary">{{ $categoryPermissions->count() }} permissions</span>
            </h6>
            <div class="row">
                @foreach($categoryPermissions as $permission)
                <div class="col-md-3 mb-2">
                    <a href="{{ route('permissions.show', $permission) }}" class="text-decoration-none">
                        <div class="card border">
                            <div class="card-body p-2">
                                <small class="d-flex align-items-center">
                                    <i class="bx bx-shield me-2"></i>
                                    {{ $permission->name }}
                                </small>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        <hr>
        @empty
        <p class="text-center text-muted">Aucune permission trouvée</p>
        @endforelse
    </div>
</div>
@endsection


