@extends('adminlte::page')

@section('title', 'Edit Kuota Cuti Tahunan')

@section('content_header')
    <h1>Edit Kuota Cuti Tahunan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Kuota Cuti</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('kuota-cuti.update', $kuotaCuti->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Karyawan</label>
                    <input type="text" class="form-control" value="{{ $kuotaCuti->karyawan->nama_karyawan ?? 'Tidak ditemukan' }}" readonly>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="text" class="form-control" value="{{ $kuotaCuti->tahun }}" readonly>
                </div>

                <div class="form-group">
                    <label for="kuota_awal">Kuota Awal (Hari)</label>
                    <input type="number" name="kuota_awal" id="kuota_awal" class="form-control @error('kuota_awal') is-invalid @enderror"
                           value="{{ old('kuota_awal', $kuotaCuti->kuota_awal) }}" min="0" max="12" required>
                    <small class="text-muted">Maksimal 12 hari per tahun</small>
                    @error('kuota_awal')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Kuota Digunakan (Hari)</label>
                    <input type="text" class="form-control" value="{{ $kuotaCuti->kuota_digunakan }}" readonly>
                </div>

                <div class="form-group">
                    <label>Kuota Sisa (Hari)</label>
                    <input type="text" class="form-control" value="{{ $kuotaCuti->kuota_sisa }}" readonly>
                </div>

                <div class="form-group">
                    <a href="{{ route('kuota-cuti.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
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
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Validate form before submission
            $('form').on('submit', function(e) {
                let isValid = true;

                // Validate kuota_awal (ensure it's between 0-12)
                const kuotaAwal = parseInt($('#kuota_awal').val());
                if (isNaN(kuotaAwal) || kuotaAwal < 0 || kuotaAwal > 12) {
                    $('#kuota_awal').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#kuota_awal').removeClass('is-invalid');
                }

                return isValid;
            });
        });
    </script>
@stop