<?php

namespace App\Models;

use App\Models\PeriodeGaji;
use App\Models\Karyawan;
use App\Models\Keuangan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penggajian extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [
        'id'
    ];

    public function formatCurrency($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function periodeGaji()
    {
        return $this->belongsTo(PeriodeGaji::class, 'id_periode');
    }

    public function keuangan()
    {
        return $this->belongsTo(Keuangan::class, 'id_keuangan');
    }


}
