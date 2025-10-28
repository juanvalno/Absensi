@extends('adminlte::page')

@section('title', 'Edit Karyawan')

@section('content_header')
    <h1>Edit Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Karyawan</h3>
            <div class="card-tools">
                <a href="{{ route('karyawans.index', request()->only(['departemen_id','bagian_id','filter','search','per_page'])) }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('karyawans.update', array_merge([$karyawan->id], request()->only(['departemen_id','bagian_id','filter','search','per_page']))) }}" method="POST" enctype="multipart/form-data"
                id="karyawanForm">
                @csrf
                @method('PUT')

                <!-- Smart Wizard -->
                <div id="smartwizard" class="mx-auto">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#step-1">
                                <div class="num">1</div>
                                Data Pribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#step-2">
                                <div class="num">2</div>
                                Data Pekerjaan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#step-3">
                                <div class="num">3</div>
                                Data Pendidikan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#step-4">
                                <div class="num">4</div>
                                Data Tambahan
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Step 1: Data Pribadi -->
                        <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik_karyawan">NIK Karyawan <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('nik_karyawan') is-invalid @enderror"
                                            id="nik_karyawan" name="nik_karyawan"
                                            value="{{ old('nik_karyawan', $karyawan->nik_karyawan) }}" required>
                                        <small class="form-text text-muted">Format: YYYYMM###</small>
                                        @error('nik_karyawan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="nama_karyawan">Nama Karyawan <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('nama_karyawan') is-invalid @enderror"
                                            id="nama_karyawan" name="nama_karyawan"
                                            value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}" required>
                                        @error('nama_karyawan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label for="tgl_awalmmasuk">Tanggal Mulai Masuk <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('tgl_awalmmasuk') is-invalid @enderror"
                                            id="tgl_awalmmasuk" name="tgl_awalmmasuk"
                                            value="{{ old('tgl_awalmmasuk', $karyawan->tgl_awalmmasuk ? date('Y-m-d', strtotime($karyawan->tgl_awalmmasuk)) : '') }}"
                                            required>
                                        @error('tgl_awalmmasuk')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="statuskaryawan">Status Karyawan <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2 @error('statuskaryawan') is-invalid @enderror"
                                            id="statuskaryawan" name="statuskaryawan" required>
                                            <option value="">-- Pilih Status --</option>
                                            @foreach ($statusKaryawan as $status)
                                                <option value="{{ $status }}"
                                                    {{ old('statuskaryawan', $karyawan->statuskaryawan?->value) == $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('statuskaryawan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="hidden" name="iskontrak" value="0">
                                            <input type="checkbox" class="form-check-input @error('iskontrak') is-invalid @enderror" 
                                                   id="iskontrak" name="iskontrak" value="1" 
                                                   {{ old('iskontrak', $karyawan->iskontrak) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="iskontrak">
                                                Karyawan Kontrak
                                            </label>
                                            <small class="form-text text-muted">Centang jika karyawan masih dalam status kontrak</small>
                                            @error('iskontrak')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="foto_karyawan">Foto Karyawan</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file"
                                                    class="custom-file-input @error('foto_karyawan') is-invalid @enderror"
                                                    id="foto_karyawan" name="foto_karyawan">
                                                <label class="custom-file-label" for="foto_karyawan">Pilih file</label>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Format: JPG, PNG, JPEG. Max: 2MB.</small>
                                        @if ($karyawan->foto_karyawan)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/karyawan/foto/' . $karyawan->foto_karyawan) }}"
                                                    alt="Foto Karyawan" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        @error('foto_karyawan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="nama_bank">Nama Bank</label>
                                        <input type="text"
                                            class="form-control @error('nama_bank') is-invalid @enderror" id="nama_bank"
                                            name="nama_bank" value="{{ old('nama_bank', $karyawan->nama_bank) }}"
                                            placeholder="Contoh: BCA, Mandiri, BRI">
                                        <small class="form-text text-muted">Masukkan nama bank tempat rekening
                                            dibuat</small>
                                        @error('nama_bank')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="nomor_rekening">Nomor Rekening</label>
                                        <input type="text"
                                            class="form-control @error('nomor_rekening') is-invalid @enderror"
                                            id="nomor_rekening" name="nomor_rekening"
                                            value="{{ old('nomor_rekening', $karyawan->nomor_rekening) }}"
                                            inputmode="numeric">
                                        <small class="form-text text-muted">Masukkan nomor rekening bank tanpa
                                            spasi</small>
                                        @error('nomor_rekening')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="nama_pemilik_rekening">Nama Pemilik Rekening</label>
                                        <input type="text"
                                            class="form-control @error('nama_pemilik_rekening') is-invalid @enderror"
                                            id="nama_pemilik_rekening" name="nama_pemilik_rekening"
                                            value="{{ old('nama_pemilik_rekening', $karyawan->nama_pemilik_rekening) }}">
                                        <small class="form-text text-muted">Masukkan nama sesuai yang tertera pada buku
                                            rekening</small>
                                        @error('nama_pemilik_rekening')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Data Pekerjaan -->
                        <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_departemen">Departemen</label>
                                        <select class="form-control select2 @error('id_departemen') is-invalid @enderror"
                                            id="id_departemen" name="id_departemen">
                                            <option value="">-- Pilih Departemen --</option>
                                            @foreach ($departemens as $departemen)
                                                <option value="{{ $departemen->id }}"
                                                    {{ old('id_departemen', $karyawan->id_departemen) == $departemen->id ? 'selected' : '' }}>
                                                    {{ $departemen->name_departemen }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_departemen')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="id_bagian">Bagian</label>
                                        <select class="form-control select2 @error('id_bagian') is-invalid @enderror"
                                            id="id_bagian" name="id_bagian">
                                            <option value="">-- Pilih Bagian --</option>
                                            @foreach ($bagians as $bagian)
                                                <option value="{{ $bagian->id }}"
                                                    data-departemen="{{ $bagian->id_departemen }}"
                                                    {{ old('id_bagian', $karyawan->id_bagian) == $bagian->id ? 'selected' : '' }}>
                                                    {{ $bagian->name_bagian }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_bagian')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_jabatan">Jabatan</label>
                                        <select class="form-control select2 @error('id_jabatan') is-invalid @enderror"
                                            id="id_jabatan" name="id_jabatan">
                                            <option value="">-- Pilih Jabatan --</option>
                                            @foreach ($jabatans as $jabatan)
                                                <option value="{{ $jabatan->id }}"
                                                    {{ old('id_jabatan', $karyawan->id_jabatan) == $jabatan->id ? 'selected' : '' }}>
                                                    {{ $jabatan->name_jabatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_jabatan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="id_profesi">Profesi</label>
                                        <select class="form-control select2 @error('id_profesi') is-invalid @enderror"
                                            id="id_profesi" name="id_profesi">
                                            <option value="">-- Pilih Profesi --</option>
                                            @foreach ($profesis as $profesi)
                                                <option value="{{ $profesi->id }}"
                                                    {{ old('id_profesi', $karyawan->id_profesi) == $profesi->id ? 'selected' : '' }}>
                                                    {{ $profesi->name_profesi }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_profesi')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Data Pendidikan -->
                        <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pendidikan_terakhir">Pendidikan Terakhir <span
                                                class="text-danger">*</span></label>
                                        <select
                                            class="form-control select2 @error('pendidikan_terakhir') is-invalid @enderror"
                                            id="pendidikan_terakhir" name="pendidikan_terakhir" required>
                                            <option value="">-- Pilih Pendidikan --</option>
                                            @foreach ($pendidikanTerakhir as $pendidikan)
                                                <option value="{{ $pendidikan }}"
                                                    {{ old('pendidikan_terakhir', $karyawan->pendidikan_terakhir) == $pendidikan ? 'selected' : '' }}>
                                                    {{ $pendidikan }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pendidikan_terakhir')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="programstudi_group">
                                        <label for="id_programstudi">Prodi / Jurusan</label>
                                        <select
                                            class="form-control select2 @error('id_programstudi') is-invalid @enderror"
                                            id="id_programstudi" name="id_programstudi"
                                            data-selected="{{ $karyawan->id_programstudi }}">
                                            <option value="">-- Pilih Prodi / Jurusan --</option>
                                        </select>
                                        @error('id_programstudi')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Input NIK -->
                                    <div class="form-group">
                                        <label for="nik">NIK (KTP) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror"
                                            id="nik" name="nik" value="{{ old('nik', $karyawan->nik) }}"
                                            required maxlength="16" inputmode="numeric" placeholder="Maksimal 16 digit">
                                        <small class="form-text text-muted">Masukkan NIK KTP (maksimal 16 digit
                                            angka)</small>
                                        @error('nik')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Input KK -->
                                    <div class="form-group">
                                        <label for="kk">Nomor KK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('kk') is-invalid @enderror"
                                            id="kk" name="kk" value="{{ old('kk', $karyawan->kk) }}"
                                            required maxlength="16" inputmode="numeric" placeholder="Maksimal 16 digit">
                                        <small class="form-text text-muted">Masukkan nomor KK (maksimal 16 digit
                                            angka)</small>
                                        @error('kk')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="upload_ktp">Upload KTP</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file"
                                                    class="custom-file-input @error('upload_ktp') is-invalid @enderror"
                                                    id="upload_ktp" name="upload_ktp">
                                                <label class="custom-file-label" for="upload_ktp">Pilih file</label>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Format: JPG, PNG, JPEG. Max: 2MB.</small>
                                        @if ($karyawan->upload_ktp)
                                            <div class="mt-2">
                                                <img src="{{ $karyawan->ktpUrl }}" alt="Scan KTP" class="img-thumbnail"
                                                    style="max-height: 100px;">
                                            </div>
                                        @endif
                                        @error('upload_ktp')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="statuskawin">Status Perkawinan <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2 @error('statuskawin') is-invalid @enderror"
                                            id="statuskawin" name="statuskawin" required>
                                            <option value="">-- Pilih Status --</option>
                                            @foreach ($statusKawin as $status)
                                                <option value="{{ $status }}"
                                                    {{ old('statuskawin', $karyawan->statuskawin) == $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('statuskawin')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="jml_anggotakk">Jumlah Anggota KK <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('jml_anggotakk') is-invalid @enderror"
                                            id="jml_anggotakk" name="jml_anggotakk"
                                            value="{{ old('jml_anggotakk', $karyawan->jml_anggotakk) }}" required>
                                        @error('jml_anggotakk')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Data Tambahan -->
                        <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ortu_bapak">Nama Ayah <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('ortu_bapak') is-invalid @enderror"
                                            id="ortu_bapak" name="ortu_bapak"
                                            value="{{ old('ortu_bapak', $karyawan->ortu_bapak) }}" required>
                                        @error('ortu_bapak')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="ortu_ibu">Nama Ibu <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('ortu_ibu') is-invalid @enderror" id="ortu_ibu"
                                            name="ortu_ibu" value="{{ old('ortu_ibu', $karyawan->ortu_ibu) }}" required>
                                        @error('ortu_ibu')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="no_hp">Nomor HP <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                            id="no_hp" name="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}"
                                            required>
                                        @error('no_hp')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ukuran_kemeja">Ukuran Kemeja <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2 @error('ukuran_kemeja') is-invalid @enderror"
                                            id="ukuran_kemeja" name="ukuran_kemeja" required>
                                            <option value="">-- Pilih Ukuran --</option>
                                            @foreach ($ukuranKemeja as $ukuran)
                                                <option value="{{ $ukuran }}"
                                                    {{ old('ukuran_kemeja', $karyawan->ukuran_kemeja) == $ukuran ? 'selected' : '' }}>
                                                    {{ $ukuran }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ukuran_kemeja')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="ukuran_celana">Ukuran Celana <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('ukuran_celana') is-invalid @enderror"
                                            id="ukuran_celana" name="ukuran_celana"
                                            value="{{ old('ukuran_celana', $karyawan->ukuran_celana) }}" required>
                                        @error('ukuran_celana')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="ukuran_sepatu">Ukuran Sepatu <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('ukuran_sepatu') is-invalid @enderror"
                                            id="ukuran_sepatu" name="ukuran_sepatu"
                                            value="{{ old('ukuran_sepatu', $karyawan->ukuran_sepatu) }}" required>
                                        @error('ukuran_sepatu')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 alert alert-info">
                                <i class="fas fa-info-circle"></i> Silakan periksa kembali semua data sebelum menyimpan
                                perubahan.
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resign Modal -->
    <div class="modal fade" id="resignModal" tabindex="-1" role="dialog" aria-labelledby="resignModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="resignModalLabel">Confirm Resign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="resignForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Anda akan mengubah status karyawan menjadi resign.
                        </div>
                        <div class="form-group">
                            <label for="tanggal_resign">Tanggal Resign <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_resign" name="tanggal_resign"
                                required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Konfirmasi Resign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- WizardJS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/css/smart_wizard_all.min.css">
    <style>
        /* Custom wizard styles matching the CodePen example */
        .sw {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .sw .tab-content {
            padding: 20px;
            border: 1px solid #eeeeee;
            background: #ffffff;
            min-height: 300px;
        }

        .sw .nav-tabs {
            border: none !important;
            background-color: #f8f9fa;
            padding: 10px 10px 0 10px;
        }

        .sw .nav-tabs .nav-link {
            border: none;
            background: transparent;
            padding: 10px 20px;
            border-radius: 0;
            margin-right: 5px;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
        }

        .sw .nav-tabs .nav-link .num {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            margin-right: 10px;
            color: #666;
            font-weight: 600;
        }

        .sw .nav-tabs .nav-link.active {
            border-bottom-color: #007bff;
            color: #007bff;
        }

        .sw .nav-tabs .nav-link.active .num {
            background-color: #007bff;
            color: #fff;
        }

        .sw .nav-tabs .nav-link.done {
            border-bottom-color: #28a745;
            color: #28a745;
        }

        .sw .nav-tabs .nav-link.done .num {
            background-color: #28a745;
            color: #fff;
        }

        .sw .toolbar {
            padding: 10px;
            background-color: #ffffff;
            text-align: right;
            margin-top: 10px;
        }

        .sw .btn-toolbar-next,
        .sw .btn-toolbar-prev,
        .sw .btn-toolbar-reset {
            margin-left: 5px;
            border-radius: 3px;
        }

        .sw .btn-toolbar-next,
        .sw .btn-toolbar-reset {
            background-color: #007bff;
            color: #fff;
        }

        .sw .btn-toolbar-prev {
            border: 1px solid #007bff;
            color: #007bff;
            background-color: transparent;
        }

        /* Fix for the tabs display */
        .sw .nav-tabs {
            display: flex;
        }

        .sw .nav-item {
            flex: 1;
        }

        .sw .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@stop



@section('js')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <!-- WizardJS Script -->
    <script src="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/js/jquery.smartWizard.min.js"></script>
    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize custom file input
            bsCustomFileInput.init();

            // Cache DOM elements
            var departemenSelect = $('#id_departemen');
            var bagianFormGroup = $('#id_bagian').closest('.form-group');
            var bagianSelect = $('#id_bagian');
            var bagianLabel = bagianFormGroup.find('label');

            // Store the currently selected bagian ID
            var selectedBagianId = '{{ old('id_bagian', $karyawan->id_bagian) }}';
            var selectedDepartemenId = '{{ old('id_departemen', $karyawan->id_departemen) }}';

            // Simpan semua opsi bagian dalam cache
            var allBagianOptions = {};
            bagianSelect.find('option').each(function() {
                var option = $(this);
                var value = option.val();
                // Simpan opsi default (-- Pilih Bagian --) secara terpisah
                if (value === '') {
                    allBagianOptions['default'] = option.clone();
                } else {
                    // Kelompokkan opsi berdasarkan departemen
                    var deptId = option.data('departemen');
                    if (!allBagianOptions[deptId]) {
                        allBagianOptions[deptId] = [];
                    }
                    allBagianOptions[deptId].push(option.clone());
                }
            });

            // Function to check if a department has any divisions
            function departmentHasDivisions(departmentId) {
                return allBagianOptions[departmentId] && allBagianOptions[departmentId].length > 0;
            }

            // Function to update bagian dropdown based on departemen
            function updateBagianDropdown(departemenId, preserveSelection = false) {
                // Reset bagian selection and clear all options
                bagianSelect.empty();

                // Selalu tambahkan opsi default
                if (allBagianOptions['default']) {
                    bagianSelect.append(allBagianOptions['default'].clone());
                }

                if (!departemenId) {
                    // Hide bagian field if no department is selected
                    bagianFormGroup.hide();
                    bagianSelect.prop('required', false);
                } else if (departmentHasDivisions(departemenId)) {
                    // Show bagian field and make it required
                    bagianFormGroup.show();
                    bagianSelect.prop('required', true);
                    bagianLabel.html('Bagian <span class="text-danger">*</span>');

                    // Tambahkan hanya opsi yang terkait dengan departemen yang dipilih
                    $.each(allBagianOptions[departemenId], function(i, option) {
                        var clonedOption = option.clone();
                        // Check if this option should be selected (only if preserveSelection is true)
                        if (preserveSelection && clonedOption.val() == selectedBagianId) {
                            clonedOption.prop('selected', true);
                        }
                        bagianSelect.append(clonedOption);
                    });

                    // Set the selected value if preserveSelection is true
                    if (preserveSelection && selectedBagianId) {
                        bagianSelect.val(selectedBagianId);
                    }
                } else {
                    // Hide bagian field if no divisions for this department
                    bagianFormGroup.hide();
                    bagianSelect.prop('required', false);
                }

                // Rebuild Select2 to apply changes
                bagianSelect.select2('destroy').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
            }

            // Handle departemen change
            departemenSelect.on('change', function() {
                var departemenId = $(this).val();
                // Don't preserve selection when user manually changes departemen
                updateBagianDropdown(departemenId, false);
            });

            // Validate departemen and bagian relationship
            function validateDepartemenBagian() {
                var departemenId = $('#id_departemen').val();
                var bagianId = $('#id_bagian').val();

                if (departemenId) {
                    // Check if department has any bagian
                    var hasBagian = departmentHasDivisions(departemenId);

                    if (hasBagian && !bagianId) {
                        // If department has bagian but none selected, show error
                        $('#id_bagian').addClass('is-invalid');
                        $('#id_bagian').closest('.form-group').find('.select2-selection').addClass('border-danger');

                        // Add error message if not exists
                        if (!$('#bagian-error').length) {
                            $('<div id="bagian-error" class="mt-1 text-danger small">' +
                                    'Silakan pilih Bagian untuk Departemen yang dipilih.</div>')
                                .insertAfter('#id_bagian').closest('.select2-container');
                        }
                        return false;
                    }
                }

                // Remove error state
                $('#id_bagian').removeClass('is-invalid');
                $('#id_bagian').closest('.form-group').find('.select2-selection').removeClass('border-danger');
                $('#bagian-error').remove();
                return true;
            }

            // Validasi NIK dan KK
            function validateNikKk() {
                var nikValue = $('#nik').val();
                var kkValue = $('#kk').val();
                var valid = true;

                // Validasi panjang harus tepat 16 digit
                if (nikValue) {
                    if (nikValue.length !== 16) {
                        $('#nik').addClass('is-invalid');
                        // Add error message if not exists
                        if (!$('#nik-length-error').length) {
                            $('<div id="nik-length-error" class="invalid-feedback">' +
                                'NIK harus terdiri dari 16 digit.</div>').insertAfter('#nik');
                        }
                        valid = false;

                    } else {
                        $('#nik-length-error').remove();
                        $('#nik').removeClass('is-invalid');
                    }
                }

                if (kkValue) {
                    if (kkValue.length !== 16) {
                        $('#kk').addClass('is-invalid');
                        // Add error message if not exists
                        if (!$('#kk-length-error').length) {
                            $('<div id="kk-length-error" class="invalid-feedback">' +
                                'Nomor KK harus terdiri dari 16 digit.</div>').insertAfter('#kk');
                        }
                        valid = false;
                    } else {
                        $('#kk-length-error').remove();
                        $('#kk').removeClass('is-invalid');
                    }
                }

                // Jika keduanya sudah diisi dan nilainya sama
                if (nikValue && kkValue && nikValue === kkValue && nikValue.length === 16) {
                    // Tampilkan pesan error
                    if (!$('#nik-kk-error').length) {
                        $('<div id="nik-kk-error" class="mt-2 alert alert-danger">' +
                            '<i class="fas fa-exclamation-triangle"></i> ' +
                            'NIK dan Nomor KK tidak boleh sama!</div>').insertAfter('#kk');
                    }

                    // Tambahkan class is-invalid
                    $('#nik, #kk').addClass('is-invalid');
                    valid = false;
                } else {
                    // Hapus pesan error jika ada
                    $('#nik-kk-error').remove();
                }

                return valid;
            }

            // Validasi numerik dan harus 16 karakter untuk NIK dan KK
            $('#nik, #kk').on('input', function() {
                // Hapus semua karakter non-numerik
                this.value = this.value.replace(/[^0-9]/g, '');

                // Batasi maksimal 16 karakter
                if (this.value.length > 16) {
                    this.value = this.value.substring(0, 16);
                }

                // Validasi panjang saat input
                var fieldId = this.id;
                var errorId = fieldId + '-length-error';

                if (this.value.length > 0 && this.value.length !== 16) {
                    $(this).addClass('is-invalid');
                    if (!$('#' + errorId).length) {
                        $('<div id="' + errorId + '" class="invalid-feedback">' +
                            (fieldId === 'nik' ? 'NIK' : 'Nomor KK') +
                            ' harus terdiri dari 16 digit.</div>').insertAfter(this);
                    }
                } else {
                    $('#' + errorId).remove();
                    if (this.value.length === 16 || this.value.length === 0) {
                        $(this).removeClass('is-invalid');
                    }
                }

                // Cek NIK dan KK tidak boleh sama
                validateNikKk();
            });

            // Handle pendidikan_terakhir change to show/hide program studi
            function updateProgramStudiDropdown(pendidikanTerakhir, selectedProgram = null) {
                var programStudiGroup = $('#programstudi_group');
                var programStudiDropdown = $('#id_programstudi');

                // Selalu kosongkan dropdown sebelum menambahkan opsi baru
                programStudiDropdown.empty().append('<option value="">-- Pilih Prodi / Jurusan --</option>');

                console.log("Pendidikan Terakhir:", pendidikanTerakhir);
                console.log("Selected Program:", selectedProgram);

                if (pendidikanTerakhir && pendidikanTerakhir !== 'SD/MI' && pendidikanTerakhir !== 'SMP/MTS') {
                    programStudiGroup.show();
                    programStudiDropdown.attr('required', true);
                    programStudiGroup.find('label').html('Prodi / Jurusan <span class="text-danger">*</span>');

                    let educationType = (pendidikanTerakhir === 'SMA/SMK/MA') ? 'SMA' : 'non-SMA';
                    console.log("Education Type:", educationType);

                    // Panggil AJAX untuk mendapatkan daftar program studi
                    $.ajax({
                        url: '/get-program-studi',
                        type: 'GET',
                        data: {
                            education_type: educationType
                        },
                        success: function(data) {
                            console.log("Loaded Options:", data);

                            // Jika data kosong, tambahkan opsi default
                            if (data.length === 0) {
                                programStudiDropdown.append(
                                    '<option value="">Tidak ada program studi tersedia</option>');
                            } else {
                                // Tambahkan opsi program studi dari server
                                $.each(data, function(index, item) {
                                    let isSelected = selectedProgram && selectedProgram == item
                                        .id ? 'selected' : '';
                                    programStudiDropdown.append(
                                        `<option value="${item.id}" ${isSelected}>${item.name_programstudi}</option>`
                                    );
                                });

                                // Set nilai dari database HANYA jika ini adalah pemuatan awal (saat selectedProgram ada)
                                if (selectedProgram) {
                                    setTimeout(() => {
                                        programStudiDropdown.val(selectedProgram).trigger(
                                            'change');
                                    }, 300);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("AJAX Error:", status, error);
                        }
                    });
                } else {
                    programStudiGroup.hide();
                    programStudiDropdown.attr('required', false);
                    programStudiDropdown.val('').trigger('change');
                }
            }

            // Jalankan hanya sekali saat halaman dimuat untuk nilai awal
            $(document).ready(function() {
                let pendidikanTerakhir = $('#pendidikan_terakhir').val();
                let selectedProgram = $('#id_programstudi').attr('data-selected');

                console.log("Initial loading - Pendidikan:", pendidikanTerakhir);
                console.log("Initial loading - Program Studi:", selectedProgram);

                // Hanya panggil dengan selectedProgram saat pertama kali jika ada data
                if (pendidikanTerakhir) {
                    updateProgramStudiDropdown(pendidikanTerakhir, selectedProgram);
                }

                // Saat user mengubah pendidikan terakhir, panggil tanpa selectedProgram
                $('#pendidikan_terakhir').on('change', function() {
                    updateProgramStudiDropdown($(this).val()); // Tanpa parameter kedua
                });
            });

            // Initialize SmartWizard
            $('#smartwizard').smartWizard({
                selected: 0,
                theme: 'default',
                justified: true,
                darkMode: false,
                autoAdjustHeight: false,
                cycleSteps: false,
                backButtonSupport: true,
                enableURLhash: false,
                transition: {
                    animation: 'fade',
                    speed: '400',
                    easing: ''
                },
                toolbarSettings: {
                    toolbarPosition: 'bottom',
                    toolbarButtonPosition: 'right',
                    showNextButton: true,
                    showPreviousButton: true,
                    toolbarExtraButtons: [
                        $(
                            '<button type="button" id="btn-submit-form" class="btn btn-success btn-finish">Simpan</button>'
                        )
                    ]
                },
                anchorSettings: {
                    anchorClickable: true,
                    enableAllAnchors: false,
                    markDoneStep: true,
                    markAllPreviousStepsAsDone: true,
                    removeDoneStepOnNavigateBack: false,
                    enableAnchorOnDoneStep: true
                },
                keyboardSettings: {
                    keyNavigation: false
                },
                lang: {
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            });


            // Enhance step navigation with validation
            $('#smartwizard').on('leaveStep', function(e, anchorObject, currentStepIndex, nextStepIndex,
                stepDirection) {
                // If moving forward, validate the current step
                if (stepDirection === 'forward') {
                    // Get all form fields in the current step
                    var $currentStep = $('#step-' + (currentStepIndex + 1));
                    var $requiredFields = $currentStep.find('[required]');

                    // Check if all required fields are filled
                    var valid = true;
                    $requiredFields.each(function() {
                        if (!$(this).val()) {
                            $(this).addClass('is-invalid');
                            valid = false;
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });

                    // Special validation for select elements with select2
                    $currentStep.find('select[required]').each(function() {
                        if (!$(this).val()) {
                            // Add red border to select2 container
                            $(this).closest('.form-group').find('.select2-selection').addClass(
                                'border-danger');
                            valid = false;
                        } else {
                            $(this).closest('.form-group').find('.select2-selection').removeClass(
                                'border-danger');
                        }
                    });

                    // Special validations for step 3 (Data Pendidikan)
                    if (currentStepIndex === 2) {
                        if (!validateNikKk()) {
                            valid = false;
                        }

                        // Validate program studi if pendidikan is higher than SMP
                        var pendidikan = $('#pendidikan_terakhir').val();
                        if (pendidikan && pendidikan !== 'SD/MI' && pendidikan !== 'SMP/MTS') {
                            if (!$('#id_programstudi').val()) {
                                $('#id_programstudi').addClass('is-invalid');
                                $('#id_programstudi').closest('.form-group').find('.select2-selection')
                                    .addClass('border-danger');
                                valid = false;
                            }
                        }
                    }

                    // Special validations for step 2 (Data Pekerjaan)
                    if (currentStepIndex === 1) {
                        if (!validateDepartemenBagian()) {
                            valid = false;
                        }
                    }

                    if (!valid) {
                        // Display an error message for the step
                        if (!$currentStep.find('.step-validation-message').length) {
                            $currentStep.prepend(
                                '<div class="step-validation-message alert alert-danger">' +
                                '<i class="fas fa-exclamation-triangle"></i> Ada kolom wajib yang belum diisi atau data tidak valid!' +
                                '</div>'
                            );
                        }
                        return false;
                    } else {
                        // Remove error message if exists
                        $currentStep.find('.step-validation-message').remove();
                    }
                }
                return true;
            });

            // Handle form submission button click
            $('#btn-submit-form').on('click', function() {
                // Validate all form fields
                var form = document.getElementById('karyawanForm');

                // Add 'was-validated' class to use Bootstrap's validation styles
                form.classList.add('was-validated');

                // Check HTML5 validation
                if (!form.checkValidity()) {
                    // Find the first invalid field's step
                    var firstInvalidField = form.querySelector(':invalid');
                    if (firstInvalidField) {
                        // Find which step contains this field
                        for (var i = 1; i <= 4; i++) {
                            if ($('#step-' + i).find(firstInvalidField).length) {
                                // Switch to this step
                                $('#smartwizard').smartWizard("goToStep", i - 1);
                                break;
                            }
                        }
                    }

                    // Display validation message
                    alert('Ada kolom wajib yang belum diisi. Mohon lengkapi formulir.');
                    return false;
                }

                // Check custom validations
                if (!validateNikKk()) {
                    $('#smartwizard').smartWizard("goToStep", 2); // Go to step 3
                    alert('NIK dan Nomor KK tidak boleh sama!');
                    return false;
                }

                if (!validateDepartemenBagian()) {
                    $('#smartwizard').smartWizard("goToStep", 1); // Go to step 2
                    alert('Silakan pilih Bagian untuk Departemen yang dipilih.');
                    return false;
                }

                // Check if program studi is required and filled
                var pendidikan = $('#pendidikan_terakhir').val();
                if (pendidikan && pendidikan !== 'SD/MI' && pendidikan !== 'SMP/MTS' && !$(
                        '#id_programstudi').val()) {
                    $('#smartwizard').smartWizard("goToStep", 2); // Go to step 3
                    alert('Silakan pilih Prodi / Jurusan untuk pendidikan yang dipilih.');
                    return false;
                }

                // Check NIK and KK length
                var nikValue = $('#nik').val();
                var kkValue = $('#kk').val();

                if (nikValue && nikValue.length !== 16) {
                    $('#smartwizard').smartWizard("goToStep", 2); // Go to step 3
                    alert('NIK harus terdiri dari 16 digit.');
                    return false;
                }

                if (kkValue && kkValue.length !== 16) {
                    $('#smartwizard').smartWizard("goToStep", 2); // Go to step 3
                    alert('Nomor KK harus terdiri dari 16 digit.');
                    return false;
                }

                // If all validations pass, submit the form
                form.submit();
            });

            // Show server-side validation errors on the appropriate tab
            $(document).ready(function() {
                // If there are validation errors, find which step they belong to
                if ($('.alert-danger').length > 0) {
                    // Get all error fields
                    var errorFields = [];
                    $('.invalid-feedback').each(function() {
                        var prevField = $(this).prev();
                        if (prevField.length && prevField.attr('id')) {
                            errorFields.push(prevField.attr('id'));
                        }
                    });

                    // Find which step contains the first error
                    var errorStep = 0;
                    for (var i = 1; i <= 4; i++) {
                        var $step = $('#step-' + i);
                        for (var j = 0; j < errorFields.length; j++) {
                            if ($step.find('#' + errorFields[j]).length > 0) {
                                errorStep = i - 1;
                                break;
                            }
                        }
                        if (errorStep > 0) break;
                    }

                    // Go to the step with errors
                    if (errorStep > 0) {
                        $('#smartwizard').smartWizard("goToStep", errorStep);
                    }
                }
            });

            // Validasi nomor rekening hanya angka
            $('#nomor_rekening').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Validasi jumlah anggota KK hanya angka
            $('#jml_anggotakk').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Validasi nomor HP hanya angka dan maksimal 13 digit
            $('#no_hp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 13) {
                    this.value = this.value.substring(0, 13);
                }
            });

            // Setup resign modal
            $('.btn-resign').on('click', function() {
                var karyawanId = $(this).data('id');
                $('#resignForm').attr('action', '/admin/karyawans/' + karyawanId + '/resign');
                $('#resignModal').modal('show');
            });

            // Run departemen change on page load to set initial state
            if (selectedDepartemenId) {
                // Set departemen value first
                departemenSelect.val(selectedDepartemenId);
                // Update bagian dropdown with preserved selection
                updateBagianDropdown(selectedDepartemenId, true);
            } else {
                // If no departemen selected, just trigger change to hide bagian
                departemenSelect.trigger('change');
            }

            // Run pendidikan_terakhir change on page load to set initial state
            // This is handled in the document ready function above
        });
    </script>
@stop
