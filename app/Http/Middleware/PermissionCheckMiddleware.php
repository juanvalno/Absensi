<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // If no specific permission was provided, attempt to derive it from the route
        if (!$permission) {
            $routeName = $request->route()->getName();
            $permission = $this->derivePermissionFromRoute($routeName);
        }

        $user = auth()->user();
        Log::info('Permission check for user: ' . $user->id, [
            'permission' => $permission,
            'has_permission' => $user->can($permission) ? 'yes' : 'no',
            'route' => $request->route()->getName(),
            'action' => $request->route()->getActionName()
        ]);

        // Check if user has the required permission
        if ($permission && !$user->can($permission)) {
            // If request wants JSON, return JSON response
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Missing permission: ' . $permission], 403);
            }

            // Otherwise redirect with error message
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }

    /**
     * Derive permission from route name
     *
     * @param string $routeName
     * @return string|null
     */
    private function derivePermissionFromRoute($routeName)
    {
        if (!$routeName) {
            return null;
        }

        // Map of route name patterns to permissions
        $routePermissionMap = [
            'users.index' => 'users.view',
            'users.create' => 'users.create',
            'users.store' => 'users.create',
            'users.edit' => 'users.edit',
            'users.update' => 'users.edit',
            'users.destroy' => 'users.delete',

            'roles.index' => 'roles.view',
            'roles.create' => 'roles.create',
            'roles.store' => 'roles.create',
            'roles.edit' => 'roles.edit',
            'roles.update' => 'roles.edit',
            'roles.destroy' => 'roles.delete',

            'permissions.index' => 'permissions.view',
            'permissions.create' => 'permissions.create',
            'permissions.store' => 'permissions.create',
            'permissions.edit' => 'permissions.edit',
            'permissions.update' => 'permissions.edit',
            'permissions.destroy' => 'permissions.delete',

            'menu.index' => 'menu.view',
            'menu.create' => 'menu.create',
            'menu.store' => 'menu.create',
            'menu.edit' => 'menu.edit',
            'menu.update' => 'menu.edit',
            'menu.destroy' => 'menu.delete',
            'menu.update-order' => 'menu.edit',

            'role-access.index' => 'roles.view',
            'role-access.update' => 'roles.edit',
            'role-access.copy-permissions' => 'roles.edit',

            'user-access.index' => 'users.view',
            'user-access.update' => 'users.edit',
            'user-access.copy-access' => 'users.edit',
        ];

        return $routePermissionMap[$routeName] ?? null;
    }
}