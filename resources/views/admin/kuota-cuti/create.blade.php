@extends('adminlte::page')

@section('title', 'Tambah Kuota Cuti Tahunan')

@section('content_header')
    <h1>Tambah Kuota Cuti Tahunan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Kuota Cuti</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('kuota-cuti.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="karyawan_id">Karyawan</label>
                    <select name="karyawan_id" id="karyawan_id" class="form-control select2 @error('karyawan_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tahun">Tahun</label>
                    <input type="number" name="tahun" id="tahun" class="form-control @error('tahun') is-invalid @enderror"
                           value="{{ old('tahun', $tahun) }}" min="2000" max="2100" required>
                    @error('tahun')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kuota_awal">Kuota Awal (Hari)</label>
                    <input type="number" name="kuota_awal" id="kuota_awal" class="form-control @error('kuota_awal') is-invalid @enderror"
                           value="{{ old('kuota_awal', 12) }}" min="0" max="12" required>
                    <small class="text-muted">Maksimal 12 hari per tahun</small>
                    @error('kuota_awal')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <a href="{{ route('kuota-cuti.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
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
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Karyawan"
            });
        });
    </script>
@stop