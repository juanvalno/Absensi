<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create(['name' => $request->name]);

        if($request->permissions) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('role-access.index', ['role' => $role->id])
            ->with('success', 'Role berhasil dibuat. Silakan konfirmasi izin untuk role ini.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $request->name]);

        if($request->has('permissions')) {
            $role->syncPermissions($request->permissions);

            // Hapus cache menu untuk semua pengguna dengan peran ini
            $users = User::role($role->name)->get();
            foreach ($users as $user) {
                Cache::forget("menu_user_{$user->id}");
            }
        }

        return redirect()->route('role-access.index', ['role' => $role->id])
            ->with('success', 'Role berhasil diupdate. Silakan konfirmasi izin di bawah ini.');
    }

    public function destroy(Role $role)
    {
        if($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Role tidak bisa dihapus karena masih digunakan oleh user');
        }

        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus');
    }
}