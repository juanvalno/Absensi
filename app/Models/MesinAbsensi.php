<?php

namespace App\Models;

use App\Models\Karyawan;
use App\Models\Absensi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MesinAbsensi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'mesinabsensis';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',              // Nama mesin absensi
        'alamat_ip',         // Alamat IP mesin absensi
        'kunci_komunikasi',  // Kunci untuk komunikasi dengan mesin
        'lokasi',            // Lokasi fisik mesin absensi
        'status_aktif',      // Status aktif/nonaktif mesin
        'keterangan'         // Keterangan tambahan
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status_aktif' => 'boolean',  // Konversi status_aktif ke tipe boolean
    ];

    /**
     * Mendapatkan data absensi yang menggunakan mesin ini untuk check-in.
     */
    public function absensisMasuk()
    {
        return $this->hasMany(Absensi::class, 'mesinabsensi_masuk_id');
    }

    /**
     * Mendapatkan data absensi yang menggunakan mesin ini untuk check-out.
     */
    public function absensisPulang()
    {
        return $this->hasMany(Absensi::class, 'mesinabsensi_pulang_id');
    }

    /**
     * Mendapatkan semua karyawan yang terdaftar di mesin ini
     * Dilakukan melalui tabel pivot karyawan_mesinabsensi
     */
    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'karyawan_mesinabsensi', 'mesinabsensi_id', 'karyawan_id')
            ->withPivot('status_sync', 'sync_at')
            ->withTimestamps();
    }

    /**
     * Mendapatkan semua karyawan terdaftar yang berhasil disinkronisasi
     */
    public function syncedKaryawans()
    {
        return $this->karyawans()->wherePivot('status_sync', true);
    }

    /**
     * Mendapatkan semua karyawan terdaftar yang gagal disinkronisasi
     */
    public function failedSyncKaryawans()
    {
        return $this->karyawans()->wherePivot('status_sync', false);
    }

    /**
     * Menguji koneksi ke mesin
     *
     * @return bool|string True jika terhubung, pesan error jika tidak
     */
    public function testConnection()
    {
        $connect = @fsockopen($this->alamat_ip, "80", $errno, $errstr, 1);
        if ($connect) {
            fclose($connect);
            return true;
        }
        return "Error: " . $errstr . " (" . $errno . ")";
    }

    /**
     * Mendapatkan log absensi dari mesin
     *
     * @return array|string Array log jika berhasil, pesan error jika tidak
     */
    public function getAttendanceLogs()
    {
        $connect = @fsockopen($this->alamat_ip, "80", $errno, $errstr, 1);
        if ($connect) {
            // Membuat permintaan SOAP untuk mendapatkan log absensi
            $soap_request = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">" . $this->kunci_komunikasi . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
            $newLine = "\r\n";

            // Mengirim permintaan HTTP
            fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($connect, "Content-Type: text/xml" . $newLine);
            fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($connect, $soap_request . $newLine);

            // Membaca respons
            $buffer = "";
            while ($response = fgets($connect, 1024)) {
                $buffer = $buffer . $response;
            }
            fclose($connect);

            return $this->parseAttendanceLogs($buffer);
        }

        return "Error: Failed to connect to " . $this->alamat_ip;
    }

    /**
     * Mengunggah nama pengguna ke mesin
     *
     * @param string $userId ID pengguna
     * @param string $name Nama pengguna
     * @return string Pesan hasil
     */
    public function uploadName($userId, $name)
    {
        $connect = @fsockopen($this->alamat_ip, "80", $errno, $errstr, 1);
        if ($connect) {
            // Membuat permintaan SOAP untuk mengatur info pengguna
            $soap_request = "<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">" . $this->kunci_komunikasi . "</ArgComKey><Arg><PIN>" . $userId . "</PIN><Name>" . $name . "</Name></Arg></SetUserInfo>";
            $newLine = "\r\n";

            // Mengirim permintaan HTTP
            fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($connect, "Content-Type: text/xml" . $newLine);
            fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($connect, $soap_request . $newLine);

            // Membaca respons
            $buffer = "";
            while ($response = fgets($connect, 1024)) {
                $buffer = $buffer . $response;
            }
            fclose($connect);

            $result = $this->parseDataBetween($buffer, "<Information>", "</Information>");

            // Memperbarui status registrasi jika pengguna ada di database
            $karyawan = Karyawan::where('nik', $userId)->first();
            if ($karyawan) {
                $success = strpos($result, 'successful') !== false || strpos($result, 'berhasil') !== false;
                $karyawan->registerToMachine($this->id, $success);
            }

            return $result;
        }

        return "Error: Failed to connect to " . $this->alamat_ip;
    }

    /**
     * Menghapus pengguna dari mesin
     *
     * @param string $userId ID pengguna
     * @return string Pesan hasil
     */
    public function deleteUser($userId)
    {
        $connect = @fsockopen($this->alamat_ip, "80", $errno, $errstr, 1);
        if ($connect) {
            // Membuat permintaan SOAP untuk menghapus pengguna
            $soap_request = "<DeleteUser><ArgComKey Xsi:type=\"xsd:integer\">" . $this->kunci_komunikasi . "</ArgComKey><Arg><PIN>" . $userId . "</PIN></Arg></DeleteUser>";
            $newLine = "\r\n";

            // Mengirim permintaan HTTP
            fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($connect, "Content-Type: text/xml" . $newLine);
            fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($connect, $soap_request . $newLine);

            // Membaca respons
            $buffer = "";
            while ($response = fgets($connect, 1024)) {
                $buffer = $buffer . $response;
            }
            fclose($connect);

            $result = $this->parseDataBetween($buffer, "<Information>", "</Information>");

            // Memperbarui status registrasi jika pengguna ada di database
            $karyawan = Karyawan::where('nik', $userId)->first();
            if ($karyawan) {
                $karyawan->unregisterFromMachine($this->id);
            }

            return $result;
        }

        return "Error: Failed to connect to " . $this->alamat_ip;
    }

    /**
     * Sinkronisasi semua karyawan aktif ke mesin
     *
     * @return array Jumlah sukses dan gagal, serta hasil detail
     */
    public function syncAllKaryawans()
    {
        // Mendapatkan semua karyawan aktif
        $karyawans = Karyawan::whereNull('tahun_keluar')->get();
        $success = 0;
        $failed = 0;
        $results = [];

        foreach ($karyawans as $karyawan) {
            // Lewati karyawan tanpa NIK
            if (!$karyawan->nik) {
                $results[] = "Karyawan {$karyawan->nama_karyawan}: Tidak memiliki NIK";
                $failed++;
                continue;
            }

            // Upload nama karyawan ke mesin
            $result = $this->uploadName($karyawan->nik, $karyawan->nama_karyawan);

            // Periksa hasil upload
            if (strpos($result, 'successful') !== false || strpos($result, 'berhasil') !== false) {
                $success++;
            } else {
                $failed++;
            }

            $results[] = "Karyawan {$karyawan->nama_karyawan} (NIK: {$karyawan->nik}): {$result}";
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'results' => $results
        ];
    }

    /**
     * Memparse log absensi dari buffer
     *
     * @param string $buffer Buffer respons
     * @return array Array log absensi
     */
    private function parseAttendanceLogs($buffer)
    {
        $logs = [];
        $data = $this->parseDataBetween($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
        $rows = explode("\r\n", $data);

        foreach ($rows as $row) {
            if (!empty($row)) {
                $rowData = $this->parseDataBetween($row, "<Row>", "</Row>");
                if (!empty($rowData)) {
                    $logs[] = [
                        'pin' => $this->parseDataBetween($rowData, "<PIN>", "</PIN>"),
                        'datetime' => $this->parseDataBetween($rowData, "<DateTime>", "</DateTime>"),
                        'verified' => $this->parseDataBetween($rowData, "<Verified>", "</Verified>"),
                        'status' => $this->parseDataBetween($rowData, "<Status>", "</Status>")
                    ];
                }
            }
        }

        return $logs;
    }

    /**
     * Memparse data di antara dua string
     *
     * @param string $data Data asli
     * @param string $start Pembatas awal
     * @param string $end Pembatas akhir
     * @return string Data yang diparsing
     */
    private function parseDataBetween($data, $start, $end)
    {
        $data = " " . $data;
        $startPos = strpos($data, $start);
        if ($startPos == 0) return "";

        $startPos += strlen($start);
        $endPos = strpos($data, $end, $startPos);
        if ($endPos == 0) return "";

        return substr($data, $startPos, $endPos - $startPos);
    }

    /**
     * Mendeteksi otomatis alamat IP untuk mesin dan memperbarui jika perlu.
     *
     * @return array [success, message, old_ip, new_ip]
     */
    public function autoDetectIp()
    {
        // Segmen IP saat ini
        $currentIpParts = explode('.', $this->alamat_ip);
        $originalIp = $this->alamat_ip;

        // Jika format IP tidak valid, kembalikan error
        if (count($currentIpParts) !== 4) {
            return [
                'success' => false,
                'message' => 'Format IP tidak valid: ' . $this->alamat_ip,
                'old_ip' => $originalIp,
                'new_ip' => null
            ];
        }

        // Periksa apakah IP saat ini masih valid
        $connect = @fsockopen($this->alamat_ip, "80", $errno, $errstr, 1);
        if ($connect) {
            fclose($connect);
            return [
                'success' => true,
                'message' => 'IP saat ini masih valid dan terhubung.',
                'old_ip' => $originalIp,
                'new_ip' => $originalIp
            ];
        }

        // Dapatkan subnet (3 bagian pertama dari IP)
        $subnet = $currentIpParts[0] . '.' . $currentIpParts[1] . '.' . $currentIpParts[2];

        // Coba temukan perangkat di jaringan
        $found = false;
        $newIp = null;

        // Coba scan rentang IP di subnet yang sama (oktet terakhir dari 1 hingga 254)
        for ($i = 1; $i <= 254; $i++) {
            $testIp = $subnet . '.' . $i;

            // Lewati IP saat ini (sudah tahu tidak berfungsi)
            if ($testIp === $originalIp) {
                continue;
            }

            // Coba terhubung ke perangkat
            $connect = @fsockopen($testIp, "80", $errno, $errstr, 0.5); // Timeout pendek untuk pemindaian cepat

            if ($connect) {
                // Coba verifikasi apakah itu perangkat sidik jari dengan memeriksa iWsService
                $soap_request = "<GetDeviceInfo><ArgComKey xsi:type=\"xsd:integer\">" . $this->kunci_komunikasi . "</ArgComKey></GetDeviceInfo>";
                $newLine = "\r\n";

                fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
                fputs($connect, "Content-Type: text/xml" . $newLine);
                fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                fputs($connect, $soap_request . $newLine);

                $buffer = "";
                while ($response = fgets($connect, 1024)) {
                    $buffer = $buffer . $response;
                }
                fclose($connect);

                // Periksa apakah itu perangkat sidik jari
                if (strpos($buffer, '<GetDeviceInfoResponse>') !== false) {
                    $found = true;
                    $newIp = $testIp;
                    break;
                }
            }
        }

        // Jika perangkat ditemukan, perbarui alamat IP
        if ($found && $newIp) {
            $this->alamat_ip = $newIp;
            $this->save();

            return [
                'success' => true,
                'message' => "IP berhasil diperbarui dari $originalIp menjadi $newIp",
                'old_ip' => $originalIp,
                'new_ip' => $newIp
            ];
        }

        return [
            'success' => false,
            'message' => "Gagal mendeteksi mesin absensi di subnet $subnet",
            'old_ip' => $originalIp,
            'new_ip' => null
        ];
    }

    /**
     * Memeriksa apakah mesin sedang online dan dapat diakses.
     *
     * @return bool
     */
    public function isOnline()
    {
        $connect = @fsockopen($this->alamat_ip, "80", $errno, $errstr, 1);
        if ($connect) {
            fclose($connect);
            return true;
        }
        return false;
    }
}
