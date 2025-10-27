@extends('adminlte::page')

@section('title', 'Profesi Management')

@section('content_header')
<h1>Profesi Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Profesi List</h3>
        <div class="card-tools">
            @can_show('profesis.create')
            <a href="{{ route('profesis.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Profesi
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

        <table class="table table-bordered" id="profesiTable">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Nama Profesi</th>
                    <th>Tunjangan Profesi</th>
                    <th>Tanggal Dibuat</th>
                    @can_show('profesis.edit')
                    <th width="150">Action</th>
                    @endcan_show
                </tr>
            </thead>
            <tbody id="profesiTableBody">
                @foreach($profesis as $index => $profesi)
                <tr data-id="{{ $profesi->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $profesi->name_profesi }}</td>
                    <td class="text-right">Rp {{ number_format($profesi->tunjangan_profesi, 0, ',', '.') }}</td>
                    <td>{{ $profesi->created_at->format('d-m-Y H:i:s') }}</td>
                    @can_show('profesis.edit')
                    <td>
                        <a href="{{ route('profesis.edit', $profesi) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('profesis.destroy', $profesi) }}" method="POST" class="d-inline">
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
        $('#profesiTable').DataTable({
            "paging": true
            , "lengthChange": true
            , "searching": true
            , "ordering": true
            , "info": true
            , "autoWidth": false
            , "responsive": true
        , });
    });

</script>
@stop
