@extends('adminlte::page')

@section('title', 'User Access Management')

@section('content_header')
<h1>User Access Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('user-access.index') }}" method="GET" class="form-inline">
                    <select name="user" class="form-control mr-2" onchange="this.form.submit()">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @if($selectedUser)
            <div class="col-md-6 text-right">
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#copyAccessModal">
                    <i class="fas fa-copy"></i> Copy Access From Other User
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

        @if($selectedUser)
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i> <strong>Info:</strong> Di halaman ini Anda dapat menugaskan peran dan memberikan izin langsung ke pengguna. <br>
            - <strong>Roles:</strong> Memberikan semua izin yang terkait dengan peran tersebut<br>
            - <strong>Direct Permissions:</strong> Memberikan izin khusus ke pengguna terlepas dari peran mereka
        </div>

        <form id="accessForm">
            <!-- Roles Section -->
            <div class="form-group">
                <label><strong>Roles</strong></label>
                <div class="row">
                    @foreach($roles as $role)
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" {{ $selectedUser->hasRole($role) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="role-{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <hr>

            <!-- Direct Permissions Section -->
            <div class="form-group">
                <label><strong>Direct Permissions</strong></label>
                <div class="mb-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="checkAll">
                        <label class="custom-control-label" for="checkAll">
                            <strong>Check/Uncheck All Permissions</strong>
                        </label>
                    </div>
                </div>

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
                                    <input type="checkbox" class="custom-control-input permission-checkbox" id="permission-{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" data-module="{{ $module }}" {{ $selectedUser->hasDirectPermission($permission->name) ? 'checked' : '' }}>
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
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="?refresh_menu=1" class="btn btn-secondary">Refresh Menu</a>
            </div>
        </form>

        <!-- Modal for copying access -->
        <div class="modal fade" id="copyAccessModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Copy Access From User</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="copyAccessForm">
                            <div class="form-group">
                                <label>Select User to Copy From</label>
                                <select name="from_user" class="form-control">
                                    <option value="">Select User</option>
                                    @foreach($users->where('id', '!=', $selectedUser->id) as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="copyAccess()">Copy</button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            Please select a user to manage their access.
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
        $('#accessForm').submit(function(e) {
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
                url: "{{ $selectedUser ? route('user-access.update', $selectedUser) : '' }}"
                , method: 'POST'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , roles: $('input[name="roles[]"]:checked').map(function() {
                        return $(this).val();
                    }).get()
                    , permissions: $('.permission-checkbox:checked').map(function() {
                        return $(this).val();
                    }).get()
                }
                , success: function(response) {
                    Swal.fire({
                        icon: 'success'
                        , title: 'Berhasil'
                        , text: 'Akses pengguna berhasil diperbarui!'
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
                        , text: 'Gagal memperbarui akses pengguna. Silakan coba lagi.'
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

    function copyAccess() {
        let fromUser = $('select[name="from_user"]').val();
        if (!fromUser) {
            alert('Please select a user to copy from');
            return;
        }

        // Tambahkan indikator loading
        Swal.fire({
            title: 'Menyalin akses...'
            , allowOutsideClick: false
            , didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ $selectedUser ? route('user-access.copy-access', $selectedUser) : '' }}"
            , method: 'POST'
            , data: {
                _token: "{{ csrf_token() }}"
                , from_user: fromUser
            }
            , success: function(response) {
                $('#copyAccessModal').modal('hide');
                Swal.fire({
                    icon: 'success'
                    , title: 'Berhasil'
                    , text: 'Akses pengguna berhasil disalin!'
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
                    , text: 'Gagal menyalin akses pengguna. Silakan coba lagi.'
                });
            }
        });
    }

</script>
@stop
