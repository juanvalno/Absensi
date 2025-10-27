<?php

namespace App\Models;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bagian extends Model
{
    use HasFactory, HasUuids;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'bagians';

    // Menentukan primary key dari tabel
    protected $primaryKey = 'id';

    // Menentukan tipe data dari primary key
    protected $keyType = 'string';

    // Menentukan bahwa primary key tidak auto increment
    public $incrementing = false;

    // Atribut yang dapat diisi secara massal
    protected $fillable = [
        'name_bagian', // Nama bagian
        'id_departemen', // ID departemen yang terkait
    ];

    /**
     * Relasi dengan model Karyawan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'id_bagian');
    }

    /**
     * Relasi dengan model Departemen
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }
}