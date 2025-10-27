<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\cutiKaryawan;
use Illuminate\Http\Request;
use App\Models\KuotaIzinTahunan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KuotaIzinTahunanController extends Controller
{
    public function index()
    {
        $kuotaIzin = KuotaIzinTahunan::with('karyawan')->latest()->get();
        $tahun = Carbon::now()->year;
        $karyawan = Karyawan::all();
        return view('admin.kuota-izin.index', compact('kuotaIzin', 'tahun', 'karyawan'));
    }

    public function create()
    {
        $karyawan = Karyawan::all();
        $tahun = Carbon::now()->year;
        return view('admin.kuota-izin.create', compact('karyawan', 'tahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tahun' => 'required|integer',
            'kuota_awal' => 'required|integer|min:0|max:6',
            'tanggal_expired' => 'nullable|date',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Check for existing quota
            $exists = KuotaIzinTahunan::where('karyawan_id', $request->karyawan_id)
                ->where('tahun', $request->tahun)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->with('error', 'Kuota izin untuk karyawan ini di tahun ' . $request->tahun . ' sudah ada');
            }

            $kuotaIzin = new KuotaIzinTahunan();
            $kuotaIzin->karyawan_id = $request->karyawan_id;
            $kuotaIzin->tahun = $request->tahun;
            $kuotaIzin->kuota_awal = $request->kuota_awal;
            $kuotaIzin->kuota_sisa = $request->kuota_awal;
            $kuotaIzin->tanggal_expired = $request->tanggal_expired ?? Carbon::createFromDate($request->tahun, 12, 31);
            $kuotaIzin->keterangan = $request->keterangan;
            $kuotaIzin->save();

            DB::commit();
            return redirect()->route('kuota-izin.index')->with('success', 'Kuota izin berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    public function edit(KuotaIzinTahunan $kuotaIzin)
    {
        $karyawan = Karyawan::all();
        return view('admin.kuota-izin.edit', compact('kuotaIzin', 'karyawan'));
    }

    public function update(Request $request, KuotaIzinTahunan $kuotaIzin)
    {
        $request->validate([
            'kuota_awal' => 'required|integer|min:0',
            'tanggal_expired' => 'nullable|date',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $selisihKuota = $request->kuota_awal - $kuotaIzin->kuota_awal;
            $kuotaIzin->kuota_awal = $request->kuota_awal;
            $kuotaIzin->kuota_sisa += $selisihKuota;
            $kuotaIzin->tanggal_expired = $request->tanggal_expired;
            $kuotaIzin->keterangan = $request->keterangan;
            $kuotaIzin->save();

            DB::commit();
            return redirect()->route('kuota-izin.index')->with('success', 'Kuota izin berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data');
        }
    }

    public function destroy(KuotaIzinTahunan $kuotaIzin)
    {
        try {
            $kuotaIzin->delete();
            return redirect()->route('kuota-izin.index')->with('success', 'Kuota izin berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }

    public function report()
    {
        $query = KuotaIzinTahunan::with(['karyawan', 'izinKaryawan']);

        if (request('tahun')) {
            $query->where('tahun', request('tahun'));
        }

        if (request('karyawan_id')) {
            $query->where('karyawan_id', request('karyawan_id'));
        }

        $kuotaIzin = $query->latest()->get();
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        return view('admin.kuota-izin.report', compact('kuotaIzin', 'karyawan'));
    }

    public function generateMassal(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'kuota_awal' => 'required|integer|min:0|max:6',
        ]);

        try {
            DB::beginTransaction();

            $karyawanIds = $request->input('karyawan_ids', []);
            $tahun = $request->tahun;
            $kuotaAwal = $request->kuota_awal;
            $tanggalExpired = Carbon::createFromDate($tahun, 12, 31);

            // If no specific karyawan selected, generate for all
            if (empty($karyawanIds)) {
                $karyawanIds = Karyawan::pluck('id')->toArray();
            }

            foreach ($karyawanIds as $karyawanId) {
                // Check if kuota already exists for this karyawan and year
                $exists = KuotaIzinTahunan::where('karyawan_id', $karyawanId)
                    ->where('tahun', $tahun)
                    ->exists();

                if (!$exists) {
                    KuotaIzinTahunan::create([
                        'karyawan_id' => $karyawanId,
                        'tahun' => $tahun,
                        'kuota_awal' => $kuotaAwal,
                        'kuota_sisa' => $kuotaAwal,
                        'tanggal_expired' => $tanggalExpired,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('kuota-izin.index')
                ->with('success', 'Kuota izin tahunan berhasil digenerate secara massal');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat generate kuota izin massal');
        }
    }

    public function show(KuotaIzinTahunan $kuotaIzin)
    {
        $kuotaIzin->load(['karyawan', 'cutiKaryawan']);
        return view('admin.kuota-izin.show', compact('kuotaIzin'));
    }
}
