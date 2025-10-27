<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Karyawan;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'user_id');
    }

    public function adminlte_image()
    {
        return $this->karyawan && $this->karyawan->foto_karyawan
            ? asset('storage/karyawan/foto/' . $this->karyawan->foto_karyawan)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4e73df&color=fff';
    }

    public function adminlte_desc()
    {
        return 'Anda login sebagai : ' . $this->roles->pluck('name')->map(function ($role) {
            return ucfirst($role);
        })->join(', ');
    }

    public function adminlte_profile_url()
    {
        return 'admin/profile';
    }
}
