<?php

namespace App\Models;

use App\Models\JadwalKerja;
use App\Models\MesinAbsensi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'karyawan_id', // ID karyawan yang melakukan absensi
        'tanggal', // Tanggal absensi
        'jadwalkerja_id', // ID jadwal kerja yang terkait
        'jam_masuk', // Waktu masuk kerja
        'jam_pulang', // Waktu pulang kerja
        'total_jam', // Total jam kerja
        'keterlambatan', // Durasi keterlambatan
        'pulang_awal', // Durasi pulang lebih awal
        'status', // Status absensi
        'jenis_absensi_masuk', // Jenis absensi saat masuk
        'mesinabsensi_masuk_id', // ID mesin absensi saat masuk
        'jenis_absensi_pulang', // Jenis absensi saat pulang
        'mesinabsensi_pulang_id', // ID mesin absensi saat pulang
        'keterangan' // Keterangan tambahan
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date', // Cast tanggal ke tipe date
        'jam_masuk' => 'datetime',
        'jam_pulang' => 'datetime',
        'keterlambatan' => 'integer', // Cast keterlambatan ke tipe integer
        'pulang_awal' => 'integer', // Cast pulang awal ke tipe integer
    ];

    /**
     * Mendapatkan karyawan yang terkait dengan absensi ini.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Mendapatkan jadwal kerja yang terkait dengan absensi ini.
     */
    public function jadwalKerja()
    {
        return $this->belongsTo(JadwalKerja::class, 'jadwalkerja_id');
    }

    /**
     * Mendapatkan mesin absensi untuk check-in.
     */
    public function mesinAbsensiMasuk()
    {
        return $this->belongsTo(MesinAbsensi::class, 'mesinabsensi_masuk_id');
    }

    /**
     * Mendapatkan mesin absensi untuk check-out.
     */
    public function mesinAbsensiPulang()
    {
        return $this->belongsTo(MesinAbsensi::class, 'mesinabsensi_pulang_id');
    }
}
