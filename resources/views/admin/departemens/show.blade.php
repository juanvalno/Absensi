@extends('adminlte::page')

@section('title', 'View Departemen')

@section('content_header')
<h1>Departemen Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $departemen->name_departemen }}</h3>
        <div class="card-tools">
            <a href="{{ route('departemens.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can_show('departemens.edit')
            <a href="{{ route('departemens.edit', $departemen) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit Departemen
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Nama Departemen</th>
                        <td>{{ $departemen->name_departemen }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $departemen->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $departemen->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <h4 class="mt-4">Bagian dalam Departemen Ini</h4>
        @if($departemen->bagians->isNotEmpty())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th>Nama Bagian</th>
                    <th>Tanggal Dibuat</th>
                    @can_show('bagians.edit')
                    <th width="150">Action</th>
                    @endcan_show
                </tr>
            </thead>
            <tbody>
                @foreach($departemen->bagians as $index => $bagian)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $bagian->name_bagian }}</td>
                    <td>{{ $bagian->created_at->format('d-m-Y H:i:s') }}</td>
                    @can_show('bagians.edit')
                    <td>
                        <a href="{{ route('bagians.edit', $bagian) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('bagians.show', $bagian) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                    @endcan_show
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info">
            Tidak ada bagian dalam departemen ini.
        </div>
        @endif
    </div>
</div>
@stop
