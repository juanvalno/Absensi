@extends('adminlte::page')

@section('title', 'Jabatan Management')

@section('content_header')
<h1>Jabatan Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Jabatan List</h3>
        <div class="card-tools">
            @can_show('jabatans.create')
            <a href="{{ route('jabatans.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Jabatan
            </a>
            @endcan_show
        </div>
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

        <table class="table table-bordered" id="jabatanTable">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Nama Jabatan</th>
                    <th>Gaji Pokok</th>
                    <th>Premi</th>
                    <th>Tunjangan Jabatan</th>
                    <th>Uang Lembur Biasa</th>
                    <th>Uang Lembur Libur</th>
                    @can_show('jabatans.edit')
                    <th width="150">Action</th>
                    @endcan_show
                </tr>
            </thead>
            <tbody id="jabatanTableBody">
                @foreach($jabatans as $index => $jabatan)
                <tr data-id="{{ $jabatan->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $jabatan->name_jabatan }}</td>
                    <td class="text-right">{{ $jabatan->gaji_pokok_rupiah }}</td>
                    <td class="text-right">{{ $jabatan->premi_rupiah }}</td>
                    <td class="text-right">{{ $jabatan->tunjangan_jabatan_rupiah }}</td>
                    <td class="text-right">{{ $jabatan->uang_lembur_biasa_rupiah }}</td>
                    <td class="text-right">{{ $jabatan->uang_lembur_libur_rupiah }}</td>
                    @can_show('jabatans.edit')
                    <td>
                        <a href="{{ route('jabatans.edit', $jabatan) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('jabatans.destroy', $jabatan) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                    @endcan_show
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
@stop

@section('js')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(function() {
        $('#jabatanTable').DataTable({
            "paging": true
            , "lengthChange": true
            , "searching": true
            , "ordering": true
            , "info": true
            , "autoWidth": false
            , "responsive": true
            , "scrollX": true
        , });
    });

</script>
@stop
