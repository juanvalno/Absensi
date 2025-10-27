@extends('adminlte::page')

@section('title', 'Edit Master cuti')

@section('content_header')
<h1>Edit Master cuti</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Master cuti Form</h3>
        <div class="card-tools">
            <a href="{{ route('mastercutis.index') }}" class="btn btn-default btn-sm">
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

        <form action="{{ route('mastercutis.update', $mastercuti) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="uraian">Uraian <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('uraian') is-invalid @enderror" id="uraian" name="uraian" value="{{ old('uraian', $mastercuti->uraian) }}" required>
                @error('uraian')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_bulanan" name="is_bulanan" value="1" {{ old('is_bulanan', $mastercuti->is_bulanan) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_bulanan">Bulanan?</label>
                </div>
                <small class="form-text text-muted">Centang jika cuti ini menggunakan perhitungan bulanan</small>
            </div>

            <div class="form-group">
                <label for="cuti_max">Cuti Maksimal</label>
                <input type="text" class="form-control @error('cuti_max') is-invalid @enderror" id="cuti_max" name="cuti_max" value="{{ old('cuti_max', $mastercuti->cuti_max) }}">
                @error('cuti_max')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="izin_max">Izin Maksimal</label>
                <input type="text" class="form-control @error('izin_max') is-invalid @enderror" id="izin_max" name="izin_max" value="{{ old('izin_max', $mastercuti->izin_max) }}">
                @error('izin_max')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_potonggaji" name="is_potonggaji" value="1" {{ old('is_potonggaji', $mastercuti->is_potonggaji) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_potonggaji">Potong Gaji?</label>
                </div>
                <small class="form-text text-muted">Centang jika cuti ini memerlukan pemotongan gaji</small>
            </div>

            <div class="form-group" id="nominal-group" style="{{ old('is_potonggaji', $mastercuti->is_potonggaji) ? '' : 'display: none;' }}">
                <label for="nominal">Nominal</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control rupiah @error('nominal') is-invalid @enderror" id="nominal" name="nominal" value="{{ old('nominal', $mastercuti->nominal) }}">
                    @error('nominal')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <small class="form-text text-muted">Format: Rp. 1.000.000</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('mastercutis.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<!-- Add CSS -->
@stop

@section('js')
<script src="{{ asset('vendor/inputmask/jquery.inputmask.min.js') }}"></script>
<script>
    $(function() {
        // Show/hide nominal field based on is_potonggaji checkbox
        $('#is_potonggaji').on('change', function() {
            if ($(this).is(':checked')) {
                $('#nominal-group').show();
            } else {
                $('#nominal-group').hide();
                $('#nominal').val('');
            }
        });

        // Initialize rupiah input mask
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
