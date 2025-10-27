<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mastercuti;

class MastercutiController extends Controller
{
    /**
     * Menampilkan daftar semua master cuti
     */
    public function index()
    {
        $mastercutis = Mastercuti::orderBy('uraian')->get();
        return view('admin.mastercutis.index', compact('mastercutis'));
    }

    /**
     * Menampilkan form untuk membuat master cuti baru
     */
    public function create()
    {
        return view('admin.mastercutis.create');
    }

    /**
     * Menyimpan data master cuti baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'uraian' => 'required|string|max:255',
            'is_bulanan' => 'nullable|boolean',
            'cuti_max' => 'nullable|string|max:255',
            'izin_max' => 'nullable|string|max:255',
            'is_potonggaji' => 'nullable|boolean',
            'nominal' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_bulanan'] = $request->has('is_bulanan') ? 1 : 0;
        $data['is_potonggaji'] = $request->has('is_potonggaji') ? 1 : 0;

        if (!empty($data['nominal'])) {
            $data['nominal'] = preg_replace('/[^0-9]/', '', $data['nominal']);
        }

        Mastercuti::create($data);

        return redirect()->route('mastercutis.index')
            ->with('success', 'Master cuti berhasil ditambahkan');
    }

    /**
     * Menampilkan detail master cuti tertentu
     */
    public function show(Mastercuti $mastercuti)
    {
        return view('admin.mastercutis.show', compact('mastercuti'));
    }

    /**
     * Menampilkan form untuk mengedit master cuti
     */
    public function edit(Mastercuti $mastercuti)
    {
        return view('admin.mastercutis.edit', compact('mastercuti'));
    }

    /**
     * Memperbarui data master cuti di database
     */
    public function update(Request $request, Mastercuti $mastercuti)
    {
        $request->validate([
            'uraian' => 'required|string|max:255' . $mastercuti->id,
            'is_bulanan' => 'nullable|boolean',
            'cuti_max' => 'nullable|string|max:255',
            'izin_max' => 'nullable|string|max:255',
            'is_potonggaji' => 'nullable|boolean',
            'nominal' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_bulanan'] = $request->has('is_bulanan') ? 1 : 0;
        $data['is_potonggaji'] = $request->has('is_potonggaji') ? 1 : 0;

        if (!empty($data['nominal'])) {
            $data['nominal'] = preg_replace('/[^0-9]/', '', $data['nominal']);
        }

        $mastercuti->update($data);

        return redirect()->route('mastercutis.index')
            ->with('success', 'Master cuti berhasil diupdate');
    }

    /**
     * Menghapus data master cuti dari database
     */
    public function destroy(Mastercuti $mastercuti)
    {
        $mastercuti->delete();

        return redirect()->route('mastercutis.index')
            ->with('success', 'Master cuti berhasil dihapus');
    }
}
