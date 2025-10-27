@extends('adminlte::page')

@section('title', 'Add Jabatan')

@section('content_header')
<h1>Add Jabatan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Jabatan Form</h3>
        <div class="card-tools">
            <a href="{{ route('jabatans.index') }}" class="btn btn-default btn-sm">
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

        <form action="{{ route('jabatans.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name_jabatan">Nama Jabatan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name_jabatan') is-invalid @enderror" id="name_jabatan" name="name_jabatan" value="{{ old('name_jabatan') }}" style="text-transform: uppercase;" required>
                @error('name_jabatan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <label for="gaji_pokok">Gaji Pokok <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control rupiah @error('gaji_pokok') is-invalid @enderror" id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok') }} required">
                    @error('gaji_pokok')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <small class="form-text text-muted">Format: Rp. 1.000.000</small>
            </div>
            <div class="form-group">
                <label for="premi">Premi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control rupiah @error('premi') is-invalid @enderror" id="premi" name="premi" value="{{ old('premi') }}">
                    @error('premi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <small class="form-text text-muted">Format: Rp. 1.000.000</small>
            </div>
            <div class="form-group">
                <label for="tunjangan_jabatan">Tunjangan Jabatan <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control rupiah @error('tunjangan_jabatan') is-invalid @enderror" id="tunjangan_jabatan" name="tunjangan_jabatan" value="{{ old('tunjangan_jabatan') }}">
                    @error('tunjangan_jabatan')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <small class="form-text text-muted">Format: Rp. 1.000.000</small>
            </div>
            <div class="form-group">
                <label for="uang_lembur_biasa">Uang Lembur Biasa <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control rupiah @error('uang_lembur_biasa') is-invalid @enderror" id="uang_lembur_biasa" name="uang_lembur_biasa" value="{{ old('uang_lembur_biasa') }}">
                    @error('uang_lembur_biasa')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <small class="form-text text-muted">Format: Rp. 1.000.000</small>
            </div>
            <div class="form-group">
                <label for="uang_lembur_libur">Uang Lembur Libur <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control rupiah @error('uang_lembur_libur') is-invalid @enderror" id="uang_lembur_libur" name="uang_lembur_libur" value="{{ old('uang_lembur_libur') }}">
                    @error('uang_lembur_libur')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <small class="form-text text-muted">Format: Rp. 1.000.000</small>
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
<!-- Add CSS for the currency input mask -->
@stop

@section('js')
<!-- Include the inputmask library -->
<script src="{{ asset('vendor/inputmask/jquery.inputmask.min.js') }}"></script>
<script>
    $(function() {
        // Apply the currency mask to the rupiah inputs
        $('.rupiah').inputmask({
            alias: 'numeric'
            , groupSeparator: '.'
            , radixPoint: ','
            , autoGroup: true
            , prefix: ''
            , digits: 0
            , rightAlign: false
            , removeMaskOnSubmit: true
        });
    });

</script>
@stop
