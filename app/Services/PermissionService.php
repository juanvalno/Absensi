<?php

namespace App\Services;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * Pemetaan aksi CRUD standar ke izin yang setara
     */
    protected $actionMap = [
        'index' => 'view',      // Melihat daftar
        'show' => 'view',       // Melihat detail
        'create' => 'create',   // Membuat form
        'store' => 'create',    // Menyimpan data baru
        'edit' => 'edit',       // Mengedit form
        'update' => 'edit',     // Memperbarui data
        'destroy' => 'delete',  // Menghapus data
        // Pemetaan tambahan
        'list' => 'view',       // Melihat daftar
        'display' => 'view',    // Menampilkan data
        'remove' => 'delete',   // Menghapus data
        'save' => 'create',     // Menyimpan data
    ];

    /**
     * Metode yang harus dikecualikan dari pembuatan izin
     */
    protected $excludedMethods = [
        '__construct',          // Konstruktor
        'middleware',           // Middleware
        'authorize',            // Otorisasi
        'validateRequest',      // Validasi permintaan
        'callAction',           // Memanggil aksi
        'getMiddleware',        // Mendapatkan middleware
        'getValidationFactory', // Mendapatkan factory validasi
        'validate',             // Validasi
        'validateWith',         // Validasi dengan
        'dispatchNow',          // Dispatch sekarang
    ];

    /**
     * Mendeteksi semua izin dari controller
     */
    public function detectPermissions($controller)
    {
        $reflection = new ReflectionClass($controller);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        $controllerName = $this->getControllerName($controller);
        $permissions = [];

        foreach ($methods as $method) {
            // Lewati metode dari kelas Controller induk
            if ($method->class === 'App\\Http\\Controllers\\Controller') {
                continue;
            }

            // Lewati metode dari traits
            if ($method->class !== $controller) {
                continue;
            }

            if ($this->shouldGeneratePermission($method->getName())) {
                $permission = $this->generatePermissionFromMethod($controllerName, $method->getName());
                if ($permission && !in_array($permission, $permissions)) {
                    $permissions[] = $permission;
                }
            }
        }

        return $permissions;
    }

    /**
     * Membuat nama izin dari metode
     */
    protected function generatePermissionFromMethod($controller, $method)
    {
        // Periksa apakah metode ada dalam peta aksi
        foreach ($this->actionMap as $action => $permission) {
            if ($method === $action || Str::startsWith($method, $action)) {
                return $controller . '.' . $permission;
            }
        }

        // Untuk metode kustom yang tidak cocok dengan pola standar,
        // buat izin dengan nama metode itu sendiri
        if (!Str::startsWith($method, ['get', 'set', '_'])) {
            return $controller . '.' . $method;
        }

        return null;
    }

    /**
     * Mendapatkan nama controller untuk izin
     */
    protected function getControllerName($controller)
    {
        $name = class_basename($controller);
        $name = str_replace('Controller', '', $name);

        // Konversi ke kebab case (mis., UserAccess => user-access)
        // dan kemudian ke snake case untuk nama izin (mis., user-access => user_access)
        $kebab = Str::kebab($name);

        // Tentukan apakah kita harus menjamakkan berdasarkan nama controller
        // Misalnya, UserController menjadi users, tetapi UserAccessController menjadi user_access
        if (!Str::contains($kebab, '-')) {
            // Nama controller sederhana, jamakkan (mis., user => users)
            return Str::plural($kebab);
        } else {
            // Nama kompleks, jangan jamakkan (mis., user-access tetap user_access)
            return str_replace('-', '_', $kebab);
        }
    }

    /**
     * Memeriksa apakah metode memerlukan pembuatan izin
     */
    protected function shouldGeneratePermission($methodName)
    {
        return !in_array($methodName, $this->excludedMethods) &&
               !Str::startsWith($methodName, ['__', 'get', 'set', '_']);
    }

    /**
     * Sinkronisasi izin ke database
     */
    public function syncPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}