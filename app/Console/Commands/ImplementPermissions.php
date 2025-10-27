<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImplementPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:implement {controller? : Specific controller to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add HasPermissionsTrait to controllers for automatic permission checking';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $specificController = $this->argument('controller');

        if ($specificController) {
            $controllerPath = $this->findControllerPath($specificController);
            if ($controllerPath) {
                $this->implementPermissionsInController($controllerPath);
            } else {
                $this->error("Controller not found: {$specificController}");
                return 1;
            }
        } else {
            $this->implementPermissionsInAllControllers();
        }

        return 0;
    }

    /**
     * Implement permissions in all eligible controllers
     */
    protected function implementPermissionsInAllControllers()
    {
        $this->info('Implementing permissions in controllers...');

        $controllerPaths = [
            app_path('Http/Controllers'),
            app_path('Http/Controllers/Admin'),
            app_path('Http/Controllers/Auth'),
            // Add other controller directories as needed
        ];

        $totalUpdated = 0;

        foreach ($controllerPaths as $path) {
            if (File::isDirectory($path)) {
                $files = File::allFiles($path);

                $bar = $this->output->createProgressBar(count($files));
                $bar->start();

                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $updated = $this->implementPermissionsInController($file->getPathname());
                        if ($updated) {
                            $totalUpdated++;
                        }
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();
            }
        }

        $this->newLine();
        $this->info("Updated {$totalUpdated} controllers with HasPermissionsTrait.");
    }

    /**
     * Find controller file path
     *
     * @param string $controller
     * @return string|null
     */
    protected function findControllerPath($controller)
    {
        // Add Controller suffix if not present
        if (!Str::endsWith($controller, 'Controller')) {
            $controller .= 'Controller';
        }

        // Search in common controller directories
        $possiblePaths = [
            app_path("Http/Controllers/{$controller}.php"),
            app_path("Http/Controllers/Admin/{$controller}.php"),
            app_path("Http/Controllers/Auth/{$controller}.php"),
        ];

        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Implement permissions in a controller file
     *
     * @param string $filePath
     * @return bool Whether the file was updated
     */
    protected function implementPermissionsInController($filePath)
    {
        $content = File::get($filePath);
        $filename = basename($filePath);

        // Skip files that are not controllers or are abstract classes
        if (!Str::endsWith($filename, 'Controller.php') ||
            Str::contains($content, 'abstract class')) {
            return false;
        }

        // Skip if already using HasPermissionsTrait
        if (Str::contains($content, 'use App\Traits\HasPermissionsTrait;') ||
            Str::contains($content, 'use HasPermissionsTrait;')) {
            return false;
        }

        // Add the trait import
        $updatedContent = $this->addTraitImport($content);

        // Add the trait use statement
        $updatedContent = $this->addTraitUse($updatedContent);

        // Save the updated content
        if ($updatedContent !== $content) {
            File::put($filePath, $updatedContent);
            $this->line("<info>Updated:</info> " . $filename);
            return true;
        }

        return false;
    }

    /**
     * Add the trait import statement to the file content
     *
     * @param string $content
     * @return string
     */
    protected function addTraitImport($content)
    {
        // Find the last use statement or namespace declaration
        preg_match_all('/^use ([^;]+);/m', $content, $matches, PREG_OFFSET_CAPTURE);

        if (!empty($matches[0])) {
            // Add after the last use statement
            $lastUse = end($matches[0]);
            $position = $lastUse[1] + strlen($lastUse[0]);

            return substr_replace(
                $content,
                "\nuse App\Traits\HasPermissionsTrait;",
                $position,
                0
            );
        } else {
            // Add after namespace
            preg_match('/namespace ([^;]+);/', $content, $matches, PREG_OFFSET_CAPTURE);

            if (!empty($matches[0])) {
                $namespace = $matches[0][0];
                $position = $matches[0][1] + strlen($namespace);

                return substr_replace(
                    $content,
                    "\n\nuse App\Traits\HasPermissionsTrait;",
                    $position,
                    0
                );
            }
        }

        return $content;
    }

    /**
     * Add the trait use statement to the class
     *
     * @param string $content
     * @return string
     */
    protected function addTraitUse($content)
    {
        // Find the class declaration
        preg_match('/class ([^\s]+)(?:\s+extends\s+[^\s{]+)?(?:\s+implements\s+[^{]+)?/', $content, $matches, PREG_OFFSET_CAPTURE);

        if (!empty($matches[0])) {
            $classDeclaration = $matches[0][0];
            $position = $matches[0][1] + strlen($classDeclaration);

            // Find opening brace of class
            $bracePos = strpos($content, '{', $position);

            if ($bracePos !== false) {
                return substr_replace(
                    $content,
                    "\n{\n    use HasPermissionsTrait;",
                    $bracePos,
                    1
                );
            }
        }

        return $content;
    }
}
