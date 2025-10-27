<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Services\PermissionService;
use ReflectionClass;
use ReflectionMethod;

class GeneratePermissions extends Command
{
    /**
     * Nama dan signature dari perintah console.
     *
     * @var string
     */
    protected $signature = 'permissions:generate {controller? : Specific controller to generate permissions for}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Generate permissions based on controller methods';

    /**
     * Layanan permission.
     *
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * Membuat instance perintah baru.
     *
     * @return void
     */
    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();
        $this->permissionService = $permissionService;
    }

    /**
     * Menjalankan perintah console.
     *
     * @return int
     */
    public function handle()
    {
        $specificController = $this->argument('controller');

        if ($specificController) {
            $controllerClass = $this->resolveControllerClass($specificController);
            if ($controllerClass) {
                $this->generatePermissionsForController($controllerClass);
            } else {
                $this->error("Controller not found: {$specificController}");
                return 1;
            }
        } else {
            $this->generateAllPermissions();
        }

        return 0;
    }

    /**
     * Menghasilkan permission untuk semua controller.
     */
    protected function generateAllPermissions()
    {
        $this->info('Scanning controllers for permission generation...');

        $controllers = $this->getControllers();
        $bar = $this->output->createProgressBar(count($controllers));
        $bar->start();

        $generatedCount = 0;

        foreach ($controllers as $controller) {
            $permissions = $this->generatePermissionsForController($controller);
            $generatedCount += count($permissions);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Generated {$generatedCount} permissions from " . count($controllers) . " controllers.");
    }

    /**
     * Mendapatkan semua kelas controller.
     *
     * @return array
     */
    protected function getControllers()
    {
        $controllers = [];
        $controllerPaths = [
            app_path('Http/Controllers'),
            app_path('Http/Controllers/Admin'),
            app_path('Http/Controllers/Auth'),
            // Tambahkan direktori controller lain sesuai kebutuhan
        ];

        foreach ($controllerPaths as $path) {
            if (File::isDirectory($path)) {
                $files = File::allFiles($path);

                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $className = $this->getClassNameFromFile($file);
                        if ($className && class_exists($className)) {
                            $controllers[] = $className;
                        }
                    }
                }
            }
        }

        return $controllers;
    }

    /**
     * Mendapatkan nama kelas dari file.
     *
     * @param \SplFileInfo $file
     * @return string|null
     */
    protected function getClassNameFromFile($file)
    {
        $content = File::get($file->getPathname());

        // Ekstrak namespace
        preg_match('/namespace\s+([^;]+)/i', $content, $matches);
        $namespace = $matches[1] ?? null;

        // Ekstrak nama kelas
        preg_match('/class\s+(\w+)/i', $content, $matches);
        $className = $matches[1] ?? null;

        if ($namespace && $className) {
            return $namespace . '\\' . $className;
        }

        return null;
    }

    /**
     * Menyelesaikan kelas controller dari input.
     *
     * @param string $controller
     * @return string|null
     */
    protected function resolveControllerClass($controller)
    {
        // Periksa apakah input adalah nama kelas yang lengkap
        if (class_exists($controller)) {
            return $controller;
        }

        // Coba dengan awalan App\Http\Controllers
        $className = 'App\\Http\\Controllers\\' . $controller;
        if (class_exists($className)) {
            return $className;
        }

        // Coba dengan awalan App\Http\Controllers\Admin
        $className = 'App\\Http\\Controllers\\Admin\\' . $controller;
        if (class_exists($className)) {
            return $className;
        }

        // Jika controller tidak diakhiri dengan "Controller", tambahkan
        if (!Str::endsWith($controller, 'Controller')) {
            return $this->resolveControllerClass($controller . 'Controller');
        }

        return null;
    }

    /**
     * Menghasilkan permission untuk controller tertentu.
     *
     * @param string $controllerClass
     * @return array
     */
    protected function generatePermissionsForController($controllerClass)
    {
        // Lewati kelas Controller dasar dan kelas abstrak
        if (
            $controllerClass === 'App\\Http\\Controllers\\Controller' ||
            (new ReflectionClass($controllerClass))->isAbstract()
        ) {
            return [];
        }

        $permissions = $this->permissionService->detectPermissions($controllerClass);

        if (!empty($permissions)) {
            $this->permissionService->syncPermissions($permissions);

            $controllerName = class_basename($controllerClass);
            $this->line("<info>{$controllerName}:</info> " . implode(', ', $permissions));
        }

        return $permissions;
    }
}
