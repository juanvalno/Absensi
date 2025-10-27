<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Harilibur;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HolidayAttendanceScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:holiday-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for holidays tomorrow and create attendance records automatically';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Tanggal untuk besok
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // Cek apakah besok ada hari libur
        $holidays = Harilibur::where('tanggal', $tomorrow)->get();

        if ($holidays->isEmpty()) {
            $this->info("Tidak ada hari libur untuk tanggal $tomorrow");
            return 0;
        }

        $this->info("Ditemukan " . $holidays->count() . " hari libur untuk tanggal $tomorrow");

        // Ambil semua karyawan aktif
        $karyawans = Karyawan::where('status', 'Aktif')->get();
        $this->info("Memproses absensi untuk " . $karyawans->count() . " karyawan aktif");

        $totalCreated = 0;

        foreach ($holidays as $holiday) {
            $created = $this->createHolidayAttendance($holiday, $karyawans);
            $totalCreated += $created;
            $this->info("Dibuat $created absensi untuk hari libur: " . $holiday->nama_libur);
        }

        $this->info("Total $totalCreated absensi berhasil dibuat");

        return 0;
    }

    /**
     * Create attendance records for a holiday
     *
     * @param \App\Models\Harilibur $holiday
     * @param \Illuminate\Database\Eloquent\Collection $karyawans
     * @return int
     */
    protected function createHolidayAttendance($holiday, $karyawans)
    {
        $count = 0;

        foreach ($karyawans as $karyawan) {
            // Cek apakah sudah ada absensi untuk karyawan ini pada tanggal tersebut
            $existingAbsensi = Absensi::where('karyawan_id', $karyawan->id)
                ->where('tanggal', $holiday->tanggal)
                ->first();

            if ($existingAbsensi) {
                continue; // Lewati jika sudah ada
            }

            try {
                // Cari jadwal kerja untuk karyawan ini
                // Prioritas:
                // 1. Jadwal khusus untuk tanggal tersebut
                // 2. Jadwal kerja yang paling sering digunakan
                // 3. Jadwal kerja pertama yang pernah dibuat

                $jadwalKerja = $this->findJadwalKerja($karyawan->id, $holiday->tanggal);

                if (!$jadwalKerja) {
                    $this->warn("Tidak ditemukan jadwal kerja untuk karyawan {$karyawan->nama_karyawan}, melewati...");
                    continue;
                }

                // Buat record absensi untuk hari libur
                Absensi::create([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $holiday->tanggal,
                    'jadwalkerja_id' => $jadwalKerja->id,
                    'status' => 'Libur',
                    'keterangan' => 'Hari Libur: ' . $holiday->nama_libur,
                    'jenis_absensi_masuk' => 'Manual',
                    'jenis_absensi_pulang' => 'Manual',
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'keterlambatan' => 0,
                    'pulang_awal' => 0,
                    'total_jam' => 0
                ]);

                $count++;
            } catch (\Exception $e) {
                Log::error("Gagal membuat absensi untuk karyawan {$karyawan->nama_karyawan} pada hari libur {$holiday->nama_libur}: " . $e->getMessage());
                $this->error("Error: " . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Find the best jadwal kerja for a karyawan
     *
     * @param string|int $karyawanId
     * @param string $tanggal
     * @return \App\Models\JadwalKerja|null
     */
    protected function findJadwalKerja($karyawanId, $tanggal)
    {
        // 1. Cek apakah ada jadwal khusus untuk tanggal ini
        $jadwal = JadwalKerja::where('karyawan_id', $karyawanId)
            ->where('tanggal', $tanggal)
            ->first();

        if ($jadwal) {
            return $jadwal;
        }

        // 2. Cari jadwal yang paling sering digunakan oleh karyawan ini berdasarkan absensi
        $mostUsedJadwal = DB::table('absensis')
            ->select('jadwalkerja_id', DB::raw('COUNT(*) as count'))
            ->where('karyawan_id', $karyawanId)
            ->groupBy('jadwalkerja_id')
            ->orderBy('count', 'desc')
            ->first();

        if ($mostUsedJadwal) {
            $jadwal = JadwalKerja::find($mostUsedJadwal->jadwalkerja_id);
            if ($jadwal) {
                return $jadwal;
            }
        }

        // 3. Ambil jadwal pertama yang pernah dibuat untuk karyawan ini
        return JadwalKerja::where('karyawan_id', $karyawanId)
            ->orderBy('created_at', 'asc')
            ->first();
    }
}
