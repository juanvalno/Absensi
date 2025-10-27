<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Profesi;
use App\Models\ProgramStudi;
use App\Models\Jabatan;
use App\Models\Bagian;
use App\Models\Departemen;
use App\Enums\KaryawanStatusEnum;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KaryawanImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        // Cari atau buat user terlebih dahulu
        $user = User::firstOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['nama_karyawan'],
                'password' => Hash::make('password123'), // Default password
                'email_verified_at' => now()
            ]
        );

        // Cari ID dari relasi berdasarkan nama
        $profesiId = $this->findIdByName(Profesi::class, $row['profesi']);
        $programStudiId = $this->findIdByName(ProgramStudi::class, $row['program_studi']);
        $jabatanId = $this->findIdByName(Jabatan::class, $row['jabatan']);
        $bagianId = $this->findIdByName(Bagian::class, $row['bagian']);
        $departemenId = $this->findIdByName(Departemen::class, $row['departemen']);

        return new Karyawan([
            'user_id' => $user->id,
            'nama' => $row['nama_karyawan'],
            'email' => $row['email'],
            'nik' => $row['nik'],
            'nip' => $row['nip'],
            'id_profesi' => $profesiId,
            'id_programstudi' => $programStudiId,
            'id_jabatan' => $jabatanId,
            'id_bagian' => $bagianId,
            'id_departemen' => $departemenId,
            'statuskaryawan' => $this->parseStatusKaryawan($row['status_karyawan']),
            'tanggal_masuk' => $this->parseDate($row['tanggal_masuk']),
            'tanggal_keluar' => $this->parseDate($row['tanggal_keluar']),
            'alamat' => $row['alamat'] ?? null,
            'no_telepon' => $row['no_telepon'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'tanggal_lahir' => $this->parseDate($row['tanggal_lahir']),
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'agama' => $row['agama'] ?? null,
            'status_pernikahan' => $row['status_pernikahan'] ?? null,
            'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? null,
            'gaji_pokok' => $row['gaji_pokok'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_karyawan' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|string|unique:karyawans,nik',
            'nip' => 'nullable|string|unique:karyawans,nip',
            'tanggal_masuk' => 'required|date',
        ];
    }

    private function findIdByName($model, $name)
    {
        if (empty($name)) return null;

        $record = $model::where('nama', $name)->first();
        return $record ? $record->id : null;
    }

    private function parseStatusKaryawan($status)
    {
        if (empty($status)) return KaryawanStatusEnum::HARIAN;

        return match(strtolower($status)) {
            'harian' => KaryawanStatusEnum::HARIAN,
            'bulanan', 'nonaktif' => KaryawanStatusEnum::BULANAN,
            'borongan' => KaryawanStatusEnum::BORONGAN,
            default => KaryawanStatusEnum::HARIAN
        };
    }

    private function parseDate($date)
    {
        if (empty($date)) return null;

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}