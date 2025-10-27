@extends('adminlte::page')

@section('title', 'Manajemen Pengguna')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-edit text-primary mr-2"></i>Edit Pengguna</h1>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-white">
            <h3 class="card-title">Form Edit Pengguna</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-user mr-1"></i>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-envelope mr-1"></i>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-lock mr-1"></i>Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-user-shield mr-1"></i>Peran <span class="text-danger">*</span></label>
                            <select name="roles[]" class="select2 @error('roles') is-invalid @enderror" multiple
                                data-placeholder="Pilih peran" style="width: 100%;" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ in_array($role->name, $user->roles->pluck('name')->toArray()) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-users-cog mr-1"></i>Tipe Pengguna <span
                                    class="text-danger">*</span></label>
                            <select class="form-control @error('user_type') is-invalid @enderror" id="user_type"
                                name="user_type" required>
                                <option value="">-- Pilih Tipe Pengguna --</option>
                                <option value="owner" {{ old('user_type', $userType) == 'owner' ? 'selected' : '' }}>
                                    Pemilik / Admin</option>
                                <option value="karyawan"
                                    {{ old('user_type', $userType) == 'karyawan' || isset($karyawan) ? 'selected' : '' }}>
                                    Karyawan</option>
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
                                <label for="departemen_id"><i class="fas fa-building mr-1"></i> Departemen <span
                                        class="text-danger">*</span></label>
                                <select class="form-control select2 @error('departemen_id') is-invalid @enderror"
                                    id="departemen_id" name="departemen_id">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach ($departemen as $dept)
                                        <option value="{{ $dept->id }}"
                                            {{ old('departemen_id', $karyawan->departemen_id ?? '') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name_departemen }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Pilih departemen tempat karyawan ini bekerja.</small>
                                @error('departemen_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="karyawan_id"><i class="fas fa-user-tie mr-1"></i> Pilih Karyawan <span
                                        class="text-danger">*</span></label>
                                <select class="form-control select2 @error('karyawan_id') is-invalid @enderror"
                                    id="karyawan_id" name="karyawan_id"
                                    {{ old('departemen_id', isset($karyawan) && $karyawan->departemen_id ? $karyawan->departemen_id : '') ? '' : 'disabled' }}>
                                    <option value="">--
                                        {{ old('departemen_id', isset($karyawan) && $karyawan->departemen_id ? $karyawan->departemen_id : '') ? 'Pilih Karyawan' : 'Pilih Departemen Terlebih Dahulu' }}
                                        --</option>
                                    @if (isset($karyawan) && $karyawan)
                                        <option value="{{ $karyawan->id }}" selected>{{ $karyawan->nik_karyawan }} -
                                            {{ $karyawan->nama_karyawan }}</option>
                                    @endif
                                </select>
                                <small class="form-text text-muted">Hubungkan akun pengguna ini dengan data karyawan yang
                                    ada.</small>
                                @error('karyawan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            background-color: rgba(0, 0, 0, 0.03);
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

            // Load karyawan berdasarkan departemen yang dipilih
            $('#departemen_id').on('change', function() {
                var departemenId = $(this).val();
                if (departemenId) {
                    $('#karyawan_id').prop('disabled', false);
                    var selectedKaryawan = $('#karyawan_id').val();
                    $('#karyawan_id').empty().append('<option value="">-- Pilih Karyawan --</option>');

                    var karyawanList = @json($karyawanByDepartemen);

                    if (karyawanList[departemenId] && karyawanList[departemenId].length > 0) {
                        $.each(karyawanList[departemenId], function(key, value) {
                            $('#karyawan_id').append('<option value="' + value.id + '"' +
                                (selectedKaryawan == value.id ? ' selected' : '') + '>' +
                                value.nik_karyawan + ' - ' + value.nama_karyawan + '</option>');
                        });
                        $('#karyawan_id').prop('required', true);
                    } else {
                        $('#karyawan_id').append(
                            '<option value="">Tidak ada karyawan di departemen ini</option>');
                    }
                } else {
                    $('#karyawan_id').prop('disabled', true);
                    $('#karyawan_id').empty().append(
                        '<option value="">-- Pilih Departemen Terlebih Dahulu --</option>');
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

            // Add animation to form submission
            $('form').on('submit', function() {
                // Validate form before animation
                if (this.checkValidity()) {
                    // Add loading state to submit button
                    $('button[type="submit"]').html(
                        '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...').attr('disabled',
                        true);

                    // Add fade effect to form
                    $(this).find('.card').css('opacity', '0.7');

                    return true;
                }
            });

            // Add tooltip to all buttons with title attribute
            $('[title]').tooltip({
                placement: 'top',
                trigger: 'hover'
            });

            // Highlight fields on focus
            $('.form-control').on('focus', function() {
                $(this).closest('.form-group').addClass('border-left border-primary pl-2');
            }).on('blur', function() {
                $(this).closest('.form-group').removeClass('border-left border-primary pl-2');
            });

            // Password strength indicator
            $('#password').on('keyup', function() {
                var password = $(this).val();
                var strength = 0;

                if (password.length >= 8) strength += 1;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
                if (password.match(/\d/)) strength += 1;
                if (password.match(/[^a-zA-Z\d]/)) strength += 1;

                var strengthBar = '';
                for (var i = 0; i < strength; i++) {
                    strengthBar += '<span class="text-success"><i class="fas fa-star"></i></span>';
                }
                for (var i = strength; i < 4; i++) {
                    strengthBar += '<span class="text-muted"><i class="far fa-star"></i></span>';
                }

                if (password.length > 0) {
                    $(this).closest('.form-group').find('.form-text').html(
                        'Kekuatan Password: ' + strengthBar +
                        (strength < 3 ? ' <small class="text-danger">(Perlu diperkuat!)</small>' :
                            ' <small class="text-success">(Bagus!)</small>')
                    );
                } else {
                    $(this).closest('.form-group').find('.form-text').html(
                        'Password minimal 8 karakter dan harus mengandung huruf dan angka.'
                    );
                }
            });
        });
    </script>
@stop
