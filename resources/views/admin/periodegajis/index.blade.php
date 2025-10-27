@extends('adminlte::page')

@section('title', 'Manage Periode Gaji')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="mr-2 fas fa-calendar-alt text-primary"></i>Manage Periode Gaji</h1>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mr-1 fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mr-1 fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Left sidebar with years and months -->
        <div class="col-md-4 col-lg-3">
            <div class="card">
                <div class="p-0 card-body">
                    <div class="p-3 compose-btn-container">
                        <a href="{{ route('periodegaji.create') }}" class="btn btn-primary btn-block">
                            <i class="mr-1 fas fa-plus"></i> Tambah Periode
                        </a>
                        <button type="button" class="mt-2 btn btn-success btn-block" data-toggle="modal"
                            data-target="#generateModal">
                            <i class="mr-1 fas fa-calendar"></i> Generate Bulanan
                        </button>
                        <button type="button" class="mt-2 btn btn-info btn-block" data-toggle="modal"
                            data-target="#generateWeeklyModal">
                            <i class="mr-1 fas fa-calendar-week"></i> Generate Mingguan
                        </button>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action active filter-year"
                            data-filter="all">
                            <i class="mr-2 fas fa-calendar"></i> Semua Periode
                            <span class="float-right badge badge-light">{{ count($periodeGajis) }}</span>
                        </a>

                        @php
                            $years = $periodeGajis->groupBy(function ($date) {
                                return $date->tanggal_mulai->format('Y');
                            });

                            $currentYear = date('Y');
                        @endphp

                        @foreach ($years as $year => $items)
                            <a href="#" class="list-group-item list-group-item-action filter-year"
                                data-filter="{{ $year }}" data-toggle="collapse"
                                data-target="#months-{{ $year }}">
                                <i class="mr-2 fas fa-calendar-alt"></i> {{ $year }}
                                <span class="float-right badge badge-info">{{ count($items) }}</span>
                            </a>

                            <div id="months-{{ $year }}" class="collapse {{ $year == $currentYear ? 'show' : '' }}">
                                @php
                                    $months = $items->groupBy(function ($date) {
                                        return $date->tanggal_mulai->format('m');
                                    });

                                    $monthNames = [
                                        '01' => 'Januari',
                                        '02' => 'Februari',
                                        '03' => 'Maret',
                                        '04' => 'April',
                                        '05' => 'Mei',
                                        '06' => 'Juni',
                                        '07' => 'Juli',
                                        '08' => 'Agustus',
                                        '09' => 'September',
                                        '10' => 'Oktober',
                                        '11' => 'November',
                                        '12' => 'Desember',
                                    ];
                                @endphp

                                @foreach ($months as $month => $monthItems)
                                    <a href="#" class="pl-5 list-group-item list-group-item-action filter-month"
                                        data-year="{{ $year }}" data-month="{{ $month }}">
                                        <i class="mr-2 fas fa-calendar-day"></i> {{ $monthNames[$month] }}
                                        <span class="float-right badge badge-secondary">{{ count($monthItems) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side with data table -->
        <div class="col-md-8 col-lg-9">
            <div class="card">
                <div class="bg-white card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title" id="current-filter">Semua Periode Gaji</h3>
                        <div>
                            <button type="button" class="btn btn-danger btn-sm" id="deleteSelected" disabled>
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                            <button type="button" class="btn btn-light" id="refreshBtn">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="deleteMultipleForm" action="{{ url('/admin/periodegaji/delete-multiple') }}" method="POST">
                        @csrf
                        <table id="periodegaji-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50px">
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="selectAll">
                                            <label for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>No</th>
                                    <th>Nama Periode</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aktif/Nonaktif</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($periodeGajis as $index => $periode)
                                    <tr data-id="{{ $periode->id }}"
                                        data-year="{{ $periode->tanggal_mulai->format('Y') }}"
                                        data-month="{{ $periode->tanggal_mulai->format('m') }}">
                                        <td>
                                            <div class="icheck-primary">
                                                <input type="checkbox" class="periode-checkbox"
                                                    id="check{{ $periode->id }}" name="ids[]"
                                                    value="{{ $periode->id }}"
                                                    {{ $periode->status == 'aktif' ? 'disabled' : '' }}>
                                                <label for="check{{ $periode->id }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $periode->nama_periode }}</td>
                                        <td>{{ $periode->tanggal_mulai->format('d-m-Y') }}</td>
                                        <td>{{ $periode->tanggal_selesai->format('d-m-Y') }}</td>
                                        <td>
                                            @if ($periode->status == 'aktif')
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input status-toggle"
                                                    id="statusToggle{{ $periode->id }}"
                                                    data-id="{{ $periode->id }}"
                                                    {{ $periode->status == 'aktif' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="statusToggle{{ $periode->id }}"></label>
                                            </div>
                                            <form id="set-active-form-{{ $periode->id }}"
                                                action="{{ route('periodegaji.set-active', $periode->id) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('periodegaji.show', $periode->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('periodegaji.edit', $periode->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('periodegaji.destroy', $periode->id) }}"
                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this period?')) document.getElementById('delete-form-{{ $periode->id }}').submit();"
                                                    class="btn btn-danger btn-sm" {{ $periode->status == 'aktif' ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $periode->id }}"
                                                    action="{{ route('periodegaji.destroy', $periode->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Monthly Periods Modal -->
    <div class="modal fade" id="generateModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Generate Periode Bulanan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('periodegaji.generate-monthly') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="year">Tahun</label>
                            <input type="number" class="form-control" id="year" name="year"
                                value="{{ date('Y') }}" min="2000" max="2100" required>
                        </div>
                        <div class="form-group">
                            <label for="start_day">Tanggal Mulai (Hari)</label>
                            <input type="number" class="form-control" id="start_day" name="start_day" value="1"
                                min="1" max="28" required>
                            <small class="form-text text-muted">Hari dalam bulan untuk tanggal mulai periode</small>
                        </div>
                        <div class="form-group">
                            <label for="end_day">Tanggal Selesai</label>
                            <select class="form-control" id="end_day" name="end_day" required>
                                <option value="end_of_month">Akhir Bulan</option>
                                <option value="1">Tanggal 1</option>
                                <option value="5">Tanggal 5</option>
                                <option value="10">Tanggal 10</option>
                                <option value="15">Tanggal 15</option>
                                <option value="20">Tanggal 20</option>
                                <option value="25">Tanggal 25</option>
                                <option value="26">Tanggal 26</option>
                                <option value="27">Tanggal 27</option>
                                <option value="28">Tanggal 28</option>
                            </select>
                            <small class="form-text text-muted">Pilih tanggal selesai periode. Jika tanggal selesai ≤
                                tanggal mulai, akan digunakan bulan berikutnya.</small>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generate Weekly Periods Modal -->
    <div class="modal fade" id="generateWeeklyModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Generate Periode Mingguan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('periodegaji.generate-weekly') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="year">Tahun</label>
                            <input type="number" class="form-control" id="year" name="year"
                                value="{{ date('Y') }}" min="2000" max="2100" required>
                        </div>
                        <div class="form-group">
                            <label for="month">Bulan</label>
                            <select class="form-control" id="month" name="month" required>
                                <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Januari</option>
                                <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Februari</option>
                                <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Maret</option>
                                <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                                <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Mei</option>
                                <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Juni</option>
                                <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Juli</option>
                                <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agustus</option>
                                <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                                <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                                <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Desember</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_day_of_week">Hari Mulai Minggu</label>
                            <select class="form-control" id="start_day_of_week" name="start_day_of_week" required>
                                <option value="monday">Senin</option>
                                <option value="tuesday">Selasa</option>
                                <option value="wednesday">Rabu</option>
                                <option value="thursday">Kamis</option>
                                <option value="friday">Jumat</option>
                                <option value="saturday">Sabtu</option>
                                <option value="sunday">Minggu</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* Left sidebar styles */
        .list-group-item {
            border-radius: 0;
            border-left: none;
            border-right: none;
            padding: 12px 15px;
            display: flex;
            align-items: center;
        }

        .list-group-item.active {
            background-color: #e8f0fe;
            color: #1a73e8;
            border-color: #e0e0e0;
            font-weight: 600;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:hover:not(.active) {
            background-color: #f8f9fa;
        }

        .list-group-item i {
            margin-right: 8px;
            min-width: 16px;
        }

        .list-group-item .badge {
            margin-left: auto;
            min-width: 28px;
            text-align: center;
            padding: 4px 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-light {
            background-color: #f8f9fa;
            color: #212529;
        }

        .badge-info {
            background-color: #e1f5fe;
            color: #0288d1;
        }

        .badge-secondary {
            background-color: #eceff1;
            color: #455a64;
        }

        .compose-btn-container {
            border-bottom: 1px solid #e0e0e0;
        }

        .card {
            border-radius: 8px;
            border: 1px solid #dadce0;
            box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
            margin-bottom: 20px;
        }

        .card-header {
            border-bottom: 1px solid #dadce0;
            padding: 12px 16px;
        }

        #refreshBtn {
            background-color: #f8f9fa;
            border-color: #dadce0;
        }

        #refreshBtn:hover {
            background-color: #f1f3f4;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            // Handle status toggle switches
            $('.status-toggle').on('change', function() {
                const id = $(this).data('id');
                const isChecked = $(this).prop('checked');

                // If turning on, submit the form to activate
                if (isChecked) {
                    document.getElementById('set-active-form-' + id).submit();
                } else {
                    // For now, we'll just reload the page since there's no deactivate route
                    // In a real implementation, you'd want to add a deactivate route
                    alert('Periode tidak dapat dinonaktifkan secara manual. Periode akan nonaktif otomatis saat periode lain diaktifkan.');
                    $(this).prop('checked', true); // Reset the toggle
                }
            });

            // Initialize DataTable
            var table = $('#periodegaji-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [
                    [3, 'desc']
                ], // Urutkan berdasarkan tanggal mulai
                "columnDefs": [{
                        "targets": 0,
                        "orderable": false
                    },
                    {
                        "targets": 1,
                        "render": function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    }
                ]
            });

            // Filter by year
            $('.filter-year').click(function(e) {
                if (!$(e.target).hasClass('badge')) {
                    e.preventDefault();

                    // Only apply filter if not clicking on collapse toggle
                    if (!$(this).data('toggle') || $(this).data('filter') === 'all') {
                        $('.filter-year, .filter-month').removeClass('active');
                        $(this).addClass('active');

                        var filter = $(this).data('filter');

                        if (filter === 'all') {
                            // Tampilkan semua data
                            table.search('').draw();
                            $('#current-filter').text('Semua Periode Gaji');
                        } else {
                            // Cari berdasarkan tahun
                            table.search(filter).draw();
                            $('#current-filter').text('Periode Gaji Tahun ' + filter);
                        }
                    }
                }
            });

            // Filter by month
            $('.filter-month').click(function(e) {
                e.preventDefault();

                $('.filter-year, .filter-month').removeClass('active');
                $(this).addClass('active');

                var year = $(this).data('year');
                var month = $(this).data('month');
                var monthName = $(this).text().trim().split(' ')[0];

                // Cari berdasarkan bulan dan tahun
                table.search(month + '-' + year).draw();
                $('#current-filter').text('Periode Gaji ' + monthName + ' ' + year);
            });

            // Handle select all checkbox
            $('#selectAll').on('click', function() {
                $('.periode-checkbox:not(:disabled)').prop('checked', this.checked);
                updateDeleteButton();
            });

            // Handle individual checkboxes
            $('.periode-checkbox').on('click', function() {
                updateButtonState();

                // If any checkbox is unchecked, uncheck the "select all" checkbox
                if (!this.checked) {
                    $('#selectAll').prop('checked', false);
                }

                // If all individual checkboxes are checked, check the "select all" checkbox
                if ($('.periode-checkbox:checked').length === $('.periode-checkbox:not(:disabled)')
                    .length) {
                    $('#selectAll').prop('checked', true);
                }
            });

            // Handle delete selected button
            $('#deleteSelected').on('click', function() {
                if (confirm('Are you sure you want to delete all selected periods?')) {
                    $('#deleteMultipleForm').submit();
                }
            });

            // Function to update button states
            function updateButtonState() {
                if ($('.periode-checkbox:checked').length > 0) {
                    $('#deleteSelected').prop('disabled', false);
                } else {
                    $('#deleteSelected').prop('disabled', true);
                }
            }

            // Refresh button
            $('#refreshBtn').click(function() {
                $(this).find('i').addClass('fa-spin');
                setTimeout(function() {
                    location.reload();
                }, 500);
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
