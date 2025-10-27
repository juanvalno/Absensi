<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mastercuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'uraian',
        'is_bulanan',
        'cuti_max',
        'izin_max',
        'is_potonggaji',
        'nominal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_bulanan' => 'boolean',
        'is_potonggaji' => 'boolean',
    ];

    /**
     * Get formatted nominal with currency format
     *
     * @return string
     */
    public function getFormattedNominalAttribute()
    {
        if ($this->nominal) {
            return 'Rp ' . number_format($this->nominal, 0, ',', '.');
        }
        return '-';
    }

    /**
     * Get is_bulanan as Yes/No text
     *
     * @return string
     */
    public function getBulananTextAttribute()
    {
        return $this->is_bulanan ? 'Ya' : 'Tidak';
    }

    /**
     * Get is_potonggaji as Yes/No text
     *
     * @return string
     */
    public function getPotonggajiTextAttribute()
    {
        return $this->is_potonggaji ? 'Ya' : 'Tidak';
    }
}