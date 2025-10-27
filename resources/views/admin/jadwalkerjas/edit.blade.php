@extends('adminlte::page')

@section('title', 'Edit Kehadiran')

@section('content_header')
<h1>Edit Kehadiran</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Kehadiran</h3>
        <div class="card-tools">
            <a href="{{ route('jadwalkerjas.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
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

        <form action="{{ route('jadwalkerjas.update', $jadwalkerja->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="karyawan_id">Karyawan <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <!-- Display the karyawan's name as a readonly field -->
                            <input type="text" class="form-control @error('karyawan_id') is-invalid @enderror" id="karyawan_id" name="karyawan_id" value="{{ old('karyawan_id', $jadwalkerja->karyawan->nama_karyawan) }}" readonly>

                            <!-- Hidden field to submit karyawan_id -->
                            <input type="hidden" name="karyawan_id" value="{{ old('karyawan_id', $jadwalkerja->karyawan_id) }}">

                            @error('karyawan_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>


                        @error('karyawan_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" readonly name="tanggal" value="{{ old('tanggal', $jadwalkerja->tanggal->format('Y-m-d')) }}" required>
                        @error('tanggal')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="shift_id">Shift <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id">
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id', $jadwalkerja->shift_id) == $shift->id ? 'selected' : '' }}>
                                {{ $shift->kode_shift }} ({{ $shift->jenis_shift }}: {{ $shift->jam_masuk }} - {{ $shift->jam_pulang }})
                            </option>
                            @endforeach
                        </select>
                        @error('shift_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('jadwalkerjas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
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
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4'
            , width: '100%'
        });
    });

</script>
@stop
