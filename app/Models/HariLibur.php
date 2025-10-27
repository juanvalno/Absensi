<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harilibur extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'tanggal',      // Tanggal hari libur
        'nama_libur',   // Nama hari libur (contoh: Hari Kemerdekaan, Idul Fitri)
        'keterangan',   // Keterangan tambahan tentang hari libur
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'tanggal' => 'date',  // Cast tanggal ke tipe date
    ];
}