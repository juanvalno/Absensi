@extends('adminlte::page')

@section('title', 'Generate Hari Minggu')

@section('content_header')
<h1>Generate Hari Minggu</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Generate Hari Minggu untuk Tahun Tertentu</h3>
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

        <form action="{{ route('hariliburs.generate-sundays') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="year">Tahun <span class="text-danger">*</span></label>
                <select class="form-control select2 @error('year') is-invalid @enderror" id="year" name="year" required>
                    @foreach($yearOptions as $yearValue => $yearLabel)
                    <option value="{{ $yearValue }}" {{ old('year', $currentYear) == $yearValue ? 'selected' : '' }}>
                        {{ $yearLabel }}
                    </option>
                    @endforeach
                </select>
                @error('year')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="replace_existing" name="replace_existing" value="1" {{ old('replace_existing') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="replace_existing">
                        Replace Existing Sundays (Hapus dan buat ulang Hari Minggu yang sudah ada untuk tahun tersebut)
                    </label>
                </div>
                <small class="form-text text-muted">
                    Jika dicentang, semua Hari Minggu yang sudah ada untuk tahun tersebut akan dihapus dan dibuat ulang.
                    Jika tidak dicentang, hanya akan menambahkan Hari Minggu yang belum ada.
                </small>
                
                <!-- Remove these duplicate checkboxes -->
                <!-- <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="replace_existing" name="replace_existing">
                        <label class="custom-control-label" for="replace_existing">Ganti hari Minggu yang sudah ada</label>
                    </div>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="create_attendance" name="create_attendance" value="1">
                    <label class="custom-control-label" for="create_attendance">Buat Absensi Otomatis untuk Semua Karyawan</label>
                </div>
                <small class="form-text text-muted">Jika dicentang, sistem akan otomatis membuat absensi dengan status 'Libur' untuk semua karyawan aktif pada hari Minggu yang dibuat.</small> -->
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-plus"></i> Generate Hari Minggu
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </form>
    </div>
    <div class="card-footer">
        <div class="mb-0 alert alert-info">
            <i class="fas fa-info-circle"></i>
            Fitur ini akan menghasilkan semua Hari Minggu untuk tahun yang dipilih dan menambahkannya ke database sebagai Hari Libur.
        </div>
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
            theme: 'bootstrap4'
        });
    });

</script>
@stop
