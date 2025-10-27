<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class DynamicMenuMiddleware
{
    /**
     * Handle permintaan HTTP yang masuk
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $userId = $user->id;

            // Cache menu berdasarkan user ID
            $cacheKey = "menu_user_{$userId}";

            // Force refresh menu jika ada parameter dalam request
            if ($request->has('refresh_menu')) {
                Cache::forget($cacheKey);
                // Jalankan optimize:clear untuk membersihkan cache aplikasi
                Artisan::call('optimize:clear');
            }

            // Periksa apakah request berasal dari rute pengelolaan menu
            $currentRoute = $request->route()->getName();
            $menuRoutes = ['menu.store', 'menu.update', 'menu.destroy', 'menu.update-order'];

            if (in_array($currentRoute, $menuRoutes)) {
                // Hapus cache menu untuk semua pengguna
                $keys = Cache::get('menu_cache_keys', []);
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
                Cache::forget('menu_cache_keys');

                // Jalankan optimize:clear untuk membersihkan cache aplikasi
                Artisan::call('optimize:clear');
            }

            // Cek apakah rute saat ini memerlukan permission tertentu
            $routeName = $request->route()->getName();
            $menu = Menu::where('route', $routeName)->first();

            if ($menu && $menu->permission && !$user->can($menu->permission)) {
                // Jika pengguna tidak memiliki permission yang diperlukan
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
            }

            // Ambil menu dari cache atau buat baru jika belum ada
            $menuItems = Cache::remember($cacheKey, 60 * 24, function () use ($user, $userId) {
                // Simpan kunci cache untuk penghapusan di masa mendatang
                $cacheKeys = Cache::get('menu_cache_keys', []);
                $cacheKey = "menu_user_{$userId}";
                if (!in_array($cacheKey, $cacheKeys)) {
                    $cacheKeys[] = $cacheKey;
                    Cache::put('menu_cache_keys', $cacheKeys, 60 * 24 * 30); // Simpan selama 30 hari
                }

                return $this->getMenuForUser($user);
            });

            // Set menu ke config AdminLTE
            config(['adminlte.menu' => $menuItems]);
        }

        return $next($request);
    }

    /**
     * Mendapatkan menu yang sesuai dengan hak akses pengguna
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getMenuForUser($user)
    {
        // Ambil semua menu dari database
        $menus = Menu::with(['children' => function ($query) {
            $query->orderBy('order', 'asc');
        }])
            ->whereNull('parent_id')
            ->orderBy('order', 'asc')
            ->get();

        $formattedMenu = [];
        $lastHeaderIndex = -1;

        foreach ($menus as $menu) {
            // Untuk tipe header
            if ($menu->type === 'header') {
                // Simpan index header untuk pengecekan nanti
                $lastHeaderIndex = count($formattedMenu);
                $formattedMenu[] = ['header' => $menu->text];
                continue;
            }

            // Skip jika user tidak memiliki permission
            if ($menu->permission && !$user->can($menu->permission)) {
                continue;
            }

            // Format menu item
            $item = ['text' => $menu->text];

            if ($menu->icon) $item['icon'] = $menu->icon;
            if ($menu->route) $item['route'] = $menu->route;

            // Tambahkan submenu jika ada
            if ($menu->children && $menu->children->count() > 0) {
                $submenu = [];

                foreach ($menu->children as $child) {
                    // Skip jika user tidak memiliki permission
                    if ($child->permission && !$user->can($child->permission)) {
                        continue;
                    }

                    $childItem = ['text' => $child->text];

                    if ($child->icon) $childItem['icon'] = $child->icon;
                    if ($child->route) $childItem['route'] = $child->route;

                    $submenu[] = $childItem;
                }

                // Tambahkan submenu hanya jika ada item
                if (count($submenu) > 0) {
                    $item['submenu'] = $submenu;
                }
            }

            $formattedMenu[] = $item;
        }

        // Bersihkan header yang tidak memiliki menu item setelahnya
        $cleanedMenu = [];
        $skipNext = false;

        for ($i = 0; $i < count($formattedMenu); $i++) {
            $item = $formattedMenu[$i];

            // Jika ini adalah header
            if (isset($item['header'])) {
                // Periksa apakah ini adalah item terakhir atau item berikutnya juga header
                if (
                    $i == count($formattedMenu) - 1 ||
                    (isset($formattedMenu[$i + 1]['header']))
                ) {
                    // Skip header ini
                    continue;
                }
            }

            $cleanedMenu[] = $item;
        }

        return $cleanedMenu;
    }
}
