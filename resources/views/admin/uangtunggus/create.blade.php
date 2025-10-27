@extends('adminlte::page')

@section('title', 'Tambah Uang Tunggu Baru')

@section('content_header')
<h1>Tambah Uang Tunggu Baru</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Uang Tunggu</h3>
        <div class="card-tools">
            <a href="{{ route('uangtunggus.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('uangtunggus.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="karyawan_id">Karyawan</label>
                <select class="form-control select2 @error('karyawan_id') is-invalid @enderror" id="karyawan_id" name="karyawan_id" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $karyawan)
                    <option value="{{ $karyawan->id }}" {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                        {{ $karyawan->nama_karyawan }}
                    </option>
                    @endforeach
                </select>
                @error('karyawan_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                @error('tanggal_mulai')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                @error('tanggal_selesai')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nominal">Nominal</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="number" step="0.01" class="form-control @error('nominal') is-invalid @enderror" id="nominal" name="nominal" value="{{ old('nominal') }}" required readonly>
                </div>
                @error('nominal')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <a href="{{ route('uangtunggus.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
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
            theme: 'bootstrap4'
        });

        // Auto calculate nominal based on date range
        function calculateNominal() {
            const startDate = new Date($('#tanggal_mulai').val());
            const endDate = new Date($('#tanggal_selesai').val());
            
            if (startDate && endDate && startDate <= endDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end dates
                const ratePerDay = 30000; // Rp 30.000 per day
                const nominal = diffDays * ratePerDay;
                $('#nominal').val(nominal);
            } else {
                $('#nominal').val('');
            }
        }

        // Calculate on date changes
        $('#tanggal_mulai, #tanggal_selesai').on('change', calculateNominal);
    });
</script>
@stop
