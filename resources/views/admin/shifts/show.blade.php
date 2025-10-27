@extends('adminlte::page')

@section('title', 'View Shift')

@section('content_header')
<h1>Shift Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $shift->kode_shift }} - {{ $shift->jenis_shift }}</h3>
        <div class="card-tools">
            <a href="{{ route('shifts.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can_show('shift.edit')
            <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit Shift
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="150">Kode Shift</th>
                        <td>{{ $shift->kode_shift }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Shift</th>
                        <td>{{ $shift->jenis_shift }}</td>
                    </tr>
                    <tr>
                        <th>Jam Masuk</th>
                        <td>{{ $shift->formatted_jam_masuk }}</td>
                    </tr>
                    <tr>
                        <th>Jam Pulang</th>
                        <td>{{ $shift->formatted_jam_pulang }}</td>
                    </tr>
                    <tr>
                        <th>Durasi</th>
                        <td>{{ $shift->duration }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $shift->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $shift->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
