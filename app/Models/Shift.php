<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_shift',
        'jenis_shift',
        'jam_masuk',
        'jam_pulang',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
    ];

    /**
     * Format jam masuk to display in 24h format
     */
    public function getFormattedJamMasukAttribute()
    {
        return $this->jam_masuk ? Carbon::parse($this->jam_masuk)->format('H:i') : '-';
    }

    /**
     * Format jam pulang to display in 24h format
     */
    public function getFormattedJamPulangAttribute()
    {
        return $this->jam_pulang ? Carbon::parse($this->jam_pulang)->format('H:i') : '-';
    }

    /**
     * Format duration between jam_masuk and jam_pulang
     */
    public function getDurationAttribute()
    {
        if (!$this->jam_masuk || !$this->jam_pulang) {
            return '-';
        }

        $start = Carbon::parse($this->jam_masuk);
        $end = Carbon::parse($this->jam_pulang);

        // Handle overnight shifts
        if ($end < $start) {
            $end->addDay();
        }

        $durationMinutes = $end->diffInMinutes($start);
        $hours = floor($durationMinutes / 60);
        $minutes = $durationMinutes % 60;

        return $hours . ' jam ' . $minutes . ' menit';
    }
}
