@extends('adminlte::page')

@section('title', 'View Program Studi')

@section('content_header')
<h1>Program Studi Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $programStudi->name_programstudi }}</h3>
        <div class="card-tools">
            <a href="{{ route('program_studis.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can_show('program_studis.edit')
            <a href="{{ route('program_studis.edit', $programStudi) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit Program Studi
            </a>
            @endcan_show
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Nama Program Studi</th>
                        <td>{{ $programStudi->name_programstudi }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>{{ $programStudi->created_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $programStudi->updated_at->format('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- You can add related data here, for example:
        <h4 class="mt-4">Mahasiswa dalam Program Studi Ini</h4>
        @if($programStudi->students->isNotEmpty())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Angkatan</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programStudi->students as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
        <td>{{ $student->name }}</td>
        <td>{{ $student->nim }}</td>
        <td>{{ $student->year }}</td>
        <td>
            <a href="{{ route('students.show', $student) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i>
            </a>
        </td>
        </tr>
        @endforeach
        </tbody>
        </table>
        @else
        <div class="alert alert-info">
            Tidak ada mahasiswa dalam program studi ini.
        </div>
        @endif
        --}}
    </div>
</div>
@stop
