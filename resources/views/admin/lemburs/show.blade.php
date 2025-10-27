@extends('adminlte::page')

@section('title', 'Detail Pengajuan Lembur')

@section('content_header')
<h1>Detail Pengajuan Lembur</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Pengajuan Lembur</h3>
        <div class="card-tools">
            <a href="{{ route('lemburs.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if($lembur->status == 'Menunggu Persetujuan')
            @can_show('lembur.edit')
            <a href="{{ route('lemburs.edit', $lembur) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan_show

            @can_show('lembur.approve')
            <a href="{{ route('lemburs.approval', $lembur) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-check"></i> Proses
            </a>
            @endcan_show
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title">Data Karyawan</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="150">Nama Karyawan</th>
                                <td>{{ $lembur->karyawan ? $lembur->karyawan->nama_karyawan : '-' }}</td>
                            </tr>
                            <tr>
                                <th>NIK Karyawan</th>
                                <td>{{ $lembur->karyawan ? $lembur->karyawan->nik_karyawan : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Departemen</th>
                                <td>{{ $lembur->karyawan && $lembur->karyawan->departemen ? $lembur->karyawan->departemen->name_departemen : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Bagian</th>
                                <td>{{ $lembur->karyawan && $lembur->karyawan->bagian ? $lembur->karyawan->bagian->name_bagian : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jabatan</th>
                                <td>{{ $lembur->karyawan && $lembur->karyawan->jabatan ? $lembur->karyawan->jabatan->name_jabatan : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title">Data Persetujuan</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="150">Supervisor</th>
                                <td>{{ $lembur->supervisor ? $lembur->supervisor->nama_karyawan : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge {{ $lembur->status_badge_class }}">
                                        {{ $lembur->status }}
                                    </span>
                                </td>
                            </tr>
                            @if($lembur->status != 'Menunggu Persetujuan')
                            <tr>
                                <th>Tanggal Approval</th>
                                <td>{{ $lembur->tanggal_approval ? $lembur->tanggal_approval->format('d-m-Y H:i:s') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Diapprove Oleh</th>
                                <td>{{ $lembur->approver ? $lembur->approver->nama_karyawan : '-' }}</td>
                            </tr>
                            @if($lembur->status == 'Ditolak')
                            <tr>
                                <th>Alasan Penolakan</th>
                                <td>{{ $lembur->keterangan_tolak ?? '-' }}</td>
                            </tr>
                            @endif
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title">Detail Pengajuan Lembur</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="150">Jenis Lembur</th>
                                <td>{{ $lembur->jenis_lembur }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Lembur</th>
                                <td>{{ $lembur->tanggal_lembur_formatted }}</td>
                            </tr>
                            <tr>
                                <th>Jam Mulai</th>
                                <td>{{ $lembur->jam_mulai_formatted }}</td>
                            </tr>
                            <tr>
                                <th>Jam Selesai</th>
                                <td>{{ $lembur->jam_selesai_formatted }}</td>
                            </tr>
                            <tr>
                                <th>Total Lembur</th>
                                <td>{{ $lembur->total_lembur }}</td>
                            </tr>
                            @if($lembur->status == 'Disetujui')
                            <tr>
                                <th>Lembur Disetujui</th>
                                <td>{{ $lembur->lembur_disetujui }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $lembur->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table th {
        background-color: #f4f6f9;
    }

</style>
@stop

@section('js')
<script>
    $(function() {
        // Additional JavaScript if needed
    });

</script>
@stop
