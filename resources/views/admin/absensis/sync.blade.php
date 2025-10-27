@extends('adminlte::page')

@section('title', 'Sinkronisasi Absensi')

@section('content_header')
<h1>Sinkronisasi Absensi Realtime</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><i class="fas fa-info-circle"></i> Sinkronisasi data absensi sedang berjalan secara otomatis setiap 30 detik.</p>
                    </div>

                    <div id="sync-status" class="alert alert-secondary">
                        Menunggu sinkronisasi...
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Status Sinkronisasi</div>
                                <div class="card-body">
                                    <ul id="sync-log" class="list-group">
                                        <li class="list-group-item">Sistem siap</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Ringkasan Hari Ini</div>
                                <div class="card-body">
                                    <div id="summary-container">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>Total Karyawan:</div>
                                            <div id="total-count">0</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>Hadir:</div>
                                            <div id="hadir-count">0</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>Terlambat:</div>
                                            <div id="terlambat-count">0</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>Izin/Sakit/Cuti:</div>
                                            <div id="izin-count">0</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>Tidak Hadir:</div>
                                            <div id="alpha-count">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Data Absensi Hari Ini</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Karyawan</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Pulang</th>
                                            <th>Keterlambatan</th>
                                            <th>Pulang Awal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendance-data">
                                        <tr>
                                            <td colspan="6" class="text-center">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referensi ke elemen-elemen UI
        const syncStatus = document.getElementById('sync-status');
        const syncLog = document.getElementById('sync-log');
        const attendanceData = document.getElementById('attendance-data');

        // Elemen summary
        const totalCount = document.getElementById('total-count');
        const hadirCount = document.getElementById('hadir-count');
        const terlambatCount = document.getElementById('terlambat-count');
        const izinCount = document.getElementById('izin-count');
        const alphaCount = document.getElementById('alpha-count');

        // Fungsi untuk menambahkan log
        function addLog(message) {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = `${new Date().toLocaleTimeString()}: ${message}`;
            syncLog.prepend(li);

            // Batasi jumlah log yang ditampilkan
            if (syncLog.children.length > 10) {
                syncLog.removeChild(syncLog.lastChild);
            }
        }

        // Fungsi untuk memperbarui data absensi
        function updateAttendanceData() {
            fetch('/api/absensi/today')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.data.length > 0) {
                            let html = '';
                            data.data.forEach(item => {
                                html += `
                                    <tr>
                                        <td>${item.karyawan}</td>
                                        <td>${item.jam_masuk || '-'}</td>
                                        <td>${item.jam_pulang || '-'}</td>
                                        <td>${item.keterlambatan || '-'}</td>
                                        <td>${item.pulang_awal || '-'}</td>
                                        <td>
                                            <span class="badge ${getStatusClass(item.status)}">
                                                ${item.status}
                                            </span>
                                        </td>
                                    </tr>
                                `;
                            });
                            attendanceData.innerHTML = html;
                        } else {
                            attendanceData.innerHTML = '<tr><td colspan="6" class="text-center">Belum ada data absensi</td></tr>';
                        }
                    } else {
                        console.error('Error fetching attendance data:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching attendance data:', error);
                });
        }

        // Fungsi untuk memperbarui ringkasan
        function updateSummary() {
            fetch('/api/absensi/summary')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const summary = data.data;
                        totalCount.textContent = summary.total;
                        hadirCount.textContent = summary.hadir;
                        terlambatCount.textContent = summary.terlambat;
                        izinCount.textContent = summary.izin + summary.sakit + summary.cuti;
                        alphaCount.textContent = summary.alpha;
                    } else {
                        console.error('Error fetching summary:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching summary:', error);
                });
        }

        // Fungsi untuk mendapatkan class CSS berdasarkan status
        function getStatusClass(status) {
            switch (status) {
                case 'hadir':
                    return 'bg-success';
                case 'terlambat':
                    return 'bg-warning';
                case 'izin':
                case 'sakit':
                case 'cuti':
                    return 'bg-info';
                case 'alpha':
                    return 'bg-danger';
                default:
                    return 'bg-secondary';
            }
        }

        // Fungsi untuk sinkronisasi data
        function syncData() {
            syncStatus.className = 'alert alert-primary';
            syncStatus.innerHTML = '<i class="fas fa-sync fa-spin"></i> Sinkronisasi sedang berjalan...';

            fetch('/api/absensi/fetch-latest')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let newData = false;

                        data.data.forEach(machine => {
                            if (machine.count > 0) {
                                addLog(`${machine.machine}: ${machine.count} data baru`);
                                newData = true;
                            } else {
                                addLog(`${machine.machine}: Tidak ada data baru`);
                            }
                        });

                        if (newData) {
                            syncStatus.className = 'alert alert-success';
                            syncStatus.innerHTML = '<i class="fas fa-check-circle"></i> Data baru berhasil disinkronkan!';

                            // Update data absensi dan ringkasan
                            updateAttendanceData();
                            updateSummary();
                        } else {
                            syncStatus.className = 'alert alert-secondary';
                            syncStatus.innerHTML = '<i class="fas fa-info-circle"></i> Tidak ada data baru.';
                        }
                    } else {
                        syncStatus.className = 'alert alert-danger';
                        syncStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Gagal sinkronisasi: ' + data.message;
                        addLog('Gagal sinkronisasi: ' + data.message);
                    }
                })
                .catch(error => {
                    syncStatus.className = 'alert alert-danger';
                    syncStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error: ' + error.message;
                    addLog('Error: ' + error.message);
                    console.error('Error syncing data:', error);
                });
        }

        // Sinkronisasi pertama kali
        addLog('Sistem absensi dimulai');
        updateAttendanceData();
        updateSummary();

        // Jalankan sinkronisasi setiap 30 detik
        syncData();
        setInterval(syncData, 30000); // 30 detik

        // Perbarui data absensi setiap 15 detik
        setInterval(() => {
            updateAttendanceData();
            updateSummary();
        }, 15000); // 15 detik
    });
</script>
@stop