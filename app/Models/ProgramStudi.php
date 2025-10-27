<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProgramStudi extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [
        'id',
    ];

    /**
     * You can add relationships here if Program Studi has relations with other models
     * For example:
     *
     * public function students()
     * {
     *     return $this->hasMany(Student::class, 'program_studi_id');
     * }
     */
}
