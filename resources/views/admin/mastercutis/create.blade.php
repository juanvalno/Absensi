@extends('adminlte::page')

@section('title', 'Add Master cuti')

@section('content_header')
<h1>Add Master cuti</h1>
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

        <form action="{{ route('mastercutis.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="uraian">Uraian <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('uraian') is-invalid @enderror" id="uraian" name="uraian" value="{{ old('uraian') }}" required>
                @error('uraian')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="jenis_karyawan">Diperuntukkan Untuk <span class="text-danger">*</span></label>
                <select class="form-control @error('jenis_karyawan') is-invalid @enderror" id="jenis_karyawan" name="jenis_karyawan" required>
                    <option value="">-- Pilih Jenis Karyawan --</option>
                    <option value="bulanan" {{ old('jenis_karyawan') == 'bulanan' ? 'selected' : '' }}>Karyawan Bulanan</option>
                    <option value="harian" {{ old('jenis_karyawan') == 'harian' ? 'selected' : '' }}>Karyawan Harian</option>
                    <option value="borongan" {{ old('jenis_karyawan') == 'borongan' ? 'selected' : '' }}>Karyawan Borongan</option>
                </select>
                @error('jenis_karyawan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <small class="form-text text-muted">Pilih jenis karyawan yang berhak atas cuti ini</small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_bulanan" name="is_bulanan" value="1" {{ old('is_bulanan') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_bulanan">Bulanan?</label>
                </div>
                <small class="form-text text-muted">Centang jika cuti ini menggunakan perhitungan bulanan</small>
            </div>

            <div class="form-group">
                <label for="cuti_max">Cuti Maksimal</label>
                <input type="text" class="form-control @error('cuti_max') is-invalid @enderror" id="cuti_max" name="cuti_max" value="{{ old('cuti_max') }}">
                @error('cuti_max')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="izin_max">Izin Maksimal</label>
                <input type="text" class="form-control @error('izin_max') is-invalid @enderror" id="izin_max" name="izin_max" value="{{ old('izin_max') }}">
                @error('izin_max')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_potonggaji" name="is_potonggaji" value="1" {{ old('is_potonggaji') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_potonggaji">Potong Gaji?</label>
                </div>
                <small class="form-text text-muted">Centang jika cuti ini memerlukan pemotongan gaji</small>
            </div>

            <div class="form-group" id="nominal-group" style="{{ old('is_potonggaji') ? '' : 'display: none;' }}">
                <label for="nominal">Nominal</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control rupiah @error('nominal') is-invalid @enderror" id="nominal" name="nominal" value="{{ old('nominal') }}">
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
        
        // Toggle form fields based on jenis_karyawan selection
        $('#jenis_karyawan').on('change', function() {
            var selectedType = $(this).val();
            
            // If bulanan is selected, show bulanan checkbox
            if (selectedType === 'bulanan') {
                $('#is_bulanan').prop('checked', true);
            } else if (selectedType === 'harian' || selectedType === 'borongan') {
                $('#is_bulanan').prop('checked', false);
            }
        });
    });
</script>
@stop