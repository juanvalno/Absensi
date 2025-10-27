<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departemen;

class DepartemenController extends Controller
{
    /**
     * Menampilkan daftar semua departemen
     */
    public function index(Request $request)
    {
        // Pagination dengan Laravel's built-in paginator
        $perPage = $request->input('per_page', 15); // Default 15 items per page
        $search = $request->input('search', '');

        $query = Departemen::query()->orderBy('name_departemen');

        // Apply search if provided
        if (!empty($search)) {
            $query->where('name_departemen', 'LIKE', "%{$search}%");
        }

        // Get paginated results
        $departemens = $query->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'data' => $departemens->items(),
                'pagination' => [
                    'total' => $departemens->total(),
                    'per_page' => $departemens->perPage(),
                    'current_page' => $departemens->currentPage(),
                    'last_page' => $departemens->lastPage(),
                ]
            ]);
        }

        return view('admin.departemens.index', compact('departemens'));
    }

    /**
     * Menampilkan form untuk membuat departemen baru
     */
    public function create()
    {
        return view('admin.departemens.create');
    }

    /**
     * Menyimpan data departemen baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'name_departemen' => 'required|string|max:255|unique:departemens,name_departemen',
        ]);

        // Simpan data departemen ke database
        Departemen::create($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('departemens.index')
            ->with('success', 'Departemen berhasil ditambahkan');
    }

    /**
     * Menampilkan detail departemen tertentu
     */
    public function show(Departemen $departemen)
    {
        // Muat relasi bagian untuk ditampilkan
        $departemen->load('bagians');
        return view('admin.departemens.show', compact('departemen'));
    }

    /**
     * Menampilkan form untuk mengedit departemen
     */
    public function edit(Departemen $departemen)
    {
        return view('admin.departemens.edit', compact('departemen'));
    }

    /**
     * Memperbarui data departemen di database
     */
    public function update(Request $request, Departemen $departemen)
    {
        // Validasi input dari form edit
        $request->validate([
            'name_departemen' => 'required|string|max:255|unique:departemens,name_departemen,' . $departemen->id,
        ]);

        // Update data departemen di database
        $departemen->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('departemens.index')
            ->with('success', 'Departemen berhasil diupdate');
    }

    /**
     * Menghapus data departemen dari database
     */
    public function destroy(Departemen $departemen)
    {
        // Cek apakah masih ada bagian di departemen ini
        if ($departemen->bagians()->exists()) {
            // Jika masih ada bagian, tampilkan pesan error
            return redirect()->route('departemens.index')
                ->with('error', 'Hapus semua bagian pada departemen ini terlebih dahulu');
        }

        // Hapus data departemen
        $departemen->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('departemens.index')
            ->with('success', 'Departemen berhasil dihapus');
    }
}
