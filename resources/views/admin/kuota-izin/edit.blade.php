@extends('adminlte::page')

@section('title', 'Edit Kuota Izin Tahunan')

@section('content_header')
    <h1>Edit Kuota Izin Tahunan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Kuota Izin</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('kuota-izin.update', $kuotaIzin->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Karyawan</label>
                    <input type="text" class="form-control" value="{{ $kuotaIzin->karyawan->nama_karyawan }}" readonly>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="text" class="form-control" value="{{ $kuotaIzin->tahun }}" readonly>
                </div>

                <div class="form-group">
                    <label for="kuota_awal">Kuota Awal (Hari)</label>
                    <input type="number" name="kuota_awal" id="kuota_awal" class="form-control @error('kuota_awal') is-invalid @enderror"
                           value="{{ old('kuota_awal', $kuotaIzin->kuota_awal) }}" min="0" max="3" required>
                    <small class="text-muted">Maksimal 3 hari per tahun</small>
                    @error('kuota_awal')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tanggal_expired">Tanggal Expired</label>
                    <input type="date" name="tanggal_expired" id="tanggal_expired" class="form-control @error('tanggal_expired') is-invalid @enderror"
                           value="{{ old('tanggal_expired', $kuotaIzin->tanggal_expired) }}">
                    @error('tanggal_expired')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $kuotaIzin->keterangan) }}</textarea>
                    @error('keterangan')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <a href="{{ route('kuota-izin.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop