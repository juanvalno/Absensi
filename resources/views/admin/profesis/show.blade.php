@extends('adminlte::page')

@section('title', 'View Profesi')

@section('content_header')
<h1>Profesi Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $profesi->name_profesi }}</h3>
        <div class="card-tools">
            <a href="{{ route('profesis.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can_show('profesi.edit')
            <a href="{{ route('profesis.edit', $profesi) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit Profesi
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Nama Profesi</th>
                        <td>{{ $profesi->name_profesi }}</td>
                    </tr>
                    <tr>
                        <th>Tunjangan Profesi</th>
                        <td>{{ $profesi->tunjangan_profesi }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $profesi->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $profesi->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- You can add related data here, for example:
        <h4 class="mt-4">Karyawan dengan Profesi Ini</h4>
        @if($profesi->karyawans->isNotEmpty())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Nama Karyawan</th>
                        <th>NIP</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profesi->karyawans as $index => $karyawan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
        <td>{{ $karyawan->name }}</td>
        <td>{{ $karyawan->nip }}</td>
        <td>
            <a href="{{ route('karyawans.show', $karyawan) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i>
            </a>
        </td>
        </tr>
        @endforeach
        </tbody>
        </table>
        @else
        <div class="alert alert-info">
            Tidak ada karyawan dengan profesi ini.
        </div>
        @endif
        --}}
    </div>
</div>
@stop
