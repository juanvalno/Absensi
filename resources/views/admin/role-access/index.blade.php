@extends('adminlte::page')

@section('title', 'Role Access Management')

@section('content_header')
<h1>Role Access Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('role-access.index') }}" method="GET" class="form-inline">
                    <select name="role" class="form-control mr-2" onchange="this.form.submit()">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @if($selectedRole)
            <div class="col-md-6 text-right">
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#copyPermissionsModal">
                    <i class="fas fa-copy"></i> Copy Permissions From Other Role
                </button>
            </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if($selectedRole)
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i> <strong>Info:</strong> Perubahan yang Anda buat di halaman ini akan mempengaruhi menu yang dapat diakses oleh semua pengguna dengan peran ini. Setelah menyimpan perubahan, pengguna dengan peran ini akan melihat menu sesuai dengan izin yang diberikan.
        </div>

        <div class="mb-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="checkAll">
                <label class="custom-control-label" for="checkAll">
                    <strong>Check/Uncheck All</strong>
                </label>
            </div>
        </div>

        <form id="permissionForm">
            <div class="row">
                @foreach($permissions as $module => $modulePermissions)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input module-checkbox" id="module-{{ $module }}">
                                <label class="custom-control-label" for="module-{{ $module }}">
                                    <strong>{{ ucfirst($module) }}</strong>
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach($modulePermissions as $permission)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input permission-checkbox" id="permission-{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" data-module="{{ $module }}" {{ $selectedRole->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="permission-{{ $permission->id }}">
                                    {{ ucwords(str_replace(['.', '-', '_'], ' ', explode('.', $permission->name)[1])) }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="?refresh_menu=1" class="btn btn-secondary">Refresh Menu</a>
            </div>
        </form>

        <!-- Modal for copying permissions -->
        <div class="modal fade" id="copyPermissionsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Copy Permissions From Role</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="copyPermissionsForm">
                            <div class="form-group">
                                <label>Select Role to Copy From</label>
                                <select name="from_role" class="form-control">
                                    <option value="">Select Role</option>
                                    @foreach($roles->where('id', '!=', $selectedRole->id) as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="copyPermissions()">Copy</button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            Please select a role to manage its permissions.
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Handle check all checkbox
        $('#checkAll').change(function() {
            $('.permission-checkbox').prop('checked', $(this).prop('checked'));
            $('.module-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Handle module checkbox
        $('.module-checkbox').change(function() {
            let module = $(this).attr('id').replace('module-', '');
            let isChecked = $(this).prop('checked');
            $(`input[data-module="${module}"]`).prop('checked', isChecked);
            updateCheckAllState();
        });

        // Handle individual permissions
        $('.permission-checkbox').change(function() {
            let module = $(this).data('module');
            let moduleCheckbox = $(`#module-${module}`);
            let modulePermissions = $(`input[data-module="${module}"]`);
            let checkedPermissions = modulePermissions.filter(':checked');

            moduleCheckbox.prop('checked', modulePermissions.length === checkedPermissions.length);
            updateCheckAllState();
        });

        // Update initial state
        $('.module-checkbox').each(function() {
            let module = $(this).attr('id').replace('module-', '');
            let modulePermissions = $(`input[data-module="${module}"]`);
            let checkedPermissions = modulePermissions.filter(':checked');
            $(this).prop('checked', modulePermissions.length === checkedPermissions.length);
        });
        updateCheckAllState();

        // Handle form submission
        $('#permissionForm').submit(function(e) {
            e.preventDefault();

            // Tambahkan indikator loading
            Swal.fire({
                title: 'Menyimpan perubahan...'
                , allowOutsideClick: false
                , didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ $selectedRole ? route('role-access.update', $selectedRole) : '' }}"
                , method: 'POST'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , permissions: $('.permission-checkbox:checked').map(function() {
                        return $(this).val();
                    }).get()
                }
                , success: function(response) {
                    Swal.fire({
                        icon: 'success'
                        , title: 'Berhasil'
                        , text: 'Perubahan izin berhasil disimpan!'
                        , showConfirmButton: true
                        , confirmButtonText: 'OK'
                        , showCancelButton: true
                        , cancelButtonText: 'Refresh Halaman'
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.reload();
                        }
                    });
                }
                , error: function(xhr) {
                    Swal.fire({
                        icon: 'error'
                        , title: 'Error'
                        , text: 'Gagal menyimpan perubahan. Silakan coba lagi.'
                    });
                }
            });
        });
    });

    function updateCheckAllState() {
        let totalPermissions = $('.permission-checkbox').length;
        let checkedPermissions = $('.permission-checkbox:checked').length;
        $('#checkAll').prop('checked', totalPermissions === checkedPermissions);
    }

    function copyPermissions() {
        let fromRole = $('select[name="from_role"]').val();
        if (!fromRole) {
            alert('Please select a role to copy from');
            return;
        }

        // Tambahkan indikator loading
        Swal.fire({
            title: 'Menyalin izin...'
            , allowOutsideClick: false
            , didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ $selectedRole ? route('role-access.copy-permissions', $selectedRole) : '' }}"
            , method: 'POST'
            , data: {
                _token: "{{ csrf_token() }}"
                , from_role: fromRole
            }
            , success: function(response) {
                $('#copyPermissionsModal').modal('hide');
                Swal.fire({
                    icon: 'success'
                    , title: 'Berhasil'
                    , text: 'Izin berhasil disalin!'
                    , showConfirmButton: true
                    , confirmButtonText: 'OK'
                }).then(() => {
                    window.location.reload();
                });
            }
            , error: function(xhr) {
                Swal.fire({
                    icon: 'error'
                    , title: 'Error'
                    , text: 'Gagal menyalin izin. Silakan coba lagi.'
                });
            }
        });
    }

</script>
@stop
