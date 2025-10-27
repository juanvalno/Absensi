@extends('adminlte::page')

@section('title', 'Shift Management')

@section('content_header')
<h1>Shift Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Shift List</h3>
        <div class="card-tools">
            @can_show('shifts.create')
            <a href="{{ route('shifts.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Shift
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

        <table class="table table-bordered" id="shiftTable">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Kode Shift</th>
                    <th>Jenis Shift</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Durasi</th>
                    @can_show('shifts.edit')
                    <th width="150">Action</th>
                    @endcan_show
                </tr>
            </thead>
            <tbody>
                @foreach($shifts as $index => $shift)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $shift->kode_shift }}</td>
                    <td>{{ $shift->jenis_shift }}</td>
                    <td>{{ $shift->formatted_jam_masuk }}</td>
                    <td>{{ $shift->formatted_jam_pulang }}</td>
                    <td>{{ $shift->duration }}</td>
                    @can_show('shifts.edit')
                    <td>
                        <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('shifts.destroy', $shift) }}" method="POST" class="d-inline">
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
        $('#shiftTable').DataTable({
            "paging": true
            , "lengthChange": true
            , "searching": true
            , "ordering": true
            , "info": true
            , "autoWidth": false
            , "responsive": true
            , "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

</script>
@stop
