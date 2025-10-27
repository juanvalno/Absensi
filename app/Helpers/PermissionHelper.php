<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class PermissionHelper
{
    /**
     * Check if current user has permission based on route
     *
     * @param string|null $permission Custom permission
     * @param string|null $route Route name
     * @param string|null $url URL string
     * @return bool
     */
    public static function can($permission = null, $route = null, $url = null)
    {
        // Super admin bypass
        if (auth()->check() && auth()->user()->hasRole('super-admin')) {
            return true;
        }

        // No auth, no access
        if (!auth()->check()) {
            return false;
        }

        // 1. If specific permission provided, use it directly
        if (!empty($permission)) {
            return auth()->user()->can($permission);
        }

        // 2. If route name provided, derive permission from it
        if (!empty($route)) {
            $permissionInfo = self::parseRouteToPermission($route);
            if ($permissionInfo) {
                return auth()->user()->can($permissionInfo['permission']);
            }
        }

        // 3. If URL provided, derive permission from URL structure
        if (!empty($url)) {
            $permissionInfo = self::parseUrlToPermission($url);
            if ($permissionInfo) {
                return auth()->user()->can($permissionInfo['permission']);
            }
        }

        // 4. Fallback: use current route
        $currentRoute = request()->route();
        if ($currentRoute) {
            // Get route name if available
            $routeName = $currentRoute->getName();
            if ($routeName) {
                $permissionInfo = self::parseRouteToPermission($routeName);
                if ($permissionInfo) {
                    return auth()->user()->can($permissionInfo['permission']);
                }
            }

            // Otherwise use controller/action
            if (isset($currentRoute->action['controller'])) {
                $controllerAction = $currentRoute->action['controller'];
                $permissionInfo = self::parseControllerToPermission($controllerAction);
                if ($permissionInfo) {
                    return auth()->user()->can($permissionInfo['permission']);
                }
            }
        }

        // If we couldn't determine the permission, be restrictive by default
        return false;
    }

    /**
     * Parse route name to permission
     *
     * @param string $routeName
     * @return array|null
     */
    private static function parseRouteToPermission($routeName)
    {
        // Common Laravel route name patterns
        if (preg_match('/^(.*)\.(index|create|store|show|edit|update|destroy)$/', $routeName, $matches)) {
            $module = $matches[1];
            $action = $matches[2];

            // Map Laravel action to permission action
            $actionMap = [
                'index' => 'view',
                'show' => 'view',
                'create' => 'create',
                'store' => 'create',
                'edit' => 'edit',
                'update' => 'edit',
                'destroy' => 'delete'
            ];

            $permissionAction = $actionMap[$action] ?? $action;

            return [
                'module' => $module,
                'action' => $permissionAction,
                'permission' => $module . '.' . $permissionAction
            ];
        }

        return null;
    }

    /**
     * Parse URL to extract permission
     *
     * @param string $url
     * @return array|null
     */
    private static function parseUrlToPermission($url)
    {
        // Remove domain if present
        $path = parse_url($url, PHP_URL_PATH);

        if (empty($path)) {
            return null;
        }

        // Remove leading slash and split by slashes
        $path = trim($path, '/');
        $segments = explode('/', $path);

        // Admin route pattern: admin/module/action
        if (count($segments) >= 2 && $segments[0] === 'admin') {
            $module = $segments[1];
            $action = 'view'; // Default

            // Determine action based on URL pattern
            if (count($segments) > 2) {
                if ($segments[2] === 'create') {
                    $action = 'create';
                } elseif ($segments[2] === 'edit' || (count($segments) > 3 && $segments[3] === 'edit')) {
                    $action = 'edit';
                } elseif (Str::contains($url, 'delete') || Str::contains($url, 'destroy') ||
                          Str::contains($url, '_method=DELETE')) {
                    $action = 'delete';
                }
            }

            return [
                'module' => $module,
                'action' => $action,
                'permission' => $module . '.' . $action
            ];
        }

        return null;
    }

    /**
     * Parse controller@action string to permission
     *
     * @param string $controllerAction
     * @return array|null
     */
    private static function parseControllerToPermission($controllerAction)
    {
        if (!is_string($controllerAction) || !Str::contains($controllerAction, '@')) {
            return null;
        }

        list($controller, $method) = explode('@', $controllerAction);

        // Get controller base name
        $controllerName = class_basename($controller);
        $controllerName = str_replace('Controller', '', $controllerName);

        // Convert to kebab case
        $kebabName = Str::kebab($controllerName);

        // Determine module name
        $module = !Str::contains($kebabName, '-')
            ? Str::plural($kebabName)
            : str_replace('-', '_', $kebabName);

        // Map method to action
        $actionMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete'
        ];

        $action = $actionMap[$method] ?? $method;

        return [
            'module' => $module,
            'action' => $action,
            'permission' => $module . '.' . $action
        ];
    }
}
