<?php

namespace App\Models;

use App\Models\AjuanCuti;
use App\Models\Departemen;
use App\Models\User;
use App\Models\Bagian;
use App\Models\Jabatan;
use App\Models\VerifikasiPenggajian;
use App\Enums\KaryawanStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Profesi;

class Karyawan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'nik_karyawan',
        'nama_karyawan',
        'foto_karyawan',
        'statuskaryawan',
        'id_departemen',
        'id_bagian',
        'tgl_awalmmasuk',
        'tahun_keluar',
        'id_jabatan',
        'id_profesi',
        'nik',
        'kk',
        'statuskawin',
        'pendidikan_terakhir',
        'id_programstudi',
        'no_hp',
        'ortu_bapak',
        'ortu_ibu',
        'alamat',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'jenis_kelamin',
        'no_rekening',
        'nama_bank',
        'npwp',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'user_id',
        'iskontrak',
        'ukuran_kemeja',
        'ukuran_celana',
        'ukuran_sepatu',
        'jml_anggotakk',
        'nama_pemilik_rekening'
    ];

    protected $casts = [
        'statuskaryawan' => KaryawanStatusEnum::class,
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'deleted_at' => 'datetime'
    ];

    public function profesi()
    {
        return $this->belongsTo(Profesi::class, 'id_profesi');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_programstudi');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'id_bagian');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function penggajians()
    {
        return $this->hasMany(Penggajian::class, 'id_karyawan');
    }

    public function verifikasiPenggajians()
    {
        return $this->hasManyThrough(
            VerifikasiPenggajian::class,
            Penggajian::class,
            'id_karyawan',
            'id_penggajian',
            'id',
            'id'
        );
    }
}
