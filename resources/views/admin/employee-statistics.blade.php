@extends('adminlte::page')

@section('title', 'Statistik Karyawan')

@section('content_header')
    <h1>Statistik Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Statistik Bulanan Karyawan</h3>
                <div class="form-group mb-0">
                    <select id="yearSelect" class="form-control" onchange="updateChart(this.value)">
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <canvas id="monthlyChart" height="400"></canvas>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let monthlyChart;

        function updateChart(year) {
            window.location.href = '{{ route("admin.employee-statistics") }}?year=' + year;
        }

        // Initialize the chart
        const monthlyData = @json($monthlyData);
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        const newEmployees = Array(12).fill(0);
        const resignations = Array(12).fill(0);
        const activeEmployees = Array(12).fill(0);

        monthlyData.forEach(data => {
            newEmployees[data.month - 1] = data.new_employees;
            resignations[data.month - 1] = data.resignations;
            activeEmployees[data.month - 1] = data.active_employees;
        });

        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Karyawan Aktif',
                    data: activeEmployees,
                    type: 'line',
                    borderColor: '#00a65a',
                    backgroundColor: '#00a65a',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4
                }, {
                    label: 'Karyawan Masuk',
                    data: newEmployees,
                    backgroundColor: '#00c0ef',
                    borderWidth: 1
                }, {
                    label: 'Karyawan Resign',
                    data: resignations,
                    backgroundColor: '#f56954',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
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
                    title: {
                        display: true,
                        text: 'Statistik Karyawan Tahun ' + {{ $selectedYear }}
                    }
                }
            }
        });
    </script>
@stop

@section('css')
    <style>
        .form-group {
            min-width: 150px;
        }
    </style>
@stop