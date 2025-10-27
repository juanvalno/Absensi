<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Karyawan;
use Carbon\CarbonPeriod;
use App\Models\JadwalKerja;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\FungsiKhususController;

class JadwalKerjaController extends Controller
{
    protected $fungsiKhususController;

    public function __construct(FungsiKhususController $fungsiKhususController)
    {
        $this->fungsiKhususController = $fungsiKhususController;
    }
    /**
     * Menampilkan daftar jadwal kerja dengan filter
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $query = JadwalKerja::with(['karyawan', 'shift'])->orderBy('tanggal', 'desc');

        // Validate per_page value
        $perPage = in_array($perPage, [15, 25, 50, 100]) ? $perPage : 15;

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        } else {
            // Default to current month start if no start date provided
            $defaultStart = now()->startOfMonth()->format('Y-m-d');
            $query->whereDate('tanggal', '>=', $defaultStart);
            $request->merge(['start_date' => $defaultStart]);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        } else {
            // Default to current month end if no end date provided
            $defaultEnd = now()->endOfMonth()->format('Y-m-d');
            $query->whereDate('tanggal', '<=', $defaultEnd);
            $request->merge(['end_date' => $defaultEnd]);
        }

        // Apply karyawan filter
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Apply shift filter
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('karyawan', function ($q) use ($search) {
                    $q->where('nama_karyawan', 'like', "%{$search}%")
                        ->orWhere('nik_karyawan', 'like', "%{$search}%");
                })
                    ->orWhereHas('shift', function ($q) use ($search) {
                        $q->where('kode_shift', 'like', "%{$search}%")
                            ->orWhere('nama_shift', 'like', "%{$search}%");
                    });
            });
        }

        $jadwalkerjas = $query->paginate($perPage);
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        $shifts = Shift::orderBy('kode_shift')->get();

        // Get summary counts
        $totalCount = JadwalKerja::count();
        $thisMonthCount = JadwalKerja::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
        $nextMonthCount = JadwalKerja::whereMonth('tanggal', now()->addMonth()->month)
            ->whereYear('tanggal', now()->addMonth()->year)
            ->count();

        if ($request->ajax()) {
            return response()->json([
                'data' => $jadwalkerjas->items(),
                'pagination' => [
                    'total' => $jadwalkerjas->total(),
                    'per_page' => $jadwalkerjas->perPage(),
                    'current_page' => $jadwalkerjas->currentPage(),
                    'last_page' => $jadwalkerjas->lastPage(),
                    'from' => $jadwalkerjas->firstItem(),
                    'to' => $jadwalkerjas->lastItem(),
                ]
            ]);
        }

        return view('admin.jadwalkerjas.index', compact(
            'jadwalkerjas',
            'karyawans',
            'shifts',
            'totalCount',
            'thisMonthCount',
            'nextMonthCount'
        ));
    }

    /**
     * Handle backdate functionality and return date input configuration
     *
     * @return array Configuration for date input fields
     */
    public function backdate()
    {
        // Check if the authenticated user has the 'jadwal_kerja.backdate' permission
        $backdate = auth()->check() && auth()->user()->can('jadwal_kerja.backdate');

        if ($backdate) {
            return [
                'min' => '1999-01-01', // Minimum date when backdate is enabled
                'enabled' => true,
                'min_attr' => '', // No min attribute when backdate is enabled
                'html_attr' => '' // No additional HTML attributes needed
            ];
        }

        // Get tomorrow's date instead of today
        $tomorrow = now()->addDay()->format('Y-m-d');

        return [
            'min' => $tomorrow, // Tomorrow's date as minimum
            'enabled' => false,
            'min_attr' => $tomorrow, // Set min attribute to tomorrow
            'html_attr' => 'min="' . $tomorrow . '"' // Ready-to-use HTML attribute
        ];
    }

    /**
     * Menampilkan form untuk membuat jadwal kerja baru
     */
    public function create()
    {
        // Siapkan data untuk dropdown di form
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        $shifts = Shift::orderBy('kode_shift')->get();

        // Get date input configuration from FungsiKhususController
        $dateConfig = $this->fungsiKhususController->AktifBackdate();

        return view('admin.jadwalkerjas.create', compact('karyawans', 'shifts', 'dateConfig'));
    }


