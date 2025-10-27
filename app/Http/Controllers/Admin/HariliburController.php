<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\AbsensiController;

class HariliburController extends Controller
{
    /**
     * Menampilkan daftar semua hari libur
     */
    public function index()
    {
        // Ambil semua data hari libur dan urutkan dari tanggal terbaru
        $hariliburs = Harilibur::orderBy('tanggal', 'desc')->get();
        return view('admin.hariliburs.index', compact('hariliburs'));
    }

    /**
     * Menampilkan form untuk membuat hari libur baru
     */
    public function create()
    {
        return view('admin.hariliburs.create');
    }

    /**
     * Menyimpan data hari libur baru ke database
     */
    /**
     * Menyimpan data hari libur baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'tanggal'    => 'required|date|unique:hariliburs,tanggal',
            'nama_libur' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Simpan data hari libur ke database
        $harilibur = HariLibur::create($request->all());

        // Buat absensi otomatis untuk semua karyawan pada hari libur ini
        $absensiController = app()->make('App\Http\Controllers\Admin\AbsensiController');
        $count             = $absensiController->createAbsensiForHoliday($harilibur->id);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('hariliburs.index')
            ->with('success', 'Hari Libur berhasil ditambahkan dan ' . $count . ' absensi otomatis telah dibuat');
    }

    /**
     * Menampilkan detail hari libur tertentu
     */
    public function show(Harilibur $harilibur)
    {
        return view('admin.hariliburs.show', compact('harilibur'));
    }

    /**
     * Menampilkan form untuk mengedit hari libur
     */
    public function edit(Harilibur $harilibur)
    {
        return view('admin.hariliburs.edit', compact('harilibur'));
    }

    /**
     * Memperbarui data hari libur di database
     */
    public function update(Request $request, Harilibur $harilibur)
    {
        // Validasi input dari form edit
        $request->validate([
            'tanggal'    => 'required|date|unique:hariliburs,tanggal,' . $harilibur->id,
            'nama_libur' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        // Update data hari libur di database
        $harilibur->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('hariliburs.index')
            ->with('success', 'Hari Libur berhasil diupdate');
    }

    /**
     * Menghapus data hari libur dari database
     */
    public function destroy(Harilibur $harilibur)
    {
        // Hapus data hari libur
        $harilibur->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('hariliburs.index')
            ->with('success', 'Hari Libur berhasil dihapus');
    }

    /**
     * Menampilkan form untuk generate hari Minggu dalam satu tahun
     */
    public function generateSundaysForm()
    {
        $currentYear = date('Y');
        $yearOptions = [];

        // Buat opsi tahun (tahun sekarang dan 5 tahun ke depan)
        for ($i = 0; $i < 6; $i++) {
            $year               = $currentYear + $i;
            $yearOptions[$year] = $year;
        }

        return view('admin.hariliburs.generate_sundays', compact('yearOptions', 'currentYear'));
    }

    /**
     * Generate semua hari Minggu untuk tahun yang dipilih
     */
    public function generateSundays(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'year'              => 'required|integer|min:2000|max:2099',
            'replace_existing'  => 'nullable|boolean',
        ]);
    
        $tahun          = $request->input('year');
        $gantiBukanBaru = $request->has('replace_existing');
    
        try {
            // Mulai transaksi database
            DB::beginTransaction();
    
            // Jika diminta untuk mengganti entri hari Minggu yang sudah ada
            if ($gantiBukanBaru) {
                // Hapus semua hari libur Minggu untuk tahun yang dipilih
                Harilibur::where('nama_libur', 'Hari Minggu')
                    ->whereYear('tanggal', $tahun)
                    ->delete();
            }
    
            // Cari semua hari Minggu untuk tahun yang dipilih
            $tanggalMulai = Carbon::createFromDate($tahun, 1, 1);
            $tanggalAkhir = Carbon::createFromDate($tahun, 12, 31);
    
            $hariMinggu = [];
            $tanggal    = $tanggalMulai->copy()->startOfWeek(Carbon::SUNDAY);
    
            // Jika Minggu pertama sebelum awal tahun, tambah 7 hari
            if ($tanggal->lt($tanggalMulai)) {
                $tanggal->addWeek();
            }
    
            // Buat semua hari Minggu untuk tahun tersebut
            $liburDibuat = [];
    
            // Loop through all Sundays in the year
            while ($tanggal->lte($tanggalAkhir)) {
                $hariLibur = [
                    'tanggal'    => $tanggal->format('Y-m-d'),
                    'nama_libur' => 'Hari Minggu',
                    'keterangan' => 'Hari Minggu',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
    
                // Lewati jika tanggal sudah ada dan tidak diminta untuk mengganti
                if (!$gantiBukanBaru && Harilibur::where('tanggal', $hariLibur['tanggal'])->exists()) {
                    $tanggal->addWeek();
                    continue;
                }
    
                $liburBaru     = Harilibur::create($hariLibur);
                $liburDibuat[] = $liburBaru;
    
                $tanggal->addWeek();
            }
    
            // Commit transaksi database
            DB::commit();
    
            // Redirect ke halaman index dengan pesan sukses
            $pesan = count($liburDibuat) . ' Hari Minggu untuk tahun ' . $tahun . ' berhasil dibuat';
    
            return redirect()->route('hariliburs.index')
                ->with('success', $pesan);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return redirect()->route('hariliburs.index')
                ->with('error', 'Gagal membuat Hari Minggu: ' . $e->getMessage());
        }
    }
}
