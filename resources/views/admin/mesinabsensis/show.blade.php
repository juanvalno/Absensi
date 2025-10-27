@extends('adminlte::page')

@section('title', 'Detail Mesin Absensi')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Detail Mesin Absensi</h1>
    <a href="{{ route('mesinabsensis.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="mr-1 fas fa-hdd"></i> Informasi Mesin Absensi
                </h3>
                <div class="card-tools">
                    @if($mesinabsensi->status_aktif)
                    <span class="badge badge-success">Aktif</span>
                    @else
                    <span class="badge badge-danger">Tidak Aktif</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Nama Mesin</th>
                        <td>{{ $mesinabsensi->nama }}</td>
                    </tr>
                    <tr>
                        <th>Alamat IP</th>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $mesinabsensi->alamat_ip }}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary copy-btn" type="button" data-clipboard-text="{{ $mesinabsensi->alamat_ip }}">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Kunci Komunikasi</th>
                        <td>{{ $mesinabsensi->kunci_komunikasi }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $mesinabsensi->lokasi }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $mesinabsensi->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $mesinabsensi->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $mesinabsensi->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                @can_show('mesinabsensis.edit')
                <a href="{{ route('mesinabsensis.edit', $mesinabsensi) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('mesinabsensis.toggle-status', $mesinabsensi) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn {{ $mesinabsensi->status_aktif ? 'btn-dark' : 'btn-light' }}">
                        <i class="fas fa-power-off"></i> {{ $mesinabsensi->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>

                @if($mesinabsensi->absensisMasuk()->count() == 0 && $mesinabsensi->absensisPulang()->count() == 0)
                <form action="{{ route('mesinabsensis.destroy', $mesinabsensi) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus mesin absensi ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
                @endif
                @endcan_show
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">
                    <i class="mr-1 fas fa-tools"></i> Manajemen Mesin
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-network-wired"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Koneksi</span>
                                <span class="info-box-number">
                                    <a href="{{ route('mesinabsensis.test-connection', $mesinabsensi) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plug"></i> Test Koneksi
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-download"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Log Absensi</span>
                                <span class="info-box-number">
                                    <a href="{{ route('mesinabsensis.download-logs', $mesinabsensi) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Download Log
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-user-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Karyawan</span>
                                <span class="info-box-number">
                                    <a href="{{ route('mesinabsensis.upload-names', $mesinabsensi) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-user-tag"></i> Upload Nama
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-sync"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sinkronisasi</span>
                                <span class="info-box-number">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#syncModal">
                                        <i class="fas fa-sync"></i> Sync Data
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 card">
            <div class="card-header bg-info">
                <h3 class="card-title">
                    <i class="mr-1 fas fa-chart-bar"></i> Statistik Penggunaan
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-sign-in-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Absensi Masuk</span>
                                <span class="info-box-number">{{ $mesinabsensi->absensisMasuk()->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-sign-out-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Absensi Pulang</span>
                                <span class="info-box-number">{{ $mesinabsensi->absensisPulang()->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 chart-container" style="position: relative; height:250px;">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1" role="dialog" aria-labelledby="syncModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">Sinkronisasi Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Pilih jenis sinkronisasi:</p>
                <div class="list-group">
                    <a href="{{ route('mesinabsensis.sync-all-users', $mesinabsensi) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Sinkronisasi Data Karyawan</h5>
                        </div>
                        <p class="mb-1">Upload semua data karyawan aktif ke mesin absensi</p>
                    </a>
                    <a href="{{ route('mesinabsensis.download-logs', $mesinabsensi) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Sinkronisasi Log Absensi</h5>
                        </div>
                        <p class="mb-1">Download dan proses log absensi dari mesin</p>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<!-- Additional CSS -->
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function() {
        // Initialize Clipboard.js
        var clipboard = new ClipboardJS('.copy-btn');
        clipboard.on('success', function(e) {
            $(e.trigger).tooltip({
                title: 'Disalin!'
                , trigger: 'manual'
            }).tooltip('show');

            setTimeout(function() {
                $(e.trigger).tooltip('hide');
            }, 1000);

            e.clearSelection();
        });

        // Usage chart
        var ctx = document.getElementById('usageChart').getContext('2d');

        // Fetch data for chart - this should be replaced with actual data
        // For demo purposes, we'll use sample data
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        var checkInData = [65, 59, 80, 81, 56, 55];
        var checkOutData = [60, 55, 75, 80, 52, 50];

        var chart = new Chart(ctx, {
            type: 'line'
            , data: {
                labels: months
                , datasets: [{
                    label: 'Absensi Masuk'
                    , data: checkInData
                    , backgroundColor: 'rgba(40, 167, 69, 0.2)'
                    , borderColor: 'rgba(40, 167, 69, 1)'
                    , borderWidth: 2
                    , pointBackgroundColor: 'rgba(40, 167, 69, 1)'
                    , tension: 0.4
                }, {
                    label: 'Absensi Pulang'
                    , data: checkOutData
                    , backgroundColor: 'rgba(255, 193, 7, 0.2)'
                    , borderColor: 'rgba(255, 193, 7, 1)'
                    , borderWidth: 2
                    , pointBackgroundColor: 'rgba(255, 193, 7, 1)'
                    , tension: 0.4
                }]
            }
            , options: {
                responsive: true
                , maintainAspectRatio: false
                , scales: {
                    y: {
                        beginAtZero: true
                        , title: {
                            display: true
                            , text: 'Jumlah Absensi'
                        }
                    }
                    , x: {
                        title: {
                            display: true
                            , text: 'Bulan'
                        }
                    }
                }
            }
        });
    });

</script>
@stop
