@extends('adminlte::page')

@section('title', 'Form Tambah Mesin Absensi')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Form Tambah Mesin Absensi</h1>
    <a href="{{ route('mesinabsensis.index') }}" class="btn btn-secondary btn-sm">
        ← Kembali
    </a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('mesinabsensis.update', $mesinabsensi) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama Mesin Absensi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $mesinabsensi->nama) }}" required>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lokasi">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi', $mesinabsensi->lokasi) }}" required>
                        @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="alamat_ip">Alamat IP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('alamat_ip') is-invalid @enderror" id="alamat_ip" name="alamat_ip" value="{{ old('alamat_ip', $mesinabsensi->alamat_ip) }}" required>
                        @error('alamat_ip')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kunci_komunikasi">Kunci Komunikasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kunci_komunikasi') is-invalid @enderror" id="kunci_komunikasi" name="kunci_komunikasi" value="{{ old('kunci_komunikasi', $mesinabsensi->kunci_komunikasi) }}" required>
                        @error('kunci_komunikasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status_aktif">Status</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="status_aktif" name="status_aktif" value="1" {{ old('status_aktif', $mesinabsensi->status_aktif) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="status_aktif">
                                {{ $mesinabsensi->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $mesinabsensi->keterangan) }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="mr-2 btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(function() {
        // Status switch label toggle
        $('#status_aktif').on('change', function() {
            $(this).next('label').text(this.checked ? 'Aktif' : 'Tidak Aktif');
        });
    });

</script>
@stop
