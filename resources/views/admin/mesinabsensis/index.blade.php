@extends('adminlte::page')

@section('title', 'Daftar Mesin Absensi')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Manajemen Mesin Absensi</h1>
    @can_show('mesin_absensi.create')
    <a href="{{ route('mesinabsensis.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Mesin Absensi
    </a>
    @endcan_show
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Mesin Absensi</h3>
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

        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="mesinAbsensiTable">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Nama Mesin</th>
                        <th>Alamat IP</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th width="300">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mesinAbsensis as $index => $mesin)
                    <tr data-id="{{ $mesin->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $mesin->nama }}</td>
                        <td>
                            <code>{{ $mesin->alamat_ip }}</code>
                            <button type="button" class="btn btn-xs btn-outline-info copy-btn" data-clipboard-text="{{ $mesin->alamat_ip }}" title="Salin Alamat IP">
                                <i class="fas fa-copy"></i>
                            </button>
                        </td>
                        <td>{{ $mesin->lokasi }}</td>
                        <td>
                            @if($mesin->status_aktif)
                            <span class="badge badge-success">Aktif</span>
                            @else
                            <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('mesinabsensis.show', $mesin) }}" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @can_show('mesin_absensi.edit')
                                <a href="{{ route('mesinabsensis.edit', $mesin) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan_show

                                <a href="{{ route('mesinabsensis.test-connection', $mesin) }}" class="btn btn-primary btn-sm" title="Test Koneksi">
                                    <i class="fas fa-network-wired"></i>
                                </a>
                                <a href="{{ route('mesinabsensis.auto-detect-ip', $mesin) }}" class="btn btn-warning btn-sm" title="Auto Detect IP">
                                    <i class="fas fa-magic"></i> Detect IP
                                </a>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('mesinabsensis.download-logs', $mesin) }}">
                                            <i class="mr-1 fas fa-list-ul"></i> Download Log Absensi
                                        </a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#downloadRangeModal" data-id="{{ $mesin->id }}">
                                            <i class="mr-1 fas fa-calendar-alt"></i> Download Range Tanggal
                                        </a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#downloadUserModal" data-id="{{ $mesin->id }}">
                                            <i class="mr-1 fas fa-user"></i> Download per Karyawan
                                        </a>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('mesinabsensis.upload-names', $mesin) }}">
                                            <i class="mr-1 fas fa-user-tag"></i> Upload Nama
                                        </a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#syncAllModal" data-id="{{ $mesin->id }}">
                                            <i class="mr-1 fas fa-sync"></i> Sinkronisasi Semua Karyawan
                                        </a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#cloneUsersModal" data-id="{{ $mesin->id }}" data-nama="{{ $mesin->nama }}">
                                            <i class="mr-1 fas fa-clone"></i> Clone User & Fingerprint
                                            <span class="badge badge-info">Clone</span>
                                        </a>
                                    </div>
                                </div>

                                @can_show('mesinabsensis.edit')
                                <form action="{{ route('mesinabsensis.toggle-status', $mesin) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn {{ $mesin->status_aktif ? 'btn-dark' : 'btn-light' }} btn-sm" title="{{ $mesin->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                @endcan_show

                                @can_show('mesin_absensi.delete')
                                <form action="{{ route('mesinabsensis.destroy', $mesin) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus mesin absensi ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan_show
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Download Range -->
<div class="modal fade" id="downloadRangeModal" tabindex="-1" role="dialog" aria-labelledby="downloadRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('mesinabsensis.download-logs-range') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadRangeModalLabel">Download Log Absensi - Range Tanggal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="mesin_id" id="rangeModalMesinId">
                    <div class="form-group">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d', strtotime('-7 days')) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Download Per User -->
<div class="modal fade" id="downloadUserModal" tabindex="-1" role="dialog" aria-labelledby="downloadUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('mesinabsensis.download-logs-user') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadUserModalLabel">Download Log Absensi - Per Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="mesin_id" id="userModalMesinId">
                    <div class="form-group">
                        <label for="user_id">ID Karyawan</label>
                        <input type="text" class="form-control" id="user_id" name="user_id" placeholder="Masukkan ID Karyawan di Mesin" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sync All Users -->
<div class="modal fade" id="syncAllModal" tabindex="-1" role="dialog" aria-labelledby="syncAllModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('mesinabsensis.sync-all-users') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="syncAllModalLabel">Sinkronisasi Semua Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="mesin_id" id="syncModalMesinId">
                    <div class="alert alert-warning">
                        <i class="mr-1 fas fa-exclamation-triangle"></i> Perhatian!
                        <p class="mb-0">Proses ini akan menyinkronkan semua karyawan aktif ke mesin absensi. Proses ini akan memakan waktu tergantung jumlah karyawan yang akan diupload.</p>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirmSync" name="confirm" value="1" required>
                            <label class="custom-control-label" for="confirmSync">Saya mengerti dan ingin melanjutkan</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSyncAll" disabled>Sinkronisasi</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal Clone Users -->
