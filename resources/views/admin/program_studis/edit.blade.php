@extends('adminlte::page')

@section('title', 'Edit Program Studi')

@section('content_header')
<h1>Edit Program Studi</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Program Studi Details</h3>
        <div class="card-tools">
            <a href="{{ route('program_studis.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('program_studis.update', $programStudi) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name_programstudi">Nama Program Studi</label>
                <input type="text" class="form-control @error('name_programstudi') is-invalid @enderror" id="name_programstudi" name="name_programstudi" value="{{ old('name_programstudi', $programStudi->name_programstudi) }}" required>
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
                    <option value="SMA" {{ old('education_type', $programStudi->education_type) == 'SMA' ? 'selected' : '' }}>SMA</option>
                    <option value="non-SMA" {{ old('education_type', $programStudi->education_type) == 'non-SMA' ? 'selected' : '' }}>non-SMA</option>
                </select>
                @error('education_type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group text-right">
                <a href="{{ route('program_studis.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@stop
