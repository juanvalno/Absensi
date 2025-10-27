<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Mastercuti;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CutiKaryawan extends Model
{
    use HasFactory, HasUuids;

    // Atribut yang dapat diisi secara massal
    protected $fillable = [
        'id_karyawan', // ID karyawan yang mengajukan cuti
        'jenis_cuti', // Jenis cuti yang diajukan
        'tanggal_mulai_cuti', // Tanggal mulai cuti
        'tanggal_akhir_cuti', // Tanggal akhir cuti
        'jumlah_hari_cuti', // Jumlah hari cuti yang diajukan
        'cuti_disetujui', // Jumlah hari cuti yang disetujui
        'master_cuti_id', // ID master cuti yang terkait
        'bukti', // Bukti pendukung cuti
        'id_supervisor', // ID supervisor yang menyetujui
        'status_acc', // Status persetujuan cuti
        'keterangan_tolak', // Keterangan jika cuti ditolak
        'tanggal_approval', // Tanggal persetujuan
        'approved_by', // ID karyawan yang menyetujui
    ];

    // Atribut yang harus di-cast ke tipe data tertentu
    protected $casts = [
        'tanggal_mulai_cuti' => 'date', // Cast tanggal mulai cuti ke tipe date
        'tanggal_akhir_cuti' => 'date', // Cast tanggal akhir cuti ke tipe date
        'tanggal_approval' => 'datetime', // Cast tanggal approval ke tipe datetime
    ];

    /**
     * Mendapatkan karyawan yang memiliki cuti ini.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    /**
     * Mendapatkan master cuti yang terkait dengan cuti karyawan ini.
     */
    public function masterCuti()
    {
        return $this->belongsTo(Mastercuti::class, 'master_cuti_id');
    }

    /**
     * Mendapatkan supervisor yang menyetujui cuti ini.
     */
    public function supervisor()
    {
        return $this->belongsTo(Karyawan::class, 'id_supervisor');
    }

    /**
     * Mendapatkan approver yang menyetujui cuti ini.
     */
    public function approver()
    {
        return $this->belongsTo(Karyawan::class, 'approved_by');
    }

    /**
     * Mendapatkan URL lengkap untuk file bukti
     */
    public function getBuktiUrlAttribute()
    {
        if ($this->bukti) {
            return asset('storage/cuti/bukti/' . $this->bukti);
        }
        return null;
    }

    /**
     * Mendapatkan tanggal mulai cuti yang sudah diformat
     */
    public function getTanggalMulaiFormattedAttribute()
    {
        return $this->tanggal_mulai_cuti ? $this->tanggal_mulai_cuti->format('d-m-Y') : '-';
    }

    /**
     * Mendapatkan tanggal akhir cuti yang sudah diformat
     */
    public function getTanggalAkhirFormattedAttribute()
    {
        return $this->tanggal_akhir_cuti ? $this->tanggal_akhir_cuti->format('d-m-Y') : '-';
    }

    /**
     * Mendapatkan kelas untuk badge status
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status_acc) {
            case 'Disetujui':
                return 'badge-success';
            case 'Ditolak':
                return 'badge-danger';
            default:
                return 'badge-warning';
        }
    }
}