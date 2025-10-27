<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;

class UserAccessController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles', 'permissions')->get();
        $selectedUser = null;
        $roles = Role::all();
        $permissions = [];

        if ($request->has('user')) {
            $selectedUser = User::with('roles', 'permissions')->findOrFail($request->user);
            // Group permissions by module
            $permissions = Permission::all()->groupBy(function($permission) {
                return explode('.', $permission->name)[0];
            });
        }

        return view('admin.user-access.index', compact('users', 'selectedUser', 'roles', 'permissions'));
    }

    // public function update(Request $request, User $user)
    // {
    //     $request->validate([
    //         'roles' => 'nullable|array',
    //         'roles.*' => 'exists:roles,id',
    //         'permissions' => 'nullable|array',
    //         'permissions.*' => 'exists:permissions,name'
    //     ]);

    //     // Sync roles and direct permissions
    //     $user->syncRoles($request->roles ?? []);
    //     $user->syncPermissions($request->permissions ?? []);

    //     // Hapus cache menu untuk pengguna ini
    //     Cache::forget("menu_user_{$user->id}");

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'User access updated successfully'
    //     ]);
    // }

    public function update(Request $request, User $user)
{
    try {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Sync roles and direct permissions
        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);

        // Hapus cache menu untuk pengguna ini
        Cache::forget("menu_user_{$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'User access updated successfully'
        ]);
    } catch (\Exception $e) {
        \Log::error('User Access Update Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    public function copyAccess(Request $request, User $user)
    {
        $request->validate([
            'from_user' => 'required|exists:users,id',
        ]);

        $fromUser = User::findOrFail($request->from_user);

        // Copy roles and direct permissions
        $user->syncRoles($fromUser->roles);
        $user->syncPermissions($fromUser->permissions);

        // Hapus cache menu untuk pengguna ini
        Cache::forget("menu_user_{$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'User access copied successfully'
        ]);
    }
}