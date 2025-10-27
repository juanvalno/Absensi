<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KuotaCutiTahunan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kuota_cuti_tahunans';

    protected $guarded = [];

    // Relationship with Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Relationship with CutiKaryawan
    public function cutiKaryawan()
    {
        return $this->hasMany(CutiKaryawan::class, 'id_karyawan', 'karyawan_id')
            ->whereYear('tanggal_mulai_cuti', $this->tahun);
    }
}
