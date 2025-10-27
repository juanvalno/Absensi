@extends('adminlte::page')

@section('title', 'Master Cuti Management')

@section('content_header')
<h1>Master Cuti Management</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Master Cuti List</h3>
        <div class="card-tools">
            @can_show('mastercutis.create')
            <a href="{{ route('mastercutis.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Master Cuti
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

        <table class="table table-bordered" id="mastercutiTable">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Uraian</th>
                    <th>Bulanan</th>
                    <th>Cuti Max</th>
                    <th>Izin Max</th>
                    <th>Potong Gaji</th>
                    <th>Nominal</th>
                    @can_show('mastercutis.edit')
                    <th width="150">Action</th>
                    @endcan_show
                </tr>
            </thead>
            <tbody>
                @foreach($mastercutis as $index => $mastercuti)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $mastercuti->uraian }}</td>
                    <td>{{ $mastercuti->bulanan_text }}</td>
                    <td>{{ $mastercuti->cuti_max ?: '-' }}</td>
                    <td>{{ $mastercuti->izin_max ?: '-' }}</td>
                    <td>{{ $mastercuti->potonggaji_text }}</td>
                    <td class="text-right">{{ $mastercuti->formatted_nominal }}</td>
                    @can_show('mastercutis.edit')
                    <td>
                        <a href="{{ route('mastercutis.edit', $mastercuti) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('mastercutis.destroy', $mastercuti) }}" method="POST" class="d-inline">
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
        $('#mastercutiTable').DataTable({
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
