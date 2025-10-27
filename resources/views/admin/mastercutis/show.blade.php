@extends('adminlte::page')

@section('title', 'View Master cuti')

@section('content_header')
<h1>Master cuti Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $mastercuti->uraian }}</h3>
        <div class="card-tools">
            <a href="{{ route('mastercutis.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can_show('mastercuti.edit')
            <a href="{{ route('mastercutis.edit', $mastercuti) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit Master cuti
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Uraian</th>
                        <td>{{ $mastercuti->uraian }}</td>
                    </tr>
                    <tr>
                        <th>Bulanan</th>
                        <td>{{ $mastercuti->bulanan_text }}</td>
                    </tr>
                    <tr>
                        <th>Cuti Maksimal</th>
                        <td>{{ $mastercuti->cuti_max ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Izin Maksimal</th>
                        <td>{{ $mastercuti->izin_max ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Potong Gaji</th>
                        <td>{{ $mastercuti->potonggaji_text }}</td>
                    </tr>
                    @if($mastercuti->is_potonggaji)
                    <tr>
                        <th>Nominal</th>
                        <td>{{ $mastercuti->formatted_nominal }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $mastercuti->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $mastercuti->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
