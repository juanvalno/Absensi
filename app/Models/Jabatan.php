<?php

namespace App\Models;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jabatan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name_jabatan',
        'gaji_pokok',
        'premi',
        'tunjangan_jabatan',
        'uang_lembur_biasa',
        'uang_lembur_libur',
    ];

    /**
     * Format the monetary value to IDR format
     */
    public function formatRupiah($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    /**
     * Get gaji_pokok in Rupiah format
     */
    public function getGajiPokokRupiahAttribute()
    {
        return $this->formatRupiah($this->gaji_pokok);
    }

    /**
     * Get premi in Rupiah format
     */
    public function getPremiRupiahAttribute()
    {
        return $this->formatRupiah($this->premi);
    }

    /**
     * Get tunjangan_jabatan in Rupiah format
     */
    public function getTunjanganJabatanRupiahAttribute()
    {
        return $this->formatRupiah($this->tunjangan_jabatan);
    }

    /**
     * Get uang_lembur_biasa in Rupiah format
     */
    public function getUangLemburBiasaRupiahAttribute()
    {
        return $this->formatRupiah($this->uang_lembur_biasa);
    }

    /**
     * Get uang_lembur_libur in Rupiah format
     */
    public function getUangLemburLiburRupiahAttribute()
    {
        return $this->formatRupiah($this->uang_lembur_libur);
    }

    // You can add relationships here if Jabatan has relations with other models
    // For example:
    // public function karyawans()
    // {
    //     return $this->hasMany(Karyawan::class, 'jabatan_id');
    // }
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'jabatan_id');
    }
}