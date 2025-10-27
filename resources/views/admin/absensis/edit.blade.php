@extends('adminlte::page')

@section('title', 'Edit Absensi')

@section('content_header')
<h1>Edit Absensi</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Absensi</h3>
        <div class="card-tools">
            <a href="{{ route('absensis.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('absensis.update', $absensi) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="karyawan_id">Karyawan</label>
                        <select class="form-control select2 @error('karyawan_id') is-invalid @enderror" id="karyawan_id" name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $absensi->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
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
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $absensi->tanggal->format('Y-m-d')) }}" required>
                        @error('tanggal')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jadwalkerja_id">Jadwal Kerja</label>
                        <select class="form-control select2 @error('jadwalkerja_id') is-invalid @enderror" id="jadwalkerja_id" name="jadwalkerja_id" required>
                            <option value="">-- Pilih Jadwal Kerja --</option>
                            @foreach($jadwalKerjas as $jadwal)
                            <option value="{{ $jadwal->id }}" {{ old('jadwalkerja_id', $absensi->jadwalkerja_id) == $jadwal->id ? 'selected' : '' }}>
                                {{ $jadwal->nama_jadwal }} ({{ $jadwal->jam_masuk }} - {{ $jadwal->jam_pulang }})
                            </option>
                            @endforeach
                        </select>
                        @error('jadwalkerja_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status Kehadiran</label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach($statusOptions as $status)
                            <option value="{{ $status }}" {{ old('status', $absensi->status) == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jam_masuk">Jam Masuk</label>
                        <input type="time" class="form-control @error('jam_masuk') is-invalid @enderror" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '') }}">
                        @error('jam_masuk')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jenis_absensi_masuk">Jenis Absensi Masuk</label>
                        <select class="form-control @error('jenis_absensi_masuk') is-invalid @enderror" id="jenis_absensi_masuk" name="jenis_absensi_masuk" required>
                            @foreach($jenisAbsensiOptions as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_absensi_masuk', $absensi->jenis_absensi_masuk) == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                            @endforeach
                        </select>
                        @error('jenis_absensi_masuk')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group" id="mesin_absensi_masuk_container">
                        <label for="mesinabsensi_masuk_id">Mesin Absensi Masuk</label>
                        <select class="form-control select2 @error('mesinabsensi_masuk_id') is-invalid @enderror" id="mesinabsensi_masuk_id" name="mesinabsensi_masuk_id">
                            <option value="">-- Pilih Mesin Absensi --</option>
                            @foreach($mesinAbsensis as $mesin)
                            <option value="{{ $mesin->id }}" {{ old('mesinabsensi_masuk_id', $absensi->mesinabsensi_masuk_id) == $mesin->id ? 'selected' : '' }}>
                                {{ $mesin->nama }} ({{ $mesin->lokasi }})
                            </option>
                            @endforeach
                        </select>
                        @error('mesinabsensi_masuk_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="keterlambatan">Keterlambatan (menit)</label>
                        <input type="number" class="form-control @error('keterlambatan') is-invalid @enderror" id="keterlambatan" name="keterlambatan" value="{{ old('keterlambatan', $absensi->keterlambatan) }}" min="0">
                        @error('keterlambatan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jam_pulang">Jam Pulang</label>
                        <input type="time" class="form-control @error('jam_pulang') is-invalid @enderror" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang', $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '') }}">
                        @error('jam_pulang')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jenis_absensi_pulang">Jenis Absensi Pulang</label>
                        <select class="form-control @error('jenis_absensi_pulang') is-invalid @enderror" id="jenis_absensi_pulang" name="jenis_absensi_pulang" required>
                            @foreach($jenisAbsensiOptions as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_absensi_pulang', $absensi->jenis_absensi_pulang) == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                            @endforeach
                        </select>
                        @error('jenis_absensi_pulang')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group" id="mesin_absensi_pulang_container">
                        <label for="mesinabsensi_pulang_id">Mesin Absensi Pulang</label>
                        <select class="form-control select2 @error('mesinabsensi_pulang_id') is-invalid @enderror" id="mesinabsensi_pulang_id" name="mesinabsensi_pulang_id">
                            <option value="">-- Pilih Mesin Absensi --</option>
                            @foreach($mesinAbsensis as $mesin)
                            <option value="{{ $mesin->id }}" {{ old('mesinabsensi_pulang_id', $absensi->mesinabsensi_pulang_id) == $mesin->id ? 'selected' : '' }}>
                                {{ $mesin->nama }} ({{ $mesin->lokasi }})
                            </option>
                            @endforeach
                        </select>
                        @error('mesinabsensi_pulang_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pulang_awal">Pulang Awal (menit)</label>
                        <input type="number" class="form-control @error('pulang_awal') is-invalid @enderror" id="pulang_awal" name="pulang_awal" value="{{ old('pulang_awal', $absensi->pulang_awal) }}" min="0">
                        @error('pulang_awal')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jenis_absensi">Jenis Absensi</label>
                        <select class="form-control @error('jenis_absensi') is-invalid @enderror" id="jenis_absensi" name="jenis_absensi" required>
                            @foreach($jenisAbsensiOptions as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_absensi', $absensi->jenis_absensi) == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                            @endforeach
                        </select>
                        @error('jenis_absensi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group" id="mesin_absensi_container">
                        <label for="mesinabsensi_id">Mesin Absensi</label>
                        <select class="form-control select2 @error('mesinabsensi_id') is-invalid @enderror" id="mesinabsensi_id" name="mesinabsensi_id">
                            <option value="">-- Pilih Mesin Absensi --</option>
                            @foreach($mesinAbsensis as $mesin)
                            <option value="{{ $mesin->id }}" {{ old('mesinabsensi_id', $absensi->mesinabsensi_id) == $mesin->id ? 'selected' : '' }}>
                                {{ $mesin->nama }} ({{ $mesin->lokasi }})
                            </option>
                            @endforeach
                        </select>
                        @error('mesinabsensi_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jam_masuk">Jam Masuk</label>
                        <input type="time" class="form-control @error('jam_masuk') is-invalid @enderror" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '') }}">
                        @error('jam_masuk')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jam_pulang">Jam Pulang</label>
                        <input type="time" class="form-control @error('jam_pulang') is-invalid @enderror" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang', $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '') }}">
                        @error('jam_pulang')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="keterlambatan">Keterlambatan (menit)</label>
                        <input type="number" class="form-control @error('keterlambatan') is-invalid @enderror" id="keterlambatan" name="keterlambatan" value="{{ old('keterlambatan', $absensi->keterlambatan) }}" min="0">
                        @error('keterlambatan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pulang_awal">Pulang Awal (menit)</label>
                        <input type="number" class="form-control @error('pulang_awal') is-invalid @enderror" id="pulang_awal" name="pulang_awal" value="{{ old('pulang_awal', $absensi->pulang_awal) }}" min="0">
                        @error('pulang_awal')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $absensi->keterangan) }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <a href="{{ route('absensis.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
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

        // Toggle availability of fields based on status
        $('#status').change(function() {
            var status = $(this).val();
            if (status === 'Izin' || status === 'Sakit' || status === 'Cuti') {
                $('#jam_masuk').prop('disabled', true).val('');
                $('#jam_pulang').prop('disabled', true).val('');
                $('#keterlambatan').prop('disabled', true).val(0);
                $('#pulang_awal').prop('disabled', true).val(0);
                $('#jenis_absensi_masuk').prop('disabled', true).val('Manual');
                $('#jenis_absensi_pulang').prop('disabled', true).val('Manual');
                $('#mesinabsensi_masuk_id').prop('disabled', true).val('');
                $('#mesinabsensi_pulang_id').prop('disabled', true).val('');
                $('#keterangan').prop('required', true);
            } else {
                $('#jam_masuk').prop('disabled', false);
                $('#jam_pulang').prop('disabled', false);
                $('#keterlambatan').prop('disabled', false);
                $('#pulang_awal').prop('disabled', false);
                $('#jenis_absensi_masuk').prop('disabled', false);
                $('#jenis_absensi_pulang').prop('disabled', false);
                $('#keterangan').prop('required', false);
            }

            // Trigger change events to update the dependent fields
            $('#jenis_absensi_masuk').trigger('change');
            $('#jenis_absensi_pulang').trigger('change');
        }).trigger('change');

        // Toggle mesin absensi masuk field based on jenis absensi masuk
        $('#jenis_absensi_masuk').change(function() {
            var jenisAbsensi = $(this).val();
            if (jenisAbsensi === 'Mesin' && !$(this).prop('disabled')) {
                $('#mesin_absensi_masuk_container').show();
                $('#mesinabsensi_masuk_id').prop('required', true).prop('disabled', false);
            } else {
                $('#mesin_absensi_masuk_container').hide();
                $('#mesinabsensi_masuk_id').prop('required', false).prop('disabled', true).val('');
            }
        }).trigger('change');

        // Toggle mesin absensi pulang field based on jenis absensi pulang
        $('#jenis_absensi_pulang').change(function() {
            var jenisAbsensi = $(this).val();
            if (jenisAbsensi === 'Mesin' && !$(this).prop('disabled')) {
                $('#mesin_absensi_pulang_container').show();
                $('#mesinabsensi_pulang_id').prop('required', true).prop('disabled', false);
            } else {
                $('#mesin_absensi_pulang_container').hide();
                $('#mesinabsensi_pulang_id').prop('required', false).prop('disabled', true).val('');
            }
        }).trigger('change');

        // Toggle mesin absensi field based on jenis absensi
        $('#jenis_absensi').change(function() {
            var jenisAbsensi = $(this).val();
            if (jenisAbsensi === 'Mesin') {
                $('#mesin_absensi_container').show();
                $('#mesinabsensi_id').prop('required', true);
            } else {
                $('#mesin_absensi_container').hide();
                $('#mesinabsensi_id').prop('required', false).val('');
            }
        }).trigger('change');
    });

</script>
@stop
