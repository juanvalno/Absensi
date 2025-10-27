<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class PermissionController extends Controller
{
    // Mapping method names to permission actions
    protected $actionMap = [
        'index' => 'view',
        'show' => 'view',
        'create' => 'create',
        'store' => 'create',
        'edit' => 'edit',
        'update' => 'edit',
        'destroy' => 'delete',
    ];

    // Methods to exclude
    protected $excludeMethods = [
        '__construct', 'middleware', 'getMiddleware', 'callAction',
        'authorize', 'authorizeResource', 'validateWith', 'validate'
    ];

    public function index()
    {
        // Membaca permissions langsung dari controller files
        $controllerPermissions = $this->scanControllersForPermissions();

        return view('admin.permissions.index', compact('controllerPermissions'));
    }

    /**
     * Update permissions database dari hasil scan controller
     */
    public function updatePermissions()
    {
        // Scan semua controller
        $controllerPermissions = $this->scanControllersForPermissions();

        // Sync ke database
        $count = $this->syncPermissionsToDatabase($controllerPermissions);

        return redirect()->route('permissions.index')
            ->with('success', $count . ' permissions berhasil diperbarui dari controller files.');
    }

    /**
     * Sync permissions ke database
     */
    protected function syncPermissionsToDatabase($controllerPermissions)
    {
        $count = 0;

        foreach ($controllerPermissions as $module => $controller) {
            foreach ($controller['permissions'] as $methodName => $permissionType) {
                $permissionName = $module . '.' . $permissionType;
                Permission::firstOrCreate(['name' => $permissionName]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Scan semua controller untuk permissions
     */
    protected function scanControllersForPermissions()
    {
        $controllerPermissions = [];

        // Tambahkan path controller yang ingin di-scan
        $paths = [
            app_path('Http/Controllers/Admin')
        ];

        foreach ($paths as $path) {
            if (File::isDirectory($path)) {
                foreach (File::allFiles($path) as $file) {
                    if ($file->getExtension() === 'php') {
                        $className = $this->getClassNameFromFile($file);
                        if ($className && class_exists($className)) {
                            $controllerInfo = [
                                'name' => class_basename($className),
                                'path' => $file->getPathname(),
                                'permissions' => $this->extractPermissionsFromController($className)
                            ];

                            if (!empty($controllerInfo['permissions'])) {
                                // Using module name as key
                                $module = $this->getModuleName($className);
                                $controllerPermissions[$module] = $controllerInfo;
                            }
                        }
                    }
                }
            }
        }

        // Sort by module name
        ksort($controllerPermissions);

        return $controllerPermissions;
    }

    /**
     * Dapatkan module name dari controller class name
     */
    protected function getModuleName($controller)
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
     * Dapatkan nama class dari file
     */
    protected function getClassNameFromFile($file)
    {
        $content = file_get_contents($file->getPathname());

        // Extract namespace
        preg_match('/namespace\s+([^;]+)/i', $content, $matches);
        $namespace = $matches[1] ?? null;

        // Extract class name
        preg_match('/class\s+(\w+)/i', $content, $matches);
        $className = $matches[1] ?? null;

        if ($namespace && $className) {
            return $namespace . '\\' . $className;
        }

        return null;
    }

    /**
     * Extract permissions dari controller class
     */
    protected function extractPermissionsFromController($controller)
    {
        // Skip abstract classes
        $reflectionClass = new ReflectionClass($controller);
        if ($reflectionClass->isAbstract()) {
            return [];
        }

        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $permissions = [];

        foreach ($methods as $method) {
            // Skip if method is from parent Controller class
            if ($method->class !== $controller) {
                continue;
            }

            $methodName = $method->getName();

            // Skip excluded methods
            if (in_array($methodName, $this->excludeMethods) ||
                Str::startsWith($methodName, ['_', 'get', 'set'])) {
                continue;
            }

            // Map method to permission name
            $permissionName = $this->mapMethodToPermission($methodName);
            $permissions[$methodName] = $permissionName;
        }

        return $permissions;
    }

    /**
     * Map method name to permission
     */
    protected function mapMethodToPermission($method)
    {
        foreach ($this->actionMap as $actionName => $permissionName) {
            if ($method === $actionName || Str::startsWith($method, $actionName)) {
                return $permissionName;
            }
        }

        // For custom methods, use method name as permission
        return $method;
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'module' => 'required'
        ]);

        // Format permission name: module.action
        $permissionName = $request->module . '.' . $request->name;

        Permission::create(['name' => $permissionName]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil ditambahkan');
    }

    public function edit(Permission $permission)
    {
        // Split permission name into module and action
        [$module, $action] = explode('.', $permission->name);
        $permission->module = $module;
        $permission->action = $action;

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
            'module' => 'required'
        ]);

        // Format permission name: module.action
        $permissionName = $request->module . '.' . $request->name;

        $permission->update(['name' => $permissionName]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil diupdate');
    }

    public function destroy(Permission $permission)
    {
        // Check if permission is being used by any roles
        if($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Permission tidak bisa dihapus karena masih digunakan oleh role');
        }

        $permission->delete();
        return redirect()->route('permissions.index')
            ->with('success', 'Permission berhasil dihapus');
    }
}
