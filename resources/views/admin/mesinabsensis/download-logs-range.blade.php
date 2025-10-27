@extends('adminlte::page')

@section('title', 'Download Log Berdasarkan Tanggal')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Download Log Berdasarkan Tanggal</h1>
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
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Tanggal</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('mesinabsensis.download-logs-range') }}" method="GET">
                    <input type="hidden" name="mesin_id" value="{{ $mesinabsensi->id }}">

                    <div class="form-group">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ request('start_date', date('Y-m-d', strtotime('-7 days'))) }}" required>
                        @error('start_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" required>
                        @error('end_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('mesinabsensis.download-logs', $mesinabsensi) }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4 card">
            <div class="card-header bg-info">
                <h3 class="card-title">Informasi Mesin</h3>
            </div>
            <div class="p-0 card-body">
                <table class="table mb-0 table-striped">
                    <tr>
                        <th>Nama Mesin</th>
                        <td>{{ $mesinabsensi->nama }}</td>
                    </tr>
                    <tr>
                        <th>Alamat IP</th>
                        <td>{{ $mesinabsensi->alamat_ip }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $mesinabsensi->lokasi }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-4 card">
            <div class="card-header bg-success">
                <h3 class="card-title">Pilihan Tanggal Cepat</h3>
            </div>
            <div class="card-body">
                <div class="btn-group-vertical w-100">
                    <a href="{{ route('mesinabsensis.download-logs-range', ['mesin_id' => $mesinabsensi->id, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" class="text-left btn btn-outline-primary">
                        <i class="mr-2 fas fa-calendar-day"></i> Hari Ini
                    </a>
                    <a href="{{ route('mesinabsensis.download-logs-range', ['mesin_id' => $mesinabsensi->id, 'start_date' => date('Y-m-d', strtotime('yesterday')), 'end_date' => date('Y-m-d', strtotime('yesterday'))]) }}" class="text-left btn btn-outline-primary">
                        <i class="mr-2 fas fa-calendar-day"></i> Kemarin
                    </a>
                    <a href="{{ route('mesinabsensis.download-logs-range', ['mesin_id' => $mesinabsensi->id, 'start_date' => date('Y-m-d', strtotime('-7 days')), 'end_date' => date('Y-m-d')]) }}" class="text-left btn btn-outline-primary">
                        <i class="mr-2 fas fa-calendar-week"></i> 7 Hari Terakhir
                    </a>
                    <a href="{{ route('mesinabsensis.download-logs-range', ['mesin_id' => $mesinabsensi->id, 'start_date' => date('Y-m-d', strtotime('-30 days')), 'end_date' => date('Y-m-d')]) }}" class="text-left btn btn-outline-primary">
                        <i class="mr-2 fas fa-calendar-alt"></i> 30 Hari Terakhir
                    </a>
                    <a href="{{ route('mesinabsensis.download-logs-range', ['mesin_id' => $mesinabsensi->id, 'start_date' => date('Y-m-01'), 'end_date' => date('Y-m-t')]) }}" class="text-left btn btn-outline-primary">
                        <i class="mr-2 fas fa-calendar-alt"></i> Bulan Ini
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Log Absensi
                    @if(request('start_date') && request('end_date'))
                    ({{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }}
                    s/d
                    {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }})
                    @endif
                </h3>
                <div class="card-tools">
                    @if(isset($filteredLogs) && count($filteredLogs) > 0)
                    <button class="btn btn-sm btn-success" id="btnExportCSV">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if(isset($filteredLogs) && count($filteredLogs) > 0)
                <form action="{{ route('mesinabsensis.process-logs', $mesinabsensi) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-import"></i> Proses Log ke Data Absensi
                        </button>
                        <button type="button" class="btn btn-info" id="btnSelectAll">
                            <i class="fas fa-check-square"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-secondary" id="btnDeselectAll">
                            <i class="fas fa-square"></i> Batalkan Pilihan
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="logsTable">
                            <thead>
                                <tr>
                                    <th width="10"><input type="checkbox" id="checkAll"></th>
                                    <th width="10">#</th>
                                    <th>NIK</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Verifikasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredLogs as $index => $log)
                                @php
                                $dateTime = \Carbon\Carbon::parse($log['datetime']);
                                $date = $dateTime->format('Y-m-d');
                                $time = $dateTime->format('H:i:s');
                                $status = $log['status'];
                                @endphp
                                <tr data-status="{{ $status }}" class="log-row">
                                    <td>
                                        <input type="checkbox" name="selected_logs[]" value="{{ $log['pin'] }}_{{ $log['datetime'] }}" class="log-checkbox">
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
                @elseif(isset($filteredLogs) && count($filteredLogs) == 0)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Tidak ada log absensi yang ditemukan pada rentang tanggal yang dipilih.
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Silakan pilih rentang tanggal dan klik tombol "Filter" untuk melihat log absensi.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
@stop

@section('js')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(function() {
        // Initialize DataTable if there's data
        @if(isset($filteredLogs) && count($filteredLogs) > 0)
        var table = $('#logsTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "{{ asset('vendor/datatables/js/indonesian.json') }}"
            },
            "columnDefs": [{
                "orderable": false,
                "targets": [0]
            }],
            "order": [
                [3, 'desc'],
                [4, 'desc']
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

        // Export CSV button
        $('#btnExportCSV').on('click', function() {
            // Get data from table
            var data = [];
            $('#logsTable tbody tr').each(function() {
                var rowData = {
                    'NIK': $(this).find('td:eq(2)').text(),
                    'Tanggal': $(this).find('td:eq(3)').text(),
                    'Jam': $(this).find('td:eq(4)').text(),
                    'Verifikasi': $(this).find('td:eq(5)').text().trim(),
                    'Status': $(this).find('td:eq(6)').text().trim()
                };
                data.push(rowData);
            });

            if (data.length === 0) {
                alert('Tidak ada data untuk diekspor.');
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
        @endif

        // Date range validation
        $('#start_date, #end_date').on('change', function() {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();

            if (startDate && endDate) {
                if (new Date(startDate) > new Date(endDate)) {
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');
                    $('#end_date').val(startDate);
                }
            }
        });
    });
</script>
@stop