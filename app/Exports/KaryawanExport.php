<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Karyawan::with([
            'profesi',
            'programStudi',
            'jabatan',
            'bagian',
            'departemen',
            'user'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Karyawan',
            'Email',
            'NIK',
            'NIP',
            'Profesi',
            'Program Studi',
            'Jabatan',
            'Bagian',
            'Departemen',
            'Status Karyawan',
            'Tanggal Masuk',
            'Tanggal Keluar',
            'Alamat',
            'No. Telepon',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Agama',
            'Status Pernikahan',
            'Pendidikan Terakhir',
            'Gaji Pokok',
            'Created At',
            'Updated At'
        ];
    }

    public function map($karyawan): array
    {
        return [
            $karyawan->id,
            $karyawan->nama ?? $karyawan->user->name ?? '',
            $karyawan->email ?? $karyawan->user->email ?? '',
            $karyawan->nik ?? '',
            $karyawan->nip ?? '',
            $karyawan->profesi->nama ?? '',
            $karyawan->programStudi->nama ?? '',
            $karyawan->jabatan->nama ?? '',
            $karyawan->bagian->nama ?? '',
            $karyawan->departemen->nama ?? '',
            $karyawan->statuskaryawan->value ?? '',
            $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('Y-m-d') : '',
            $karyawan->tanggal_keluar ? $karyawan->tanggal_keluar->format('Y-m-d') : '',
            $karyawan->alamat ?? '',
            $karyawan->no_telepon ?? '',
            $karyawan->jenis_kelamin ?? '',
            $karyawan->tanggal_lahir ?? '',
            $karyawan->tempat_lahir ?? '',
            $karyawan->agama ?? '',
            $karyawan->status_pernikahan ?? '',
            $karyawan->pendidikan_terakhir ?? '',
            $karyawan->gaji_pokok ?? '',
            $karyawan->created_at ? $karyawan->created_at->format('Y-m-d H:i:s') : '',
            $karyawan->updated_at ? $karyawan->updated_at->format('Y-m-d H:i:s') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}