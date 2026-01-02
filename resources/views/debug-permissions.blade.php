<!DOCTYPE html>
<html>
<head>
    <title>Debug Permissions</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #fff; }
        .success { color: #4caf50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; }
        .info { background: #333; padding: 15px; margin: 10px 0; border-left: 4px solid #2196f3; }
        h1, h2 { color: #2196f3; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #444; }
        th { background: #444; }
        .big-result { font-size: 24px; padding: 20px; text-align: center; margin: 20px 0; background: #000; }
    </style>
</head>
<body>
    <h1>🔍 DEBUG PERMISSIONS SUPER ADMIN</h1>
    
    @if(!$authenticated)
        <div class="error big-result">❌ VOUS N'ÊTES PAS CONNECTÉ</div>
        <p><a href="{{ route('login') }}" style="color: #2196f3;">Se connecter</a></p>
    @else
        <div class="info">
            <h2>✅ Utilisateur connecté</h2>
            <table>
                <tr><th>ID</th><td>{{ $user->id }}</td></tr>
                <tr><th>Nom</th><td>{{ $user->name }}</td></tr>
                <tr><th>Email</th><td>{{ $user->email }}</td></tr>
                <tr><th>Rôle (legacy)</th><td>{{ $user->role ?? 'aucun' }}</td></tr>
            </table>
        </div>

        <div class="info">
            <h2>Rôles Spatie ({{ $roles->count() }})</h2>
            @if($roles->count() > 0)
                <ul>
                    @foreach($roles as $role)
                        <li class="success">✅ {{ $role->name }} ({{ $role->permissions->count() }} permissions)</li>
                    @endforeach
                </ul>
            @else
                <p class="error">❌ AUCUN RÔLE SPATIE ASSIGNÉ - C'EST LE PROBLÈME!</p>
            @endif
        </div>

        <div class="info">
            <h2>🎯 TESTS DE PERMISSIONS</h2>
            <table>
                <tr>
                    <th>Test</th>
                    <th>Résultat</th>
                    <th>Explication</th>
                </tr>
                <tr>
                    <td>hasRole('Super Admin')</td>
                    <td class="{{ $tests['hasRole'] ? 'success' : 'error' }}">
                        {{ $tests['hasRole'] ? '✅ TRUE' : '❌ FALSE' }}
                    </td>
                    <td>{{ $tests['hasRole'] ? 'OK' : 'PROBLÈME: Pas de rôle Super Admin' }}</td>
                </tr>
                <tr>
                    <td>can('view-users')</td>
                    <td class="{{ $tests['canViewUsers'] ? 'success' : 'error' }}">
                        {{ $tests['canViewUsers'] ? '✅ TRUE' : '❌ FALSE' }}
                    </td>
                    <td>{{ $tests['canViewUsers'] ? 'OK' : 'PROBLÈME: Pas la permission' }}</td>
                </tr>
                <tr>
                    <td>can('view-roles')</td>
                    <td class="{{ $tests['canViewRoles'] ? 'success' : 'error' }}">
                        {{ $tests['canViewRoles'] ? '✅ TRUE' : '❌ FALSE' }}
                    </td>
                    <td>{{ $tests['canViewRoles'] ? 'OK' : 'PROBLÈME: Pas la permission' }}</td>
                </tr>
                <tr>
                    <td>can('view-permissions')</td>
                    <td class="{{ $tests['canViewPermissions'] ? 'success' : 'error' }}">
                        {{ $tests['canViewPermissions'] ? '✅ TRUE' : '❌ FALSE' }}
                    </td>
                    <td>{{ $tests['canViewPermissions'] ? 'OK' : 'PROBLÈME: Pas la permission' }}</td>
                </tr>
                <tr>
                    <td>Gate::before() test</td>
                    <td class="{{ $tests['canAnyTest'] ? 'success' : 'error' }}">
                        {{ $tests['canAnyTest'] ? '✅ TRUE' : '❌ FALSE' }}
                    </td>
                    <td>{{ $tests['canAnyTest'] ? 'Gate::before() fonctionne!' : 'Gate::before() ne fonctionne PAS' }}</td>
                </tr>
            </table>
        </div>

        <div class="info">
            <h2>Permissions directes ({{ $permissions->count() }})</h2>
            @if($permissions->count() > 0)
                <div style="max-height: 200px; overflow-y: auto;">
                    @foreach($permissions as $perm)
                        <div>{{ $perm->name }}</div>
                    @endforeach
                </div>
            @else
                <p class="warning">⚠️ Aucune permission directe</p>
            @endif
        </div>

        @php
            $allOk = $tests['hasRole'] && $tests['canViewUsers'] && $tests['canViewRoles'] && $tests['canViewPermissions'];
        @endphp

        <div class="big-result {{ $allOk ? 'success' : 'error' }}">
            @if($allOk)
                ✅✅✅ TOUT FONCTIONNE!
                <br>LE MENU DEVRAIT S'AFFICHER
            @else
                ❌ PROBLÈME DÉTECTÉ
                <br>LE MENU NE PEUT PAS S'AFFICHER
            @endif
        </div>

        @if(!$allOk)
            <div class="info">
                <h2>🔧 SOLUTIONS</h2>
                @if(!$tests['hasRole'])
                    <p class="error">1. Vous n'avez pas le rôle Super Admin</p>
                    <p>Exécutez: <code style="background: #000; padding: 5px;">php artisan db:seed --class=AssignRolesToExistingUsersSeeder --force</code></p>
                @endif
                
                @if($tests['hasRole'] && !$tests['canViewUsers'])
                    <p class="error">2. Gate::before() ne fonctionne pas</p>
                    <p>Le AppServiceProvider n'est pas correctement chargé</p>
                @endif
            </div>
        @endif

        <div style="margin-top: 30px;">
            <a href="{{ route('dashboard') }}" style="padding: 10px 20px; background: #2196f3; color: white; text-decoration: none; border-radius: 5px;">
                ← Retour au Dashboard
            </a>
        </div>
    @endif

    <p style="margin-top: 30px; color: #666; text-align: center;">
        Généré le {{ date('Y-m-d H:i:s') }}
    </p>
</body>
</html>

