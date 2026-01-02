<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Grouper les permissions par catégorie (basé sur le préfixe)
        $allPermissions = Permission::all();
        
        $permissions = $allPermissions->groupBy(function ($permission) {
            $parts = explode('-', $permission->name);
            if (count($parts) >= 2) {
                return ucfirst($parts[1]);
            }
            return 'Autres';
        });

        // Trier les groupes
        $permissions = $permissions->sortKeys();

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('permissions.show', compact('permission'));
    }
}

