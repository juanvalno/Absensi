<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\JadwalKerja;
use App\Models\MesinAbsensi;
use App\Services\AttendanceService;
use App\Jobs\ProcessAttendanceData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Menampilkan daftar semua absensi
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $departemen_id = $request->input('departemen_id');
        $bagian_id = $request->input('bagian_id');
        $status_karyawan = $request->input('status_karyawan');
        $search = $request->input('search');

        // Query builder with relationships
        $absensis = Absensi::with(['karyawan.departemen', 'karyawan.bagian', 'jadwalKerja', 'mesinAbsensiMasuk', 'mesinAbsensiPulang'])
            ->when($tanggal, function ($query) use ($tanggal) {
                return $query->where('tanggal', $tanggal);
            })
            ->when($departemen_id, function ($query) use ($departemen_id) {
                return $query->whereHas('karyawan', function ($q) use ($departemen_id) {
                    $q->where('id_departemen', $departemen_id);
                });
            })
            ->when($bagian_id, function ($query) use ($bagian_id) {
                return $query->whereHas('karyawan', function ($q) use ($bagian_id) {
                    $q->where('id_bagian', $bagian_id);
                });
            })
            ->when($status_karyawan, function ($query) use ($status_karyawan) {
                return $query->whereHas('karyawan', function ($q) use ($status_karyawan) {
                    $q->where('statuskaryawan', $status_karyawan);
                });
            })
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('karyawan', function ($q) use ($search) {
                    $q->where('nama_karyawan', 'like', '%' . $search . '%')
                        ->orWhere('nik_karyawan', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        // Automatically recalculate and fix any incorrect total_jam values
        $this->recalculateAndFixTotalJam($absensis);

        // Get data for filter dropdowns
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        $departemens = \App\Models\Departemen::orderBy('name_departemen')->get();
        $statusKaryawanOptions = ['Harian', 'Borongan', 'Bulanan'];

        return view('admin.absensis.index', compact(
            'absensis',
            'tanggal',
            'karyawans',
            'departemens',
            'statusKaryawanOptions',
            'departemen_id',
            'bagian_id',
            'status_karyawan',
            'search'
        ));
    }

    /**
     * Menampilkan form untuk membuat absensi baru
     */
    public function create()
    {
        // Siapkan data untuk dropdown di form
        $karyawans           = Karyawan::orderBy('nama_karyawan')->get();
        $jadwalKerjas        = JadwalKerja::all();
        $mesinAbsensis       = MesinAbsensi::where('status_aktif', 1)->get();
        $statusOptions       = ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Cuti', 'Libur'];
        $jenisAbsensiOptions = ['Manual', 'Mesin'];

        return view('admin.absensis.create', compact('karyawans', 'jadwalKerjas', 'mesinAbsensis', 'statusOptions', 'jenisAbsensiOptions'));
    }



    /**
     * Menyimpan data absensi baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'karyawan_id'            => 'required|exists:karyawans,id',
            'tanggal'                => 'required|date',
            'jadwalkerja_id'         => 'required|exists:jadwalkerjas,id',
            'jam_masuk'              => 'nullable|date_format:H:i',
            'jam_pulang'             => 'nullable|date_format:H:i',
            'status'                 => 'required|in:Hadir,Terlambat,Izin,Sakit,Cuti,Libur',
            'jenis_absensi_masuk'    => 'required|in:Manual,Mesin',
            'mesinabsensi_masuk_id'  => 'nullable|required_if:jenis_absensi_masuk,Mesin|exists:mesinabsensis,id',
            'jenis_absensi_pulang'   => 'required|in:Manual,Mesin',
            'mesinabsensi_pulang_id' => 'nullable|required_if:jenis_absensi_pulang,Mesin|exists:mesinabsensis,id',
            'keterlambatan'          => 'nullable|integer|min:0',
            'pulang_awal'            => 'nullable|integer|min:0',
            'keterangan'             => 'nullable|string',
        ]);

        // Check if the date is a holiday
        $tanggal   = Carbon::parse($request->tanggal);
        $hariLibur = \App\Models\HariLibur::where('tanggal', $tanggal->format('Y-m-d'))->first();

        if ($hariLibur) {
            // If it's a holiday, automatically set status to "Libur"
            $request->merge([
                'status'        => 'Libur',
                'keterangan'    => 'Hari Libur: ' . $hariLibur->nama_libur,
                'jam_masuk'     => null,
                'jam_pulang'    => null,
                'keterlambatan' => 0,
                'pulang_awal'   => 0,
                'total_jam'     => 0
            ]);
        } else {
            // Hitung total jam kerja jika ada jam masuk dan jam pulang
            if ($request->jam_masuk && $request->jam_pulang) {
                $totalJam = $this->calculateTotalJam($request->jam_masuk, $request->jam_pulang);
                $request->merge(['total_jam' => $totalJam]);
            }
        }

        // Simpan data absensi ke database
        Absensi::create($request->all());

        // Redirect ke halaman index dengan pesan sukses
    return redirect()->route('absensis.index')
            ->with('success', 'Absensi berhasil ditambahkan' . ($hariLibur ? ' (Hari Libur: ' . $hariLibur->nama_libur . ')' : ''));
    }

    /**
     * Menampilkan detail absensi tertentu
     */
    public function show(Absensi $absensi)
    {
        // Muat relasi yang dibutuhkan untuk detail absensi
        $absensi->load(['karyawan', 'jadwalKerja', 'mesinAbsensiMasuk', 'mesinAbsensiPulang']);
        return view('admin.absensis.show', compact('absensi'));
    }

    /**
     * Menampilkan form untuk mengedit absensi
     */
    public function edit(Absensi $absensi)
    {
        // Siapkan data untuk dropdown di form edit
        $karyawans           = Karyawan::orderBy('nama_karyawan')->get();
        $jadwalKerjas        = JadwalKerja::all();
        $mesinAbsensis       = MesinAbsensi::where('status_aktif', 1)->get();
        $statusOptions       = ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Cuti', 'Libur'];
        $jenisAbsensiOptions = ['Manual', 'Mesin'];

        return view('admin.absensis.edit', compact('absensi', 'karyawans', 'jadwalKerjas', 'mesinAbsensis', 'statusOptions', 'jenisAbsensiOptions'));
    }

    /**
     * Memperbarui data absensi di database
     */
    public function update(Request $request, Absensi $absensi)
    {
        // Validasi input dari form edit
        $request->validate([
            'karyawan_id'            => 'required|exists:karyawans,id',
            'tanggal'                => 'required|date',
            'jadwalkerja_id'         => 'required|exists:jadwalkerjas,id',
            'jam_masuk'              => 'nullable|date_format:H:i',
            'jam_pulang'             => 'nullable|date_format:H:i',
            'status'                 => 'required|in:Hadir,Terlambat,Izin,Sakit,Cuti,Libur',
            'jenis_absensi_masuk'    => 'required|in:Manual,Mesin',
            'mesinabsensi_masuk_id'  => 'nullable|required_if:jenis_absensi_masuk,Mesin|exists:mesinabsensis,id',
            'jenis_absensi_pulang'   => 'required|in:Manual,Mesin',
            'mesinabsensi_pulang_id' => 'nullable|required_if:jenis_absensi_pulang,Mesin|exists:mesinabsensis,id',
            'keterlambatan'          => 'nullable|integer|min:0',
            'pulang_awal'            => 'nullable|integer|min:0',
            'keterangan'             => 'nullable|string',
        ]);

        // Check if the date is a holiday
        $tanggal   = Carbon::parse($request->tanggal);
        $hariLibur = \App\Models\HariLibur::where('tanggal', $tanggal->format('Y-m-d'))->first();

        if ($hariLibur) {
            // If it's a holiday, automatically set status to "Libur"
            $request->merge([
                'status'        => 'Libur',
                'keterangan'    => 'Hari Libur: ' . $hariLibur->nama_libur,
                'jam_masuk'     => null,
                'jam_pulang'    => null,
                'keterlambatan' => 0,
                'pulang_awal'   => 0,
                'total_jam'     => 0
            ]);
        } else {
            // Hitung total jam kerja jika ada jam masuk dan jam pulang
            if ($request->jam_masuk && $request->jam_pulang) {
                $totalJam = $this->calculateTotalJam($request->jam_masuk, $request->jam_pulang);
                $request->merge(['total_jam' => $totalJam]);
            }
        }

        // Update data absensi
        $absensi->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
    return redirect()->route('absensis.index')
            ->with('success', 'Absensi berhasil diperbarui' . ($hariLibur ? ' (Hari Libur: ' . $hariLibur->nama_libur . ')' : ''));
    }


    /**
     * Menghapus data absensi dari database
     */
    public function destroy(Absensi $absensi)
    {
        // Hapus data absensi
        $absensi->delete();

        // Redirect ke halaman index dengan pesan sukses
    return redirect()->route('absensis.index')
            ->with('success', 'Absensi berhasil dihapus');
    }

    /**
     * Check if an employee has a work schedule for the given date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSchedule(Request $request)
    {
        $karyawanId = $request->input('karyawan_id');
        $tanggal    = $request->input('tanggal');

        // Check if the employee has a schedule for this date
        $jadwal = Jadwalkerja::where('karyawan_id', $karyawanId)
            ->where('tanggal', $tanggal)
            ->first();

        if ($jadwal) {
            return response()->json([
                'has_schedule' => true,
                'jadwal_id'    => $jadwal->id
            ]);
        }

        return response()->json([
            'has_schedule' => false
        ]);
    }

    /**
     * Menampilkan formulir untuk mengambil data dari mesin absensi
     */
    public function showFetchForm()
    {
        $machines = MesinAbsensi::where('status_aktif', 1)->get();
        return view('admin.absensis.fetch', compact('machines'));
    }

    /**
     * Mengambil data dari mesin absensi secara manual
     */
    public function fetchData(Request $request)
    {
        $request->validate([
            'mesin_id' => 'required|exists:mesinabsensis,id'
        ]);

        $machine = MesinAbsensi::findOrFail($request->mesin_id);

        try {
            // Menggunakan service untuk mengambil data
            $attendanceData = $this->attendanceService->fetchDataFromMachine($machine);

            if (!$attendanceData) {
                return redirect()->back()->with('error', 'Gagal mengambil data dari mesin. Periksa koneksi.');
            }

            // Proses data dan simpan ke database
            $processed = $this->processAttendanceData($attendanceData, $machine);

            return redirect()->route('absensis.index')->with('success', "Berhasil mengambil {$processed} data absensi dari mesin {$machine->nama}");
        } catch (\Exception $e) {
            Log::error("Error fetching attendance data: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Proses data dari mesin absensi
     */
    private function processAttendanceData($attendanceData, $machine)
    {
        $count = 0;

        foreach ($attendanceData as $data) {
            // Mencari karyawan berdasarkan PIN
            $karyawan = Karyawan::where('pin_mesin', $data['pin'])->first();

            if (!$karyawan) {
                Log::warning("Karyawan dengan PIN {$data['pin']} tidak ditemukan");
                continue;
            }

            // Format datetime dari mesin
            $attendanceTime = Carbon::parse($data['datetime']);
            $attendanceDate = $attendanceTime->format('Y-m-d');

            // Mendapatkan jadwal kerja karyawan untuk tanggal tersebut
            $jadwalKerja = JadwalKerja::where('karyawan_id', $karyawan->id)
                ->where('tanggal', $attendanceDate)
                ->first();

            if (!$jadwalKerja) {
                Log::warning("Jadwal kerja untuk karyawan {$karyawan->nama_karyawan} pada tanggal {$attendanceDate} tidak ditemukan");
                continue;
            }

            // Status dari mesin: 0 = Check-In, 1 = Check-Out
            $isCheckIn = ($data['status'] == '0');

            // Cari record absensi hari ini untuk karyawan ini
            $absensi = Absensi::where('karyawan_id', $karyawan->id)
                ->where('tanggal', $attendanceDate)
                ->first();

            if ($isCheckIn) {
                // Proses Check-In
                if (!$absensi) {
                    // Buat record absensi baru jika belum ada
                    $absensi = new Absensi([
                        'karyawan_id'           => $karyawan->id,
                        'tanggal'               => $attendanceDate,
                        'jadwalkerja_id'        => $jadwalKerja->id,
                        'jam_masuk'             => $attendanceTime->format('H:i'),
                        'jenis_absensi_masuk'   => 'Mesin',
                        'mesinabsensi_masuk_id' => $machine->id,
                        'status'                => 'Hadir'
                    ]);

                    // Hitung keterlambatan
                    $jadwalMasuk = Carbon::parse($jadwalKerja->jam_masuk);
                    if ($attendanceTime->gt($jadwalMasuk)) {
                        $keterlambatan          = $attendanceTime->diffInMinutes($jadwalMasuk);
                        $absensi->keterlambatan = $keterlambatan;

                        // Update status jika terlambat
                        if ($keterlambatan > 0) {
                            $absensi->status = 'Terlambat';
                        }
                    }

                    $absensi->save();
                    $count++;
                } elseif (!$absensi->jam_masuk) {
                    // Update absensi yang sudah ada tapi belum ada jam masuk
                    $absensi->jam_masuk             = $attendanceTime->format('H:i');
                    $absensi->jenis_absensi_masuk   = 'Mesin';
                    $absensi->mesinabsensi_masuk_id = $machine->id;

                    // Hitung keterlambatan
                    $jadwalMasuk = Carbon::parse($jadwalKerja->jam_masuk);
                    if ($attendanceTime->gt($jadwalMasuk)) {
                        $keterlambatan          = $attendanceTime->diffInMinutes($jadwalMasuk);
                        $absensi->keterlambatan = $keterlambatan;

                        // Update status jika terlambat
                        if ($keterlambatan > 0) {
                            $absensi->status = 'Terlambat';
                        }
                    }

                    $absensi->save();
                    $count++;
                }
            } else {
                // Proses Check-Out
                if (!$absensi) {
                    // Tidak normal: Check-out tanpa check-in sebelumnya
                    Log::warning("Check-out tanpa check-in untuk karyawan {$karyawan->nama_karyawan}");
                    continue;
                }

                // Update jam pulang
                $absensi->jam_pulang             = $attendanceTime->format('H:i');
                $absensi->jenis_absensi_pulang   = 'Mesin';
                $absensi->mesinabsensi_pulang_id = $machine->id;

                // Hitung pulang awal
                $jadwalPulang = Carbon::parse($jadwalKerja->jam_pulang);
                if ($attendanceTime->lt($jadwalPulang)) {
                    $pulangAwal           = $jadwalPulang->diffInMinutes($attendanceTime);
                    $absensi->pulang_awal = $pulangAwal;
                }

                // Hitung total jam kerja jika ada jam masuk
                if ($absensi->jam_masuk) {
                    $absensi->total_jam = $this->calculateTotalJam($absensi->jam_masuk, $attendanceTime);
                }

                $absensi->save();
                $count++;
            }
        }

        return $count;
    }

    /**
     * Memulai sinkronisasi realtime absensi
     */
    public function startSync()
    {
        $machines = MesinAbsensi::where('status_aktif', 1)->get();
        return view('admin.absensis.sync', compact('machines'));
    }

    /**
     * Mengambil data terbaru dari mesin absensi melalui AJAX
     */
    public function fetchLatestData()
    {
        try {
            $machines = MesinAbsensi::where('status_aktif', 1)->get();
            $result   = [];

            foreach ($machines as $machine) {
                // Ambil tanggal terakhir penarikan data (5 menit yang lalu untuk menghindari duplikasi)
                $lastFetch = Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s');

                // Ambil data terbaru dari mesin
                $attendanceData = $this->attendanceService->getNewAttendanceData($machine, $lastFetch);

                if ($attendanceData && count($attendanceData) > 0) {
                    // Proses data absensi di background
                    ProcessAttendanceData::dispatch($attendanceData, $machine->id);

                    $result[] = [
                        'machine' => $machine->nama,
                        'status'  => 'success',
                        'count'   => count($attendanceData)
                    ];
                } else {
                    $result[] = [
                        'machine' => $machine->nama,
                        'status'  => 'no_new_data',
                        'count'   => 0
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data'    => $result
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching latest attendance data: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar absensi hari ini untuk AJAX
     */
    public function getTodayAttendance()
    {
        try {
            $today = Carbon::today()->format('Y-m-d');

            $absensi = Absensi::with(['karyawan', 'jadwalKerja'])
                ->where('tanggal', $today)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'karyawan'      => $item->karyawan->nama_karyawan,
                        'jam_masuk'     => $item->jam_masuk ?: null,
                        'jam_pulang'    => $item->jam_pulang ?: null,
                        'keterlambatan' => $item->keterlambatan ? $item->keterlambatan . ' menit' : null,
                        'pulang_awal'   => $item->pulang_awal ? $item->pulang_awal . ' menit' : null,
                        'status'        => $item->status,
                    ];
                });

            return response()->json([
                'success' => true,
                'data'    => $absensi
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting today's attendance: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan ringkasan absensi hari ini untuk AJAX
     */
    public function getTodaySummary()
    {
        try {
            $today = Carbon::today()->format('Y-m-d');

            $absensi = Absensi::where('tanggal', $today)->get();

            $summary = [
                'total'         => $absensi->count(),
                'hadir'         => $absensi->where('status', 'Hadir')->count(),
                'terlambat'     => $absensi->where('status', 'Terlambat')->count(),
                'izin'          => $absensi->where('status', 'Izin')->count(),
                'sakit'         => $absensi->where('status', 'Sakit')->count(),
                'cuti'          => $absensi->where('status', 'Cuti')->count(),
                'libur'         => $absensi->where('status', 'Libur')->count(),
                'tidak_hadir'   => $absensi->whereNotIn('status', ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Cuti', 'Libur'])->count(),
                'keterlambatan' => [
                    'jumlah_karyawan' => $absensi->where('keterlambatan', '>', 0)->count(),
                    'total_menit'     => $absensi->sum('keterlambatan')
                ],
                'pulang_awal'   => [
                    'jumlah_karyawan' => $absensi->where('pulang_awal', '>', 0)->count(),
                    'total_menit'     => $absensi->sum('pulang_awal')
                ],
                'lembur'        => [
                    'jumlah_karyawan' => $absensi->where('total_jam', '>', 8)->count(),
                    'total_jam'       => $absensi->where('total_jam', '>', 8)->sum(function ($item) {
                        return $item->total_jam > 8 ? $item->total_jam - 8 : 0;
                    })
                ]
            ];

            return response()->json([
                'success' => true,
                'data'    => $summary
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting today's summary: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan rekap absensi harian
     */
    public function dailyReport(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));

        $absensi = Absensi::with(['karyawan', 'jadwalKerja'])
            ->where('tanggal', $tanggal)
            ->get();

        $summary = [
            'total'     => $absensi->count(),
            'hadir'     => $absensi->where('status', 'Hadir')->count(),
            'terlambat' => $absensi->where('status', 'Terlambat')->count(),
            'izin'      => $absensi->where('status', 'Izin')->count(),
            'sakit'     => $absensi->where('status', 'Sakit')->count(),
            'cuti'      => $absensi->where('status', 'Cuti')->count(),
        ];

        return view('admin.absensis.daily-report', compact('absensi', 'tanggal', 'summary'));
    }

    /**
     * Menampilkan rekap absensi per karyawan
     */
    public function employeeReport(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan'       => 'required|date_format:Y-m',
        ]);

        $karyawanId = $request->karyawan_id;
        $bulan      = $request->bulan;

        $karyawan = Karyawan::findOrFail($karyawanId);

        // Parse bulan dan tahun dari input
        $tahun    = substr($bulan, 0, 4);
        $bulanNum = substr($bulan, 5, 2);

        $absensi = Absensi::with(['jadwalKerja'])
            ->where('karyawan_id', $karyawanId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulanNum)
            ->orderBy('tanggal')
            ->get();

        $summary = [
            'total_hari'         => $absensi->count(),
            'total_jam'          => $absensi->sum('total_jam'),
            'total_terlambat'    => $absensi->where('keterlambatan', '>', 0)->count(),
            'durasi_terlambat'   => $absensi->sum('keterlambatan'),
            'total_pulang_awal'  => $absensi->where('pulang_awal', '>', 0)->count(),
            'durasi_pulang_awal' => $absensi->sum('pulang_awal'),
        ];

        return view('admin.absensis.employee-report', compact('karyawan', 'absensi', 'bulan', 'summary'));
    }

    /**
     * Membuat absensi otomatis untuk semua karyawan pada hari libur
     * (Versi global - tidak membuat record individual)
     *
     * @param int $hariLiburId
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Menampilkan hari libur tanpa membuat record di database
     *
     * @param int $hariLiburId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createAbsensiForHoliday($hariLiburId)
    {
        $hariLibur = \App\Models\HariLibur::findOrFail($hariLiburId);
        $karyawans = Karyawan::whereNull('tahun_keluar')->get();
        $count     = 0;

        foreach ($karyawans as $karyawan) {
            // Check if attendance already exists for this employee on this date
            $existingAbsensi = Absensi::where('karyawan_id', $karyawan->id)
                ->where('tanggal', $hariLibur->tanggal)
                ->first();

            if (!$existingAbsensi) {
                try {
                    // Create attendance record for holiday
                    Absensi::create([
                        'karyawan_id'          => $karyawan->id,
                        'tanggal'              => $hariLibur->tanggal,
                        'jadwalkerja_id'       => null,
                        'status'               => 'Libur', // Make sure this matches exactly with your enum values
                        'keterangan'           => 'Hari Libur: ' . $hariLibur->nama_libur,
                        'jenis_absensi_masuk'  => 'Manual',
                        'jenis_absensi_pulang' => 'Manual',
                        'jam_masuk'            => null,
                        'jam_pulang'           => null,
                        'keterlambatan'        => 0,
                        'pulang_awal'          => 0,
                        'total_jam'            => 0
                    ]);

                    $count++;
                } catch (\Exception $e) {
                    Log::error("Error creating holiday attendance for employee {$karyawan->id}: " . $e->getMessage());
                    continue;
                }
            }
        }

        // Jika dipanggil dari halaman detail, lakukan redirect
        if (request()->routeIs('admin.hariliburs.show')) {
            return redirect()->route('admin.hariliburs.show', $hariLibur)
                ->with('success', "Berhasil membuat {$count} absensi untuk hari libur ini.");
        }

        // Jika dipanggil dari store(), kembalikan count
        return $count;
    }


    /**
     * Proses checkout/absensi pulang untuk karyawan
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'jam_pulang' => 'required|date_format:H:i',
        ]);

        // Find today's attendance record for this employee
        $today = Carbon::today()->format('Y-m-d');
        $absensi = Absensi::where('karyawan_id', $request->karyawan_id)
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) {
            return redirect()->back()->with('error', 'Tidak ditemukan absensi masuk untuk hari ini');
        }

        // Update checkout information
        $absensi->jam_pulang = $request->jam_pulang;
        $absensi->jenis_absensi_pulang = 'Manual';

        if ($request->has('mesinabsensi_pulang_id')) {
            $absensi->mesinabsensi_pulang_id = $request->mesinabsensi_pulang_id;
            $absensi->jenis_absensi_pulang = 'Mesin';
        }

        // Calculate early departure if applicable
        if ($absensi->jadwalKerja) {
            $jadwalPulang = Carbon::parse($absensi->jadwalKerja->jam_pulang);
            $actualPulang = Carbon::parse($request->jam_pulang);

            if ($actualPulang->lt($jadwalPulang)) {
                $pulangAwal = $jadwalPulang->diffInMinutes($actualPulang);
                $absensi->pulang_awal = $pulangAwal;
            }
        }

        // Calculate total hours if check-in exists
        if ($absensi->jam_masuk) {
            $absensi->total_jam = $this->calculateTotalJam($absensi->jam_masuk, $request->jam_pulang);
        }

        $absensi->save();

    return redirect()->route('absensis.index')
            ->with('success', 'Absensi pulang berhasil dicatat');
    }

    /**
     * Helper method to calculate total_jam correctly
     */
    private function calculateTotalJam($jamMasuk, $jamPulang)
    {
        if (!$jamMasuk || !$jamPulang) {
            return 0;
        }

        try {
            $jamMasukCarbon = Carbon::parse($jamMasuk);
            $jamPulangCarbon = Carbon::parse($jamPulang);

            // Handle overnight shifts
            if ($jamPulangCarbon->lt($jamMasukCarbon)) {
                $jamPulangCarbon->addDay();
            }

            $diffMinutes = $jamMasukCarbon->diffInMinutes($jamPulangCarbon);
            return round($diffMinutes / 60, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating total_jam: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Recalculate and fix total_jam for attendance records
     */
    private function recalculateAndFixTotalJam($attendances)
    {
        foreach ($attendances as $attendance) {
            if ($attendance->jam_masuk && $attendance->jam_pulang) {
                $correctTotalJam = $this->calculateTotalJam($attendance->jam_masuk, $attendance->jam_pulang);
                
                // Only update if the value is different
                if ($attendance->total_jam != $correctTotalJam) {
                    $attendance->update(['total_jam' => $correctTotalJam]);
                }
            }
        }
    }
}
