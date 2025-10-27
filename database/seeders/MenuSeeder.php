<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            [
                'text' => 'MAIN NAVIGATION',  // Header
                'type' => 'header',
                'order' => 1
            ],
            [
                'text' => 'Dashboard',
                'type' => 'menu',
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'admin.dashboard',
                'order' => 1
            ],
            [
                'text' => 'User Management',
                'type' => 'header',
                'order' => 2
            ],
            [
                'text' => 'Users',
                'type' => 'menu',
                'icon' => 'fas fa-users',
                'route' => 'users.index',
                'permission' => 'users.view',
                'order' => 3
            ],
            [
                'text' => 'Roles',
                'type' => 'menu',
                'icon' => 'fas fa-user-tag',
                'route' => 'roles.index',
                'permission' => 'roles.view',
                'order' => 4
            ],
            [
                'text' => 'Permissions',
                'type' => 'menu',
                'icon' => 'fas fa-key',
                'route' => 'permissions.index',
                'permission' => 'permissions.view',
                'order' => 5
            ],
            [
                'text' => 'Settings',
                'type' => 'header',
                'order' => 6
            ],
            [
                'text' => 'Menu Management',
                'type' => 'menu',
                'icon' => 'fas fa-bars',
                'route' => 'menu.index',
                'permission' => 'menu.view',
                'order' => 7
            ],
            [
                'text' => 'Role Access',
                'type' => 'menu',
                'icon' => 'fas fa-lock',
                'route' => 'role-access.index',
                'permission' => 'roles.view',
                'order' => 8
            ],
            [
                'text' => 'User Access',
                'type' => 'menu',
                'icon' => 'fas fa-user-lock',
                'route' => 'user-access.index',
                'permission' => 'users.view',
                'order' => 9
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}