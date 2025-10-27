@extends('adminlte::page')

@section('title', 'Manage Potongan')

@section('content_header')
<h1>Manage Potongan</h1>
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h5><i class="icon fas fa-check"></i> Success!</h5>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h5><i class="icon fas fa-ban"></i> Error!</h5>
    {{ session('error') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Potongan List</h3>
        <div class="card-tools">
            <a href="{{ route('potongans.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="potongans-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Potongan</th>
                    <th>Tipe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($potongans as $index => $potongan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $potongan->nama_potongan }}</td>
                    <td>
                        @if($potongan->type == 'wajib')
                            <span class="badge badge-primary">Wajib</span>
                        @else
                            <span class="badge badge-info">Tidak Wajib</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('potongans.show', $potongan->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('potongans.edit', $potongan->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('potongans.destroy', $potongan->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this potongan?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/vendor/datatables/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="/vendor/datatables/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#potongans-table').DataTable();
    });
</script>
@stop