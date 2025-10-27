@extends('adminlte::page')

@section('title', 'Manajemen Pengguna')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-user-plus text-primary mr-2"></i>Tambah Pengguna Baru</h1>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="mr-1 fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="card">
    <div class="card-header bg-white">
        <h3 class="card-title">Form Pengguna Baru</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-user mr-1"></i>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope mr-1"></i>Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock mr-1"></i>Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-user-shield mr-1"></i>Peran <span class="text-danger">*</span></label>
                        <select name="roles[]" class="select2 @error('roles') is-invalid @enderror" multiple data-placeholder="Pilih peran" style="width: 100%;" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('roles')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-users-cog mr-1"></i>Tipe Pengguna <span class="text-danger">*</span></label>
                        <select class="form-control @error('user_type') is-invalid @enderror" id="user_type" name="user_type" required>
                            <option value="">-- Pilih Tipe Pengguna --</option>
                            <option value="owner" {{ old('user_type') == 'owner' ? 'selected' : '' }}>Pemilik / Admin</option>
                            <option value="karyawan" {{ old('user_type') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                        </select>
                        @error('user_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div id="karyawan_section" style="display: none;">
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-building mr-1"></i>Departemen <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('departemen_id') is-invalid @enderror" id="departemen_id" name="departemen_id">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departemen as $dept)
                                <option value="{{ $dept->id }}" {{ old('departemen_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name_departemen }}
                                </option>
                                @endforeach
                            </select>
                            @error('departemen_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-user-tie mr-1"></i>Karyawan <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('karyawan_id') is-invalid @enderror" id="karyawan_id" name="karyawan_id" disabled>
                                <option value="">-- Pilih Departemen Terlebih Dahulu --</option>
                            </select>
                            @error('karyawan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/vendor/select2/css/select2.min.css">
<link rel="stylesheet" href="/vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.125);
        background-color: rgba(0,0,0,0.03);
    }
    .form-control {
        border-radius: 4px;
    }
    .select2-container--bootstrap4 .select2-selection {
        border-radius: 4px;
    }
    .text-danger {
        font-weight: bold;
    }
    .form-text {
        font-size: 0.85rem;
    }
    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border-color: #006fe6;
        color: #fff;
        padding: 0 10px;
        margin-top: 0.31rem;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fff;
        opacity: 0.8;
    }
</style>
@stop


@section('js')
<script src="/vendor/select2/js/select2.full.min.js"></script>
<script>
    $(function() {
        // Initialize select2
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        // Toggle password visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const passwordFieldType = passwordField.attr('type');
            const newType = passwordFieldType === 'password' ? 'text' : 'password';
            passwordField.attr('type', newType);

            // Toggle icon
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        });

        // Toggle karyawan section based on user type
        $('#user_type').on('change', function() {
            var type = $(this).val();
            if (type === 'karyawan') {
                $('#karyawan_section').slideDown(300);
                $('#departemen_id').prop('required', true);
            } else {
                $('#karyawan_section').slideUp(300);
                $('#departemen_id').prop('required', false);
                $('#karyawan_id').prop('required', false);
            }
        });

        // Load karyawan based on selected departemen
        $('#departemen_id').on('change', function() {
            var departemenId = $(this).val();
            if (departemenId) {
                // Enable karyawan dropdown
                $('#karyawan_id').prop('disabled', false);

                // Clear current options
                $('#karyawan_id').empty().append('<option value="">-- Pilih Karyawan --</option>');

                // Tampilkan karyawan dari data yang sudah disiapkan
                var karyawanList = @json($karyawanByDepartemen);

                if (karyawanList[departemenId] && karyawanList[departemenId].length > 0) {
                    // Populate karyawan dropdown
                    $.each(karyawanList[departemenId], function(key, value) {
                        $('#karyawan_id').append('<option value="' + value.id + '">' +
                            value.nik_karyawan + ' - ' + value.nama_karyawan + '</option>');
                    });
                    $('#karyawan_id').prop('required', true);
                } else {
                    $('#karyawan_id').append('<option value="">Tidak ada karyawan di departemen ini</option>');
                }
            } else {
                $('#karyawan_id').prop('disabled', true);
                $('#karyawan_id').empty().append('<option value="">-- Pilih Departemen Terlebih Dahulu --</option>');
                $('#karyawan_id').prop('required', false);
            }
        });

        // Trigger change event on page load
        $('#user_type').trigger('change');

        // If departemen_id has a value on page load, trigger its change event
        var selectedDepartemen = "{{ old('departemen_id') }}";
        if (selectedDepartemen) {
            $('#departemen_id').trigger('change');
        }
    });
</script>
@stop