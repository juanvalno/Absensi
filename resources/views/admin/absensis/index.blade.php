@extends('adminlte::page')

@section('title', 'Manajemen Absensi')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="mr-2 fas fa-clipboard-check text-primary"></i>Manajemen Absensi</h1>
        <div>
            <a href="{{ route('admin.absensis.daily-report') }}" class="btn btn-info">
                <i class="mr-1 fas fa-file-alt"></i> Laporan Harian
            </a>
            {{-- <a href="{{ route('absensis.fetch-form') }}" class="btn btn-warning ml-2">
                <i class="mr-1 fas fa-sync"></i> Sinkronisasi Mesin
            </a> --}}
            @can('absensis.create')
                <a href="{{ route('absensis.create') }}" class="ml-2 btn btn-primary">
                    <i class="mr-1 fas fa-plus"></i> Tambah Absensi
                </a>
            @endcan
        </div>
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

    <!-- Info Boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total</span>
                    <span class="info-box-number" id="total-count">{{ $absensis->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Hadir</span>
                    <span class="info-box-number" id="hadir-count">
                        {{ $absensis->whereIn('status', ['Hadir', 'Terlambat'])->count() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Terlambat</span>
                    <span class="info-box-number"
                        id="terlambat-count">{{ $absensis->where('status', 'Terlambat')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-procedures"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Sakit</span>
                    <span class="info-box-number" id="sakit-count">{{ $absensis->where('status', 'Sakit')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-umbrella-beach"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Cuti/Izin</span>
                    <span class="info-box-number"
                        id="cuti-izin-count">{{ $absensis->whereIn('status', ['Izin', 'Cuti'])->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-calendar-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Libur</span>
                    <span class="info-box-number" id="libur-count">{{ $absensis->where('status', 'Libur')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex mb-2">
                    <div class="btn-group mr-3">
                        <a href="{{ route('absensis.index') }}"
                            class="btn btn-default {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                            Semua <span class="badge badge-light ml-1" id="total-badge">{{ $absensis->count() }}</span>
                        </a>
                        <a href="{{ route('absensis.index', ['status' => 'Hadir']) }}"
                            class="btn btn-default {{ request('status') == 'Hadir' ? 'active' : '' }}">
                            Hadir <span
                                class="badge badge-success ml-1">{{ $absensis->where('status', 'Hadir')->count() }}</span>
                        </a>
                        <a href="{{ route('absensis.index', ['status' => 'Terlambat']) }}"
                            class="btn btn-default {{ request('status') == 'Terlambat' ? 'active' : '' }}">
                            Terlambat <span
                                class="badge badge-warning ml-1">{{ $absensis->where('status', 'Terlambat')->count() }}</span>
                        </a>
                        <a href="{{ route('absensis.index', ['status' => 'Izin']) }}"
                            class="btn btn-default {{ request('status') == 'Izin' ? 'active' : '' }}">
                            Izin <span
                                class="badge badge-info ml-1">{{ $absensis->where('status', 'Izin')->count() }}</span>
                        </a>
                        <a href="{{ route('absensis.index', ['status' => 'Sakit']) }}"
                            class="btn btn-default {{ request('status') == 'Sakit' ? 'active' : '' }}">
                            Sakit <span
                                class="badge badge-primary ml-1">{{ $absensis->where('status', 'Sakit')->count() }}</span>
                        </a>
                        <a href="{{ route('absensis.index', ['status' => 'Cuti']) }}"
                            class="btn btn-default {{ request('status') == 'Cuti' ? 'active' : '' }}">
                            Cuti <span
                                class="badge badge-secondary ml-1">{{ $absensis->where('status', 'Cuti')->count() }}</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('absensis.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ request('tanggal', now()->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="departemen_id">Departemen</label>
                                <select class="form-control" id="departemen_id" name="departemen_id">
                                    <option value="">Semua Departemen</option>
                                    @foreach ($departemens as $departemen)
                                        <option value="{{ $departemen->id }}"
                                            {{ request('departemen_id') == $departemen->id ? 'selected' : '' }}>
                                            {{ $departemen->name_departemen }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bagian_id">Bagian</label>
                                <select class="form-control" id="bagian_id" name="bagian_id" disabled>
                                    <option value="">Pilih Bagian</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status_karyawan">Status Karyawan</label>
                                <select class="form-control" id="status_karyawan" name="status_karyawan">
                                    <option value="">Semua Status</option>
                                    @foreach ($statusKaryawanOptions as $status)
                                        <option value="{{ $status }}"
                                            {{ request('status_karyawan') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">Cari Karyawan</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Nama atau NIP" value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @php
            // Check if the current date is a holiday
            $currentDate = request('tanggal', \Carbon\Carbon::today()->format('Y-m-d'));
            $hariLibur = \App\Models\HariLibur::where('tanggal', $currentDate)->first();
        @endphp

        @if ($hariLibur)
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <h5><i class="fas fa-calendar-alt mr-2"></i>Hari Libur: {{ $hariLibur->nama_libur }}</h5>
                    <p class="mb-0">Tanggal: {{ \Carbon\Carbon::parse($hariLibur->tanggal)->format('d F Y') }}</p>
                    <p class="mb-0">Semua karyawan dianggap libur pada hari ini.</p>

                    @if ($absensis->where('status', 'Libur')->count() == 0)
                        <div class="mt-3">
                            <a href="{{ route('absensis.create-holiday', $hariLibur->id) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-plus mr-1"></i> Buat Absensi Otomatis untuk Semua Karyawan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Karyawan</th>
                            <th width="10%">Tanggal</th>
                            <th width="10%">Jam Masuk</th>
                            <th width="10%">Jam Pulang</th>
                            <th width="10%">Total Jam</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensis as $index => $absensi)
                            <tr>
                                <td>{{ $absensis->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $absensi->karyawan->nama_karyawan ?? 'N/A' }}</strong>
                                    @if ($absensi->karyawan)
                                        <div class="small text-muted">
                                            {{ $absensi->karyawan->nik_karyawan ?? 'Tidak ada NIK' }}</div>
                                    @endif
                                </td>
                                <td>{{ $absensi->tanggal->format('d-m-Y') }}</td>
                                <td>
                                    @if ($absensi->jam_masuk)
                                        {{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}
                                        @if ($absensi->keterlambatan > 0)
                                            <div class="small text-danger">
                                                <i class="fas fa-exclamation-circle"></i> {{ $absensi->keterlambatan }}
                                                menit
                                            </div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($absensi->jam_pulang)
                                        {{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }}
                                        @if ($absensi->pulang_awal > 0)
                                            <div class="small text-warning">
                                                <i class="fas fa-exclamation-circle"></i> {{ $absensi->pulang_awal }}
                                                menit
                                            </div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalJam = $absensi->total_jam;
                                        if (is_numeric($totalJam)) {
                                            $jam = floor($totalJam);
                                            $menit = round(($totalJam - $jam) * 60);
                                            echo $jam . ' jam ' . $menit . ' menit';
                                        } elseif (preg_match('/^(\d+):(\d+):(\d+)$/', $totalJam, $matches)) {
                                            $jam = intval($matches[1]);
                                            $menit = intval($matches[2]);
                                            $detik = intval($matches[3]);
                                            echo "$jam jam $menit menit $detik detik";
                                        } else {
                                            echo $totalJam ?? '-';
                                        }
                                    @endphp
                                </td>
                                <td>
                                    @if ($absensi->status == 'Hadir')
                                        <span class="badge badge-success">{{ $absensi->status }}</span>
                                    @elseif($absensi->status == 'Terlambat')
                                        <span class="badge badge-warning">{{ $absensi->status }}</span>
                                    @elseif($absensi->status == 'Izin')
                                        <span class="badge badge-info">{{ $absensi->status }}</span>
                                    @elseif($absensi->status == 'Sakit')
                                        <span class="badge badge-primary">{{ $absensi->status }}</span>
                                    @elseif($absensi->status == 'Cuti')
                                        <span class="badge badge-secondary">{{ $absensi->status }}</span>
                                    @elseif($absensi->status == 'Libur')
                                        <span class="badge badge-danger">{{ $absensi->status }}</span>
                                    @endif

                                    @if ($absensi->keterangan)
                                        <div class="small text-muted mt-1" title="{{ $absensi->keterangan }}">
                                            {{ \Str::limit($absensi->keterangan, 20) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('absensis.show', $absensi) }}" class="btn btn-info"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if (
                                            $absensi->jam_pulang == null &&
                                                $absensi->status != 'Izin' &&
                                                $absensi->status != 'Sakit' &&
                                                $absensi->status != 'Cuti' &&
                                                $absensi->status != 'Libur')
                                            <button type="button" class="btn btn-success checkout-btn"
                                                data-toggle="modal" data-target="#checkoutModal"
                                                data-id="{{ $absensi->id }}"
                                                data-karyawan="{{ $absensi->karyawan->nama_karyawan ?? 'N/A' }}"
                                                data-karyawan-id="{{ $absensi->karyawan_id }}" title="Absen Pulang">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('absensis.edit', $absensi) }}" class="btn btn-primary"
                                            title="Edit Absensi">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('absensis.destroy', $absensi) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Hapus Absensi"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">
                                    <i class="mr-1 fas fa-info-circle"></i> Tidak ada data absensi yang ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Menampilkan {{ $absensis->firstItem() ?? 0 }} sampai {{ $absensis->lastItem() ?? 0 }} dari
                    {{ $absensis->total() }} data
                </div>
                <div>
                    {{ $absensis->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Absen Pulang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('absensis.checkout') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="karyawan_id" id="karyawan_id">

                        <div class="form-group">
                            <label>Karyawan</label>
                            <input type="text" class="form-control" id="karyawan_nama" readonly>
                        </div>

                        <div class="form-group">
                            <label>Jam Pulang</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                </div>
                                <input type="time" name="jam_pulang" class="form-control"
                                    value="{{ now()->format('H:i') }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Jenis Absensi</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="manual" name="jenis_absensi_pulang"
                                    class="custom-control-input" value="Manual" checked>
                                <label class="custom-control-label" for="manual">Manual</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="mesin" name="jenis_absensi_pulang"
                                    class="custom-control-input" value="Mesin">
                                <label class="custom-control-label" for="mesin">Mesin</label>
                            </div>
                        </div>

                        <div class="form-group mesin-options" style="display: none;">
                            <label>Mesin Absensi</label>
                            <select name="mesinabsensi_pulang_id" class="form-control">
                                <option value="">-- Pilih Mesin --</option>
                                @foreach ($mesinAbsensis ?? [] as $mesin)
                                    <option value="{{ $mesin->id }}">{{ $mesin->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .table td {
            border-top: none;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
            padding: 5px 8px;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }

        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .btn-group-sm>.btn {
            padding: .25rem .5rem;
        }

        .btn-default.active {
            background-color: #007bff;
            color: white;
        }

        .pagination {
            margin-bottom: 0;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            // Initialize select2
            $('.select2').select2();

            // Fade out alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Toggle mesin options based on absensi type
            $('input[name="jenis_absensi_pulang"]').change(function() {
                if ($(this).val() === 'Mesin') {
                    $('.mesin-options').slideDown();
                } else {
                    $('.mesin-options').slideUp();
                }
            });

            // Set checkout modal data
            $('.checkout-btn').click(function() {
                var karyawanId = $(this).data('karyawan-id');
                var karyawanNama = $(this).data('karyawan');

                $('#karyawan_id').val(karyawanId);
                $('#karyawan_nama').val(karyawanNama);
            });

            // Live data refresh
            function refreshData() {
                $.ajax({
                    url: '{{ route('admin.absensis.getTodaySummary') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Update summary counters
                            $('#total-count, #total-badge').text(response.data.total);
                            $('#hadir-count').text(response.data.hadir);
                            $('#terlambat-count').text(response.data.terlambat);
                            $('#sakit-count').text(response.data.sakit);
                            $('#cuti-izin-count').text(response.data.izin + response.data.cuti);
                        }
                    }
                });
            }

            // Real-time sync for attendance machines
            let syncInterval;

            $('#start-sync').click(function() {
                $(this).prop('disabled', true);
                $('#stop-sync').prop('disabled', false);
                $('#sync-status').removeClass('d-none');

                // Start the sync interval (every 30 seconds)
                syncInterval = setInterval(fetchLatestData, 30000);

                // Run it immediately
                fetchLatestData();
            });

            $('#stop-sync').click(function() {
                $(this).prop('disabled', true);
                $('#start-sync').prop('disabled', false);
                $('#sync-status').addClass('d-none');

                // Clear the interval
                clearInterval(syncInterval);
            });

            function fetchLatestData() {
                $.ajax({
                    url: '{{ route('admin.absensis.fetchLatestData') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#last-sync').text(new Date().toLocaleTimeString());

                            // Update the sync results
                            let resultHtml = '';
                            response.data.forEach(function(item) {
                                let statusClass = item.status === 'success' ? 'text-success' :
                                    'text-muted';
                                let statusIcon = item.status === 'success' ? 'check-circle' :
                                    'info-circle';

                                resultHtml +=
                                    `<div class="${statusClass}"><i class="fas fa-${statusIcon} mr-1"></i> ${item.machine}: ${item.count} data baru</div>`;
                            });

                            $('#sync-results').html(resultHtml);

                            // If we got new data, refresh the table data
                            if (response.data.some(item => item.count > 0)) {
                                refreshData();
                            }
                        }
                    }
                });
            }

            // Simple cascading dropdown for bagian
            $('#departemen_id').on('change', function() {
                var departemenId = $(this).val();
                var bagianSelect = $('#bagian_id');
                
                // Reset bagian dropdown
                bagianSelect.html('<option value="">Pilih Bagian</option>');
                bagianSelect.prop('disabled', true);
                
                if (departemenId) {
                    // Simple AJAX call
                    $.get('/get-bagian', { departemen_id: departemenId })
                        .done(function(data) {
                            if (data.length > 0) {
                                bagianSelect.prop('disabled', false);
                                $.each(data, function(index, bagian) {
                                    bagianSelect.append('<option value="' + bagian.id + '">' + bagian.name_bagian + '</option>');
                                });
                            }
                        })
                        .fail(function() {
                            console.log('Error loading bagian data');
                        });
                }
            });
            
            // Load bagian on page load if department is selected
            if ($('#departemen_id').val()) {
                $('#departemen_id').trigger('change');
            }
        });
    </script>
@stop
