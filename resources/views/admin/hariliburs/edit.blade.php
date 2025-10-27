@extends('adminlte::page')

@section('title', 'Edit Hari Libur')

@section('content_header')
<h1>Edit Hari Libur</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hari Libur Form</h3>
        <div class="card-tools">
            <a href="{{ route('hariliburs.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <form action="{{ route('hariliburs.update', $harilibur) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $harilibur->tanggal->format('Y-m-d')) }}" required>
                @error('tanggal')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <label for="nama_libur">Nama Libur <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_libur') is-invalid @enderror" id="nama_libur" name="nama_libur" value="{{ old('nama_libur', $harilibur->nama_libur) }}" required>
                @error('nama_libur')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $harilibur->keterangan) }}</textarea>
                @error('keterangan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<!-- Any additional CSS -->
@stop

@section('js')
<!-- Any additional JavaScript -->
@stop
