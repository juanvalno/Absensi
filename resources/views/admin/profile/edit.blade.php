@extends('adminlte::page')

@section('title', 'Ubah Profil')

@section('content_header')
    <h1>Ubah Profil</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mr-2 fas fa-check-circle"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mr-2 fas fa-exclamation-circle"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff"
                            alt="Foto Profil">
                    </div>
                    <h3 class="text-center profile-username">{{ $user->name }}</h3>
                    <p class="text-center text-muted">
                        <i class="mr-1 fas fa-envelope"></i>{{ $user->email }}
                    </p>
                </div>
            </div>

            @if($user->karyawan)
                <div class="mt-3 card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">
                            <i class="mr-2 fas fa-id-card"></i>Data Karyawan
                        </h3>
                    </div>
                    <div class="p-0 card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <b>NIK</b>
                                <span class="float-right">{{ $user->karyawan->nik_karyawan }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Departemen</b>
                                <span class="float-right">{{ $user->karyawan->departemen->name_departemen }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="mr-2 fas fa-user-edit"></i>Ubah Informasi Profil
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($user->email_verified_at)
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Mengubah email akan memerlukan verifikasi ulang.
                                </small>
                            @endif
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">
                        <i class="mr-2 fas fa-key"></i>Ubah Password
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mr-2 fas fa-info-circle"></i>
                        Password minimal 8 karakter. Gunakan kombinasi huruf, angka, dan simbol untuk keamanan yang lebih baik.
                    </div>
                    <form method="post" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control"
                                id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key"></i> Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .profile-user-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid #adb5bd;
            padding: 3px;
        }
        .card-header {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .alert {
            margin-bottom: 1rem;
        }
        .list-group-item {
            padding: 0.75rem 1.25rem;
        }
        .invalid-feedback {
            font-size: 85%;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto hide alerts after 5 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert:not(.alert-info)').fadeOut('slow');
            }, 5000);
        });
    </script>
@stop