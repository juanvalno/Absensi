@extends('adminlte::page')

@section('title', 'Download Log Per Karyawan')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Download Log Per Karyawan</h1>
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
                <h3 class="card-title">Filter Karyawan</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('mesinabsensis.download-logs-user') }}" method="GET">
                    <input type="hidden" name="mesin_id" value="{{ $mesinabsensi->id }}">

                    <div class="form-group">
                        <label for="user_id">NIK Karyawan</label>
                        <input type="text" class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" value="{{ request('user_id') }}" required>
                        <small class="form-text text-muted">Masukkan NIK (KTP) karyawan</small>
                        @error('user_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari
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
                <h3 class="card-title">Cari Karyawan</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="search_karyawan">Cari Berdasarkan Nama</label>
                    <input type="text" class="form-control" id="search_karyawan" placeholder="Ketik nama karyawan (min. 3 karakter)">
                </div>
                <div id="search_results" class="mt-2" style="display: none;">
                    <div class="list-group" id="search_results_list">
                        <!-- Search results will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Log Absensi
                    @if(request('user_id'))
                    untuk NIK: {{ request('user_id') }}
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
                    <i class="fas fa-info-circle"></i> Tidak ada log absensi yang ditemukan untuk karyawan dengan NIK {{ request('user_id') }}.
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Silakan masukkan NIK karyawan dan klik tombol "Cari" untuk melihat log absensi.
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
                [2, 'desc']
                , [3, 'desc']
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
                    'Tanggal': $(this).find('td:eq(2)').text()
                    , 'Jam': $(this).find('td:eq(3)').text()
                    , 'Verifikasi': $(this).find('td:eq(4)').text().trim()
                    , 'Status': $(this).find('td:eq(5)').text().trim()
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
            link.setAttribute('download', 'log_absensi_{{ request('
                user_id ') }}_{{ date("Ymd") }}.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
        @endif

        // Handle search for employees
        let searchTimeout;
        $('#search_karyawan').on('keyup', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val();

            if (query.length < 3) {
                $('#search_results').hide();
                return;
            }

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: '{{ route("karyawans.search") }}'
                    , method: 'GET'
                    , data: {
                        q: query
                    }
                    , dataType: 'json'
                    , success: function(response) {
                        let html = '';

                        if (response.data.length === 0) {
                            html = '<div class="list-group-item">Tidak ada hasil yang ditemukan</div>';
                        } else {
                            response.data.forEach(function(karyawan) {
                                html += `
                                    <a href="{{ route('mesinabsensis.download-logs-user') }}?mesin_id={{ $mesinabsensi->id }}&user_id=${karyawan.nik}"
                                       class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">${karyawan.nama_karyawan}</h5>
                                            <small>${karyawan.nik || 'NIK tidak tersedia'}</small>
                                        </div>
                                        <p class="mb-1">${karyawan.bagian ? karyawan.bagian.name_bagian : 'Bagian tidak tersedia'}</p>
                                    </a>
                                `;
                            });
                        }

                        $('#search_results_list').html(html);
                        $('#search_results').show();
                    }
                    , error: function(xhr) {
                        console.error('Error searching for employees:', xhr);
                        $('#search_results_list').html('<div class="list-group-item text-danger">Terjadi kesalahan saat mencari</div>');
                        $('#search_results').show();
                    }
                });
            }, 500);
        });
    });

</script>
@stop
