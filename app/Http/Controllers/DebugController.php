<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DebugController extends Controller
{
    public function permissions()
    {
        $user = Auth::user();
        
        if (!$user) {
            return view('debug-permissions', [
                'authenticated' => false,
                'user' => null,
                'roles' => [],
                'permissions' => [],
                'tests' => []
            ]);
        }

        $tests = [
            'hasRole' => $user->hasRole('Super Admin'),
            'canViewUsers' => $user->can('view-users'),
            'canViewRoles' => $user->can('view-roles'),
            'canViewPermissions' => $user->can('view-permissions'),
            'gateAllowsViewUsers' => Gate::allows('view-users'),
            'gateForUserViewUsers' => Gate::forUser($user)->allows('view-users'),
            'canAnyTest' => $user->can('non-existent-permission-test'),
        ];

        return view('debug-permissions', [
            'authenticated' => true,
            'user' => $user,
            'roles' => $user->roles,
            'permissions' => $user->getAllPermissions(),
            'tests' => $tests,
        ]);
    }
}

