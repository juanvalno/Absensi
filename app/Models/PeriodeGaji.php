<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeGaji extends Model
{
    use HasFactory;

    protected $table = 'periodegajis';

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relationship with penggajian
    public function keuangans()
    {
        return $this->hasMany(Keuangan::class, 'id_periode');
    }

    public function penggajians()
    {
        return $this->hasMany(Penggajian::class, 'id_periode');
    }

    // Check if this period has any associated payroll data
    public function hasPayrollData()
    {
        return $this->penggajians()->exists();
    }

    // Get total karyawan yang sudah diproses untuk periode ini
    public function getKaryawanProcessedCountAttribute()
    {
        return $this->penggajians()->count();
    }

    // Get total gaji bersih untuk periode ini
    public function getTotalGajiBersihAttribute()
    {
        return $this->penggajians()->sum('gaji_bersih');
    }

    // Format tanggal mulai
    public function getTanggalMulaiFormattedAttribute()
    {
        return $this->tanggal_mulai ? $this->tanggal_mulai->format('d-m-Y') : '-';
    }

    // Format tanggal selesai
    public function getTanggalSelesaiFormattedAttribute()
    {
        return $this->tanggal_selesai ? $this->tanggal_selesai->format('d-m-Y') : '-';
    }

    // Get duration in days
    public function getDurationDaysAttribute()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) {
            return 0;
        }

        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    // Get badge class based on status
    public function getStatusBadgeClassAttribute()
    {
        return $this->status === 'aktif' ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Set this period as active and deactivate all others
     */
    public function setAsActive()
    {
        // Deactivate all other periods
        static::where('id', '!=', $this->id)
            ->where('status', 'aktif')
            ->update(['status' => 'nonaktif']);

        // Set this period as active
        $this->status = 'aktif';
        $this->save();

        return $this;
    }
}