<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        // If not authenticated, redirect to login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Super admin bypass all permission checks
        if (auth()->user()->hasRole('super-admin')) {
            return $next($request);
        }

        // If no specific permission was provided, try to detect it from route
        if (!$permission) {
            $permission = $this->detectPermissionFromRoute($request);
        }

        // Check if user has permission
        if ($permission && !auth()->user()->can($permission)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk: ' . $permission
                ], 403);
            }

            return abort(403, 'Anda tidak memiliki izin untuk: ' . $permission);
        }

        return $next($request);
    }

    /**
     * Detect permission from route
     */
    protected function detectPermissionFromRoute(Request $request)
    {
        $route = $request->route();

        // If no controller action, return null
        if (!$route || !isset($route->action['controller'])) {
            return null;
        }

        // Get controller and method from route
        $action = $route->action['controller'];
        list($controller, $method) = explode('@', $action);

        // Get module name from controller name
        $module = $this->getModuleFromController($controller);

        // Map method to permission action
        $permission = $this->mapMethodToPermission($method);

        // Return formatted permission name
        return $module . '.' . $permission;
    }

    /**
     * Get module name from controller class
     */
    protected function getModuleFromController($controller)
    {
        $name = class_basename($controller);
        $name = str_replace('Controller', '', $name);

        // Convert to kebab case
        $kebabName = Str::kebab($name);

        // For simple names, pluralize them (ex. User -> users)
        // For compound names, convert to snake_case (ex. UserAccess -> user_access)
        if (!Str::contains($kebabName, '-')) {
            return Str::plural($kebabName);
        } else {
            return str_replace('-', '_', $kebabName);
        }
    }

    /**
     * Map controller method to permission action
     */
    protected function mapMethodToPermission($method)
    {
        $actionMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete'
        ];

        foreach ($actionMap as $actionName => $permissionName) {
            if ($method === $actionName || Str::startsWith($method, $actionName)) {
                return $permissionName;
            }
        }

        // For custom methods, use method name as permission
        return $method;
    }
}
