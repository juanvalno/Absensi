<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Lembur;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Potongan;
use App\Models\Harilibur;
use App\Models\Departemen;
use App\Models\Keuangan;
use App\Models\Penggajian;
use App\Models\PeriodeGaji;
use App\Models\CutiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;


class PenggajianController extends Controller
{
    /**
     * Generate payslip for multiple employees by period
     *
     * @param  int  $periodeId
     * @return \Illuminate\Http\Response
     */
    /**
     * Generate payslips for multiple employees by period
     *
     * @param  int  $periodeId
     * @param  array|null  $selectedIds
     * @param  array  $filters
     * @return \Illuminate\Http\Response
     */
    public function generatePayslips($periodeId, $selectedIds = null, $filters = [])
    {
        // Verify period exists
        $periode = PeriodeGaji::findOrFail($periodeId);

        // Build base query with relationships
        $query = Penggajian::with([
            'karyawan.departemen',
            'karyawan.bagian',
            'karyawan.jabatan',
            'karyawan.profesi',
            'periodeGaji'
        ])
            ->where('id_periode', $periodeId)
            ->where('status_verifikasi', 'Disetujui');

        // Apply selected IDs filter if provided
        if ($selectedIds) {
            $query->whereIn('id', $selectedIds);
        }

        // Apply department filter if provided
        if (!empty($filters['department'])) {
            $query->whereHas('karyawan.departemen', function ($q) use ($filters) {
                $q->where('name_departemen', $filters['department']);
            });
        }

        // Apply status filter if provided
        if (!empty($filters['status'])) {
            $query->whereHas('karyawan', function ($q) use ($filters) {
                $q->where('statuskaryawan', $filters['status']);
            });
        }

        // Get penggajian data
        $penggajians = $query->orderBy('id_karyawan')->get();

        // If no penggajian found for this period
        if ($penggajians->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada data penggajian yang sesuai dengan kriteria tersebut.');
        }

        // Process each penggajian to add attendance data
        foreach ($penggajians as $penggajian) {
            $this->processAttendanceData($penggajian, $periode);

            // Ensure JSON fields are properly decoded
            if (is_string($penggajian->detail_tunjangan)) {
                $penggajian->detail_tunjangan = json_decode($penggajian->detail_tunjangan, true) ?: [];
            }
            if (is_string($penggajian->detail_potongan)) {
                $penggajian->detail_potongan = json_decode($penggajian->detail_potongan, true) ?: [];
            }
            if (is_string($penggajian->detail_departemen)) {
                $penggajian->detail_departemen = json_decode($penggajian->detail_departemen, true) ?: [];
            }
        }

        // Return view with processed data
        return view('admin.penggajians.slip', compact('penggajians', 'periode'));
    }

