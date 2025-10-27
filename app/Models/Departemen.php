<?php

namespace App\Models;

use App\Models\Bagian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departemen extends Model
{
    use HasFactory, HasUuids;

    // Atribut yang dapat diisi secara massal
    protected $fillable = [
        'name_departemen', // Nama departemen
    ];

    /**
     * Mendapatkan bagian-bagian yang terkait dengan departemen ini.
     */
    public function bagians()
    {
        return $this->hasMany(Bagian::class, 'id_departemen');
    }

    /**
     * Memeriksa apakah departemen ini memiliki bagian.
     *
     * @return bool
     */
    public function hasBagians()
    {
        return $this->bagians()->count() > 0;
    }

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'id_departemen');
    }
}
