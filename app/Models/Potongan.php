<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potongan extends Model
{
    use HasFactory;
    
    // protected $table = 'potongan';
    
    protected $fillable = [
        'nama_potongan',
        'type'
    ];
    
    // Relationship with penggajian if needed
    public function penggajian()
    {
        return $this->hasMany(Penggajian::class);
    }
}