    /**
     * Generate individual payslip for a single employee
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePayslip($id)
    {
        // Get single penggajian with related data
        $penggajian = \App\Models\Penggajian::with([
            'karyawan.departemen',
            'karyawan.bagian',
            'karyawan.jabatan',
            'karyawan.profesi',
            'periodeGaji'  // Changed from 'periode' to 'periodeGaji'
        ])->findOrFail($id);

        // Get the period
        $periode = $penggajian->periodeGaji;

        // Process attendance data
        $this->processAttendanceData($penggajian, $periode);

        // Create a collection with single item and pass variables needed for the view
        $penggajians = collect([$penggajian]);

        // Pass all necessary variables to the view
        return view('admin.penggajians.slip', compact('penggajians', 'periode'));
    }

    /**
     * Process attendance data for a specific penggajian
     *
     * @param Penggajian $penggajian
     * @param PeriodeGaji $periode
     * @return Penggajian
     */
    protected function processAttendanceData(Penggajian $penggajian, PeriodeGaji $periode)
    {
        // Load attendance data
        $absensi = Absensi::where('karyawan_id', $penggajian->id_karyawan)
            ->whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Load overtime data
        $lembur = Lembur::where('karyawan_id', $penggajian->id_karyawan)
            ->where('status', 'Disetujui')
            ->whereBetween('tanggal_lembur', [$periode->tanggal_mulai, $periode->tanggal_selesai])
            ->get();

        // Load leave data
        $cuti = CutiKaryawan::where('id_karyawan', $penggajian->id_karyawan)
            ->where('status_acc', 'Disetujui')
            ->where(function ($query) use ($periode) {
                $query->whereBetween('tanggal_mulai_cuti', [$periode->tanggal_mulai, $periode->tanggal_selesai])
                    ->orWhereBetween('tanggal_akhir_cuti', [$periode->tanggal_mulai, $periode->tanggal_selesai]);
            })
            ->get();

        // Calculate total days in period
        $totalHari = $periode->tanggal_mulai->diffInDays($periode->tanggal_selesai) + 1;

        // Get holidays
        $hariLibur = Harilibur::whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])
            ->pluck('tanggal')
            ->toArray();

        // Format holidays for comparison
        $hariLiburFormatted = array_map(function ($date) {
            return date('Y-m-d', strtotime($date));
        }, $hariLibur);

        // Calculate working days
        $hariKerja = $totalHari;
        $currentDate = clone $periode->tanggal_mulai;

        while ($currentDate <= $periode->tanggal_selesai) {
            $currentDateFormatted = $currentDate->format('Y-m-d');

            // If Sunday or holiday
            if ($currentDate->dayOfWeek === 0 || in_array($currentDateFormatted, $hariLiburFormatted)) {
                $hariKerja--;
            }

            $currentDate->addDay();
        }

        // Calculate attendance statistics
        $hariHadir = $absensi->where('status', 'Hadir')->count();
        $hariIzin = $absensi->where('status', 'Izin')->count();
        $hariCuti = $absensi->where('status', 'Cuti')->count();

        // Add leave days from leave table
        $izinCuti = 0;
        foreach ($cuti as $c) {
            $startDate = max($c->tanggal_mulai_cuti, $periode->tanggal_mulai);
            $endDate = min($c->tanggal_akhir_cuti, $periode->tanggal_selesai);
            $izinCuti += $startDate->diffInDays($endDate) + 1;
        }

        // Calculate absences
        $hariTidakHadir = $hariKerja - $hariHadir - $hariIzin - $hariCuti - $izinCuti;
        $hariTidakHadir = max(0, $hariTidakHadir);

        // Calculate attendance rate
        $kehadiranRate = $hariKerja > 0 ? round(($hariHadir / $hariKerja) * 100, 2) : 0;

        // Calculate total lateness in minutes
        $totalKeterlambatan = $absensi->sum('keterlambatan');

        // Format keterlambatan in days and minutes
        $hariKeterlambatan = floor($totalKeterlambatan / (60 * 8)); // Assuming 8 hours = 1 work day
        $menitKeterlambatan = $totalKeterlambatan % (60 * 8);

        // Format keterlambatan for display
        $keterlambatanFormatted = '';
        if ($hariKeterlambatan > 0) {
            $keterlambatanFormatted = $hariKeterlambatan . ' hari / ';
        }
        $keterlambatanFormatted .= $totalKeterlambatan . ' menit';

        // Calculate total early departure in minutes
        $totalPulangAwal = $absensi->sum('pulang_awal');

        // Format pulang awal in days and minutes
        $hariPulangAwal = floor($totalPulangAwal / (60 * 8)); // Assuming 8 hours = 1 work day
        $menitPulangAwal = $totalPulangAwal % (60 * 8);

        // Format pulang awal for display
        $pulangAwalFormatted = '';
        if ($hariPulangAwal > 0) {
            $pulangAwalFormatted = $hariPulangAwal . ' hari / ';
        }
        $pulangAwalFormatted .= $totalPulangAwal . ' menit';

        // Calculate overtime hours
        $totalLembur = 0;
        $lemburHariBiasa = 0;
        $lemburHariLibur = 0;

        foreach ($lembur as $item) {
            $jamMulai = \Carbon\Carbon::parse($item->jam_mulai);
            $jamSelesai = \Carbon\Carbon::parse($item->jam_selesai);
            $durasiJam = $jamSelesai->diffInHours($jamMulai);

            $totalLembur += $durasiJam;

            if ($item->jenis_lembur == 'Hari Kerja') {
                $lemburHariBiasa += $durasiJam;
            } else { // Hari Libur
                $lemburHariLibur += $durasiJam;
            }
        }

        // Store all calculated data with the penggajian object
        $penggajian->dataAbsensi = [
            'absensi' => $absensi,
            'total_hari' => $totalHari,
            'total_hari_kerja' => $hariKerja,
            'hadir' => $hariHadir,
            'izin' => $hariIzin,
            'cuti' => $hariCuti,
            'izin_cuti' => $izinCuti,
            'tidak_hadir' => $hariTidakHadir,
            'total_keterlambatan' => $totalKeterlambatan,
            'total_pulang_awal' => $totalPulangAwal,
            'total_lembur' => $totalLembur,
            'lembur_hari_biasa' => $lemburHariBiasa,
            'lembur_hari_libur' => $lemburHariLibur,
            'lembur_disetujui' => $lembur
        ];

        // Add variables needed for the slip view
        $penggajian->hariKerja = $hariKerja;
        $penggajian->hariHadir = $hariHadir;
        $penggajian->hariIzin = $hariIzin;
        $penggajian->hariCuti = $hariCuti;
        $penggajian->izinCuti = $izinCuti;
        $penggajian->hariTidakHadir = $hariTidakHadir;
        $penggajian->kehadiranRate = $kehadiranRate;
        $penggajian->keterlambatanFormatted = $keterlambatanFormatted;
        $penggajian->pulangAwalFormatted = $pulangAwalFormatted;
        $penggajian->totalLembur = $totalLembur;
        $penggajian->lemburHariBiasa = $lemburHariBiasa;
        $penggajian->lemburHariLibur = $lemburHariLibur;

        return $penggajian;
    }

    public function index()
    {
        $penggajians = Penggajian::with(['karyawan', 'periodeGaji'])->latest()->paginate(10);
        $activePeriod = PeriodeGaji::where('status', 'aktif')->first();

        // Ensure JSON fields are properly decoded for each penggajian
        foreach ($penggajians as $penggajian) {
            if (is_string($penggajian->detail_tunjangan)) {
                $penggajian->detail_tunjangan = json_decode($penggajian->detail_tunjangan, true) ?: [];
            }

            if (is_string($penggajian->detail_potongan)) {
                $penggajian->detail_potongan = json_decode($penggajian->detail_potongan, true) ?: [];
            }

            if (is_string($penggajian->detail_departemen)) {
                $penggajian->detail_departemen = json_decode($penggajian->detail_departemen, true) ?: [];
            }
        }

        return view('admin.penggajians.index', compact('penggajians', 'activePeriod'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $periodeGajis  = PeriodeGaji::where('status', 'aktif')->get();
        $departemens   = Departemen::all();
        $statusOptions = ['aktif', 'nonaktif', 'cuti'];

        return view('admin.penggajians.create', compact('periodeGajis', 'departemens', 'statusOptions'));
    }

    /**
     * Get employees based on filters that don't have payroll entries for the selected period
     */
    public function getFilteredKaryawans(Request $request)
    {
        $request->validate([
            'periode_id'    => 'required|exists:periodegajis,id',
            'departemen_id' => 'nullable|exists:departemens,id',
            'status'        => 'nullable|in:aktif,nonaktif,cuti',
        ]);

        $periodeId    = $request->periode_id;
        $departemenId = $request->departemen_id;
        $status       = $request->status;

        // Get IDs of employees who already have payroll entries for this period
        $processedKaryawanIds = Penggajian::where('id_periode', $periodeId)
            ->pluck('id_karyawan')
            ->toArray();

        // Query to get filtered employees
        $query = Karyawan::query();

        // Filter by department if specified
        if ($departemenId) {
            $query->where('id_departemen', $departemenId);
        }

        // Filter by status if specified
        if ($status) {
            $query->where('statuskaryawan', $status);
        }

        // Exclude employees who already have payroll entries for this period
        if (!empty($processedKaryawanIds)) {
            $query->whereNotIn('id', $processedKaryawanIds);
        }

        // Get employees with their related data
        $karyawans = $query->with(['departemen', 'bagian', 'jabatan', 'profesi'])->get();

        return response()->json([
            'success' => true,
            'data'    => $karyawans,
            'count'   => $karyawans->count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'periode_id'     => 'required|exists:periodegajis,id',
            'karyawan_ids'   => 'required|array',
            'karyawan_ids.*' => 'exists:karyawans,id',
        ]);

        $periodeId   = $request->periode_id;
        $karyawanIds = $request->karyawan_ids;
        $periode     = PeriodeGaji::findOrFail($periodeId);

        $count  = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($karyawanIds as $karyawanId) {
                // Load karyawan dengan relasi yang ada
                $karyawan = Karyawan::with(['jabatan', 'profesi', 'departemen', 'bagian'])->findOrFail($karyawanId);

                // Check if payroll entry already exists for this employee and period
                $exists = Penggajian::where('id_periode', $periodeId)
                    ->where('id_karyawan', $karyawanId)
                    ->exists();

                if ($exists) {
                    $errors[] = "Penggajian untuk karyawan {$karyawan->nama_karyawan} pada periode ini sudah ada.";
                    continue;
                }

                // Calculate basic salary
                $gajiPokok = $karyawan->jabatan ? $karyawan->jabatan->gaji_pokok : 0;

                // Calculate allowances
                $tunjangan       = 0;
                $detailTunjangan = [];

                // Tunjangan dari jabatan
                if ($karyawan->jabatan && $karyawan->jabatan->tunjangan_jabatan > 0) {
                    $tunjangan         += $karyawan->jabatan->tunjangan_jabatan;
                    $detailTunjangan[]  = [
                        'nama'    => 'Tunjangan Jabatan',
                        'nominal' => $karyawan->jabatan->tunjangan_jabatan
                    ];
                }

                // Tunjangan dari profesi
                if ($karyawan->profesi && $karyawan->profesi->tunjangan_profesi > 0) {
                    $tunjangan         += $karyawan->profesi->tunjangan_profesi;
                    $detailTunjangan[]  = [
                        'nama'    => 'Tunjangan Profesi',
                        'nominal' => $karyawan->profesi->tunjangan_profesi
                    ];
                }

                // For now, no deductions
                $potongan       = 0;
                $detailPotongan = [];

                // Calculate net salary
                $gajiBersih = $gajiPokok + $tunjangan - $potongan;

                // Department details
                $detailDepartemen = [
                    'departemen' => $karyawan->departemen ? $karyawan->departemen->name_departemen : null,
                    'bagian'     => $karyawan->bagian ? $karyawan->bagian->name_bagian : null,
                    'jabatan'    => $karyawan->jabatan ? $karyawan->jabatan->name_jabatan : null,
                    'profesi'    => $karyawan->profesi ? $karyawan->profesi->name_profesi : null,
                ];

                // Create payroll entry
                Penggajian::create([
                    'id'                => Str::uuid(),
                    'id_periode'        => $periodeId,
                    'id_karyawan'       => $karyawanId,
                    'periode_awal'      => $periode->tanggal_mulai,
                    'periode_akhir'     => $periode->tanggal_selesai,
                    'gaji_pokok'        => $gajiPokok,
                    'tunjangan'         => $tunjangan,
                    'detail_tunjangan'  => json_encode($detailTunjangan),
                    'potongan'          => $potongan,
                    'detail_potongan'   => json_encode($detailPotongan),
                    'detail_departemen' => json_encode($detailDepartemen),
                    'gaji_bersih'       => $gajiBersih,
                ]);

                $count++;
            }

            DB::commit();

            if ($count > 0) {
                return redirect()->route('penggajian.index')
                    ->with('success', "Berhasil membuat {$count} data penggajian.")
                    ->with('errors', $errors);
            } else {
                return redirect()->back()
                    ->with('error', "Tidak ada data penggajian yang dibuat.")
                    ->with('errors', $errors);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', "Terjadi kesalahan: " . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penggajian $penggajian)
    {
        $penggajian->load(['karyawan', 'periodeGaji']);

        // Ensure JSON fields are properly decoded
        if (is_string($penggajian->detail_tunjangan)) {
            $penggajian->detail_tunjangan = json_decode($penggajian->detail_tunjangan, true) ?: [];
        }

        if (is_string($penggajian->detail_potongan)) {
            $penggajian->detail_potongan = json_decode($penggajian->detail_potongan, true) ?: [];
        }

        if (is_string($penggajian->detail_departemen)) {
            $penggajian->detail_departemen = json_decode($penggajian->detail_departemen, true) ?: [];
        }

        return view('admin.penggajians.show', compact('penggajian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penggajian $penggajian)
    {
        $penggajian->load(['karyawan', 'periodeGaji']);

        // Ensure JSON fields are properly decoded
        if (is_string($penggajian->detail_tunjangan)) {
            $penggajian->detail_tunjangan = json_decode($penggajian->detail_tunjangan, true) ?: [];
        }

        if (is_string($penggajian->detail_potongan)) {
            $penggajian->detail_potongan = json_decode($penggajian->detail_potongan, true) ?: [];
        }

        if (is_string($penggajian->detail_departemen)) {
            $penggajian->detail_departemen = json_decode($penggajian->detail_departemen, true) ?: [];
        }

        return view('admin.penggajians.edit', compact('penggajian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penggajian $penggajian)
    {
        $request->validate([
            'gaji_pokok'       => 'required|numeric|min:0',
            'tunjangan'        => 'nullable|numeric|min:0',
            'potongan'         => 'nullable|numeric|min:0',
            'detail_tunjangan' => 'nullable|array',
            'detail_potongan'  => 'nullable|array',
        ]);

        // Process allowance details
        $totalTunjangan  = 0;
        $detailTunjangan = [];

        if ($request->has('detail_tunjangan')) {
            foreach ($request->detail_tunjangan as $tunjangan) {
                if (!empty($tunjangan['nama']) && !empty($tunjangan['nominal'])) {
                    $nominal            = floatval($tunjangan['nominal']);
                    $totalTunjangan    += $nominal;
                    $detailTunjangan[]  = [
                        'nama'    => $tunjangan['nama'],
                        'nominal' => $nominal
                    ];
                }
            }
        }

        // Process deduction details
        $totalPotongan  = 0;
        $detailPotongan = [];

        if ($request->has('detail_potongan')) {
            foreach ($request->detail_potongan as $potongan) {
                if (!empty($potongan['nama']) && !empty($potongan['nominal'])) {
                    $nominal           = floatval($potongan['nominal']);
                    $totalPotongan    += $nominal;
                    $detailPotongan[]  = [
                        'nama'    => $potongan['nama'],
                        'nominal' => $nominal
                    ];
                }
            }
        }

        // Calculate net salary
        $gajiBersih = $request->gaji_pokok + $totalTunjangan - $totalPotongan;

        $penggajian->update([
            'gaji_pokok'       => $request->gaji_pokok,
            'tunjangan'        => $totalTunjangan,
            'detail_tunjangan' => json_encode($detailTunjangan),
            'potongan'         => $totalPotongan,
            'detail_potongan'  => json_encode($detailPotongan),
            'gaji_bersih'      => $gajiBersih,
        ]);

        return redirect()->route('penggajian.index')
            ->with('success', 'Data penggajian berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penggajian $penggajian)
    {
        $penggajian->delete();
        return redirect()->route('penggajian.index')
            ->with('success', 'Data penggajian berhasil dihapus');
    }

    /**
     * Generate payroll report by period
     */
    public function reportByPeriod(Request $request)
    {
        $periodeGajis = PeriodeGaji::orderBy('tanggal_mulai', 'desc')->get();
        $periodeId    = $request->periode_id;

        $penggajians     = [];
        $selectedPeriode = null;

        if ($periodeId) {
            $selectedPeriode = PeriodeGaji::findOrFail($periodeId);
            $penggajians     = Penggajian::with(['karyawan', 'periodeGaji'])
                ->where('id_periode', $periodeId)
                ->get();
        }

        return view('admin.penggajians.report-by-period', compact('periodeGajis', 'penggajians', 'selectedPeriode'));
    }

    /**
     * Generate payroll report by department
     */
    public function reportByDepartment(Request $request)
    {
        $departemens  = Departemen::all();
        $periodeGajis = PeriodeGaji::orderBy('tanggal_mulai', 'desc')->get();

        $departemenId = $request->departemen_id;
        $periodeId    = $request->periode_id;

        $penggajians        = [];
        $selectedDepartemen = null;
        $selectedPeriode    = null;

        if ($departemenId && $periodeId) {
            $selectedDepartemen = Departemen::findOrFail($departemenId);
            $selectedPeriode    = PeriodeGaji::findOrFail($periodeId);

            // Get all employees in the department
            $karyawanIds = Karyawan::where('id_departemen', $departemenId)->pluck('id')->toArray();

            // Get payroll data for these employees in the selected period
            $penggajians = Penggajian::with(['karyawan', 'periodeGaji'])
                ->where('id_periode', $periodeId)
                ->whereIn('id_karyawan', $karyawanIds)
                ->get();
        }

        return view('admin.penggajians.report-by-department', compact(
            'departemens',
            'periodeGajis',
            'penggajians',
            'selectedDepartemen',
            'selectedPeriode'
        ));
    }

    /**
     * Add dynamic allowance or deduction to payroll
     */
    public function addComponent(Request $request, Penggajian $penggajian)
    {
        $request->validate([
            'type'    => 'required|in:tunjangan,potongan',
            'nama'    => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ]);

        $type      = $request->type;
        $component = [
            'nama'    => $request->nama,
            'nominal' => floatval($request->nominal)
        ];

        if ($type === 'tunjangan') {
            $details        = $penggajian->detail_tunjangan ?: [];
            $details[]      = $component;
            $totalTunjangan = array_sum(array_column($details, 'nominal'));

            $penggajian->update([
                'detail_tunjangan' => json_encode($details),
                'tunjangan'        => $totalTunjangan,
                'gaji_bersih'      => $penggajian->gaji_pokok + $totalTunjangan - $penggajian->potongan
            ]);

            return redirect()->back()->with('success', 'Tunjangan berhasil ditambahkan');
        } else {
            $details       = $penggajian->detail_potongan ?: [];
            $details[]     = $component;
            $totalPotongan = array_sum(array_column($details, 'nominal'));

            $penggajian->update([
                'detail_potongan' => json_encode($details),
                'potongan'        => $totalPotongan,
                'gaji_bersih'     => $penggajian->gaji_pokok + $penggajian->tunjangan - $totalPotongan
            ]);

            return redirect()->back()->with('success', 'Potongan berhasil ditambahkan');
        }
    }

    /**
     * Remove dynamic allowance or deduction from payroll
     */
    public function removeComponent(Request $request, Penggajian $penggajian)
    {
        $request->validate([
            'type'  => 'required|in:tunjangan,potongan',
            'index' => 'required|integer|min:0',
        ]);

        $type  = $request->type;
        $index = $request->index;

        if ($type === 'tunjangan') {
            $details = $penggajian->detail_tunjangan ?: [];

            if (isset($details[$index])) {
                $nominal = $details[$index]['nominal'];
                array_splice($details, $index, 1);

                $totalTunjangan = array_sum(array_column($details, 'nominal'));

                $penggajian->update([
                    'detail_tunjangan' => json_encode($details),
                    'tunjangan'        => $totalTunjangan,
                    'gaji_bersih'      => $penggajian->gaji_pokok + $totalTunjangan - $penggajian->potongan
                ]);

                return redirect()->back()->with('success', 'Tunjangan berhasil dihapus');
            }
        } else {
            $details = $penggajian->detail_potongan ?: [];

            if (isset($details[$index])) {
                $nominal = $details[$index]['nominal'];
                array_splice($details, $index, 1);

                $totalPotongan = array_sum(array_column($details, 'nominal'));

                $penggajian->update([
                    'detail_potongan' => json_encode($details),
                    'potongan'        => $totalPotongan,
                    'gaji_bersih'     => $penggajian->gaji_pokok + $penggajian->tunjangan - $totalPotongan
                ]);

                return redirect()->back()->with('success', 'Potongan berhasil dihapus');
            }
        }

        return redirect()->back()->with('error', 'Komponen tidak ditemukan');
    }




    /**
     * Batch process payroll for multiple employees
     */
    public function batchProcess(Request $request)
    {
        $request->validate([
            'periode_id'    => 'required|exists:periodegajis,id',
            'departemen_id' => 'nullable|exists:departemens,id',
            'status'        => 'nullable|in:aktif,nonaktif,cuti',
        ]);

        $periodeId    = $request->periode_id;
        $departemenId = $request->departemen_id;
        $status       = $request->status;

        // Get employees who haven't been processed yet
        $query = Karyawan::with(['jabatan', 'tunjangans', 'departemen', 'bagian', 'profesi']);

        // Filter by department if specified
        if ($departemenId) {
            $query->where('id_departemen', $departemenId);
        }

        // Filter by status if specified
        if ($status) {
            $query->where('status', $status);
        }

        // Get IDs of employees who already have payroll entries for this period
        $processedKaryawanIds = Penggajian::where('id_periode', $periodeId)
            ->pluck('id_karyawan')
            ->toArray();

        // Exclude employees who already have payroll entries
        if (!empty($processedKaryawanIds)) {
            $query->whereNotIn('id', $processedKaryawanIds);
        }

        $karyawans = $query->get();

        if ($karyawans->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada karyawan yang belum diproses untuk periode ini');
        }

        $periode = PeriodeGaji::findOrFail($periodeId);
        $count   = 0;

        DB::beginTransaction();
        try {
            foreach ($karyawans as $karyawan) {
                // Calculate basic salary
                $gajiPokok = $karyawan->jabatan ? $karyawan->jabatan->gaji_pokok : 0;

                // Calculate allowances
                $tunjangan       = 0;
                $detailTunjangan = [];

                foreach ($karyawan->tunjangans as $tunjangan_item) {
                    $tunjangan         += $tunjangan_item->nominal;
                    $detailTunjangan[]  = [
                        'nama'    => $tunjangan_item->nama,
                        'nominal' => $tunjangan_item->nominal
                    ];
                }

                // Tunjangan dari jabatan
                if ($karyawan->jabatan && $karyawan->jabatan->tunjangan_jabatan > 0) {
                    $tunjangan         += $karyawan->jabatan->tunjangan_jabatan;
                    $detailTunjangan[]  = [
                        'nama'    => 'Tunjangan Jabatan',
                        'nominal' => $karyawan->jabatan->tunjangan_jabatan
                    ];
                }

                // Tunjangan dari profesi
                if ($karyawan->profesi && $karyawan->profesi->tunjangan_profesi > 0) {
                    $tunjangan         += $karyawan->profesi->tunjangan_profesi;
                    $detailTunjangan[]  = [
                        'nama'    => 'Tunjangan Profesi',
                        'nominal' => $karyawan->profesi->tunjangan_profesi
                    ];
                }

                // For now, no deductions
                $potongan       = 0;
                $detailPotongan = [];

                // Calculate net salary
                $gajiBersih = $gajiPokok + $tunjangan - $potongan;

                // Department details
                $detailDepartemen = [
                    'departemen' => $karyawan->departemen ? $karyawan->departemen->name_departemen : null,
                    'bagian'     => $karyawan->bagian ? $karyawan->bagian->name_bagian : null,
                    'jabatan'    => $karyawan->jabatan ? $karyawan->jabatan->name_jabatan : null,
                    'profesi'    => $karyawan->profesi ? $karyawan->profesi->name_profesi : null,
                ];

                // Create payroll entry
                Penggajian::create([
                    'id'                => Str::uuid(),
                    'id_periode'        => $periodeId,
                    'id_karyawan'       => $karyawan->id,
                    'periode_awal'      => $periode->tanggal_mulai,
                    'periode_akhir'     => $periode->tanggal_selesai,
                    'gaji_pokok'        => $gajiPokok,
                    'tunjangan'         => $tunjangan,
                    'detail_tunjangan'  => json_encode($detailTunjangan),
                    'potongan'          => $potongan,
                    'detail_potongan'   => json_encode($detailPotongan),
                    'detail_departemen' => json_encode($detailDepartemen),
                    'gaji_bersih'       => $gajiBersih,
                ]);

                $count++;
            }

            DB::commit();

            return redirect()->route('penggajian.index')
                ->with('success', "Berhasil memproses {$count} data penggajian secara batch");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', "Terjadi kesalahan: " . $e->getMessage());
        }
    }


    public function review(Request $request)
    {
        $request->validate([
            'karyawan_ids' => 'required|array',
            'karyawan_ids.*' => 'exists:karyawans,id',
            'periode_id' => 'required|exists:periodegajis,id'
        ]);

        $karyawanId = $request->karyawan_ids[0];
        $periodeId = $request->periode_id;

        // Get employee data with relations
        $karyawan = Karyawan::with(['jabatan', 'profesi', 'departemen', 'bagian'])
            ->findOrFail($karyawanId);

        // Get period data
        $periode = PeriodeGaji::findOrFail($periodeId);

        // Check if payroll exists
        $exists = Penggajian::where('id_periode', $periodeId)
            ->where('id_karyawan', $karyawanId)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', "Penggajian untuk karyawan {$karyawan->nama_karyawan} pada periode ini sudah ada.");
        }

        // Get attendance data for the period
        $absensi = Absensi::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Get approved overtime data for the period
        $lembur = Lembur::where('karyawan_id', $karyawanId)
            ->where('status', 'Disetujui')
            ->whereBetween('tanggal_lembur', [$periode->tanggal_mulai, $periode->tanggal_selesai])
            ->get();

        // Get approved leave data for the period
        $cuti = CutiKaryawan::where('id_karyawan', $karyawanId)
            ->where('status_acc', 'Disetujui')
            ->where(function ($query) use ($periode) {
                $query->whereBetween('tanggal_mulai_cuti', [$periode->tanggal_mulai, $periode->tanggal_selesai])
                    ->orWhereBetween('tanggal_akhir_cuti', [$periode->tanggal_mulai, $periode->tanggal_selesai]);
            })
            ->get();

        // Calculate total days in period
        $totalHari = $periode->tanggal_mulai->diffInDays($periode->tanggal_selesai) + 1;

        // Get holidays in period
        $hariLibur = Harilibur::whereBetween('tanggal', [$periode->tanggal_mulai, $periode->tanggal_selesai])
            ->pluck('tanggal')
            ->toArray();

        // Format dates for comparison
        $hariLiburFormatted = array_map(function ($date) {
            return date('Y-m-d', strtotime($date));
        }, $hariLibur);

        // Calculate working days
        $totalHariKerja = $totalHari;
        $currentDate = clone $periode->tanggal_mulai;

        while ($currentDate <= $periode->tanggal_selesai) {
            $currentDateFormatted = $currentDate->format('Y-m-d');
            if ($currentDate->dayOfWeek === 0 || in_array($currentDateFormatted, $hariLiburFormatted)) {
                $totalHariKerja--;
            }
            $currentDate->addDay();
        }

        // Count attendance status
        $hadirCount = $absensi->where('status', 'Hadir')->count();
        $izinCount = $absensi->where('status', 'Izin')->count();
        $cutiCount = $absensi->where('status', 'Cuti')->count();

        // Add leave days from leave table
        $izinCutiCount = 0;
        foreach ($cuti as $c) {
            $startDate = max($c->tanggal_mulai_cuti, $periode->tanggal_mulai);
            $endDate = min($c->tanggal_akhir_cuti, $periode->tanggal_selesai);
            $izinCutiCount += $startDate->diffInDays($endDate) + 1;
        }

        // Calculate absences
        $tidakHadirCount = max(0, $totalHariKerja - $hadirCount - $izinCount - $cutiCount - $izinCutiCount);

        // Calculate total lateness with proper type handling
        $totalKeterlambatan = intval($absensi->sum('keterlambatan'));
        $hariKeterlambatan = intval(floor(floatval($totalKeterlambatan) / (60 * 8)));
        $keterlambatanDisplay = ($hariKeterlambatan > 0 ? $hariKeterlambatan . ' hari / ' : '') . $totalKeterlambatan . ' menit';

        // Calculate early leave with proper type handling
        $totalPulangAwal = intval($absensi->sum('pulang_awal'));
        $hariPulangAwal = intval(floor(floatval($totalPulangAwal) / (60 * 8)));
        $pulangAwalDisplay = ($hariPulangAwal > 0 ? $hariPulangAwal . ' hari / ' : '') . $totalPulangAwal . ' menit';

        // Calculate overtime with proper type handling
        $lemburHariBiasaTotal = floatval($lembur->where('jenis_lembur', 'Hari Kerja')
            ->where('status', 'Disetujui')
            ->sum(function ($item) {
                if (empty($item->lembur_disetujui)) return 0;
                $durasi = explode(':', $item->lembur_disetujui);
                $jam = isset($durasi[0]) ? intval($durasi[0]) : 0;
                $menit = isset($durasi[1]) ? intval($durasi[1]) : 0;
                return ($jam * 60) + $menit;
            }));

        $lemburHariLiburTotal = floatval($lembur->where('jenis_lembur', 'Hari Libur')
            ->where('status', 'Disetujui')
            ->sum(function ($item) {
                if (empty($item->lembur_disetujui)) return 0;
                $durasi = explode(':', $item->lembur_disetujui);
                $jam = isset($durasi[0]) ? intval($durasi[0]) : 0;
                $menit = isset($durasi[1]) ? intval($durasi[1]) : 0;
                return ($jam * 60) + $menit;
            }));

        // Convert minutes to hours and minutes format
        $lemburHariBiasaJam = intval(floor($lemburHariBiasaTotal / 60));
        $lemburHariBiasaMenit = intval($lemburHariBiasaTotal % 60);

        $lemburHariLiburJam = intval(floor($lemburHariLiburTotal / 60));
        $lemburHariLiburMenit = intval($lemburHariLiburTotal % 60);

        $totalLemburMenit = $lemburHariBiasaTotal + $lemburHariLiburTotal;
        $totalLemburJam = intval(floor($totalLemburMenit / 60));
        $totalLemburSisaMenit = intval($totalLemburMenit % 60);

        // Calculate salary components
        $gajiPokok = $karyawan->jabatan ? floatval($karyawan->jabatan->gaji_pokok) : 0;
        $potonganPerHari = $gajiPokok / 30;
        $potonganTidakHadir = round($potonganPerHari * $tidakHadirCount);
        $potonganKeterlambatan = round(25000 * ($totalKeterlambatan / 30));

        // Calculate attendance allowance
        $tunjanganKehadiran = ($tidakHadirCount > 0 || $totalKeterlambatan > 60) ? 0 : 100000;

        // Calculate overtime allowance
        $tarifLemburBiasa = $karyawan->jabatan ? floatval($karyawan->jabatan->uang_lembur_biasa) : 0;
        $tarifLemburLibur = $karyawan->jabatan ? floatval($karyawan->jabatan->uang_lembur_libur) : 0;
        $tunjanganLemburBiasa = ($lemburHariBiasaTotal / 60) * $tarifLemburBiasa;
        $tunjanganLemburLibur = ($lemburHariLiburTotal / 60) * $tarifLemburLibur;
        $totalTunjanganLembur = $tunjanganLemburBiasa + $tunjanganLemburLibur;

        // Calculate BPJS deductions
        $potonganBPJSKesehatan = round($gajiPokok * 0.01);
        $potonganBPJSKetenagakerjaan = round($gajiPokok * 0.02);

        // Prepare data for view
        $dataAbsensi = [
            'absensi' => $absensi,
            'total_hari' => $totalHari,
            'total_hari_kerja' => $totalHariKerja,
            'hadir' => $hadirCount,
            'izin' => $izinCount,
            'cuti' => $cutiCount,
            'izin_cuti' => $izinCutiCount,
            'tidak_hadir' => $tidakHadirCount,
            'total_keterlambatan' => $totalKeterlambatan,
            'keterlambatan_display' => $keterlambatanDisplay,
            'total_pulang_awal' => $totalPulangAwal,
            'pulang_awal_display' => $pulangAwalDisplay,
            'total_lembur' => sprintf('%d jam %d menit', $totalLemburJam, $totalLemburSisaMenit),
            'lembur_hari_biasa' => sprintf('%d jam %d menit', $lemburHariBiasaJam, $lemburHariBiasaMenit),
            'lembur_hari_libur' => sprintf('%d jam %d menit', $lemburHariLiburJam, $lemburHariLiburMenit),
            'tunjangan_lembur_biasa' => $tunjanganLemburBiasa,
            'tunjangan_lembur_libur' => $tunjanganLemburLibur,
            'total_tunjangan_lembur' => $totalTunjanganLembur,
            'tunjangan_kehadiran' => $tunjanganKehadiran,
            'lembur_disetujui' => $lembur
        ];

        $potonganBPJS = [
            'kesehatan' => $potonganBPJSKesehatan,
            'ketenagakerjaan' => $potonganBPJSKetenagakerjaan
        ];

        $potonganAbsensi = [
            'tidak_hadir' => $potonganTidakHadir,
            'keterlambatan' => $potonganKeterlambatan
        ];

        $dataPotongan = Potongan::all();

        return view('admin.penggajians.review', compact(
            'karyawan',
            'periode',
            'dataAbsensi',
            'potonganBPJS',
            'potonganAbsensi',
            'dataPotongan'
        ));
    }

    /**
     * Process payroll data and create entry
     * This method is modified to handle proper JSON encoding for detail fields
     */
    public function massPayslip()
    {
        $activePeriod = PeriodeGaji::where('status', 'aktif')->first();
        $departments = Departemen::all();
        $penggajians = Penggajian::with(['karyawan.departemen'])
            ->where('id_periode', $activePeriod->id)
            ->where('status_verifikasi', 'Disetujui')
            ->get();

        return view('admin.penggajians.mass-payslip', compact('activePeriod', 'departments', 'penggajians'));
    }

    public function processMassPayslip(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodegajis,id',
            'penggajian_ids' => 'required|array',
            'penggajian_ids.*' => 'exists:penggajians,id'
        ]);

        return $this->generatePayslips($request->periode_id, $request->penggajian_ids);
    }

    public function process(Request $request)
    {
        Log::info('Process method called', ['request' => $request->all()]);

        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'periode_id' => 'required|exists:periodegajis,id',
            'gaji_pokok' => 'required|numeric|min:0',
            'total_tunjangan' => 'required|numeric|min:0',
            'total_potongan' => 'required|numeric|min:0',
            'gaji_bersih' => 'required|numeric',
            'tunjangan' => 'array',
            'tunjangan.*.nama' => 'required|string',
            'tunjangan.*.nominal' => 'required|numeric|min:0',
            'potongan' => 'array',
            'potongan.*.nama' => 'required|string',
            'potongan.*.nominal' => 'required|numeric|min:0',
        ]);

        $karyawanId = $request->karyawan_id;
        $periodeId = $request->periode_id;

        // Get employee and period data
        $karyawan = Karyawan::with(['departemen', 'bagian', 'jabatan', 'profesi'])->findOrFail($karyawanId);
        $periode = PeriodeGaji::findOrFail($periodeId);

        // Check for existing payroll
        $exists = Penggajian::where('id_periode', $periodeId)
            ->where('id_karyawan', $karyawanId)
            ->exists();

        if ($exists) {
            return redirect()->route('penggajian.index')
                ->with('error', "Penggajian untuk karyawan {$karyawan->nama_karyawan} pada periode ini sudah ada.");
        }

        // Calculate base components
        $gajiPokok = $request->gaji_pokok;
        $totalTunjangan = 0;
        $totalPotongan = 0;

        // Process allowances based on employee type
        $detailTunjangan = [];
        if ($karyawan->jenis_karyawan != 'Borongan') {
            foreach ($request->tunjangan as $tunjangan) {
                if (!empty($tunjangan['nama']) && isset($tunjangan['nominal']) && $tunjangan['nominal'] > 0) {
                    // Skip attendance allowance for daily workers
                    if ($karyawan->jenis_karyawan == 'Harian' && $tunjangan['nama'] == 'Tunjangan Kehadiran') {
                        continue;
                    }
                    $detailTunjangan[] = $tunjangan;
                    $totalTunjangan += $tunjangan['nominal'];
                }
            }
        }

        // Process deductions based on employee type
        $detailPotongan = [];
        if ($karyawan->jenis_karyawan != 'Borongan') {
            foreach ($request->potongan as $potongan) {
                if (!empty($potongan['nama']) && isset($potongan['nominal']) && $potongan['nominal'] > 0) {
                    // Calculate late arrival deductions
                    if ($potongan['nama'] == 'Potongan Keterlambatan') {
                        $totalKeterlambatan = $request->total_keterlambatan ?? 0;
                        if ($totalKeterlambatan > 60) {
                            $potongan['nominal'] = ($gajiPokok + ($karyawan->profesi->tunjangan_profesi ?? 0) + ($karyawan->jabatan->tunjangan_jabatan ?? 0)) * 0.15;
                        } elseif ($totalKeterlambatan > 30) {
                            $potongan['nominal'] = ($gajiPokok + ($karyawan->profesi->tunjangan_profesi ?? 0) + ($karyawan->jabatan->tunjangan_jabatan ?? 0)) * 0.12;
                        }
                    }

                    // Calculate absence deductions
                    if ($potongan['nama'] == 'Potongan Ketidakhadiran') {
                        if ($karyawan->jenis_karyawan == 'Harian') {
                            $potongan['nominal'] = $gajiPokok * ($request->jumlah_ketidakhadiran ?? 0);
                        } else {
                            $potongan['nominal'] = (($gajiPokok + ($karyawan->profesi->tunjangan_profesi ?? 0) + ($karyawan->jabatan->tunjangan_jabatan ?? 0)) / 25) * ($request->jumlah_ketidakhadiran ?? 0);
                        }
                    }

                    $detailPotongan[] = $potongan;
                    $totalPotongan += $potongan['nominal'];
                }
            }
        }

        // Calculate net salary
        $gajiBersih = $gajiPokok + $totalTunjangan - $totalPotongan;

        // Department details
        $detailDepartemen = [
            'departemen' => $karyawan->departemen ? $karyawan->departemen->name_departemen : null,
            'bagian' => $karyawan->bagian ? $karyawan->bagian->name_bagian : null,
            'jabatan' => $karyawan->jabatan ? $karyawan->jabatan->name_jabatan : null,
            'profesi' => $karyawan->profesi ? $karyawan->profesi->name_profesi : null,
        ];

        // Create payroll entry
        DB::beginTransaction();
        try {
            Penggajian::create([
                'id' => Str::uuid(),
                'id_periode' => $periodeId,
                'id_karyawan' => $karyawanId,
                'periode_awal' => $periode->tanggal_mulai,
                'periode_akhir' => $periode->tanggal_selesai,
                'gaji_pokok' => $gajiPokok,
                'tunjangan' => $totalTunjangan,
                'detail_tunjangan' => json_encode($detailTunjangan),
                'potongan' => $totalPotongan,
                'detail_potongan' => json_encode($detailPotongan),
                'detail_departemen' => json_encode($detailDepartemen),
                'gaji_bersih' => $gajiBersih,
            ]);

            DB::commit();

            return redirect()->route('penggajian.index')
                ->with('success', "Penggajian untuk karyawan {$karyawan->nama_karyawan} berhasil diproses.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing payroll', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', "Terjadi kesalahan: " . $e->getMessage());
        }
    }

    /**
     * Generate detailed payslips with 3 per page
     */
    public function printDetailedSlips(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodegajis,id',
            'departemen_id' => 'nullable|exists:departemens,id',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'slips_per_page' => 'nullable|integer|min:1|max:3',
        ]);

        // Default to 3 slips per page if not specified
        $slipsPerPage = $request->slips_per_page ?? 3;

        // Get the period
        $periode = PeriodeGaji::findOrFail($request->periode_id);

        // Build query
        $query = Penggajian::with([
            'karyawan.departemen',
            'karyawan.bagian',
            'karyawan.jabatan',
            'karyawan.profesi',
            'periodeGaji'
        ])->where('id_periode', $request->periode_id);

        // Filter by department if specified
        if ($request->filled('departemen_id')) {
            $karyawanIds = Karyawan::where('id_departemen', $request->departemen_id)->pluck('id')->toArray();
            $query->whereIn('id_karyawan', $karyawanIds);
        }

        // Filter by specific karyawan if specified
        if ($request->filled('karyawan_id')) {
            $query->where('id_karyawan', $request->karyawan_id);
        }

        // Get penggajian data
        $penggajians = $query->orderBy('id_karyawan')->get();

        // If no data found
        if ($penggajians->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data penggajian yang sesuai dengan kriteria tersebut.');
        }

        // Process each penggajian to add attendance data
        foreach ($penggajians as $penggajian) {
            $this->processAttendanceData($penggajian, $periode);
        }

        // Pass to view - use our detailed payslip view
        return view('admin.penggajians.detailed-slip', compact('penggajians', 'periode', 'slipsPerPage'));
    }

    /**
     * Send payroll data to finance department for approval
     */
    /**
     * Mengirim data penggajian ke bagian keuangan untuk persetujuan
     * Method ini akan membuat record baru di tabel keuangan dan mengupdate status penggajian
     */
    public function sendToFinance(Request $request)
    {
        try {
            // Validasi input dari request
            $request->validate([
                'period_id' => 'required|exists:periodegajis,id',
                'total_gaji' => 'required|numeric|min:0'
            ]);

            // Ambil data periode yang dipilih
            $currentPeriod = PeriodeGaji::findOrFail($request->period_id);

            // Ambil semua data penggajian untuk periode ini yang belum diproses keuangan
            $penggajians = Penggajian::with(['karyawan.departemen'])
                ->where('id_periode', $currentPeriod->id)
                ->whereNull('id_keuangan')  // Hanya ambil yang belum diproses
                ->get();

            if ($penggajians->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data penggajian untuk periode ini.');
            }

            // Hitung total gaji bersih dari semua penggajian
            $actualTotal = $penggajians->sum('gaji_bersih');
            Log::info('Total gaji calculation:', [
                'actual_total' => $actualTotal,
                'penggajians_count' => $penggajians->count(),
                'periode' => $currentPeriod->nama_periode
            ]);

            // Format total gaji untuk memastikan angka yang valid
            $totalGaji = floatval(str_replace(',', '', $request->total_gaji));
            Log::info('Input total gaji:', [
                'total_gaji_input' => $totalGaji,
                'raw_input' => $request->total_gaji
            ]);

            // Calculate summary by department and status
            $summary = [];
            foreach ($penggajians as $penggajian) {
                $dept = strval($penggajian->karyawan->departemen->name_departemen ?? 'Tidak Ada');
                // Get status directly from database column
                $status = $penggajian->karyawan->statuskaryawan ? $penggajian->karyawan->statuskaryawan->value : 'Tidak Ada';

                if (!isset($summary[$dept])) {
                    $summary[$dept] = [
                        'total' => 0,
                        'status' => []
                    ];
                }

                if (!isset($summary[$dept]['status'][$status])) {
                    $summary[$dept]['status'][$status] = [
                        'count' => 0,
                        'total' => 0
                    ];
                }

                $summary[$dept]['status'][$status]['count']++;
                $summary[$dept]['status'][$status]['total'] += $penggajian->gaji_bersih;
                $summary[$dept]['total'] += $penggajian->gaji_bersih;
            }

            // Mulai transaksi database
            DB::beginTransaction();

            // Generate kode keuangan unik dengan format: KEU-YYYYMM-XXXX
            $kodeKeuangan = 'KEU-' . date('Ym') . '-' . str_pad(Keuangan::count() + 1, 4, '0', STR_PAD_LEFT);

            // Buat record baru di tabel keuangan
            $keuangan = Keuangan::create([
                'id' => Str::uuid(),
                'kode_keuangan' => $kodeKeuangan,
                'id_periode' => $currentPeriod->id,
                'status' => 'menunggu',
                'total_gaji' => $actualTotal,
                'summary' => $summary,
                'grand_total' => $actualTotal
            ]);

            // Update semua record penggajian yang terkait
            foreach ($penggajians as $penggajian) {
                $penggajian->update([
                    'id_keuangan' => $keuangan->id,
                    'status_keuangan' => 'menunggu'
                ]);
            }

            // Commit transaksi jika semua proses berhasil
            DB::commit();

            return redirect()->route('penggajian.index')
                ->with('success', 'Data penggajian berhasil dikirim ke bagian keuangan untuk persetujuan.');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            Log::error('Error senSding to finance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim data ke bagian keuangan: ' . $e->getMessage());
        }
    }

    public function exportHistoryExcel($id)
    {
        try {
            // Get finance data with relationships
            $keuangan = \App\Models\Keuangan::with([
                'periode',
                'penggajians.karyawan.departemen',
                'penggajians.karyawan.bagian',
                'penggajians.karyawan.jabatan',
                'penggajians.karyawan.profesi'
            ])->findOrFail($id);

            // Create new HRD Export instance
            $export = new \App\Exports\HRDExport($keuangan);

            // Generate filename
            $filename = 'Laporan_Detail_Penggajian_' . date('Ymd_His') . '.xlsx';

            // Return the download response
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        } catch (\Exception $e) {
            Log::error('Error exporting payroll history: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }

    /**
     * Convert number to Indonesian words
     *
     * @param float $number
     * @return string
     */
    private function terbilang($number)
    {
        $angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

        if ($number < 12) {
            return $angka[$number];
        } elseif ($number < 20) {
            return $this->terbilang($number - 10) . " Belas";
        } elseif ($number < 100) {
            return $this->terbilang(floor($number / 10)) . " Puluh " . $this->terbilang($number % 10);
        } elseif ($number < 200) {
            return "Seratus " . $this->terbilang($number - 100);
        } elseif ($number < 1000) {
            return $this->terbilang(floor($number / 100)) . " Ratus " . $this->terbilang($number % 100);
        } elseif ($number < 2000) {
            return "Seribu " . $this->terbilang($number - 1000);
        } elseif ($number < 1000000) {
            return $this->terbilang(floor($number / 1000)) . " Ribu " . $this->terbilang($number % 1000);
        } elseif ($number < 1000000000) {
            return $this->terbilang(floor($number / 1000000)) . " Juta " . $this->terbilang($number % 1000000);
        } elseif ($number < 1000000000000) {
            return $this->terbilang(floor($number / 1000000000)) . " Milyar " . $this->terbilang($number % 1000000000);
        } elseif ($number < 1000000000000000) {
            return $this->terbilang(floor($number / 1000000000000)) . " Trilyun " . $this->terbilang($number % 1000000000000);
        }

        return "";
    }

    private function addDepartmentSubtotal($sheet, $row, $deptName, $total)
    {
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'TOTAL ' . $deptName);
        $sheet->mergeCells('B' . $row . ':W' . $row);
        $sheet->setCellValue('X' . $row, $total);

        $sheet->getStyle('A' . $row . ':X' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ]);
    }

    private function addGrandTotal($sheet, $row, $total)
    {
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'GRAND TOTAL');
        $sheet->mergeCells('B' . $row . ':W' . $row);
        $sheet->setCellValue('X' . $row, $total);

        $sheet->getStyle('A' . $row . ':X' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C0C0C0']
            ]
        ]);
    }

    private function formatColumns($sheet, $lastRow)
    {
        // Format currency columns (O-X)
        $sheet->getStyle('O6:X' . $lastRow)->getNumberFormat()
            ->setFormatCode('#,##0');

        // Format number columns (F-N)
        $sheet->getStyle('F6:N' . $lastRow)->getNumberFormat()
            ->setFormatCode('0');
    }

    private function calculateLemburAmount($karyawan, $durasi, $jenisLembur)
    {
        if ($karyawan->jenis_karyawan == 'Borongan') {
            return 0;
        }

        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganJabatan = $karyawan->jabatan->tunjangan_jabatan ?? 0;
        $tunjanganProfesi = $karyawan->profesi->tunjangan_profesi ?? 0;

        if ($karyawan->jenis_karyawan == 'Harian') {
            // For Harian: Lembur calculation based on daily wage
            $gajiPerHari = $gajiPokok;
            if ($jenisLembur == 'Hari Libur') {
                return ($gajiPerHari / 4) * $durasi; // Divide by 4 for holiday overtime
            }
            return ($gajiPerHari / 6) * $durasi; // Divide by 6 for regular overtime
        } else {
            // For Bulanan: Lembur calculation based on total monthly salary
            $totalGajiBulanan = $gajiPokok + $tunjanganJabatan + $tunjanganProfesi;
            if ($jenisLembur == 'Hari Libur') {
                return ($totalGajiBulanan / 100) * $durasi; // Divide by 100 for holiday overtime
            }
            return ($totalGajiBulanan / 150) * $durasi; // Divide by 150 for regular overtime
        }
    }

    private function calculateKeterlambatanPotongan($karyawan, $totalKeterlambatan)
    {
        if ($karyawan->jenis_karyawan == 'Borongan') {
            return 0;
        }

        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganJabatan = $karyawan->jabatan->tunjangan_jabatan ?? 0;
        $tunjanganProfesi = $karyawan->profesi->tunjangan_profesi ?? 0;
        $totalGajiBulanan = $gajiPokok + $tunjanganJabatan + $tunjanganProfesi;

        // No deduction for less than 30 minutes per month
        if ($totalKeterlambatan <= 30) {
            return 0;
        }

        // 12% deduction for 30-60 minutes
        if ($totalKeterlambatan <= 60) {
            return $totalGajiBulanan * 0.12;
        }

        // 15% deduction for more than 60 minutes
        return $totalGajiBulanan * 0.15;
    }

    private function calculateKehadiranTunjangan($karyawan, $totalHadir)
    {
        if ($karyawan->jenis_karyawan == 'Harian' || $karyawan->jenis_karyawan == 'Borongan') {
            return 0;
        }

        // Add your logic for attendance allowance here
        return 100000; // Base attendance allowance
    }

    private function calculateKetidakhadiranPotongan($karyawan, $jumlahKetidakhadiran)
    {
        if ($karyawan->jenis_karyawan == 'Borongan') {
            return 0;
        }

        $gajiPokok = $karyawan->jabatan->gaji_pokok;
        $tunjanganJabatan = $karyawan->jabatan->tunjangan_jabatan ?? 0;
        $tunjanganProfesi = $karyawan->profesi->tunjangan_profesi ?? 0;

        if ($karyawan->jenis_karyawan == 'Harian') {
            return $gajiPokok * $jumlahKetidakhadiran; // Full day's wage per absence
        } else {
            $totalGajiBulanan = $gajiPokok + $tunjanganJabatan + $tunjanganProfesi;
            return ($totalGajiBulanan / 25) * $jumlahKetidakhadiran; // Monthly salary divided by 25 working days
        }
    }

    public function salarySubmissionHistory()
    {
        // Get all finance records with related data
        $history = Keuangan::with(['periode', 'penggajians.karyawan.departemen'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.penggajians.history', compact('history'));
    }
    public function exportAllHistoryExcel() {}
}
