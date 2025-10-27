@extends('adminlte::page')
@section('title', 'Manajemen Pengguna')
@section('content_header')
<h1><i class="fas fa-users text-primary mr-2"></i>Manajemen Pengguna</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h3 class="card-title">Daftar Pengguna Sistem</h3>
        <div class="card-tools">
            @can_show('users.create')
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Pengguna Baru
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        {{-- Notifikasi Sukses --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        {{-- Notifikasi Error --}}
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <table class="table table-bordered table-striped" id="users-table">
            <thead class="thead-light">
                <tr>
                    <th style="width: 50px">No</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Tipe</th>
                    <th>Terhubung Dengan</th>
                    <th>Peran</th>
                    @can_show('users.create')
                    <th style="width: 150px" class="text-center">Aksi</th>
                    @endcan_show
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        @if($user->karyawan)
                        <span class="badge badge-primary">Karyawan</span>
                        @else
                        <span class="badge badge-warning">Pemilik/Admin</span>
                        @endif
                    </td>
                    <td>
                        @if($user->karyawan)
                        <a href="{{ route('karyawans.show', $user->karyawan->id) }}" class="text-primary">
                            <i class="fas fa-user-tie mr-1"></i>{{ $user->karyawan->nama_karyawan }}
                            <small class="d-block text-muted">
                                <i class="fas fa-id-card mr-1"></i>{{ $user->karyawan->nik_karyawan }}
                                <br>
                                <i class="fas fa-building mr-1"></i>{{ $user->karyawan->departemen->name_departemen ?? 'Belum Ada Departemen' }}
                            </small>
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @foreach($user->roles as $role)
                        <span class="badge badge-info">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    @can_show('users.create')
                    <td class="text-center">
                        @can_show('users.edit')
                        <div class="btn-group">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) document.getElementById('delete-form-{{ $user->id }}').submit()" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="if(confirm('Reset password menjadi 12345678?')) document.getElementById('reset-form-{{ $user->id }}').submit()" title="Reset Password">
                                <i class="fas fa-key"></i>
                            </button>
                        </div>
                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                        <form id="reset-form-{{ $user->id }}" action="{{ route('users.reset-password', $user) }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        @endcan_show
                    </td>
                    @endcan_show
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/vendor/datatables-bs4/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="/vendor/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#users-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>
@stop
