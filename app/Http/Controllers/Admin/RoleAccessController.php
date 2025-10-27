<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class RoleAccessController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::all();
        $selectedRole = null;
        $permissions = [];

        if ($request->has('role')) {
            $selectedRole = Role::findById($request->role);
            // Group permissions by module
            $permissions = Permission::all()->groupBy(function($permission) {
                return explode('.', $permission->name)[0];
            });
        }

        return view('admin.role-access.index', compact('roles', 'selectedRole', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Sync permissions
        $role->syncPermissions($request->permissions ?? []);

        // Hapus cache menu untuk semua pengguna dengan peran ini
        $users = User::role($role->name)->get();
        foreach ($users as $user) {
            Cache::forget("menu_user_{$user->id}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully'
        ]);
    }

    public function copyPermissions(Request $request, Role $role)
    {
        $request->validate([
            'from_role' => 'required|exists:roles,id',
        ]);

        $fromRole = Role::findById($request->from_role);
        $permissions = $fromRole->permissions->pluck('name')->toArray();

        $role->syncPermissions($permissions);

        // Hapus cache menu untuk semua pengguna dengan peran ini
        $users = User::role($role->name)->get();
        foreach ($users as $user) {
            Cache::forget("menu_user_{$user->id}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissions copied successfully'
        ]);
    }
}