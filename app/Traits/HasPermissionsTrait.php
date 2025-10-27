<?php

namespace App\Traits;

use App\Services\PermissionService;
use Illuminate\Support\Str;
use ReflectionClass;

trait HasPermissionsTrait
{
    /**
     * Register this trait in the controller's constructor
     */
    public function initializeHasPermissionsTrait()
    {
        $this->registerPermissions();
    }

    /**
     * Register permissions and middleware
     */
    protected function registerPermissions()
    {
        // Get or create permission service instance
        $service = app(PermissionService::class);

        // Detect permissions for this controller
        $permissions = $service->detectPermissions(get_class($this));

        // Sync detected permissions
        $service->syncPermissions($permissions);

        // Register middleware that checks permissions
        $this->middleware(function ($request, $next) use ($permissions) {
            // Skip permission check if user is a super admin
            if (auth()->check() && auth()->user()->hasRole('super-admin')) {
                return $next($request);
            }

            // Get the current action
            $action = $this->getCurrentAction();

            // Get permission for this action
            $permission = $this->getPermissionForAction($action);

            // Check if permission exists and user doesn't have it
            if ($permission && auth()->check() && !auth()->user()->can($permission)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized action.'
                    ], 403);
                }

                abort(403, 'Unauthorized action.');
            }

            return $next($request);
        });
    }

    /**
     * Get the current action name from the route
     *
     * @return string
     */
    protected function getCurrentAction()
    {
        $routeAction = request()->route()->getActionMethod();

        // Map to standard permission actions
        $actionMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete',
        ];

        foreach ($actionMap as $method => $action) {
            if ($routeAction === $method || Str::startsWith($routeAction, $method)) {
                return $action;
            }
        }

        // For custom methods, use the method name itself
        return $routeAction;
    }

    /**
     * Get the permission name for this action
     *
     * @param string $action
     * @return string
     */
    protected function getPermissionForAction($action)
    {
        $controllerName = $this->getControllerName();
        return $controllerName . '.' . $action;
    }

    /**
     * Get normalized controller name
     *
     * @return string
     */
    protected function getControllerName()
    {
        $reflectionClass = new ReflectionClass($this);
        $name = str_replace('Controller', '', $reflectionClass->getShortName());

        // Convert to kebab case
        $kebab = Str::kebab($name);

        // Handle pluralization the same way as PermissionService
        if (!Str::contains($kebab, '-')) {
            // Simple controller name, pluralize it
            return Str::plural($kebab);
        } else {
            // Complex name, don't pluralize
            return str_replace('-', '_', $kebab);
        }
    }
}
