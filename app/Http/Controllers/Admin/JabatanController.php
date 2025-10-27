<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    /**
     * Menampilkan daftar semua jabatan
     */
    public function index()
    {
        // Ambil semua data jabatan dan urutkan berdasarkan nama
        $jabatans = Jabatan::orderBy('name_jabatan')->get();
        return view('admin.jabatans.index', compact('jabatans'));
    }

    /**
     * Menampilkan form untuk membuat jabatan baru
     */
    public function create()
    {
        return view('admin.jabatans.create');
    }

    /**
     * Menyimpan data jabatan baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'name_jabatan' => 'required|string|max:255|unique:jabatans,name_jabatan',
            'gaji_pokok' => 'numeric|min:0',
            'premi' => 'numeric|min:0',
            'tunjangan_jabatan' => 'numeric|min:0',
            'uang_lembur_biasa' => 'numeric|min:0',
            'uang_lembur_libur' => 'numeric|min:0',
        ]);

        // Bersihkan format nilai uang dan simpan sebagai integer
        $data = $request->all();
        $data['gaji_pokok'] = $this->cleanMoneyFormat($request->gaji_pokok);
        $data['premi'] = $this->cleanMoneyFormat($request->premi);
        $data['tunjangan_jabatan'] = $this->cleanMoneyFormat($request->tunjangan_jabatan);
        $data['uang_lembur_biasa'] = $this->cleanMoneyFormat($request->uang_lembur_biasa);
        $data['uang_lembur_libur'] = $this->cleanMoneyFormat($request->uang_lembur_libur);

        // Simpan data jabatan ke database
        Jabatan::create($data);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('jabatans.index')
            ->with('success', 'Jabatan berhasil ditambahkan');
    }

    /**
     * Menampilkan detail jabatan tertentu
     */
    public function show(Jabatan $jabatan)
    {
        return view('admin.jabatans.show', compact('jabatan'));
    }

    /**
     * Menampilkan form untuk mengedit jabatan
     */
    public function edit(Jabatan $jabatan)
    {
        return view('admin.jabatans.edit', compact('jabatan'));
    }

    /**
     * Memperbarui data jabatan di database
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        // Validasi input dari form edit
        $request->validate([
            'name_jabatan' => 'nullable|string|max:255|unique:jabatans,name_jabatan,' . $jabatan->id,
            'gaji_pokok' => 'nullable|numeric|min:0',
            'premi' => 'nullable|numeric|min:0',
            'tunjangan_jabatan' => 'nullable|numeric|min:0',
            'uang_lembur_biasa' => 'nullable|numeric|min:0',
            'uang_lembur_libur' => 'nullable|numeric|min:0',
        ]);


        // Bersihkan format nilai uang dan simpan sebagai integer
        $data = $request->all();
        $data['gaji_pokok'] = $this->cleanMoneyFormat($request->gaji_pokok);
        $data['premi'] = $this->cleanMoneyFormat($request->premi);
        $data['tunjangan_jabatan'] = $this->cleanMoneyFormat($request->tunjangan_jabatan);
        $data['uang_lembur_biasa'] = $this->cleanMoneyFormat($request->uang_lembur_biasa);
        $data['uang_lembur_libur'] = $this->cleanMoneyFormat($request->uang_lembur_libur);

        // Update data jabatan di database
        $jabatan->update($data);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('jabatans.index')
            ->with('success', 'Jabatan berhasil diupdate');
    }

    /**
     * Menghapus data jabatan dari database
     */
    public function destroy(Jabatan $jabatan)
    {
        // Check for relationships before deleting
        // For example:
        // if($jabatan->karyawans()->exists()) {
        //     return redirect()->route('jabatans.index')
        //         ->with('error', 'Hapus semua karyawan dengan jabatan ini terlebih dahulu');
        // }

        // Hapus data jabatan
        $jabatan->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('jabatans.index')
            ->with('success', 'Jabatan berhasil dihapus');
    }

    /**
     * Membersihkan format uang dengan menghapus karakter non-numerik
     *
     * @param mixed $value Nilai uang dalam format string
     * @return int Nilai uang dalam format integer
     */
    private function cleanMoneyFormat($value)
    {
        // Hapus karakter non-numerik kecuali titik desimal
        $cleanValue = preg_replace('/[^0-9.]/', '', $value);

        // Konversi ke integer
        return (int) $cleanValue;
    }
}
