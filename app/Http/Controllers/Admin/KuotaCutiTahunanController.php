<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KuotaCutiTahunan;
use App\Models\Karyawan;
use App\Models\CutiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuotaCutiTahunanController extends Controller
{
    public function index()
    {
        $kuotaCuti = KuotaCutiTahunan::with(['cutiKaryawan', 'karyawan'])
            ->select([
                'kuota_cuti_tahunans.*',
                'karyawans.nama_karyawan',
                'karyawans.statuskaryawan'
            ])
            ->join('karyawans', 'kuota_cuti_tahunans.karyawan_id', '=', 'karyawans.id')
            ->orderBy('kuota_cuti_tahunans.tahun', 'desc')
            ->orderBy('karyawans.nama_karyawan')
            ->get();

        // Get unique years from kuota_cuti_tahunans table
        $years = KuotaCutiTahunan::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->map(function ($year) {
                return ['year' => $year, 'count' => KuotaCutiTahunan::where('tahun', $year)->count()];
            })
            ->pluck('count', 'year');

        $allKaryawan = Karyawan::where('statuskaryawan', 'Bulanan')
            ->orderBy('nama_karyawan')
            ->get();
        $departemens = \App\Models\Departemen::orderBy('name_departemen')->get();

        return view('admin.kuota-cuti.index', compact('kuotaCuti', 'allKaryawan', 'years', 'departemens'));
    }

    public function create()
    {
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();
        $tahun = date('Y');

        return view('admin.kuota-cuti.create', compact('karyawan', 'tahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tahun' => 'required|integer|min:2000|max:2100',
            'kuota_awal' => 'required|integer|min:0|max:12',
        ]);

        // Cek apakah sudah ada kuota untuk karyawan dan tahun yang sama
        $exists = KuotaCutiTahunan::where('karyawan_id', $request->karyawan_id)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kuota cuti untuk karyawan dan tahun tersebut sudah ada');
        }

        // Buat kuota cuti baru
        KuotaCutiTahunan::create([
            'karyawan_id' => $request->karyawan_id,
            'tahun' => $request->tahun,
            'kuota_awal' => $request->kuota_awal,
            'kuota_digunakan' => 0,
            'kuota_sisa' => $request->kuota_awal,
        ]);

        return redirect()->route('kuota-cuti.index')
            ->with('success', 'Kuota cuti tahunan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kuotaCuti = KuotaCutiTahunan::findOrFail($id);
        $karyawan = Karyawan::orderBy('nama_karyawan')->get();

        return view('admin.kuota-cuti.edit', compact('kuotaCuti', 'karyawan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kuota_awal' => 'required|integer|min:0|max:12',
        ]);

        $kuotaCuti = KuotaCutiTahunan::findOrFail($id);

        // Hitung selisih kuota awal baru dengan yang lama
        $selisih = $request->kuota_awal - $kuotaCuti->kuota_awal;

        // Update kuota awal dan kuota sisa
        $kuotaCuti->kuota_awal = $request->kuota_awal;
        $kuotaCuti->kuota_sisa = $kuotaCuti->kuota_sisa + $selisih;
        $kuotaCuti->save();

        return redirect()->route('kuota-cuti.index')
            ->with('success', 'Kuota cuti tahunan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kuotaCuti = KuotaCutiTahunan::findOrFail($id);

        // Cek apakah kuota cuti sudah digunakan
        if ($kuotaCuti->kuota_digunakan > 0) {
            return redirect()->route('kuota-cuti.index')
                ->with('error', 'Tidak dapat menghapus kuota cuti yang sudah digunakan');
        }

        // Cek apakah ada pengajuan cuti yang terkait
        // Perbaikan query untuk mencari cuti terkait
        $cutiTerkait = CutiKaryawan::where('id_karyawan', $kuotaCuti->karyawan_id)
            ->whereYear('tanggal_mulai_cuti', $kuotaCuti->tahun)
            ->exists();

        if ($cutiTerkait) {
            return redirect()->route('kuota-cuti.index')
                ->with('error', 'Tidak dapat menghapus kuota cuti yang memiliki pengajuan cuti terkait');
        }

        $kuotaCuti->delete();

        return redirect()->route('kuota-cuti.index')
            ->with('success', 'Kuota cuti tahunan berhasil dihapus');
    }

    public function report()
    {
        $tahunIni = date('Y');
        $tahunList = range($tahunIni - 5, $tahunIni + 1);
        $selectedTahun = request('tahun', $tahunIni);

        // Get all departments for the filter
        $departemens = \App\Models\Departemen::orderBy('name_departemen')->get();

        // Get status filter (default to 'Bulanan')
        $statusKaryawan = request('statuskaryawan', 'Bulanan');

        // Build the query
        $query = KuotaCutiTahunan::join('karyawans', 'kuota_cuti_tahunans.karyawan_id', '=', 'karyawans.id')
            ->join('departemens', 'karyawans.id_departemen', '=', 'departemens.id')
            ->where('tahun', $selectedTahun);

        // Apply department filter if selected
        if (request('id_departemen')) {
            $query->where('karyawans.id_departemen', request('id_departemen'));
        }

        // Apply status filter
        if ($statusKaryawan !== 'all') {
            $query->where('karyawans.statuskaryawan', $statusKaryawan);
        }

        // Get the data
        $kuotaReport = $query->select(
            'karyawans.nama_karyawan as nama_karyawan',
            'departemens.name_departemen as name_departemen',
            'kuota_cuti_tahunans.kuota_awal',
            'kuota_cuti_tahunans.kuota_digunakan',
            'kuota_cuti_tahunans.kuota_sisa',
            DB::raw('(kuota_cuti_tahunans.kuota_digunakan / NULLIF(kuota_cuti_tahunans.kuota_awal, 0) * 100) as persentase_penggunaan')
        )
            ->orderBy('karyawans.nama_karyawan')
            ->get();

        return view('admin.kuota-cuti.report', compact('kuotaReport', 'tahunList', 'selectedTahun', 'departemens'));
    }

    // Add this new method to your KuotaCutiTahunanController

    public function generateMassal(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
            'kuota_default' => 'required|integer|min:0|max:365',
            'selection_type' => 'required|in:all,department,selected',
            'department_id' => 'required_if:selection_type,department',
            'karyawan_ids' => 'required_if:selection_type,selected|array',
        ]);

        $query = Karyawan::where('statuskaryawan', 'Bulanan')
            ->whereNotExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('kuota_cuti_tahunans')
                    ->whereColumn('kuota_cuti_tahunans.karyawan_id', 'karyawans.id')
                    ->where('kuota_cuti_tahunans.tahun', $request->tahun);
            });

        // Filter based on selection type
        switch ($request->selection_type) {
            case 'department':
                $query->where('id_departemen', $request->department_id);
                break;
            case 'selected':
                $query->whereIn('id', $request->karyawan_ids);
                break;
        }

        $karyawanList = $query->get();

        // Generate quota for filtered employees
        foreach ($karyawanList as $karyawan) {
            KuotaCutiTahunan::create([
                'karyawan_id' => $karyawan->id,
                'tahun' => $request->tahun,
                'kuota_awal' => $request->kuota_default,
                'kuota_digunakan' => 0,
                'kuota_sisa' => $request->kuota_default,
            ]);
        }

        return redirect()->route('kuota-cuti.index')
            ->with('success', 'Kuota cuti berhasil di-generate untuk ' . $karyawanList->count() . ' karyawan.');
    }
}
