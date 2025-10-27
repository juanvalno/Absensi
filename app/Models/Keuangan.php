<?php

namespace App\Models;

use App\Models\User;
use App\Models\PeriodeGaji;
use App\Models\Penggajian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keuangan extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'keuangans';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
        'summary' => 'array'
    ];

    public function penggajians()
    {
        return $this->hasMany(Penggajian::class, 'id_keuangan');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeGaji::class, 'id_periode');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}
