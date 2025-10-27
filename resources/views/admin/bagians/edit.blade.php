@extends('adminlte::page')

@section('title', 'Edit Bagian')

@section('content_header')
<h1>Edit Bagian</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Bagian Details</h3>
        <div class="card-tools">
            <a href="{{ route('bagians.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('bagians.update', $bagian) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="id_departemen">Departemen <span class="text-danger">*</span></label>
                <select class="form-control select2 @error('id_departemen') is-invalid @enderror" id="id_departemen" name="id_departemen" required>
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($departemens as $departemen)
                    <option value="{{ $departemen->id }}" {{ old('id_departemen', $bagian->id_departemen) == $departemen->id ? 'selected' : '' }}>
                        {{ $departemen->name_departemen }}
                    </option>
                    @endforeach
                </select>
                @error('id_departemen')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name_bagian">Nama Bagian <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name_bagian') is-invalid @enderror" id="name_bagian" name="name_bagian" value="{{ old('name_bagian', $bagian->name_bagian) }}" required autofocus>
                @error('name_bagian')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group text-right">
                <a href="{{ route('bagians.index') }}" class="btn btn-secondary mr-2">
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

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@stop

@section('js')
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "-- Pilih Departemen --",
            allowClear: true
        });
    });
</script>
@stop
