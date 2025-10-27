<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Profesi extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name_profesi',
        'tunjangan_profesi',
    ];

    // You can add relationships here if Profesi has relations with other models
    // For example:
    // public function karyawans()
    // {
    //     return $this->hasMany(Karyawan::class, 'profesi_id');
    // }
}