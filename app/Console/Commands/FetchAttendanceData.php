<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Jadwalkerja;
use App\Models\Mesinabsensi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchAttendanceData extends Command
{
    /**
     * Nama dan signature dari perintah console.
     *
     * @var string
     */
    protected $signature = 'attendance:fetch';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Fetch attendance data from biometric machines';

    /**
     * Eksekusi perintah console.
     */
    public function handle()
    {
        $this->info('Fetching attendance data from machines...');

        // Mengambil semua mesin absensi yang aktif
        $machines = Mesinabsensi::whereNull('tahun_keluar')->get();

        foreach ($machines as $machine) {
            $this->fetchDataFromMachine($machine);
        }

        $this->info('Attendance data fetch completed');

        return 0;
    }

    /**
     * Mengambil data dari mesin tertentu.
     *
     * @param \App\Models\Mesinabsensi $machine
     * @return void
     */
    private function fetchDataFromMachine($machine)
    {
        $this->info("Connecting to machine: {$machine->nama} at {$machine->ip_address}");

        try {
            // Koneksi ke mesin absensi menggunakan SOAP
            $connect = fsockopen($machine->ip_address, "80", $errno, $errstr, 1);

            if (!$connect) {
                $this->error("Failed to connect to machine: {$machine->nama}. Error: {$errstr}");
                Log::error("Failed to connect to machine: {$machine->nama}. Error: {$errstr}");
                return;
            }

            // Membuat SOAP request untuk GetAttLog
            $soap_request = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">{$machine->comm_key}</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
            $newLine = "\r\n";

            fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($connect, "Content-Type: text/xml" . $newLine);
            fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($connect, $soap_request . $newLine);

            $buffer = "";
            while ($response = fgets($connect, 1024)) {
                $buffer .= $response;
            }

            fclose($connect);

            // Parsing data
            $this->processAttendanceData($buffer, $machine);
        } catch (\Exception $e) {
            $this->error("Error processing machine {$machine->nama}: " . $e->getMessage());
            Log::error("Error processing machine {$machine->nama}: " . $e->getMessage());
        }
    }

    /**
     * Memproses data absensi dari mesin.
     *
     * @param string $buffer
     * @param \App\Models\Mesinabsensi $machine
     * @return void
     */
    private function processAttendanceData($buffer, $machine)
    {
        // Parse respons SOAP
        $buffer = $this->parseData($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
        $buffer = explode("\r\n", $buffer);

        $count = 0;

        foreach ($buffer as $row) {
            if (empty($row)) continue;

            $data = $this->parseData($row, "<Row>", "</Row>");
            if (empty($data)) continue;

            $pin = $this->parseData($data, "<PIN>", "</PIN>");
            $dateTime = $this->parseData($data, "<DateTime>", "</DateTime>");
            $verified = $this->parseData($data, "<Verified>", "</Verified>");
            $status = $this->parseData($data, "<Status>", "</Status>");

            // Mencari karyawan berdasarkan PIN (ID di mesin)
            $karyawan = Karyawan::where('pin_mesin', $pin)->first();

            if (!$karyawan) {
                $this->warn("No employee found with PIN: {$pin}");
                continue;
            }

            // Format datetime dari mesin
            $attendanceTime = Carbon::parse($dateTime);
            $attendanceDate = $attendanceTime->format('Y-m-d');

            // Mendapatkan jadwal kerja karyawan untuk tanggal tersebut
            $jadwalKerja = Jadwalkerja::where('karyawan_id', $karyawan->id)
                ->where('tanggal', $attendanceDate)
                ->first();

            if (!$jadwalKerja) {
                $this->warn("No work schedule found for employee {$karyawan->nama} on {$attendanceDate}");
                continue;
            }

            // Proses data absensi
            $this->processEmployeeAttendance($karyawan, $jadwalKerja, $attendanceTime, $status, $machine);
            $count++;
        }

        $this->info("Processed {$count} attendance records from machine {$machine->nama}");
    }

    /**
     * Memproses absensi individu karyawan.
     *
     * @param \App\Models\Karyawan $karyawan
     * @param \App\Models\Jadwalkerja $jadwalKerja
     * @param \Carbon\Carbon $attendanceTime
     * @param string $status
     * @param \App\Models\Mesinabsensi $machine
     * @return void
     */
    private function processEmployeeAttendance($karyawan, $jadwalKerja, $attendanceTime, $status, $machine)
    {
        // Status dari mesin: 0 = Check-In, 1 = Check-Out
        $isCheckIn = ($status == '0');

        // Cari record absensi hari ini untuk karyawan ini
        $absensi = Absensi::where('karyawan_id', $karyawan->id)
            ->where('tanggal', $attendanceTime->format('Y-m-d'))
            ->first();

        if ($isCheckIn) {
            // Proses Check-In
            if (!$absensi) {
                // Buat record absensi baru jika belum ada
                $absensi = new Absensi([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $attendanceTime->format('Y-m-d'),
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
                $this->info("Check-in recorded for {$karyawan->nama}");
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
                $this->info("Check-in updated for {$karyawan->nama}");
            }
        } else {
            // Proses Check-Out
            if (!$absensi) {
                // Tidak normal: Check-out tanpa check-in sebelumnya
                $this->warn("Check-out without check-in for {$karyawan->nama}");
                return;
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
            $this->info("Check-out recorded for {$karyawan->nama}");
        }
    }

    /**
     * Mengurai data dari buffer.
     *
     * @param string $data
     * @param string $start
     * @param string $end
     * @return string
     */
    private function parseData($data, $start, $end)
    {
        $data = " " . $data;
        $startPos = strpos($data, $start);
        if ($startPos === false) {
            return "";
        }

        $startPos += strlen($start);
        $endPos = strpos($data, $end, $startPos);

        if ($endPos === false) {
            return "";
        }

        return substr($data, $startPos, $endPos - $startPos);
    }
}