<div class="modal fade" id="cloneUsersModal" tabindex="-1" role="dialog" aria-labelledby="cloneUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('mesinabsensis.clone-users') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="cloneUsersModalLabel">Clone User & Fingerprint</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mr-1 fas fa-info-circle"></i> Fitur ini akan mengkloning user beserta sidik jari dari mesin sumber ke mesin tujuan.
                        <p class="mt-2 mb-0">Berguna untuk skenario di mana karyawan absen masuk di satu mesin dan absen pulang di mesin lainnya.</p>
                    </div>

                    <input type="hidden" name="source_machine_id" id="sourceMachineId">

                    <div class="form-group">
                        <label>Mesin Sumber</label>
                        <input type="text" class="form-control" id="sourceMachineName" readonly>
                        <small class="form-text text-muted">Mesin yang datanya akan diclone</small>
                    </div>

                    <div class="form-group">
                        <label for="target_machine_id">Mesin Tujuan <span class="text-danger">*</span></label>
                        <select class="form-control" id="targetMachineId" name="target_machine_id" required>
                            <option value="">-- Pilih Mesin Tujuan --</option>
                            @foreach($mesinAbsensis as $targetMesin)
                            <option value="{{ $targetMesin->id }}">{{ $targetMesin->nama }} ({{ $targetMesin->alamat_ip }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Mesin yang akan menerima data user dan sidik jari</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="includeFingerprint" name="include_fingerprint" value="1" checked>
                            <label class="custom-control-label" for="includeFingerprint">Termasuk data sidik jari</label>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="mr-1 fas fa-exclamation-triangle"></i> Perhatian!
                        <p class="mb-0">Proses ini memerlukan waktu tergantung jumlah user. Pastikan kedua mesin dalam kondisi menyala dan terhubung dengan jaringan.</p>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirmClone" name="confirm" value="1" required>
                            <label class="custom-control-label" for="confirmClone">Saya mengerti dan ingin melanjutkan</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnClone" disabled>
                        <i class="mr-1 fas fa-clone"></i> Clone User & Fingerprint
                    </button>
                </div>
            </form>
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
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script>
    $(function() {
        // Initialize DataTable
        $('#mesinAbsensiTable').DataTable({
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
        });

        // Initialize Clipboard.js
        var clipboard = new ClipboardJS('.copy-btn');
        clipboard.on('success', function(e) {
            $(e.trigger).tooltip({
                title: 'Copied!'
                , trigger: 'manual'
            }).tooltip('show');

            setTimeout(function() {
                $(e.trigger).tooltip('hide');
            }, 1000);

            e.clearSelection();
        });

        // Modal handlers
        $('#downloadRangeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var mesinId = button.data('id');
            $('#rangeModalMesinId').val(mesinId);
        });

        $('#downloadUserModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var mesinId = button.data('id');
            $('#userModalMesinId').val(mesinId);
        });

        $('#syncAllModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var mesinId = button.data('id');
            $('#syncModalMesinId').val(mesinId);
        });

        // Enable/disable sync button based on checkbox
        $('#confirmSync').on('change', function() {
            $('#btnSyncAll').prop('disabled', !this.checked);
        });
    });


    // Tambahkan ke dalam script yang sudah ada
    $('#cloneUsersModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var mesinId = button.data('id');
        var mesinNama = button.data('nama');

        $('#sourceMachineId').val(mesinId);
        $('#sourceMachineName').val(mesinNama);

        // Hapus mesin sumber dari opsi tujuan
        $('#targetMachineId option').show();
        $('#targetMachineId option[value="' + mesinId + '"]').hide();
        $('#targetMachineId').val('');

        // Reset checkbox
        $('#confirmClone').prop('checked', false);
        $('#btnClone').prop('disabled', true);
    });

    // Enable/disable clone button based on checkbox
    $('#confirmClone').on('change', function() {
        $('#btnClone').prop('disabled', !this.checked || $('#targetMachineId').val() === '');
    });

    // Validasi pemilihan mesin tujuan
    $('#targetMachineId').on('change', function() {
        $('#btnClone').prop('disabled', !$('#confirmClone').is(':checked') || $(this).val() === '');
    });

</script>
@stop
