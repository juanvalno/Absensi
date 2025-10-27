@extends('adminlte::page')

@section('title', 'Edit Potongan')

@section('content_header')
<h1>Edit Potongan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Potongan Details</h3>
        <div class="card-tools">
            <a href="{{ route('potongans.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('potongans.update', $potongan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nama_potongan">Nama Potongan</label>
                <input type="text" class="form-control @error('nama_potongan') is-invalid @enderror" id="nama_potongan" name="nama_potongan" value="{{ old('nama_potongan', $potongan->nama_potongan) }}" required>
                @error('nama_potongan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <label for="type">Tipe Potongan</label>
                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">Pilih Tipe</option>
                    <option value="wajib" {{ (old('type', $potongan->type) == 'wajib') ? 'selected' : '' }}>Wajib</option>
                    <option value="tidak_wajib" {{ (old('type', $potongan->type) == 'tidak_wajib') ? 'selected' : '' }}>Tidak Wajib</option>
                </select>
                @error('type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <a href="{{ route('potongans.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@stop