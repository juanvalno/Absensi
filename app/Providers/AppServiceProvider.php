<?php

namespace App\Providers;

use App\View\Components\ActionButton;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('action-button', ActionButton::class);

        // Only load menus if authenticated and table exists
        if (auth()->check() && Schema::hasTable('menus')) {
            $menus = \App\Models\Menu::with('children')
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();

            $formattedMenus = [];
            foreach ($menus as $menu) {
                if ($menu->type === 'header') {
                    $formattedMenus[] = ['header' => $menu->text];
                } else {
                    $item = [
                        'text' => $menu->text,
                        'icon' => $menu->icon,
                    ];

                    if ($menu->route) {
                        $item['route'] = $menu->route;
                    }

                    $formattedMenus[] = $item;
                }
            }

            config(['adminlte.menu' => $formattedMenus]);
        }
    }
}
