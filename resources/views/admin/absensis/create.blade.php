@extends('adminlte::page')

@section('title', 'Tambah Absensi Baru')

@section('content_header')
<h1>Tambah Absensi Baru</h1>
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
        <form action="{{ route('absensis.store') }}" method="POST">
            @csrf

            <!-- Employee and Date Information Section -->
            <div class="mb-4 card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi Karyawan & Tanggal</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
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
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jadwalkerja_id">Jadwal Kerja</label>
                                <select class="form-control select2 @error('jadwalkerja_id') is-invalid @enderror" id="jadwalkerja_id" name="jadwalkerja_id" required>
                                    <option value="">-- Pilih Jadwal Kerja --</option>
                                    @foreach($jadwalKerjas as $jadwal)
                                    <option value="{{ $jadwal->id }}" {{ old('jadwalkerja_id') == $jadwal->id ? 'selected' : '' }}>
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Status Section -->
            <div class="mb-4 card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Status Kehadiran</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status Kehadiran</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    @foreach($statusOptions as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
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
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="1">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Check-in Information Section -->
            <div class="mb-4 card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Informasi Absensi Masuk</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="jam_masuk">Jam Masuk</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                    </div>
                                    <input type="time" class="form-control @error('jam_masuk') is-invalid @enderror" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', date('H:i')) }}">
                                    @error('jam_masuk')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="jenis_absensi_masuk">Jenis Absensi Masuk</label>
                                <select class="form-control @error('jenis_absensi_masuk') is-invalid @enderror" id="jenis_absensi_masuk" name="jenis_absensi_masuk" required>
                                    @foreach($jenisAbsensiOptions as $jenis)
                                    <option value="{{ $jenis }}" {{ old('jenis_absensi_masuk', 'Manual') == $jenis ? 'selected' : '' }}>
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="mesin_absensi_masuk_container">
                                <label for="mesinabsensi_masuk_id">Mesin Absensi Masuk</label>
                                <select class="form-control select2 @error('mesinabsensi_masuk_id') is-invalid @enderror" id="mesinabsensi_masuk_id" name="mesinabsensi_masuk_id">
                                    <option value="">-- Pilih Mesin Absensi --</option>
                                    @foreach($mesinAbsensis as $mesin)
                                    <option value="{{ $mesin->id }}" {{ old('mesinabsensi_masuk_id') == $mesin->id ? 'selected' : '' }}>
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="keterlambatan">Keterlambatan (menit)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('keterlambatan') is-invalid @enderror" id="keterlambatan" name="keterlambatan" value="{{ old('keterlambatan', 0) }}" min="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text">menit</span>
                                    </div>
                                    @error('keterlambatan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Check-out Information Section -->
            <div class="mb-4 card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Informasi Absensi Pulang</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="jam_pulang">Jam Pulang</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                    </div>
                                    <input type="time" class="form-control @error('jam_pulang') is-invalid @enderror" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang') }}">
                                    @error('jam_pulang')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="jenis_absensi_pulang">Jenis Absensi Pulang</label>
                                <select class="form-control @error('jenis_absensi_pulang') is-invalid @enderror" id="jenis_absensi_pulang" name="jenis_absensi_pulang" required>
                                    @foreach($jenisAbsensiOptions as $jenis)
                                    <option value="{{ $jenis }}" {{ old('jenis_absensi_pulang', 'Manual') == $jenis ? 'selected' : '' }}>
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="mesin_absensi_pulang_container">
                                <label for="mesinabsensi_pulang_id">Mesin Absensi Pulang</label>
                                <select class="form-control select2 @error('mesinabsensi_pulang_id') is-invalid @enderror" id="mesinabsensi_pulang_id" name="mesinabsensi_pulang_id">
                                    <option value="">-- Pilih Mesin Absensi --</option>
                                    @foreach($mesinAbsensis as $mesin)
                                    <option value="{{ $mesin->id }}" {{ old('mesinabsensi_pulang_id') == $mesin->id ? 'selected' : '' }}>
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="pulang_awal">Pulang Awal (menit)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('pulang_awal') is-invalid @enderror" id="pulang_awal" name="pulang_awal" value="{{ old('pulang_awal', 0) }}" min="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text">menit</span>
                                    </div>
                                    @error('pulang_awal')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="row">
                <div class="text-right col-12">
                    <a href="{{ route('absensis.index') }}" class="btn btn-secondary">
                        <i class="mr-1 fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="mr-1 fas fa-save"></i> Simpan Absensi
                    </button>
                </div>
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
                $('#jam_masuk').prop('disabled', false).val('{{ date("H:i") }}');
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

        // Calculate keterlambatan automatically when jadwal and jam masuk change
        $('#jadwalkerja_id, #jam_masuk').change(function() {
            if ($('#status').val() === 'Hadir') {
                calculateKeterlambatan();
            }
        });

        // Calculate pulang_awal automatically when jadwal and jam pulang change
        $('#jadwalkerja_id, #jam_pulang').change(function() {
            if ($('#status').val() === 'Hadir') {
                calculatePulangAwal();
            }
        });

        function calculateKeterlambatan() {
            var jadwalId = $('#jadwalkerja_id').val();
            var jamMasuk = $('#jam_masuk').val();

            if (jadwalId && jamMasuk) {
                // Get the selected jadwal option text which contains jam_masuk
                var jadwalText = $('#jadwalkerja_id option:selected').text();
                var matches = jadwalText.match(/\(([^)]+)\)/);

                if (matches && matches.length > 1) {
                    var times = matches[1].split('-');
                    var jadwalMasuk = times[0].trim();

                    // Convert times to minutes for comparison
                    var jadwalMinutes = convertTimeToMinutes(jadwalMasuk);
                    var masukMinutes = convertTimeToMinutes(jamMasuk);

                    // Calculate keterlambatan
                    if (masukMinutes > jadwalMinutes) {
                        $('#keterlambatan').val(masukMinutes - jadwalMinutes);
                    } else {
                        $('#keterlambatan').val(0);
                    }
                }
            }
        }

        function calculatePulangAwal() {
            var jadwalId = $('#jadwalkerja_id').val();
            var jamPulang = $('#jam_pulang').val();

            if (jadwalId && jamPulang) {
                // Get the selected jadwal option text which contains jam_pulang
                var jadwalText = $('#jadwalkerja_id option:selected').text();
                var matches = jadwalText.match(/\(([^)]+)\)/);

                if (matches && matches.length > 1) {
                    var times = matches[1].split('-');
                    var jadwalPulang = times[1].trim();

                    // Convert times to minutes for comparison
                    var jadwalMinutes = convertTimeToMinutes(jadwalPulang);
                    var pulangMinutes = convertTimeToMinutes(jamPulang);

                    // Calculate pulang_awal
                    if (pulangMinutes < jadwalMinutes) {
                        $('#pulang_awal').val(jadwalMinutes - pulangMinutes);
                    } else {
                        $('#pulang_awal').val(0);
                    }
                }
            }
        }

        function convertTimeToMinutes(timeString) {
            var parts = timeString.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        }
    });
</script>
@stop