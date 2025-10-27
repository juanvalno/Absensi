@extends('adminlte::page')

@section('title', 'Profil Saya')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="mr-2 fas fa-user-circle"></i>Profil Saya</h1>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Ubah Profil
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mr-2 fas fa-check-circle"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mr-2 fas fa-exclamation-circle"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center position-relative">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ $user->karyawan && $user->karyawan->foto_karyawan ? asset('storage/karyawan/foto/' . $user->karyawan->foto_karyawan) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=4e73df&color=fff' }}"
                            alt="Foto Profil">
                    </div>
                    <h3 class="text-center profile-username">{{ $user->name }}</h3>
                    <p class="text-center text-muted">
                        <i class="fas fa-envelope"></i> {{ $user->email }}
                    </p>
                    <hr>
                    <div class="user-stats">
                        <div class="text-center row">
                            <div class="col-12">
                                <div class="description-block">
                                    <h5 class="description-header">Role</h5>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-primary badge-lg">
                                            <i class="fas {{ $role->name === 'admin' ? 'fa-user-shield' : 'fa-user-tie' }}"></i>
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($user->karyawan)
                <div class="card">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title">
                            <i class="mr-2 fas fa-id-card"></i>Data Karyawan
                        </h3>
                    </div>
                    <div class="p-0 card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="mr-2 fas fa-fingerprint text-primary"></i>
                                    <strong>NIK</strong>
                                </div>
                                <span>{{ $user->karyawan->nik_karyawan }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="mr-2 fas fa-building text-primary"></i>
                                    <strong>Departemen</strong>
                                </div>
                                <span>{{ $user->karyawan->departemen->name_departemen }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <!-- Info Tabs -->
            <div class="card">
                <div class="p-2 card-header">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#profile" data-toggle="tab">
                                <i class="mr-2 fas fa-user"></i>Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#security" data-toggle="tab">
                                <i class="mr-2 fas fa-shield-alt"></i>Keamanan
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tr>
                                        <th style="width: 200px">
                                            <i class="mr-2 fas fa-user"></i>Nama Lengkap
                                        </th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <i class="mr-2 fas fa-envelope"></i>Email
                                        </th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <!-- Remove the verification status row -->
                                    <tr>
                                        <th>
                                            <i class="mr-2 fas fa-calendar-plus"></i>Tanggal Dibuat
                                        </th>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <i class="mr-2 fas fa-clock"></i>Terakhir Diperbarui
                                        </th>
                                        <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @if ($user->karyawan)
                                        <tr>
                                            <th>
                                                <i class="mr-2 fas fa-id-badge"></i>NIK Karyawan
                                            </th>
                                            <td>{{ $user->karyawan->nik_karyawan }}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <i class="mr-2 fas fa-building"></i>Departemen
                                            </th>
                                            <td>{{ $user->karyawan->departemen->name_departemen }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>
                                            <i class="mr-2 fas fa-user-tag"></i>Role
                                        </th>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge badge-{{ $role->name === 'admin' ? 'primary' : 'info' }} mr-1">
                                                    <i class="fas {{ $role->name === 'admin' ? 'fa-user-shield' : 'fa-user-tie' }}"></i>
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="security">
                            <div class="security-info">
                                <div class="alert alert-info border-left-primary">
                                    <div class="d-flex">
                                        <div class="mr-3">
                                            <i class="fas fa-shield-alt fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h5 class="alert-heading">Keamanan Akun</h5>
                                            <p class="mb-0">Pastikan email Anda terverifikasi dan password selalu dijaga
                                                kerahasiaannya.
                                                Ubah password secara berkala untuk keamanan akun Anda.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Add more security information here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .profile-user-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Remove profile-status related styles */
        .badge-lg {
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .description-block {
            padding: 10px 0;
        }

        .description-header {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .border-left-primary {
            border-left: 4px solid #4e73df;
        }

        .nav-pills .nav-link.active {
            background-color: #4e73df;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
        }

        .card {
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto hide alerts
            setTimeout(function() {
                $('.alert:not(.alert-info)').fadeOut('slow');
            }, 5000);

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
