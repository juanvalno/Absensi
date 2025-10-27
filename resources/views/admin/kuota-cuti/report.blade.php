@extends('adminlte::page')

@section('title', 'Laporan Kuota Cuti Tahunan')

@section('content_header')
    <h1>Laporan Kuota Cuti Tahunan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Laporan Penggunaan Kuota Cuti</h3>
                <div>
                    <a href="{{ route('kuota-cuti.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('kuota-cuti.report') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tahun">Pilih Tahun</label>
                            <select name="tahun" id="tahun" class="form-control">
                                @foreach($tahunList as $tahun)
                                    <option value="{{ $tahun }}" {{ $selectedTahun == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="departemen_id">Departemen</label>
                            <select name="departemen_id" id="departemen_id" class="form-control">
                                <option value="">Semua Departemen</option>
                                @foreach($departemens as $departemen)
                                    <option value="{{ $departemen->id }}" {{ request('departemen_id') == $departemen->id ? 'selected' : '' }}>
                                        {{ $departemen->name_departemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status_karyawan">Status Karyawan</label>
                            <select name="status_karyawan" id="status_karyawan" class="form-control">
                                <option value="bulanan" {{ request('status_karyawan', 'bulanan') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="all" {{ request('status_karyawan') == 'all' ? 'selected' : '' }}>Semua Status</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('kuota-cuti.report') }}?tahun={{ $selectedTahun }}&status_karyawan=bulanan" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Karyawan</th>
                            <th>Departemen</th>
                            <th>Kuota Awal</th>
                            <th>Kuota Digunakan</th>
                            <th>Kuota Sisa</th>
                            <th>Persentase Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kuotaReport as $index => $kuota)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $kuota->nama_karyawan }}</td>
                                <td>{{ $kuota->name_departemen }}</td>
                                <td>{{ $kuota->kuota_awal }} hari</td>
                                <td>{{ $kuota->kuota_digunakan }} hari</td>
                                <td>
                                    <span class="badge {{ $kuota->kuota_sisa > 3 ? 'bg-success' : ($kuota->kuota_sisa > 0 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $kuota->kuota_sisa }} hari
                                    </span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar {{ $kuota->persentase_penggunaan > 75 ? 'bg-danger' : ($kuota->persentase_penggunaan > 50 ? 'bg-warning' : 'bg-success') }}"
                                             role="progressbar"
                                             style="width: {{ $kuota->persentase_penggunaan }}%"
                                             aria-valuenow="{{ $kuota->persentase_penggunaan }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                            {{ number_format($kuota->persentase_penggunaan, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data kuota cuti untuk tahun {{ $selectedTahun }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Penggunaan Cuti</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="kuotaCutiChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Ringkasan</h3>
                            </div>
                            <div class="card-body">
                                <p>Tahun: <strong>{{ $selectedTahun }}</strong></p>
                                <p>Total Karyawan: <strong>{{ count($kuotaReport) }}</strong></p>
                                <p>Rata-rata Penggunaan Cuti: <strong>{{ $kuotaReport->avg('kuota_digunakan') > 0 ? number_format($kuotaReport->avg('kuota_digunakan'), 1) : 0 }} hari</strong></p>
                                <p>Rata-rata Sisa Cuti: <strong>{{ $kuotaReport->avg('kuota_sisa') > 0 ? number_format($kuotaReport->avg('kuota_sisa'), 1) : 0 }} hari</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .badge.bg-success {
            background-color: #28a745;
            color: white;
        }
        .badge.bg-warning {
            background-color: #ffc107;
            color: black;
        }
        .badge.bg-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Data untuk chart
            const labels = {!! json_encode($kuotaReport->pluck('nama_karyawan')) !!};
            const dataKuotaAwal = {!! json_encode($kuotaReport->pluck('kuota_awal')) !!};
            const dataKuotaDigunakan = {!! json_encode($kuotaReport->pluck('kuota_digunakan')) !!};
            const dataKuotaSisa = {!! json_encode($kuotaReport->pluck('kuota_sisa')) !!};

            // Buat chart
            const ctx = document.getElementById('kuotaCutiChart').getContext('2d');
            const kuotaCutiChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Kuota Awal',
                            data: dataKuotaAwal,
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Kuota Digunakan',
                            data: dataKuotaDigunakan,
                            backgroundColor: 'rgba(210, 214, 222, 0.8)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Kuota Sisa',
                            data: dataKuotaSisa,
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Hari'
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop