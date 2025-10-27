@extends('adminlte::page')

@section('title', 'Detail Kuota Izin Tahunan')

@section('content_header')
    <h1>Detail Kuota Izin Tahunan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Kuota Izin</h3>
            <div class="card-tools">
                <a href="{{ route('kuota-izin.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Nama Karyawan</th>
                            <td>{{ $kuotaIzin->karyawan->nama_karyawan }}</td>
                        </tr>
                        <tr>
                            <th>Tahun</th>
                            <td>{{ $kuotaIzin->tahun }}</td>
                        </tr>
                        <tr>
                            <th>Kuota Awal</th>
                            <td>{{ $kuotaIzin->kuota_awal }} hari</td>
                        </tr>
                        <tr>
                            <th>Kuota Digunakan</th>
                            <td>{{ $kuotaIzin->kuota_digunakan }} hari</td>
                        </tr>
                        <tr>
                            <th>Sisa Kuota</th>
                            <td>{{ $kuotaIzin->kuota_sisa }} hari</td>
                        </tr>
                        <tr>
                            <th>Tanggal Expired</th>
                            <td>{{ $kuotaIzin->tanggal_expired ? date('d/m/Y', strtotime($kuotaIzin->tanggal_expired)) : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $kuotaIzin->keterangan ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h4>Riwayat Penggunaan Izin</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Jumlah Hari</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kuotaIzin->izinKaryawan as $index => $izin)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ date('d/m/Y', strtotime($izin->tanggal_mulai)) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($izin->tanggal_selesai)) }}</td>
                                    <td>{{ $izin->jumlah_hari }} hari</td>
                                    <td>{{ $izin->keterangan }}</td>
                                    <td>{{ $izin->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada riwayat penggunaan izin</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>
@stop
