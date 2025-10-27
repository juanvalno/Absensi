@extends('adminlte::page')

@section('title', 'Tambah Mesin Absensi')

@section('content_header')
<h1>Tambah Mesin Absensi</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Mesin Absensi</h3>
        <div class="card-tools">
            <a href="{{ route('mesinabsensis.index') }}" class="btn btn-default btn-sm">
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

        <form action="{{ route('mesinabsensis.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama Mesin Absensi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                        @error('nama')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="alamat_ip">Alamat IP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('alamat_ip') is-invalid @enderror" id="alamat_ip" name="alamat_ip" value="{{ old('alamat_ip') }}" required>
                        @error('alamat_ip')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kunci_komunikasi">Kunci Komunikasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kunci_komunikasi') is-invalid @enderror" id="kunci_komunikasi" name="kunci_komunikasi" value="{{ old('kunci_komunikasi', '0') }}" required>
                        @error('kunci_komunikasi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lokasi">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" required>
                        @error('lokasi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status_aktif">Status</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="status_aktif" name="status_aktif" value="1" {{ old('status_aktif', '1') == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="status_aktif">Aktif</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<div class="mt-4 card">
    <div class="card-header bg-info">
        <h3 class="card-title">Panduan Konfigurasi Mesin Absensi</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info"></i> Informasi Penting</h5>
            <p>Untuk mengintegrasikan mesin absensi dengan sistem, pastikan mesin absensi mendukung komunikasi melalui SOAP Web Service dan diakses melalui jaringan yang sama atau dapat dijangkau oleh server aplikasi.</p>
        </div>

        <dl class="row">
            <dt class="col-sm-3">Nama Mesin Absensi</dt>
            <dd class="col-sm-9">Nama untuk identifikasi mesin absensi di sistem.</dd>

            <dt class="col-sm-3">Alamat IP</dt>
            <dd class="col-sm-9">Alamat IP mesin absensi dalam format <code>xxx.xxx.xxx.xxx</code> (misalnya: 192.168.1.201).</dd>

            <dt class="col-sm-3">Kunci Komunikasi</dt>
            <dd class="col-sm-9">Kunci untuk otentikasi komunikasi dengan mesin absensi (biasanya diatur pada mesin atau defaultnya '0').</dd>

            <dt class="col-sm-3">Lokasi</dt>
            <dd class="col-sm-9">Lokasi fisik penempatan mesin absensi untuk memudahkan identifikasi.</dd>
        </dl>

        <div class="alert alert-warning">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian</h5>
            <p>Pastikan konfigurasi jaringan dan firewall memungkinkan komunikasi antara server aplikasi dan mesin absensi pada port 80 (HTTP).</p>
        </div>
    </div>
</div>
@stop

@section('css')
<!-- Additional CSS -->
@stop

@section('js')
<script>
    $(function() {
        // Auto-format IP address
        $('#alamat_ip').on('blur', function() {
            let ip = $(this).val();
            if (ip && !ip.match(/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/)) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Format alamat IP tidak valid.</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Auto-convert kunci komunikasi to number
        $('#kunci_komunikasi').on('blur', function() {
            let key = $(this).val();
            if (key && !isNaN(key)) {
                $(this).val(parseInt(key));
            }
        });
    });

</script>
@stop
