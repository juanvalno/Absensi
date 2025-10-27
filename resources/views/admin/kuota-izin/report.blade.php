@extends('adminlte::page')

@section('title', 'Laporan Kuota Izin Tahunan')

@section('content_header')
    <h1>Laporan Kuota Izin Tahunan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('kuota-izin.report') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tahun">Tahun</label>
                            <select name="tahun" id="tahun" class="form-control">
                                <option value="">Semua Tahun</option>
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="karyawan_id">Karyawan</label>
                            <select name="karyawan_id" id="karyawan_id" class="form-control select2">
                                <option value="">Semua Karyawan</option>
                                @foreach ($karyawan as $k)
                                    <option value="{{ $k->id }}"
                                        {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_karyawan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <a href="{{ route('kuota-izin.report') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-sync"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Penggunaan Kuota Izin</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Karyawan</th>
                            <th>Tahun</th>
                            <th>Kuota Awal</th>
                            <th>Digunakan</th>
                            <th>Sisa</th>
                            <th>Expired</th>
                            <th>Detail Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kuotaIzin as $index => $kuota)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $kuota->karyawan->nama_karyawan }}</td>
                                <td>{{ $kuota->tahun }}</td>
                                <td>{{ $kuota->kuota_awal }}</td>
                                <td>{{ $kuota->kuota_digunakan }}</td>
                                <td>{{ $kuota->kuota_sisa }}</td>
                                <td>{{ $kuota->tanggal_expired ? date('d/m/Y', strtotime($kuota->tanggal_expired)) : '-' }}
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @forelse($kuota->izinKaryawan as $izin)
                                            <li class="mb-1">
                                                <span class="badge badge-info">
                                                    {{ date('d/m/Y', strtotime($izin->tanggal_mulai)) }} -
                                                    {{ date('d/m/Y', strtotime($izin->tanggal_selesai)) }}
                                                    ({{ $izin->jumlah_hari }} hari)
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $izin->keterangan }}</small>
                                            </li>
                                        @empty
                                            <li>Belum ada penggunaan izin</li>
                                        @endforelse
                                    </ul>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Karyawan"
            });

            $('.table').DataTable({
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@stop
