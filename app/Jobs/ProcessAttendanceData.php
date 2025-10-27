<?php

namespace App\Jobs;

use App\Models\Absensi;
use App\Models\Jadwalkerja;
use App\Models\Karyawan;
use App\Models\Mesinabsensi;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAttendanceData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attendanceData;
    protected $machineId;

    /**
     * Create a new job instance.
     *
     * @param array $attendanceData
     * @param int $machineId
     */
    public function __construct(array $attendanceData, int $machineId)
    {
        $this->attendanceData = $attendanceData;
        $this->machineId = $machineId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $machine = Mesinabsensi::find($this->machineId);

        if (!$machine) {
            Log::error("Machine with ID {$this->machineId} not found");
            return;
        }

        $count = 0;

        foreach ($this->attendanceData as $data) {
            // Mencari karyawan berdasarkan PIN
            $karyawan = Karyawan::where('pin_mesin', $data['pin'])->first();

            if (!$karyawan) {
                Log::warning("Karyawan dengan PIN {$data['pin']} tidak ditemukan");
                continue;
            }

            try {
                // Format datetime dari mesin
                $attendanceTime = Carbon::parse($data['datetime']);
                $attendanceDate = $attendanceTime->format('Y-m-d');

                // Mendapatkan jadwal kerja karyawan untuk tanggal tersebut
                $jadwalKerja = Jadwalkerja::where('karyawan_id', $karyawan->id)
                    ->where('tanggal', $attendanceDate)
                    ->first();

                if (!$jadwalKerja) {
                    Log::warning("Jadwal kerja untuk karyawan {$karyawan->nama} pada tanggal {$attendanceDate} tidak ditemukan");
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
                            'karyawan_id' => $karyawan->id,
                            'tanggal' => $attendanceDate,
                            'jadwalkerja_id' => $jadwalKerja->id,
                            'jam_masuk' => $attendanceTime,
                            'jenis_absensi_masuk' => 'mesin',
                            'mesinabsensi_masuk_id' => $machine->id,
                            'status' => 'hadir'
                        ]);

                        // Hitung keterlambatan
                        $jadwalMasuk = Carbon::parse($jadwalKerja->jam_masuk);
                        if ($attendanceTime->gt($jadwalMasuk)) {
                            $keterlambatan = $attendanceTime->diffInMinutes($jadwalMasuk);
                            $absensi->keterlambatan = $keterlambatan;

                            // Update status jika terlambat
                            if ($keterlambatan > 0) {
                                $absensi->status = 'terlambat';
                            }
                        }

                        $absensi->save();
                        $count++;
                    } elseif (!$absensi->jam_masuk) {
                        // Update absensi yang sudah ada tapi belum ada jam masuk
                        $absensi->jam_masuk = $attendanceTime;
                        $absensi->jenis_absensi_masuk = 'mesin';
                        $absensi->mesinabsensi_masuk_id = $machine->id;

                        // Hitung keterlambatan
                        $jadwalMasuk = Carbon::parse($jadwalKerja->jam_masuk);
                        if ($attendanceTime->gt($jadwalMasuk)) {
                            $keterlambatan = $attendanceTime->diffInMinutes($jadwalMasuk);
                            $absensi->keterlambatan = $keterlambatan;

                            // Update status jika terlambat
                            if ($keterlambatan > 0) {
                                $absensi->status = 'terlambat';
                            }
                        }

                        $absensi->save();
                        $count++;
                    }
                } else {
                    // Proses Check-Out
                    if (!$absensi) {
                        // Tidak normal: Check-out tanpa check-in sebelumnya
                        Log::warning("Check-out tanpa check-in untuk karyawan {$karyawan->nama}");
                        continue;
                    }

                    // Update jam pulang
                    $absensi->jam_pulang = $attendanceTime;
                    $absensi->jenis_absensi_pulang = 'mesin';
                    $absensi->mesinabsensi_pulang_id = $machine->id;

                    // Hitung pulang awal
                    $jadwalPulang = Carbon::parse($jadwalKerja->jam_pulang);
                    if ($attendanceTime->lt($jadwalPulang)) {
                        $pulangAwal = $jadwalPulang->diffInMinutes($attendanceTime);
                        $absensi->pulang_awal = $pulangAwal;
                    }

                    // Hitung total jam kerja jika ada jam masuk
                    if ($absensi->jam_masuk) {
                        $jamMasuk = Carbon::parse($absensi->jam_masuk);
                        $totalJam = $jamMasuk->diffInMinutes($attendanceTime) / 60;
                        $absensi->total_jam = round($totalJam, 2);
                    }

                    $absensi->save();
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error("Error processing attendance for {$karyawan->nama}: " . $e->getMessage());
            }
        }

        Log::info("Processed {$count} attendance records from machine {$machine->nama}");
    }
}
