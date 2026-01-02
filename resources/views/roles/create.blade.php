@extends('layouts.app')

@section('title', 'Créer un rôle')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Créer un nouveau rôle</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Permissions</label>
                <div class="row">
                    @foreach($permissions as $category => $categoryPermissions)
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <strong class="text-capitalize">{{ ucfirst($category) }}</strong>
                            </div>
                            <div class="card-body">
                                @foreach($categoryPermissions as $permission)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" 
                                           value="{{ $permission->id }}" id="permission{{ $permission->id }}"
                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('permissions')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i> Créer le rôle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

