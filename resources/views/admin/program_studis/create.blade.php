@extends('adminlte::page')

@section('title', 'Add New Jurusan')

@section('content_header')
<h1>Add New Jurusan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Jurusan Details</h3>
        <div class="card-tools">
            <a href="{{ route('program_studis.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('program_studis.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name_programstudi">Nama Jurusan</label>
                <input type="text" class="form-control @error('name_programstudi') is-invalid @enderror" id="name_programstudi" name="name_programstudi" value="{{ old('name_programstudi') }}" required>
                @error('name_programstudi')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="education_type">Jenis Pendidikan</label>
                <select class="form-control @error('education_type') is-invalid @enderror" id="education_type" name="education_type" required>
                    <option value="">-- Pilih Jenis Pendidikan --</option>
                    <option value="SMA" {{ old('education_type') == 'SMA' ? 'selected' : '' }}>SMA</option>
                    <option value="non-SMA" {{ old('education_type') == 'non-SMA' ? 'selected' : '' }}>non-SMA</option>
                </select>
                @error('education_type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            
            <div class="form-group">
                <a href="{{ route('program_studis.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@stop
