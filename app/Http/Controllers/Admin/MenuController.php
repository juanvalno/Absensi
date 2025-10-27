<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar semua menu
     */
    public function index()
    {
        // Ambil semua data menu dengan relasi parent dan urutkan berdasarkan parent_id dan order
        $menus = Menu::with('parent')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get();
        return view('admin.menu.index', compact('menus'));
    }

    /**
     * Menampilkan form untuk membuat menu baru
     */
    public function create()
    {
        // Siapkan data untuk dropdown di form
        $parentMenus = Menu::whereNull('parent_id')->get();
        $permissions = Permission::all();
        return view('admin.menu.create', compact('parentMenus', 'permissions'));
    }

    /**
     * Menyimpan data menu baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input dari form dengan aturan kondisional untuk route
        $request->validate([
            'text' => 'required',
            'type' => 'required|in:header,menu',
            'icon' => 'nullable',
            'route' => $request->input('type') === 'menu' && !$request->has('has_submenu') ? 'required' : 'nullable',
            'parent_id' => 'nullable|exists:menus,id',
            'permission' => 'nullable|exists:permissions,name',
            'order' => 'required|integer'
        ]);

        // Simpan data menu ke database
        Menu::create($request->all());

        // Clear cache setelah create menu untuk memperbarui menu di aplikasi
        Artisan::call('optimize:clear');

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil ditambahkan');
    }

    /**
     * Menampilkan form untuk mengedit menu
     */
    public function edit(Menu $menu)
    {
        // Siapkan data untuk dropdown di form edit
        // Exclude menu saat ini dari daftar parent untuk mencegah self-reference
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->get();
        $permissions = Permission::all();
        return view('admin.menu.edit', compact('menu', 'parentMenus', 'permissions'));
    }

    /**
     * Memperbarui data menu di database
     */
    public function update(Request $request, Menu $menu)
    {
        // Validasi input dari form edit dengan aturan kondisional untuk route
        $request->validate([
            'text' => 'required',
            'type' => 'required|in:header,menu',
            'icon' => 'nullable',
            'route' => $request->input('type') === 'menu' && !$request->has('has_submenu') ? 'required' : 'nullable',
            'parent_id' => 'nullable|exists:menus,id',
            'permission' => 'nullable|exists:permissions,name',
            'order' => 'required|integer'
        ]);

        // Update data menu di database
        $menu->update($request->all());

        // Clear cache setelah update menu untuk memperbarui menu di aplikasi
        Artisan::call('optimize:clear');

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil diupdate');
    }

    /**
     * Menghapus data menu dari database
     */
    public function destroy(Menu $menu)
    {
        // Cek apakah menu memiliki submenu sebelum dihapus
        if($menu->children()->exists()) {
            return redirect()->route('menu.index')
                ->with('error', 'Hapus submenu terlebih dahulu');
        }

        // Hapus data menu
        $menu->delete();

        // Clear cache setelah delete menu untuk memperbarui menu di aplikasi
        Artisan::call('optimize:clear');

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('menu.index')
            ->with('success', 'Menu berhasil dihapus');
    }

    /**
     * Memperbarui urutan menu (untuk AJAX)
     */
    public function updateOrder(Request $request)
    {
        // Validasi input dari request AJAX
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:menus,id',
            'order.*.order' => 'required|integer'
        ]);

        // Update urutan untuk setiap menu
        foreach($request->order as $item) {
            Menu::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        // Clear cache setelah update order untuk memperbarui menu di aplikasi
        Artisan::call('optimize:clear');

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => true]);
    }
}
