@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="text-white mb-0"><i class="fas fa-chart-line mr-2"></i>Selamat Datang di Sistem E-Gaji
                            </h2>
                            <p class="text-white lead mt-3 mb-0">
                                Kelola penggajian karyawan dengan mudah, cepat, dan akurat.
                                <br>Silahkan gunakan menu di sidebar untuk navigasi.
                            </p>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <i class="fas fa-money-bill-wave fa-5x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Karyawan</h3>
                    <p>Kelola data karyawan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('karyawans.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Penggajian</h3>
                    <p>Proses penggajian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Laporan</h3>
                    <p>Lihat dan cetak laporan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Chart Cards -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status Karyawan</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Jenis Karyawan</h3>
                </div>
                <div class="card-body">
                    <canvas id="employeeTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Existing chart cards -->
    </div>

    <!-- Add new row for yearly trend -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Tren Karyawan per Tahun</h3>
                    <a href="{{ route('admin.employee-statistics') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-chart-bar mr-1"></i> Detail Statistik
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="yearlyTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Status Karyawan Chart (Doughnut)
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Resign'],
                datasets: [{
                    data: [{{ $activeCount }}, {{ $resignCount }}],
                    backgroundColor: ['#00a65a', '#f56954'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Employee Type Chart (Bar)
        new Chart(document.getElementById('employeeTypeChart'), {
            type: 'bar',
            data: {
                labels: ['Bulanan', 'Harian', 'Borongan'],
                datasets: [{
                    label: 'Jumlah Karyawan',
                    data: [{{ $bulananCount }}, {{ $harianCount }}, {{ $boronganCount }}],
                    backgroundColor: ['#00c0ef', '#f39c12', '#605ca8'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Yearly Trend Chart
        const yearlyData = @json($yearlyData);
        const yearlyResign = @json($yearlyResign);

        // Process data for chart
        const years = yearlyData.map(item => item.year);
        const joinData = yearlyData.map(item => item.total);
        const resignData = yearlyResign.map(item => item.total);

        // Calculate cumulative totals
        let cumulativeTotal = 0;
        const activeEmployees = years.map((year, index) => {
            cumulativeTotal += joinData[index];
            const resignsThisYear = resignData[index] || 0;
            cumulativeTotal -= resignsThisYear;
            return cumulativeTotal;
        });

        new Chart(document.getElementById('yearlyTrendChart'), {
            type: 'line',
            data: {
                labels: years,
                datasets: [{
                    label: 'Total Karyawan Aktif',
                    data: activeEmployees,
                    borderColor: '#00a65a',
                    tension: 0.1,
                    fill: false
                }, {
                    label: 'Karyawan Masuk',
                    data: joinData,
                    borderColor: '#00c0ef',
                    tension: 0.1,
                    fill: false
                }, {
                    label: 'Karyawan Resign',
                    data: resignData,
                    borderColor: '#f56954',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    </script>
@stop
