@extends('adminlte::page')

@section('title', 'Laporan Jadwal Kerja')

@section('content_header')
    <h1>Laporan Jadwal Kerja</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Filter Laporan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('jadwalkerjas.report') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="karyawan_id">Karyawan</label>
                                    <select class="form-control select2" id="karyawan_id" name="karyawan_id">
                                        <option value="">-- Semua Karyawan --</option>
                                        @foreach($karyawans as $karyawan)
                                            <option value="{{ $karyawan->id }}" {{ $karyawanId == $karyawan->id ? 'selected' : '' }}>
                                                {{ $karyawan->nama_karyawan }} ({{ $karyawan->nik }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shift_id">Shift</label>
                                    <select class="form-control select2" id="shift_id" name="shift_id">
                                        <option value="">-- Semua Shift --</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ $shiftId == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->kode_shift }} ({{ $shift->nama_shift }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group float-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('jadwalkerjas.report') }}" class="btn btn-default">
                                        <i class="fas fa-sync-alt mr-1"></i> Reset
                                    </a>
                                    <button type="button" class="btn btn-success" onclick="window.print()">
                                        <i class="fas fa-print mr-1"></i> Cetak
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Ringkasan Jadwal Kerja</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th>NIK</th>
                                    <th>Total Hari</th>
                                    @foreach($shifts as $shift)
                                        <th>{{ $shift->kode_shift }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary as $item)
                                    <tr>
                                        <td>{{ $item['karyawan']->nama_karyawan }}</td>
                                        <td>{{ $item['karyawan']->nik }}</td>
                                        <td>{{ $item['total_days'] }}</td>
                                        @foreach($shifts as $shift)
                                            <td>{{ $item['shift_counts'][$shift->id] ?? 0 }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + $shifts->count() }}" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Detail Jadwal Kerja</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Karyawan</th>
                                    <th>NIK</th>
                                    <th>Shift</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwalkerjas as $jadwal)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($jadwal->tanggal)->locale('id')->dayName }}</td>
                                        <td>{{ $jadwal->karyawan->nama_karyawan }}</td>
                                        <td>{{ $jadwal->karyawan->nik }}</td>
                                        <td>{{ $jadwal->shift->kode_shift }} - {{ $jadwal->shift->nama_shift }}</td>
                                        <td>{{ $jadwal->shift->jam_masuk }}</td>
                                        <td>{{ $jadwal->shift->jam_keluar }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        @media print {
            .card-header, form, .no-print {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-body {
                padding: 0 !important;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@stop
