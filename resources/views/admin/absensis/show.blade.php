@extends('adminlte::page')

@section('title', 'Detail Absensi')

@section('content_header')
<h1>Detail Absensi</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Absensi</h3>
        <div class="card-tools">
            <a href="{{ route('absensis.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @can_show('absensis.edit')
            <a href="{{ route('absensis.edit', $absensi) }}" class="btn btn-warning btn-sm">
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
                        <td>{{ $absensi->karyawan->nama_karyawan ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $absensi->tanggal->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Jadwal Kerja</th>
                        <td>{{ $absensi->jadwalKerja->nama_jadwal ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Jam Masuk</th>
                        <td>{{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jam Pulang</th>
                        <td>{{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Total Jam</th>
                        <td>{{ $absensi->total_jam ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Keterlambatan</th>
                        <td>{{ $absensi->keterlambatan }} menit</td>
                    </tr>
                    <tr>
                        <th>Pulang Awal</th>
                        <td>{{ $absensi->pulang_awal }} menit</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($absensi->status == 'Hadir')
                            <span class="badge badge-success">{{ $absensi->status }}</span>
                            @elseif($absensi->status == 'Terlambat')
                            <span class="badge badge-warning">{{ $absensi->status }}</span>
                            @elseif($absensi->status == 'Izin')
                            <span class="badge badge-info">{{ $absensi->status }}</span>
                            @elseif($absensi->status == 'Sakit')
                            <span class="badge badge-primary">{{ $absensi->status }}</span>
                            @elseif($absensi->status == 'Cuti')
                            <span class="badge badge-secondary">{{ $absensi->status }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis Absensi Masuk</th>
                        <td>{{ $absensi->jenis_absensi_masuk }}</td>
                    </tr>
                    @if($absensi->jenis_absensi_masuk == 'Mesin')
                    <tr>
                        <th>Mesin Absensi Masuk</th>
                        <td>{{ $absensi->mesinAbsensiMasuk->nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi Mesin Masuk</th>
                        <td>{{ $absensi->mesinAbsensiMasuk->lokasi ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Jenis Absensi Pulang</th>
                        <td>{{ $absensi->jenis_absensi_pulang }}</td>
                    </tr>
                    @if($absensi->jenis_absensi_pulang == 'Mesin')
                    <tr>
                        <th>Mesin Absensi Pulang</th>
                        <td>{{ $absensi->mesinAbsensiPulang->nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi Mesin Pulang</th>
                        <td>{{ $absensi->mesinAbsensiPulang->lokasi ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $absensi->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $absensi->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $absensi->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
