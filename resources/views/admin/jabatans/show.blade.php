@extends('adminlte::page')

@section('title', 'View Jabatan')

@section('content_header')
<h1>Jabatan Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $jabatan->name_jabatan }}</h3>
        <div class="card-tools">
            <a href="{{ route('jabatans.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can_show('jabatan.edit')
            <a href="{{ route('jabatans.edit', $jabatan) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit Jabatan
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Nama Jabatan</th>
                        <td>{{ $jabatan->name_jabatan }}</td>
                    </tr>
                    <tr>
                        <th>Gaji Pokok</th>
                        <td class="text-right">{{ $jabatan->gaji_pokok_rupiah }}</td>
                    </tr>
                    <tr>
                        <th>Premi</th>
                        <td class="text-right">{{ $jabatan->premi_rupiah }}</td>
                    </tr>
                    <tr>
                        <th>Tunjangan Jabatan</th>
                        <td class="text-right">{{ $jabatan->tunjangan_jabatan_rupiah }}</td>
                    </tr>
                    <tr>
                        <th>Uang Lembur Biasa</th>
                        <td class="text-right">{{ $jabatan->uang_lembur_biasa_rupiah }}</td>
                    </tr>
                    <tr>
                        <th>Uang Lembur Libur</th>
                        <td class="text-right">{{ $jabatan->uang_lembur_libur_rupiah }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $jabatan->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $jabatan->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- You can add related data here, for example:
        <h4 class="mt-4">Karyawan dengan Jabatan Ini</h4>
        @if($jabatan->karyawans->isNotEmpty())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Nama Karyawan</th>
                        <th>NIP</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jabatan->karyawans as $index => $karyawan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
        <td>{{ $karyawan->name }}</td>
        <td>{{ $karyawan->nip }}</td>
        <td>
            <a href="{{ route('karyawans.show', $karyawan) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i>
            </a>
        </td>
        </tr>
        @endforeach
        </tbody>
        </table>
        @else
        <div class="alert alert-info">
            Tidak ada karyawan dengan jabatan ini.
        </div>
        @endif
        --}}
    </div>
</div>
@stop
