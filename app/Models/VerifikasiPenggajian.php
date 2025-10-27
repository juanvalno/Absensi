<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiPenggajian extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_penggajian';

    protected $fillable = [
        'id_penggajian',
        'user_id',
        'status',
        'keterangan',
        'total_verifikasi',
        'departemen_id',
    ];

    public function penggajian()
    {
        return $this->belongsTo(Penggajian::class, 'id_penggajian');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function periodeGaji()
    {
        return $this->hasOneThrough(
            PeriodeGaji::class,
            Penggajian::class,
            'id', // Foreign key on Penggajian table
            'id', // Foreign key on PeriodeGaji table
            'id_penggajian', // Local key on VerifikasiPenggajian table
            'periode_id' // Local key on Penggajian table
        );
    }
}