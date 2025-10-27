<?php

namespace App\Models;

use App\Models\Karyawan;
use App\Models\CutiKaryawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KuotaIzinTahunan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kuota_izin_tahunan';

    protected $guarded = [];

    // Relationship with Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Relationship with CutiKaryawan for izin records
    public function izinKaryawan()
    {
        return $this->hasMany(cutiKaryawan::class, 'id_karyawan', 'karyawan_id')
            ->whereYear('tanggal_mulai_cuti', $this->tahun);
    }
}
