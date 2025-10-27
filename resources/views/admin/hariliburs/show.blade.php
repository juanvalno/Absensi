@extends('adminlte::page')

@section('title', 'View Hari Libur')

@section('content_header')
<h1>Hari Libur Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $harilibur->nama_libur }}</h3>
        <div class="card-tools">
            <a href="{{ route('hariliburs.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can_show('harilibur.edit')
            <a href="{{ route('hariliburs.edit', $harilibur) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit Hari Libur
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="150">Tanggal</th>
                        <td>{{ $harilibur->tanggal->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Nama Libur</th>
                        <td>{{ $harilibur->nama_libur }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $harilibur->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $harilibur->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $harilibur->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
