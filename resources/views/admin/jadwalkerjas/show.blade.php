@extends('adminlte::page')

@section('title', 'Detail Kehadiran')

@section('content_header')
<h1>Detail Kehadiran</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Kehadiran</h3>
        <div class="card-tools">
            <a href="{{ route('jadwalkerjas.edit', $jadwalkerja->id) }}" class="btn btn-primary btn-sm mr-1">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('jadwalkerjas.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px">ID</th>
                        <td>{{ $jadwalkerja->id }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $jadwalkerja->tanggal->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Nama Karyawan</th>
                        <td>{{ $jadwalkerja->karyawan->nama_karyawan ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>ID Karyawan</th>
                        <td>{{ $jadwalkerja->karyawan->nik_karyawan }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px">Kode Shift</th>
                        <td>{{ $jadwalkerja->shift->kode_shift ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Shift</th>
                        <td>{{ $jadwalkerja->shift->jenis_shift ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Jam Kerja</th>
                        <td>
                            @if($jadwalkerja->shift)
                            {{ $jadwalkerja->shift->jam_masuk->translatedFormat('H i') }} - {{ $jadwalkerja->shift->jam_pulang->translatedFormat('H i') }}
                            @else
                            N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat / Diupdate</th>
                        <td>
                            {{ $jadwalkerja->created_at->format('d-m-Y') }} /
                            {{ $jadwalkerja->updated_at->format('d-m-Y') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <form action="{{ route('jadwalkerjas.destroy', $jadwalkerja->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus data ini?');" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@stop
