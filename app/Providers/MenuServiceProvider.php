<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Menu;

class MenuServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Get menu items only when authenticated
        $this->app->booted(function () {
            if (auth()->check()) {
                $menuItems = $this->generateMenu();
                \Log::info('Menu items:', $menuItems);
                config(['adminlte.menu' => $menuItems]);
            }
        });
    }

    protected function generateMenu()
{
    try {
        // Get menu items from database
        $menus = Menu::with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        if ($menus->count() === 0) {
            \Log::info('No menu items found in database');
            return [];
        }

        \Log::info('Menus found: ' . $menus->count());
        return $this->formatMenu($menus);
    } catch (\Exception $e) {
        \Log::error('Error loading menu: ' . $e->getMessage());
        return [];
    }
}

    // protected function formatMenu($menus)
    // {
    //     $formattedMenu = [];

    //     foreach ($menus as $menu) {
    //         $menuItem = [
    //             'text' => $menu->text,
    //             'order' => $menu->order
    //         ];

    //         // Add icon if exists
    //         if ($menu->icon) {
    //             $menuItem['icon'] = $menu->icon;
    //         }

    //         // Add route if exists
    //         if ($menu->route) {
    //             $menuItem['route'] = $menu->route;
    //         }

    //         // Add permission if exists
    //         if ($menu->permission) {
    //             $menuItem['can'] = $menu->permission;
    //         }

    //         // Add header type
    //         if ($menu->type === 'header') {
    //             $menuItem['header'] = true;
    //         }

    //         // Add submenu if has children
    //         if ($menu->children->count() > 0) {
    //             $menuItem['submenu'] = $this->formatMenu($menu->children);
    //         }

    //         $formattedMenu[] = $menuItem;
    //     }

    //     return $formattedMenu;
    // }
    protected function formatMenu($menus)
    {
        $formattedMenu = [];

        foreach ($menus as $menu) {
            // Debug item
            \Log::info('Processing menu:', ['menu' => $menu->toArray()]);

            // Untuk tipe header, gunakan format yang benar untuk AdminLTE
            if ($menu->type === 'header') {
                $formattedMenu[] = ['header' => $menu->text];
                continue; // Skip ke item menu berikutnya
            }

            // Format untuk menu item biasa
            $menuItem = [
                'text' => $menu->text
            ];

            // Add icon if exists
            if ($menu->icon) {
                $menuItem['icon'] = $menu->icon;
            }

            // Add route if exists
            if ($menu->route) {
                $menuItem['route'] = $menu->route;
            }

            // Add permission if exists
            if ($menu->permission) {
                $menuItem['can'] = $menu->permission;
            }

            // Add submenu if has children
            if ($menu->children->count() > 0) {
                \Log::info('Menu has children:', ['count' => $menu->children->count()]);
                $menuItem['submenu'] = $this->formatMenu($menu->children);
            }

            $formattedMenu[] = $menuItem;
        }

        return $formattedMenu;
    }
}