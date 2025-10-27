@extends('adminlte::page')

@section('title', 'Log Mesin Absensi')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Log Mesin Absensi</h1>
    <div>
        <a href="{{ route('mesinabsensis.show', $mesinabsensi) }}" class="mr-1 btn btn-info">
            <i class="fas fa-info-circle"></i> Detail Mesin
        </a>
        <a href="{{ route('mesinabsensis.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Log Absensi dari {{ $mesinabsensi->nama }} ({{ $mesinabsensi->alamat_ip }})</h3>
    </div>
    <div class="card-body">
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

        @if(is_array($logs) && count($logs) > 0)
        <form action="{{ route('mesinabsensis.process-logs', $mesinabsensi) }}" method="POST">
            @csrf
            <div class="mb-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-import"></i> Proses Log ke Data Absensi
                </button>
                <button type="button" class="btn btn-info" id="btnSelectAll">
                    <i class="fas fa-check-square"></i> Pilih Semua
                </button>
                <button type="button" class="btn btn-secondary" id="btnDeselectAll">
                    <i class="fas fa-square"></i> Batalkan Pilihan
                </button>
                <div class="float-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-filter="all">Semua</a>
                            <a class="dropdown-item" href="#" data-filter="0">Check-In (Masuk)</a>
                            <a class="dropdown-item" href="#" data-filter="1">Check-Out (Pulang)</a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnExportCSV">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="logsTable">
                    <thead>
                        <tr>
                            <th width="10"><input type="checkbox" id="checkAll"></th>
                            <th width="10">#</th>
                            <th>ID Karyawan</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Verifikasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $index => $log)
                        @php
                        $dateTime = \Carbon\Carbon::parse($log['datetime']);
                        $date = $dateTime->format('Y-m-d');
                        $time = $dateTime->format('H:i:s');
                        $status = $log['status'];
                        @endphp
                        <tr data-status="{{ $status }}" class="log-row">
                            <td>
                                <input type="checkbox" name="selected_logs[]" value="{{ $log['pin'] }}_{{ $log['datetime'] }}_{{ $log['status'] }}" class="log-checkbox">
                            </td>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $log['pin'] }}</td>
                            <td>{{ $date }}</td>
                            <td>{{ $time }}</td>
                            <td>
                                @if($log['verified'] == '1')
                                <span class="badge badge-success">Terverifikasi</span>
                                @else
                                <span class="badge badge-warning">Tidak Terverifikasi</span>
                                @endif
                            </td>
                            <td>
                                @if($status == 0)
                                <span class="badge badge-primary">Masuk</span>
                                @elseif($status == 1)
                                <span class="badge badge-info">Pulang</span>
                                @else
                                <span class="badge badge-secondary">{{ $status }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Tidak ada log absensi yang ditemukan pada mesin ini.
        </div>
        @endif
    </div>
</div>

<!-- Card for statistics -->
<div class="mt-4 card">
    <div class="card-header">
        <h3 class="card-title">Statistik Log</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Tanggal</span>
                        <span class="info-box-number" id="totalDates">0</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Karyawan</span>
                        <span class="info-box-number" id="totalUsers">0</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-sign-in-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Masuk</span>
                        <span class="info-box-number" id="totalCheckins">0</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-sign-out-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pulang</span>
                        <span class="info-box-number" id="totalCheckouts">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
<style>
    .dataTables_wrapper .dt-buttons {
        float: left;
        margin-bottom: 15px;
    }

</style>
@stop

@section('js')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('vendor/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/pdfmake/pdfmake.min.js') }}"></script>
<script>
    $(function() {
        // Initialize DataTable
        var table = $('#logsTable').DataTable({
            "paging": true
            , "lengthChange": true
            , "searching": true
            , "ordering": true
            , "info": true
            , "autoWidth": false
            , "responsive": true
            , "language": {
                "url": "{{ asset('vendor/datatables/js/indonesian.json') }}"
            }
            , "columnDefs": [{
                "orderable": false
                , "targets": [0]
            }]
            , "order": [
                [3, 'desc']
                , [4, 'desc']
            ] // Sort by date & time
        });

        // Check all boxes
        $('#checkAll').on('click', function() {
            $('.log-checkbox:visible').prop('checked', this.checked);
        });

        // Select all button
        $('#btnSelectAll').on('click', function() {
            $('.log-checkbox:visible').prop('checked', true);
            $('#checkAll').prop('checked', $('.log-checkbox:visible').length === $('.log-checkbox:visible').length);
        });

        // Deselect all button
        $('#btnDeselectAll').on('click', function() {
            $('.log-checkbox:visible').prop('checked', false);
            $('#checkAll').prop('checked', false);
        });

        // Filter dropdown
        $('[data-filter]').on('click', function(e) {
            e.preventDefault();
            var filterValue = $(this).data('filter');

            if (filterValue === 'all') {
                table.search('').columns().search('').draw();
            } else {
                table.column(6).search(filterValue === '0' ? 'Masuk' : 'Pulang', true, false).draw();
            }
        });

        // Export CSV button
        $('#btnExportCSV').on('click', function() {
            // Get only checked rows
            var data = [];
            $('.log-checkbox:checked').each(function() {
                var $row = $(this).closest('tr');
                var rowData = {
                    'ID Karyawan': $row.find('td:eq(2)').text()
                    , 'Tanggal': $row.find('td:eq(3)').text()
                    , 'Jam': $row.find('td:eq(4)').text()
                    , 'Status': $row.find('td:eq(6)').text().trim()
                };
                data.push(rowData);
            });

            if (data.length === 0) {
                alert('Tidak ada data yang dipilih untuk diekspor.');
                return;
            }

            // Convert to CSV
            var csv = '';
            var headers = Object.keys(data[0]);
            csv += headers.join(',') + '\r\n';

            data.forEach(function(row) {
                var values = headers.map(header => {
                    var val = row[header];
                    return '"' + val.replace(/"/g, '""') + '"';
                });
                csv += values.join(',') + '\r\n';
            });

            // Download CSV file
            var blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'log_absensi_{{ $mesinabsensi->nama }}_{{ date("Ymd") }}.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Calculate statistics
        function calculateStats() {
            // Total dates
            var dates = {};
            $('.log-row:visible').each(function() {
                var date = $(this).find('td:eq(3)').text();
                dates[date] = true;
            });
            $('#totalDates').text(Object.keys(dates).length);

            // Total users
            var users = {};
            $('.log-row:visible').each(function() {
                var user = $(this).find('td:eq(2)').text();
                users[user] = true;
            });
            $('#totalUsers').text(Object.keys(users).length);

            // Total check-ins
            var checkins = $('.log-row[data-status="0"]:visible').length;
            $('#totalCheckins').text(checkins);

            // Total check-outs
            var checkouts = $('.log-row[data-status="1"]:visible').length;
            $('#totalCheckouts').text(checkouts);
        }

        // Calculate initial stats
        calculateStats();

        // Recalculate on filter/search
        table.on('search.dt', calculateStats);
    });

</script>
@stop
