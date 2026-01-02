@extends('layouts.app')

@section('title', 'Détails de l\'Arrêt')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Informations de l'Arrêt</h5>
        <a href="{{ route('stops.edit', $stop) }}" class="btn btn-sm btn-primary">Modifier</a>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th width="200">Nom:</th>
                <td>{{ $stop->name }}</td>
            </tr>
            <tr>
                <th>Ville:</th>
                <td>{{ $stop->city }}</td>
            </tr>
            <tr>
                <th>Type:</th>
                <td>
                    <span class="badge bg-{{ $stop->type === 'Gare principale' ? 'primary' : 'secondary' }}">
                        {{ $stop->type }}
                    </span>
                </td>
            </tr>
            @if($stop->address)
            <tr>
                <th>Adresse:</th>
                <td>{{ $stop->address }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('stops.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection

