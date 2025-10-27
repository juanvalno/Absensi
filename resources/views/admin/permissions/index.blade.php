@extends('adminlte::page')

@section('title', 'Manajemen Izin Sistem')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-shield-alt mr-2 text-primary"></i>Manajemen Izin Sistem</h1>
    <a href="{{ route('permissions.update-db') }}" class="btn btn-primary">
        <i class="mr-1 fas fa-sync-alt"></i> Simpan ke Database
    </a>
</div>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-body p-0">
        <!-- Explorer Navigation Bar -->
        <div class="explorer-navbar d-flex align-items-center p-2 bg-light border-bottom">
            <div class="btn-group mr-3">
                <button class="btn btn-default" id="collapseAllBtn">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn btn-default" id="expandAllBtn">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="explorer-address-bar flex-grow-1 d-flex align-items-center">
                <span class="mr-2"><i class="fas fa-shield-alt text-primary"></i></span>
                <span class="mr-2">Sistem</span>
                <span class="mr-2">></span>
                <span class="mr-2">Izin</span>
                <span class="mr-2">></span>
                <span class="font-weight-bold">Daftar Izin</span>
            </div>

            <div class="explorer-search ml-3">
                <div class="input-group">
                    <input type="text" id="searchPermission" class="form-control" placeholder="Cari...">
                    <div class="input-group-append">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Explorer Content -->
        <div class="d-flex">
            <!-- Left Sidebar - Categories -->
            <div class="explorer-sidebar p-2 border-right" style="width: 220px;">
                <div class="sidebar-header mb-2 font-weight-bold">
                    <i class="fas fa-filter mr-1"></i> Jenis Izin
                </div>
                <div class="sidebar-item d-flex align-items-center p-2 mb-1 active">
                    <i class="fas fa-list-ul mr-2 text-primary"></i> Semua Izin
                </div>
                <div class="sidebar-item d-flex align-items-center p-2 mb-1">
                    <i class="fas fa-eye mr-2 text-success"></i> Izin Lihat
                </div>
                <div class="sidebar-item d-flex align-items-center p-2 mb-1">
                    <i class="fas fa-plus-circle mr-2 text-primary"></i> Izin Tambah
                </div>
                <div class="sidebar-item d-flex align-items-center p-2 mb-1">
                    <i class="fas fa-edit mr-2 text-warning"></i> Izin Edit
                </div>
                <div class="sidebar-item d-flex align-items-center p-2 mb-1">
                    <i class="fas fa-trash mr-2 text-danger"></i> Izin Hapus
                </div>
                <div class="sidebar-item d-flex align-items-center p-2 mb-1">
                    <i class="fas fa-cog mr-2 text-secondary"></i> Izin Lainnya
                </div>

                <div class="sidebar-header mt-4 mb-2 font-weight-bold">
                    <i class="fas fa-info-circle mr-1"></i> Informasi
                </div>
                <div class="p-2">
                    <div class="mb-2 small">Total Fitur: <span class="badge badge-primary">{{ count($controllerPermissions) }}</span></div>
                    <div class="small text-muted">Klik tombol "Simpan ke Database" untuk menyimpan daftar izin ini ke database sistem</div>
                </div>
            </div>

            <!-- Main Content - Files List -->
            <div class="explorer-content flex-grow-1">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible m-2">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    {{ session('success') }}
                </div>
                @endif

                <div class="explorer-view p-2">
                    <table class="table table-hover explorer-table mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 40%">Nama Fitur</th>
                                <th style="width: 40%">Lokasi File</th>
                                <th style="width: 10%" class="text-center">Jumlah Izin</th>
                                <th style="width: 10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($controllerPermissions as $module => $controller)
                            <tr class="module-row" data-module="{{ $module }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-folder text-warning mr-2"></i>
                                        <span>{{ $controller['name'] }}</span>
                                    </div>
                                </td>
                                <td class="text-muted small">{{ Str::after($controller['path'], base_path('/')) }}</td>
                                <td class="text-center"><span class="badge badge-info badge-pill">{{ count($controller['permissions']) }}</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light toggle-details" data-target="details-{{ $loop->index }}">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="details-row" id="details-{{ $loop->index }}" style="display: none;">
                                <td colspan="4" class="p-0">
                                    <div class="p-2 bg-light border-top border-bottom">
                                        <table class="table table-sm mb-0 explorer-details-table">
                                            <thead>
                                                <tr class="bg-white">
                                                    <th style="width: 30%">Fungsi</th>
                                                    <th style="width: 30%">Jenis Izin</th>
                                                    <th style="width: 40%">Kode Izin</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($controller['permissions'] as $methodName => $permissionType)
                                                <tr class="permission-item" data-permission="{{ $module }}.{{ $permissionType }}">
                                                    <td class="small">{{ $methodName }}()</td>
                                                    <td>
                                                        @if($permissionType == 'view')
                                                        <span class="badge badge-success"><i class="fas fa-eye mr-1"></i> Lihat</span>
                                                        @elseif($permissionType == 'create')
                                                        <span class="badge badge-primary"><i class="fas fa-plus-circle mr-1"></i> Tambah</span>
                                                        @elseif($permissionType == 'edit')
                                                        <span class="badge badge-warning"><i class="fas fa-edit mr-1"></i> Edit</span>
                                                        @elseif($permissionType == 'delete')
                                                        <span class="badge badge-danger"><i class="fas fa-trash mr-1"></i> Hapus</span>
                                                        @else
                                                        <span class="badge badge-secondary"><i class="fas fa-cog mr-1"></i> Lainnya</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-muted">{{ $module }}.{{ $permissionType }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Explorer Status Bar -->
                <div class="explorer-statusbar p-2 bg-light border-top d-flex justify-content-between">
                    <div class="small">
                        <span id="itemCount">{{ count($controllerPermissions) }}</span> item
                    </div>
                    <div class="small text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Klik pada baris untuk melihat detail izin
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Windows 10 Explorer Style */
    .explorer-navbar {
        border-bottom: 1px solid #e0e0e0;
    }

    .explorer-address-bar {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 3px;
        padding: 5px 10px;
    }

    .explorer-sidebar {
        background-color: #f9f9f9;
    }

    .sidebar-item {
        border-radius: 4px;
        cursor: pointer;
    }

    .sidebar-item:hover {
        background-color: #f0f0f0;
    }

    .sidebar-item.active {
        background-color: #e9ecef;
    }

    .explorer-table {
        margin-bottom: 0;
    }

    .explorer-table th {
        font-weight: 600;
        border-top: none;
        border-bottom: 1px solid #dee2e6;
    }

    .explorer-table td {
        border-top: none;
        border-bottom: 1px solid #f0f0f0;
    }

    .module-row {
        cursor: pointer;
    }

    .module-row:hover {
        background-color: #f5f5f5;
    }

    .module-row.selected {
        background-color: #e9ecef;
    }

    .explorer-details-table {
        background-color: white;
        border: 1px solid #e0e0e0;
    }

    .explorer-details-table th,
    .explorer-details-table td {
        padding: 6px 12px;
    }

    .toggle-details {
        width: 30px;
        height: 30px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toggle-details i {
        transition: transform 0.3s;
    }

    .toggle-details.active i {
        transform: rotate(180deg);
    }

    .badge {
        font-size: 85%;
        font-weight: 500;
    }

    .badge-pill {
        padding-right: 0.8em;
        padding-left: 0.8em;
    }

    .explorer-statusbar {
        color: #666;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Toggle details for a single module
        $('.toggle-details').click(function(e) {
            e.stopPropagation();
            var targetId = $(this).data('target');
            $('#' + targetId).toggle();
            $(this).toggleClass('active');
        });

        // Click on row to toggle details
        $('.module-row').click(function() {
            var button = $(this).find('.toggle-details');
            button.click();
        });

        // Search functionality with highlighting
        $("#searchPermission").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            var hasResults = false;

            // Hide all detail rows first
            $('.details-row').hide();
            $('.toggle-details').removeClass('active');

            // Filter module rows
            $(".module-row").each(function() {
                var moduleMatch = $(this).data('module').toLowerCase().indexOf(value) > -1 ||
                                 $(this).find('strong').text().toLowerCase().indexOf(value) > -1;
                var detailsId = $(this).find('.toggle-details').data('target');
                var permissionMatch = false;

                // Check if any permission in this module matches
                $('#' + detailsId + ' .permission-item').each(function() {
                    if($(this).data('permission').toLowerCase().indexOf(value) > -1) {
                        permissionMatch = true;
                        return false; // break the loop
                    }
                });

                if (moduleMatch || permissionMatch) {
                    $(this).show();
                    hasResults = true;

                    // If searching and found match in permissions, show details
                    if (permissionMatch && value.length > 0) {
                        $('#' + detailsId).show();
                        $(this).find('.toggle-details').addClass('active');
                    }
                } else {
                    $(this).hide();
                }
            });

            // Show message if no results
            if (!hasResults && value.length > 0) {
                if ($('#no-results-row').length === 0) {
                    $('tbody').append('<tr id="no-results-row"><td colspan="4" class="text-center py-4"><i class="fas fa-search mr-2"></i>Tidak ada izin yang sesuai dengan pencarian</td></tr>');
                }
            } else {
                $('#no-results-row').remove();
            }
        });

        // Expand all details
        $("#expandAllBtn").click(function() {
            $('.details-row').show();
            $('.toggle-details').addClass('active');
        });

        // Collapse all details
        $("#collapseAllBtn").click(function() {
            $('.details-row').hide();
            $('.toggle-details').removeClass('active');
        });

        // Filter by permission type
        $('.filter-permission').click(function() {
            var permissionType = '';
            if ($(this).data('type') === 'view') permissionType = 'view';
            else if ($(this).data('type') === 'create') permissionType = 'create';
            else if ($(this).data('type') === 'edit') permissionType = 'edit';
            else if ($(this).data('type') === 'delete') permissionType = 'delete';
            else return; // Jika tidak ada tipe yang cocok, keluar dari fungsi

            // Reset tampilan
            $('.details-row').hide();
            $('.toggle-details').removeClass('active');
            $('.module-row').hide();

            // Tampilkan modul yang memiliki izin yang sesuai
            $(".module-row").each(function() {
                var detailsId = $(this).find('.toggle-details').data('target');
                var hasPermission = false;

                $('#' + detailsId + ' .permission-item').each(function() {
                    if($(this).data('permission').toLowerCase().includes('.' + permissionType)) {
                        hasPermission = true;
                        return false; // break the loop
                    }
                });

                if (hasPermission) {
                    $(this).show();
                    $('#' + detailsId).show();
                    $(this).find('.toggle-details').addClass('active');
                }
            });
        });
    });
</script>
@stop