    /**
     * Menyimpan data jadwal kerja baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'karyawan_id' => 'required|array',
            'karyawan_id.*' => 'exists:karyawans,id',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        // Dapatkan rentang tanggal
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_akhir);

        // Dapatkan semua tanggal dalam rentang
        $dateRange = CarbonPeriod::create($startDate, $endDate);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            $inserted = 0;
            $skipped = 0;

            // Untuk setiap tanggal dan setiap karyawan, buat jadwal kerja
            foreach ($dateRange as $date) {
                foreach ($request->karyawan_id as $karyawanId) {
                    // Cek apakah jadwal sudah ada untuk karyawan ini pada tanggal ini
                    $exists = JadwalKerja::where('tanggal', $date->format('Y-m-d'))
                        ->where('karyawan_id', $karyawanId)
                        ->exists();

                    if (!$exists) {
                        // Buat jadwal baru jika belum ada
                        JadwalKerja::create([
                            'id' => Str::uuid(),
                            'tanggal' => $date->format('Y-m-d'),
                            'karyawan_id' => $karyawanId,
                            'shift_id' => $request->shift_id,
                        ]);
                        $inserted++;
                    } else {
                        $skipped++;
                    }
                }
            }

            // Commit transaksi database
            DB::commit();

            // Tampilkan pesan sesuai hasil
            if ($skipped > 0) {
                $message = "Berhasil menambahkan $inserted jadwal kerja. $skipped jadwal dilewati karena sudah ada.";
                return redirect()->route('jadwalkerjas.index')->with('warning', $message);
            } else {
                return redirect()->route('jadwalkerjas.index')->with('success', "Berhasil menambahkan $inserted jadwal kerja.");
            }
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail jadwal kerja tertentu
     */
    public function show(JadwalKerja $jadwalkerja)
    {
        // Muat relasi yang dibutuhkan
        $jadwalkerja->load(['karyawan', 'shift']);

        return view('admin.jadwalkerjas.show', compact('jadwalkerja'));
    }

    /**
     * Menampilkan form untuk mengedit jadwal kerja
     */
    public function edit(JadwalKerja $jadwalkerja)
    {
        // Siapkan data untuk dropdown di form edit
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        $shifts = Shift::orderBy('kode_shift')->get();
        // Get date input configuration from FungsiKhususController
        $dateConfig = $this->fungsiKhususController->AktifBackdate();

        return view('admin.jadwalkerjas.edit', compact('jadwalkerja', 'karyawans', 'shifts', 'dateConfig'));
    }

    /**
     * Memperbarui data jadwal kerja di database
     */
    public function update(Request $request, JadwalKerja $jadwalkerja)
    {
        // Validasi input dari form edit
        $request->validate([
            'tanggal' => 'required|date',
            'karyawan_id' => 'required|exists:karyawans,id',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        // Cek apakah jadwal sudah ada untuk karyawan ini pada tanggal ini (kecuali jadwal saat ini)
        $exists = JadwalKerja::where('tanggal', $request->tanggal)
            ->where('karyawan_id', $request->karyawan_id)
            ->where('id', '!=', $jadwalkerja->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jadwal kerja untuk karyawan ini pada tanggal tersebut sudah ada');
        }

        // Update data jadwal kerja
        $jadwalkerja->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('jadwalkerjas.index')
            ->with('success', 'Jadwal kerja berhasil diupdate');
    }

    /**
     * Menghapus data jadwal kerja dari database
     */
    public function destroy(JadwalKerja $jadwalkerja)
    {
        // Hapus data jadwal kerja
        $jadwalkerja->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('jadwalkerjas.index')
            ->with('success', 'Jadwal kerja berhasil dihapus');
    }

    /**
     * Menampilkan laporan jadwal kerja dengan filter dan ringkasan
     */
    public function report(Request $request)
    {
        // Siapkan data untuk dropdown filter
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        $shifts = Shift::orderBy('kode_shift')->get();

        // Set nilai default untuk filter tanggal
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');
        $karyawanId = $request->karyawan_id;
        $shiftId = $request->shift_id;

        // Query jadwal kerja dengan filter
        $query = JadwalKerja::with(['karyawan', 'shift'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($karyawanId) {
            $query->where('karyawan_id', $karyawanId);
        }

        if ($shiftId) {
            $query->where('shift_id', $shiftId);
        }

        // Ambil data jadwal kerja
        $jadwalkerjas = $query->orderBy('tanggal')
            ->orderBy('karyawan_id')
            ->get();

        // Buat ringkasan statistik berdasarkan karyawan
        $summary = $jadwalkerjas->groupBy('karyawan_id')
            ->map(function ($items, $key) use ($shifts) {
                $karyawan = $items->first()->karyawan;
                $totalDays = $items->count();

                // Hitung berdasarkan shift
                $shiftCounts = $items->groupBy('shift_id')
                    ->map(function ($shiftItems) {
                        return $shiftItems->count();
                    });

                return [
                    'karyawan' => $karyawan,
                    'total_days' => $totalDays,
                    'shift_counts' => $shiftCounts
                ];
            });

        // Tampilkan view laporan dengan data
        return view('admin.jadwalkerjas.report', compact(
            'jadwalkerjas',
            'karyawans',
            'shifts',
            'startDate',
            'endDate',
            'karyawanId',
            'shiftId',
            'summary'
        ));
    }
}
