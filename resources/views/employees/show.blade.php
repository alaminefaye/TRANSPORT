@extends('layouts.app')

@section('title', 'Détails de l\'Employé')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Photo</h5>
            </div>
            <div class="card-body text-center">
                @if($employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="Photo" class="img-fluid rounded" style="max-width: 200px;">
                @else
                    <div class="bg-light rounded p-5">
                        <i class="bx bx-user" style="font-size: 100px; color: #ccc;"></i>
                        <p class="text-muted mt-2">Aucune photo</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations de l'Employé</h5>
                <div>
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-primary">Modifier</a>
                    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-secondary">Retour</a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Matricule:</dt>
                    <dd class="col-sm-8">{{ $employee->matricule ?? '-' }}</dd>
                    
                    <dt class="col-sm-4">Nom complet:</dt>
                    <dd class="col-sm-8">{{ $employee->name }}</dd>
                    
                    <dt class="col-sm-4">Email:</dt>
                    <dd class="col-sm-8">{{ $employee->email ?? '-' }}</dd>
                    
                    <dt class="col-sm-4">Téléphone:</dt>
                    <dd class="col-sm-8">{{ $employee->phone ?? '-' }}</dd>
                    
                    <dt class="col-sm-4">Adresse:</dt>
                    <dd class="col-sm-8">{{ $employee->address ?? '-' }}</dd>
                    
                    <dt class="col-sm-4">Poste:</dt>
                    <dd class="col-sm-8">{{ $employee->position ?? '-' }}</dd>
                    
                    <dt class="col-sm-4">Date d'embauche:</dt>
                    <dd class="col-sm-8">{{ $employee->hire_date ? $employee->hire_date->format('d/m/Y') : '-' }}</dd>
                    
                    <dt class="col-sm-4">Statut:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $employee->status === 'Actif' ? 'success' : ($employee->status === 'Inactif' ? 'secondary' : ($employee->status === 'Congé' ? 'warning' : 'danger')) }}">
                            {{ $employee->status }}
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Salaire:</dt>
                    <dd class="col-sm-8">{{ $employee->salary ? number_format($employee->salary, 0, ',', ' ') . ' FCFA' : '-' }}</dd>
                    
                    <dt class="col-sm-4">Date de création:</dt>
                    <dd class="col-sm-8">{{ $employee->created_at->format('d/m/Y H:i') }}</dd>
                    
                    <dt class="col-sm-4">Dernière modification:</dt>
                    <dd class="col-sm-8">{{ $employee->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

