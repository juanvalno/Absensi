@extends('adminlte::page')

@section('title', 'Owner Dashboard')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <h1><i class="fas fa-crown mr-2"></i>Owner Dashboard</h1>
    <div>
        <span class="text-muted"><i class="fas fa-calendar-alt mr-1"></i> {{ date('l, d F Y') }}</span>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <!-- Executive Summary Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-primary">
            <div class="inner">
                <h3>Rp 1.2<sup style="font-size: 20px">M</sup></h3>
                <p>Total Pengeluaran Gaji</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-check-alt"></i>
            </div>
            <a href="#" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-success">
            <div class="inner">
                <h3>350</h3>
                <p>Total Karyawan</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="#" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-info">
            <div class="inner">
                <h3>15<sup style="font-size: 20px">%</sup></h3>
                <p>Pertumbuhan YoY</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <a href="#" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-warning">
            <div class="inner">
                <h3>8</h3>
                <p>Departemen</p>
            </div>
            <div class="icon">
                <i class="fas fa-building"></i>
            </div>
            <a href="#" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Ringkasan Keuangan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="financialChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i>
                    Perbandingan Departemen
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Departemen</th>
                            <th>Jumlah Staff</th>
                            <th>Total Gaji</th>
                            <th>Rata-rata</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>IT</td>
                            <td>45</td>
                            <td>Rp 225.000.000</td>
                            <td>Rp 5.000.000</td>
                            <td><span class="badge bg-success">Optimal</span></td>
                        </tr>
                        <tr>
                            <td>Marketing</td>
                            <td>38</td>
                            <td>Rp 190.000.000</td>
                            <td>Rp 5.000.000</td>
                            <td><span class="badge bg-success">Optimal</span></td>
                        </tr>
                        <tr>
                            <td>Finance</td>
                            <td>25</td>
                            <td>Rp 150.000.000</td>
                            <td>Rp 6.000.000</td>
                            <td><span class="badge bg-warning">Review</span></td>
                        </tr>
                        <tr>
                            <td>HR</td>
                            <td>15</td>
                            <td>Rp 75.000.000</td>
                            <td>Rp 5.000.000</td>
                            <td><span class="badge bg-success">Optimal</span></td>
                        </tr>
                        <tr>
                            <td>Operations</td>
                            <td>120</td>
                            <td>Rp 480.000.000</td>
                            <td>Rp 4.000.000</td>
                            <td><span class="badge bg-danger">Over</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Perhatian Khusus
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-arrow-up text-danger mr-2"></i> Kenaikan biaya gaji 8%</span>
                            <span class="badge bg-danger">Penting</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-times text-warning mr-2"></i> Turnover rate 5%</span>
                            <span class="badge bg-warning">Perhatian</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-clock text-info mr-2"></i> Overtime meningkat 12%</span>
                            <span class="badge bg-info">Info</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-1"></i>
                    Jadwal Meeting
                </h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <h5>Board Meeting</h5>
                    <p>Senin, 10 Juli 2023 - 10:00 WIB</p>
                </div>
                <div class="callout callout-warning">
                    <h5>Financial Review</h5>
                    <p>Rabu, 12 Juli 2023 - 13:00 WIB</p>
                </div>
                <div class="callout callout-success">
                    <h5>Strategic Planning</h5>
                    <p>Jumat, 14 Juli 2023 - 09:00 WIB</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-1"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-pdf mr-2 text-danger"></i> Download Laporan Keuangan
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope mr-2 text-primary"></i> Kirim Memo ke Direktur
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-check-circle mr-2 text-success"></i> Approve Budget Request
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .small-box {
        border-radius: 10px;
        transition: all 0.3s;
    }
    .small-box:hover {
        transform: translateY(-5px);
    }
    .callout {
        border-radius: 8px;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function() {
        // Financial Chart
        var ctx = document.getElementById('financialChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Pengeluaran Gaji (dalam juta)',
                    data: [1100, 1150, 1180, 1200, 1220, 1250],
                    backgroundColor: 'rgba(60, 141, 188, 0.7)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 1000
                    }
                }
            }
        });
    });
</script>
@stop