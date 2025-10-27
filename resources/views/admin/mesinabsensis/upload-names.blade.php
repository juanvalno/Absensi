@extends('adminlte::page')

@section('title', 'Upload Nama Karyawan ke Mesin Absensi')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Upload Nama Karyawan ke Mesin Absensi</h1>
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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload Nama Single</h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('mesinabsensis.upload-names-store', $mesinabsensi) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="user_id">NIK Karyawan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('user_id') is-invalid @enderror" id="user_id"
                                name="user_id" value="{{ old('user_id') }}" required>
                            <small class="form-text text-muted">Masukkan NIK (numerik) yang dikenali oleh mesin
                                absensi</small>
                            @error('user_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">Nama Karyawan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            <small class="form-text text-muted">Masukkan nama yang akan ditampilkan di mesin absensi (maks.
                                24 karakter)</small>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Nama
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if (session('upload_results'))
                <div class="mt-4 card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Hasil Upload</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (session('upload_results') as $index => $result)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $result }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Daftar Karyawan Terdaftar di Mesin -->
    <div class="mt-4 row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Karyawan Terdaftar di Mesin Absensi</h3>
                    <div class="card-tools d-flex">
                        <button type="button" class="btn btn-warning me-2" id="btnRefreshRegisteredUsers">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                        <button type="button" class="btn btn-primary" id="btnImportFromDatabase">
                            <i class="fas fa-database"></i> Import Semua Data dari Tabel Karyawan
                        </button>
                    </div>



                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="registeredUsersTable">
                            <thead>
                                <tr>
                                    <th width="10">#</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Status Sinkronisasi</th>
                                    <th>Tanggal Sinkronisasi</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="py-3">
                                            <i class="mr-2 fas fa-spinner fa-spin"></i> Memuat data karyawan...
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus User -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Konfirmasi Hapus User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus karyawan berikut dari mesin absensi?</p>
                    <div id="deleteUserInfo" class="p-3 border rounded bg-light">
                        <div><strong>NIK:</strong> <span id="deleteUserNIK"></span></div>
                        <div><strong>Nama:</strong> <span id="deleteUserName"></span></div>
                    </div>
                    <div class="mt-3 alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Perhatian! Data karyawan di mesin akan dihapus
                        permanen.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUser">Hapus</button>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal Preview Karyawan -->
    <div class="modal fade" id="modalPreviewKaryawan" tabindex="-1" role="dialog"
        aria-labelledby="modalPreviewKaryawanLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPreviewKaryawanLabel">Preview Data Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Filter dan Controls -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filterDepartemen">Filter Departemen:</label>
                            <select class="form-control" id="filterDepartemen">
                                <option value="">Semua Departemen</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterBagian">Filter Bagian:</label>
                            <select class="form-control" id="filterBagian">
                                <option value="">Semua Bagian</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="searchKaryawan">Cari Karyawan:</label>
                            <input type="text" class="form-control" id="searchKaryawan"
                                placeholder="Cari berdasarkan NIK atau nama...">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllKaryawan">
                                <label class="form-check-label" for="selectAllKaryawan">
                                    Select All
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Info Selected -->
                    <div class="alert alert-info" id="selectedInfo" style="display: none;">
                        <i class="fas fa-info-circle"></i> <span id="selectedCount">0</span> karyawan dipilih
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="karyawanTable">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAllHeader">
                                    </th>
                                    <th>NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th>Departemen</th>
                                    <th>Bagian</th>
                                </tr>
                            </thead>
                            <tbody id="previewDataKaryawan">
                                <!-- Data karyawan akan dimuat di sini -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmImport" disabled>Import ke Mesin (<span
                            id="importCount">0</span>)</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            // Queue for batch upload
            let queue = [];

            // Load registered users on page load
            loadRegisteredUsers();

            // DataTable for registered users
            let registeredUsersTable;

            function initRegisteredUsersTable() {
                if ($.fn.DataTable.isDataTable('#registeredUsersTable')) {
                    registeredUsersTable.destroy();
                }

                registeredUsersTable = $('#registeredUsersTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true
                });
            }

            // Load registered users from the machine
            function loadRegisteredUsers() {
                $.ajax({
                    url: "{{ route('mesinabsensis.get-registered-users', $mesinabsensi) }}",
                    method: 'GET',
                    dataType: 'json'
                        // cache: false
                        // ,
                        ,
                    beforeSend: function() {
                        $('#registeredUsersTable tbody').html(`
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="py-3">
                                    <i class="mr-2 fas fa-spinner fa-spin"></i> Memuat data karyawan...
                                </div>
                            </td>
                        </tr>
                    `);
                    },
                    success: function(response) {
                        if (response.success) {
                            let html = '';

                            if (response.data.length === 0) {
                                html = `
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Tidak ada karyawan yang terdaftar di mesin absensi
                                    </td>
                                </tr>
                            `;
                            } else {
                                response.data.forEach(function(user, index) {
                                    let syncStatus = '';
                                    if (user.status_sync) {
                                        syncStatus =
                                            '<span class="badge badge-success">Berhasil</span>';
                                    } else {
                                        syncStatus =
                                            '<span class="badge badge-danger">Gagal</span>';
                                    }

                                    html += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${user.nik}</td>
                                        <td>${user.nama}</td>
                                        <td>${syncStatus}</td>
                                        <td>${user.sync_at ? new Date(user.sync_at).toLocaleString() : '-'}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-delete-user"
                                                data-id="${user.nik}"
                                                data-name="${user.nama}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                                });
                            }

                            $('#registeredUsersTable tbody').html(html);
                            initRegisteredUsersTable();

                            // Handle delete buttons
                            $('.btn-delete-user').on('click', function() {
                                const userId = $(this).data('id');
                                const userName = $(this).data('name');

                                // Set modal content
                                $('#deleteUserNIK').text(userId);
                                $('#deleteUserName').text(userName);

                                // Set delete handler
                                $('#confirmDeleteUser').data('id', userId);

                                // Show modal
                                $('#deleteUserModal').modal('show');
                            });
                        } else {
                            $('#registeredUsersTable tbody').html(`
                            <tr>
                                <td colspan="6" class="text-center text-danger">
                                    Error: ${response.message || 'Gagal memuat data karyawan'}
                                </td>
                            </tr>
                        `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading registered users:', xhr, status, error);
                        $('#registeredUsersTable tbody').html(`
                        <tr>
                            <td colspan="6" class="text-center text-danger">
                                Terjadi kesalahan saat memuat data: ${error}
                            </td>
                        </tr>
                    `);
                    }
                });
            }

            // Refresh registered users
            $('#btnRefreshRegisteredUsers').on('click', function() {
                $(this).prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Menyegarkan Data...');

                $.ajax({
                    url: "{{ route('mesinabsensis.get-registered-users', $mesinabsensi) }}",
                    method: 'GET',
                    dataType: 'json',
                    cache: false,
                    headers: {
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0'
                    },
                    success: function(response) {
                        if (response.success) {
                            let html = '';
                            if (response.data.length === 0) {
                                html =
                                    `<tr><td colspan="6" class="text-center">Tidak ada karyawan yang terdaftar di mesin absensi</td></tr>`;
                            } else {
                                response.data.forEach(function(user, index) {
                                    let syncStatus = user.status_sync ?
                                        '<span class="badge badge-success">Berhasil</span>' :
                                        '<span class="badge badge-danger">Gagal</span>';
                                    html += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${user.nik}</td>
                                        <td>${user.nama}</td>
                                        <td>${syncStatus}</td>
                                        <td>${user.sync_at ? new Date(user.sync_at).toLocaleString() : '-'}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-delete-user"
                                                data-id="${user.nik}"
                                                data-name="${user.nama}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                                });
                            }

                            if ($.fn.DataTable.isDataTable('#registeredUsersTable')) {
                                $('#registeredUsersTable').DataTable().destroy();
                            }

                            $('#registeredUsersTable tbody').html(html);

                            $('#registeredUsersTable').DataTable({
                                "paging": true,
                                "lengthChange": true,
                                "searching": true,
                                "ordering": true,
                                "info": true,
                                "autoWidth": false,
                                "responsive": true,
                                "order": [
                                    [0, 'asc']
                                ]
                            });
                        } else {
                            alert('Gagal memuat data: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyegarkan data');
                    },
                    complete: function() {
                        $('#btnRefreshRegisteredUsers')
                            .prop('disabled', false)
                            .html('<i class="fas fa-sync-alt"></i> Refresh Data');
                    }
                });
            });

            // Confirm delete user
            $('#confirmDeleteUser').on('click', function() {
                const userId = $(this).data('id');

                $.ajax({
                    url: "{{ route('mesinabsensis.delete-user', $mesinabsensi) }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_id: userId
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#confirmDeleteUser').prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Menghapus...');
                    },
                    success: function(response) {
                        $('#deleteUserModal').modal('hide');

                        if (response.success) {
                            // Show success message
                            alert(response.message ||
                                'Karyawan berhasil dihapus dari mesin absensi');

                            // Reload the registered users
                            loadRegisteredUsers();
                        } else {
                            // Show error message
                            alert('Error: ' + (response.message || 'Gagal menghapus karyawan'));
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#deleteUserModal').modal('hide');
                        console.error('Error deleting user:', xhr, status, error);
                        alert('Terjadi kesalahan saat menghapus karyawan: ' + error);
                    },
                    complete: function() {
                        $('#confirmDeleteUser').prop('disabled', false).html('Hapus');
                    }
                });
            });

            // Handle search for employees
            let searchTimeout;
            $('#search_karyawan').on('keyup', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val();

                if (query.length < 3) {
                    $('#search_results').hide();
                    $('#search_actions').hide();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('karyawans.search') }}",
                        method: 'GET',
                        data: {
                            q: query
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            // Tampilkan indikator loading
                            $('#karyawanSearchTable tbody').html(
                                '<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Mencari...</td></tr>'
                                );
                            $('#search_results').show();
                        },
                        success: function(response) {
                            let html = '';

                            if (!response.success) {
                                html =
                                    '<tr><td colspan="4" class="text-center text-danger">Error: ' +
                                    (response.message || 'Terjadi kesalahan') +
                                    '</td></tr>';
                                $('#search_actions').hide();
                            } else if (response.data.length === 0) {
                                html =
                                    '<tr><td colspan="4" class="text-center">Tidak ada data karyawan yang sesuai</td></tr>';
                                $('#search_actions').hide();
                            } else {
                                response.data.forEach(function(karyawan) {
                                    const nik = karyawan.nik || '';
                                    html += `
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="check_${karyawan.id}">
                                            </div>
                                        </td>
                                        <td>${nik !== '' ? nik : 'N/A'}</td>
                                        <td>${karyawan.nama_karyawan}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm btn-add-to-queue"
                                                data-id="${nik}"
                                                data-name="${karyawan.nama_karyawan}"
                                                ${nik === '' ? 'disabled' : ''}>
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </td>
                                    </tr>
                                `;
                                });

                                // Show action buttons for multiple selection
                                $('#search_actions').show();
                            }

                            $('#karyawanSearchTable tbody').html(html);
                            $('#search_results').show();

                            // Handle add to queue
                            $('.btn-add-to-queue').on('click', function() {
                                const id = $(this).data('id');
                                const name = $(this).data('name');
                                addToQueue(id, name);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error searching for employees:', xhr, status,
                                error);
                            $('#karyawanSearchTable tbody').html(
                                '<tr><td colspan="4" class="text-center text-danger">Terjadi kesalahan saat mencari data: ' +
                                error + '</td></tr>');
                            $('#search_results').show();
                            $('#search_actions').hide();
                        }
                    });
                }, 500);
            });

            // Checkbox "Pilih Semua"
            $(document).on('click', '#checkAll', function() {
                $('.form-check-input').prop('checked', this.checked);
            });

            // Add selected employees to queue
            $('#btnAddSelected').on('click', function() {
                const selectedRows = $('#karyawanSearchTable tbody tr').filter(function() {
                    return $(this).find('.form-check-input').is(':checked');
                });

                if (selectedRows.length === 0) {
                    alert('Tidak ada karyawan yang dipilih');
                    return;
                }

                let addedCount = 0;
                selectedRows.each(function() {
                    const id = $(this).find('.btn-add-to-queue').data('id');
                    const name = $(this).find('.btn-add-to-queue').data('name');

                    if (id && addToQueue(id, name)) {
                        addedCount++;
                    }
                });

                if (addedCount > 0) {
                    alert(`Berhasil menambahkan ${addedCount} karyawan ke antrian`);
                }
            });

            // Add all search results to queue
            $('#btnAddAll').on('click', function() {
                const rows = $('#karyawanSearchTable tbody tr');

                if (rows.length === 0 || (rows.length === 1 && rows.find('td').length === 1)) {
                    alert('Tidak ada data karyawan yang dapat ditambahkan');
                    return;
                }

                let addedCount = 0;
                rows.each(function() {
                    const id = $(this).find('.btn-add-to-queue').data('id');
                    const name = $(this).find('.btn-add-to-queue').data('name');

                    if (id && addToQueue(id, name)) {
                        addedCount++;
                    }
                });

                if (addedCount > 0) {
                    alert(`Berhasil menambahkan ${addedCount} karyawan ke antrian`);
                }
            });

            // Add to queue
            function addToQueue(id, name) {
                // Validate ID is numeric and not empty
                if (!id) {
                    alert('NIK karyawan tidak boleh kosong!');
                    return false;
                }

                if (isNaN(id)) {
                    alert('NIK karyawan harus berupa angka!');
                    return false;
                }

                // Check if already in queue
                if (queue.some(item => item.id === id)) {
                    alert('Karyawan ini sudah ada dalam antrian!');
                    return false;
                }

                // Add to queue
                queue.push({
                    id: id,
                    name: name
                });

                updateQueueTable();
                return true;
            }

            // Remove from queue
            $(document).on('click', '.btn-remove-queue', function() {
                const id = $(this).data('id');
                queue = queue.filter(item => item.id !== id);
                updateQueueTable();
            });

            // Clear queue
            $('#btnClearQueue').on('click', function() {
                if (confirm('Anda yakin ingin mengosongkan antrian?')) {
                    queue = [];
                    updateQueueTable();
                }
            });

            // Update queue table
            function updateQueueTable() {
                const tableBody = $('#queueTable');
                tableBody.empty();

                if (queue.length === 0) {
                    tableBody.append(
                        '<tr id="noData"><td colspan="3" class="text-center">Belum ada data dalam antrian</td></tr>'
                        );
                    $('#btnUploadBatch').prop('disabled', true);
                    $('#batchData').val('');
                    return;
                }

                $('#btnUploadBatch').prop('disabled', false);

                queue.forEach(item => {
                    tableBody.append(`
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.name}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-remove-queue" data-id="${item.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `);
                });

                // Update hidden input
                $('#batchData').val(JSON.stringify(queue));
            }
        });
    </script>
    <script>
        $(document).ready(function() {

            // Variable global untuk menyimpan data karyawan
            let allKaryawanData = [];
            let filteredKaryawanData = [];
            let selectedKaryawan = [];

            $('#btnImportFromDatabase').on('click', function() {
                // First, get registered users from machine
                $.ajax({
                    url: "{{ route('mesinabsensis.get-registered-users', $mesinabsensi) }}",
                    method: 'GET',
                    dataType: 'json',
                    success: function(registeredUsers) {
                        if (registeredUsers.success) {
                            // Get registered NIKs
                            const registeredNIKs = registeredUsers.data.map(user => user.nik);

                            // Then get all active employees and filter
                            $.ajax({
                                url: "{{ route('karyawans.get-all-active') }}",
                                method: 'GET',
                                dataType: 'json',
                                beforeSend: function() {
                                    $('#btnImportFromDatabase').prop('disabled',
                                            true)
                                        .html(
                                            '<i class="fas fa-spinner fa-spin"></i> Loading...'
                                            );
                                    $('#previewDataKaryawan').html(
                                        '<tr><td colspan="4">Memuat data...</td></tr>'
                                        );
                                },
                                success: function(response) {
                                    if (response.success) {
                                        // Filter only employees not in machine
                                        allKaryawanData = response.data.filter(
                                            karyawan =>
                                            karyawan.nik_karyawan &&
                                            karyawan.nama_karyawan
                                        .toLowerCase() !== 'admin' &&
                                            !registeredNIKs.includes(karyawan
                                                .nik_karyawan)
                                        );

                                        if (allKaryawanData.length === 0) {
                                            $('#previewDataKaryawan').html(
                                                '<tr><td colspan="4">Semua karyawan sudah terdaftar di mesin</td></tr>'
                                                );
                                            $('#modalPreviewKaryawan').modal(
                                            'show');
                                            return;
                                        }

                                        // Reset selected karyawan
                                        selectedKaryawan = [];

                                        // Populate filter departemen
                                        populateFilterDepartemen();
                                        
                                        // Populate filter bagian
                                        populateFilterBagian();

                                        // Tampilkan data awal
                                        filteredKaryawanData = [...allKaryawanData];
                                        displayKaryawanData(filteredKaryawanData);

                                        // Update info selected
                                        updateSelectedInfo();

                                        $('#modalPreviewKaryawan').modal('show');
                                    } else {
                                        alert('Gagal mendapatkan data: ' + response
                                            .message);
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Error:', xhr);
                                    alert('Terjadi kesalahan saat mengambil data.');
                                },
                                complete: function() {
                                    $('#btnImportFromDatabase').prop('disabled',
                                            false)
                                        .html(
                                            '<i class="fas fa-database"></i> Import Semua Data'
                                            );
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal mendapatkan data dari mesin absensi');
                        $('#btnImportFromDatabase').prop('disabled', false)
                            .html('<i class="fas fa-database"></i> Import Semua Data');
                    }
                });
            });

            // Fungsi untuk populate filter departemen
            function populateFilterDepartemen() {
                const departments = [...new Set(allKaryawanData.map(k => k.departemen))].sort();
                let options = '<option value="">Semua Departemen</option>';
                departments.forEach(dept => {
                    if (dept) {
                        options += `<option value="${dept}">${dept}</option>`;
                    }
                });
                $('#filterDepartemen').html(options);
            }

            // Fungsi untuk populate filter bagian
            function populateFilterBagian() {
                const bagians = [...new Set(allKaryawanData.map(k => k.bagian))].sort();
                let options = '<option value="">Semua Bagian</option>';
                bagians.forEach(bagian => {
                    if (bagian) {
                        options += `<option value="${bagian}">${bagian}</option>`;
                    }
                });
                $('#filterBagian').html(options);
            }

            // Fungsi untuk menampilkan data karyawan
            function displayKaryawanData(data) {
                let html = '';
                data.forEach((karyawan, index) => {
                    const isSelected = selectedKaryawan.includes(karyawan.nik_karyawan);
                    html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="karyawan-checkbox"
                                   value="${karyawan.nik_karyawan}"
                                   data-index="${index}"
                                   ${isSelected ? 'checked' : ''}>
                        </td>
                        <td>${karyawan.nik_karyawan}</td>
                        <td>${karyawan.nama_karyawan}</td>
                        <td>${karyawan.departemen || '-'}</td>
                        <td>${karyawan.bagian || '-'}</td>
                    </tr>
                `;
                });
                $('#previewDataKaryawan').html(html);

                // Update checkbox header
                updateSelectAllCheckbox();
            }

            // Fungsi untuk update info selected
            function updateSelectedInfo() {
                const count = selectedKaryawan.length;
                $('#selectedCount').text(count);
                $('#importCount').text(count);

                if (count > 0) {
                    $('#selectedInfo').show();
                    $('#btnConfirmImport').prop('disabled', false);
                } else {
                    $('#selectedInfo').hide();
                    $('#btnConfirmImport').prop('disabled', true);
                }
            }

            // Fungsi untuk update select all checkbox
            function updateSelectAllCheckbox() {
                const visibleCheckboxes = $('.karyawan-checkbox:visible');
                const checkedCheckboxes = $('.karyawan-checkbox:visible:checked');

                if (visibleCheckboxes.length === 0) {
                    $('#selectAllHeader, #selectAllKaryawan').prop('indeterminate', false).prop('checked', false);
                } else if (checkedCheckboxes.length === visibleCheckboxes.length) {
                    $('#selectAllHeader, #selectAllKaryawan').prop('indeterminate', false).prop('checked', true);
                } else if (checkedCheckboxes.length > 0) {
                    $('#selectAllHeader, #selectAllKaryawan').prop('indeterminate', true);
                } else {
                    $('#selectAllHeader, #selectAllKaryawan').prop('indeterminate', false).prop('checked', false);
                }
            }

            // Event handlers untuk modal preview
            $(document).on('change', '#filterDepartemen', function() {
                filterAndDisplayData();
            });

            $(document).on('change', '#filterBagian', function() {
                filterAndDisplayData();
            });

            $(document).on('input', '#searchKaryawan', function() {
                filterAndDisplayData();
            });

            $(document).on('change', '#selectAllHeader, #selectAllKaryawan', function() {
                const isChecked = $(this).is(':checked');
                $('.karyawan-checkbox:visible').each(function() {
                    const nik = $(this).val();
                    $(this).prop('checked', isChecked);

                    if (isChecked) {
                        if (!selectedKaryawan.includes(nik)) {
                            selectedKaryawan.push(nik);
                        }
                    } else {
                        selectedKaryawan = selectedKaryawan.filter(n => n !== nik);
                    }
                });
                updateSelectedInfo();
                updateSelectAllCheckbox();
            });

            $(document).on('change', '.karyawan-checkbox', function() {
                const nik = $(this).val();
                const isChecked = $(this).is(':checked');

                if (isChecked) {
                    if (!selectedKaryawan.includes(nik)) {
                        selectedKaryawan.push(nik);
                    }
                } else {
                    selectedKaryawan = selectedKaryawan.filter(n => n !== nik);
                }

                updateSelectedInfo();
                updateSelectAllCheckbox();
            });

            // Fungsi untuk filter dan display data
            function filterAndDisplayData() {
                const departemenFilter = $('#filterDepartemen').val();
                const bagianFilter = $('#filterBagian').val();
                const searchText = $('#searchKaryawan').val().toLowerCase();

                filteredKaryawanData = allKaryawanData.filter(karyawan => {
                    const matchDepartemen = !departemenFilter || karyawan.departemen === departemenFilter;
                    const matchBagian = !bagianFilter || karyawan.bagian === bagianFilter;
                    const matchSearch = !searchText ||
                        karyawan.nik_karyawan.toLowerCase().includes(searchText) ||
                        karyawan.nama_karyawan.toLowerCase().includes(searchText);
                    return matchDepartemen && matchBagian && matchSearch;
                });

                displayKaryawanData(filteredKaryawanData);
            }

            $(document).on('click', '#btnConfirmImport', function() {
                if (selectedKaryawan.length === 0) {
                    alert("Pilih minimal satu karyawan untuk diimport!");
                    return;
                }

                if (!confirm(
                        `Anda yakin ingin mengupload ${selectedKaryawan.length} karyawan ke mesin absensi?`
                        )) {
                    return;
                }

                // Disable button dan tampilkan loading spinner
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                $('#modalPreviewKaryawan .modal-content').append(
                    '<div class="modal-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);display:flex;justify-content:center;align-items:center;z-index:1050;"><div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Mengupload data ke mesin...</p></div></div>'
                    );

                // Filter data karyawan yang dipilih
                const dataToUpload = [];
                allKaryawanData.forEach(karyawan => {
                    if (selectedKaryawan.includes(karyawan.nik_karyawan) &&
                        karyawan.nik_karyawan &&
                        karyawan.nama_karyawan &&
                        karyawan.nama_karyawan.toLowerCase() !== 'admin') {
                        dataToUpload.push({
                            nik_karyawan: karyawan.nik_karyawan,
                            nama_karyawan: karyawan.nama_karyawan
                        });
                    }
                });

                if (dataToUpload.length === 0) {
                    alert("Tidak ada data valid untuk diupload!");
                    $('#btnConfirmImport').prop('disabled', false).html(
                        'Import ke Mesin (<span id="importCount">0</span>)');
                    $('.modal-overlay').remove();
                    return;
                }

                // Kirim langsung ke mesin absensi
                $.ajax({
                    url: "{{ route('mesinabsensis.upload-direct-batch', $mesinabsensi) }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        batch_data: JSON.stringify(dataToUpload)
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.message) {
                            alert(response.message);
                            // Refresh data tanpa menggunakan fungsi
                            $.ajax({
                                url: "{{ route('mesinabsensis.get-registered-users', $mesinabsensi) }}",
                                method: 'GET',
                                dataType: 'json',
                                cache: false,
                                success: function(refreshResponse) {
                                    if (refreshResponse.success) {
                                        let html = '';
                                        if (refreshResponse.data.length === 0) {
                                            html =
                                                `<tr><td colspan="6" class="text-center">Tidak ada karyawan yang terdaftar di mesin absensi</td></tr>`;
                                        } else {
                                            refreshResponse.data.forEach(function(
                                                user, index) {
                                                let syncStatus = user
                                                    .status_sync ?
                                                    '<span class="badge badge-success">Berhasil</span>' :
                                                    '<span class="badge badge-danger">Gagal</span>';
                                                html += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${user.nik}</td>
                                            <td>${user.nama}</td>
                                            <td>${syncStatus}</td>
                                            <td>${user.sync_at ? new Date(user.sync_at).toLocaleString() : '-'}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete-user"
                                                    data-id="${user.nik}"
                                                    data-name="${user.nama}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                            });
                                        }

                                        // Destroy existing datatable
                                        if ($.fn.DataTable.isDataTable(
                                                '#registeredUsersTable')) {
                                            $('#registeredUsersTable').DataTable()
                                                .destroy();
                                        }

                                        // Update tabel
                                        $('#registeredUsersTable tbody').html(html);

                                        // Reinisialisasi DataTable
                                        $('#registeredUsersTable').DataTable({
                                            "paging": true,
                                            "lengthChange": true,
                                            "searching": true,
                                            "ordering": true,
                                            "info": true,
                                            "autoWidth": false,
                                            "responsive": true
                                        });
                                    }
                                }
                            });
                        } else {
                            alert('Hasil proses tidak jelas, silakan refresh halaman.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error uploading data:', xhr, status, error);
                        alert('Terjadi kesalahan saat mengupload data: ' + (xhr.responseJSON ?
                            xhr.responseJSON.message : error));
                    },
                    complete: function() {
                        // Re-enable tombol dan kembalikan teks asli
                        $('#btnConfirmImport').prop('disabled', false).html(
                            'Import ke Mesin (<span id="importCount">0</span>)');
                        $('.modal-overlay').remove();
                        $('#modalPreviewKaryawan').modal('hide');
                    }
                });
            });
        });
    </script>

@stop
