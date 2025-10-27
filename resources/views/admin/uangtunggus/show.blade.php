@extends('adminlte::page')

@section('title', 'Detail Uang Tunggu')

@section('content_header')
<h1>Detail Uang Tunggu</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Uang Tunggu</h3>
        <div class="card-tools">
            <a href="{{ route('uangtunggus.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @can_show('uang_tunggu.edit')
            <a href="{{ route('uangtunggus.edit', $uangtunggu) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Nama Karyawan</th>
                        <td>{{ $uangtunggu->karyawan->nama_karyawan ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>ID Karyawan</th>
                        <td>{{ $uangtunggu->karyawan_id }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Mulai</th>
                        <td>{{ $uangtunggu->tanggal_mulai->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Selesai</th>
                        <td>{{ $uangtunggu->tanggal_selesai->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Durasi</th>
                        <td>{{ $uangtunggu->tanggal_mulai->diffInDays($uangtunggu->tanggal_selesai) + 1 }} hari</td>
                    </tr>
                    <tr>
                        <th>Nominal</th>
                        <td>Rp {{ number_format($uangtunggu->nominal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $uangtunggu->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $uangtunggu->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
