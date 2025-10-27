<?php

namespace App\Providers;

use Illuminate\Support\Str;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Single flexible @can_show directive to handle all cases
        Blade::directive('can_show', function ($expression) {
            // Parse the arguments
            if (empty($expression)) {
                // No arguments, use current route
                return "<?php if(\\App\\Helpers\\PermissionHelper::can()): ?>";
}

// Check if there are multiple arguments
if (Str::contains($expression, ',')) {
// Format: @can_show('permission', 'route', 'url')
return "<?php if(\\App\\Helpers\\PermissionHelper::can($expression)): ?>";
}

// Single argument - could be permission, route or URL
// We'll let the helper method figure it out
return "<?php if(\\App\\Helpers\\PermissionHelper::can($expression)): ?>";
});

Blade::directive('endcan_show', function () {
return "<?php endif; ?>";
});

// Keeping existing directives for backward compatibility
Blade::directive('can_action', function ($expression) {
return "<?php if(\\App\\Helpers\\PermissionHelper::can($expression)): ?>";
});

Blade::directive('endcan_action', function () {
return "<?php endif; ?>";
});

Blade::directive('can_button', function ($expression) {
return "<?php if(\\App\\Helpers\\PermissionHelper::can(null, null, $expression)): ?>";
});

Blade::directive('endcan_button', function () {
return "<?php endif; ?>";
});
}
}