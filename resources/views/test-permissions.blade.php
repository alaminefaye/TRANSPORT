<!DOCTYPE html>
<html>
<head>
    <title>Test Permissions</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 Test des Permissions</h1>
    
    @auth
        <div class="info">
            <h2>✅ Utilisateur connecté</h2>
            <p><strong>Nom:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p><strong>Rôle (legacy):</strong> {{ Auth::user()->role ?? 'aucun' }}</p>
        </div>

        <div class="info">
            <h2>Rôles Spatie</h2>
            @if(Auth::user()->roles->count() > 0)
                <ul>
                    @foreach(Auth::user()->roles as $role)
                        <li class="success">✅ {{ $role->name }}</li>
                    @endforeach
                </ul>
            @else
                <p class="error">❌ Aucun rôle Spatie assigné</p>
            @endif
        </div>

        <div class="info">
            <h2>Permissions pour le menu</h2>
            <ul>
                @can('view-users')
                    <li class="success">✅ view-users</li>
                @else
                    <li class="error">❌ view-users</li>
                @endcan

                @can('view-roles')
                    <li class="success">✅ view-roles</li>
                @else
                    <li class="error">❌ view-roles</li>
                @endcan

                @can('view-permissions')
                    <li class="success">✅ view-permissions</li>
                @else
                    <li class="error">❌ view-permissions</li>
                @endcan
            </ul>
        </div>

        <div class="info">
            <h2>Test @canany</h2>
            @canany(['view-users', 'view-roles', 'view-permissions'])
                <p class="success">✅ @canany fonctionne - Le menu DEVRAIT s'afficher</p>
            @else
                <p class="error">❌ @canany ne fonctionne pas - Le menu ne s'affichera pas</p>
            @endcanany
        </div>

        <div class="info">
            <h2>Toutes les permissions ({{ Auth::user()->getAllPermissions()->count() }})</h2>
            <ul style="column-count: 3;">
                @foreach(Auth::user()->getAllPermissions() as $permission)
                    <li>{{ $permission->name }}</li>
                @endforeach
            </ul>
        </div>

        <div style="margin-top: 20px;">
            <a href="{{ route('dashboard') }}" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                ← Retour au Dashboard
            </a>
        </div>
    @else
        <p class="error">❌ Vous n'êtes pas connecté</p>
        <a href="{{ route('login') }}">Se connecter</a>
    @endauth
</body>
</html>

