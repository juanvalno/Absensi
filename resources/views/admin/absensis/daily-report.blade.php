@extends('adminlte::page')

@section('title', 'Laporan Absensi Harian')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="mr-2 fas fa-calendar-day text-primary"></i>Laporan Absensi Harian</h1>
        <div>
            <a href="{{ route('admin.absensis.index') }}" class="btn btn-secondary">
                <i class="mr-1 fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.absensis.daily-report') }}" method="GET" class="form-inline">
                <div class="form-group mr-3">
                    <label for="tanggal" class="mr-2">Tanggal:</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                        value="{{ $tanggal ?? date('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="mr-1 fas fa-search"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>

    @if (isset($absensi))
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">Ringkasan Absensi Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Hadir</span>
                                <span class="info-box-number">{{ $summary['hadir'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Terlambat</span>
                                <span class="info-box-number">{{ $summary['terlambat'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Izin</span>
                                <span class="info-box-number">{{ $summary['izin'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-procedures"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sakit</span>
                                <span class="info-box-number">{{ $summary['sakit'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Detail Absensi</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>Departemen</th>
                                <th>Bagian</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absensi as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->karyawan->nik }}</td>
                                    <td>{{ $item->karyawan->nama_lengkap }}</td>
                                    <td>{{ $item->karyawan->departemen->nama_departemen ?? '-' }}</td>
                                    <td>{{ $item->karyawan->bagian->nama_bagian ?? '-' }}</td>
                                    <td>{{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i:s') : '-' }}
                                    </td>
                                    <td>{{ $item->jam_keluar ? \Carbon\Carbon::parse($item->jam_keluar)->format('H:i:s') : '-' }}
                                    </td>
                                    <td>
                                        @if ($item->status == 'Hadir')
                                            <span class="badge badge-success">Hadir</span>
                                        @elseif($item->status == 'Terlambat')
                                            <span class="badge badge-warning">Terlambat</span>
                                        @elseif($item->status == 'Izin')
                                            <span class="badge badge-info">Izin</span>
                                        @elseif($item->status == 'Sakit')
                                            <span class="badge badge-danger">Sakit</span>
                                        @elseif($item->status == 'Cuti')
                                            <span class="badge badge-primary">Cuti</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data absensi untuk tanggal ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .info-box-number {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            // Initialize any plugins or event handlers here
        });
    </script>
@stop
