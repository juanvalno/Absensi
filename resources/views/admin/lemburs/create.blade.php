@extends('adminlte::page')

@section('title', 'Ajukan Lembur')

@section('content_header')
<h1>Ajukan Lembur</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Pengajuan Lembur</h3>
        <div class="card-tools">
            <a href="{{ route('lemburs.index') }}" class="btn btn-default btn-sm">
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

        <form action="{{ route('lemburs.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="karyawan_id">Karyawan <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('karyawan_id') is-invalid @enderror" id="karyawan_id" name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                {{ $karyawan->nik_karyawan }} - {{ $karyawan->nama_karyawan }}
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
                        <label for="jenis_lembur">Jenis Lembur <span class="text-danger">*</span></label>
                        <select class="form-control @error('jenis_lembur') is-invalid @enderror" id="jenis_lembur" name="jenis_lembur" required>
                            <option value="">-- Pilih Jenis Lembur --</option>
                            @foreach($jenisLembur as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_lembur') == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                            @endforeach
                        </select>
                        @error('jenis_lembur')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_lembur">Tanggal Lembur <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_lembur') is-invalid @enderror" id="tanggal_lembur" name="tanggal_lembur" value="{{ old('tanggal_lembur') }}" required>
                        @error('tanggal_lembur')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                        @error('jam_mulai')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jam_selesai">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                        @error('jam_selesai')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Durasi Lembur</label>
                        <div class="alert alert-info" id="durasi-lembur">
                            <span id="durasi-text">-</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="keterangan">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3" required>{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supervisor_id">Supervisor <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('supervisor_id') is-invalid @enderror" id="supervisor_id" name="supervisor_id" required>
                            <option value="">-- Pilih Supervisor --</option>
                            @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ old('supervisor_id') == $karyawan->id ? 'selected' : '' }}>
                                {{ $karyawan->nik_karyawan }} - {{ $karyawan->nama_karyawan }}
                            </option>
                            @endforeach
                        </select>
                        @error('supervisor_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Ajukan Lembur
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </button>
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
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4'
            , width: '100%'
        });

        // Calculate duration when times change
        $('#jam_mulai, #jam_selesai').on('change', function() {
            calculateDuration();
        });

        function calculateDuration() {
            var startTime = $('#jam_mulai').val();
            var endTime = $('#jam_selesai').val();

            if (startTime && endTime) {
                // Create Date objects for calculation
                var start = new Date('2000-01-01T' + startTime + ':00');
                var end = new Date('2000-01-01T' + endTime + ':00');

                // If end time is before start time, assume it's the next day
                if (end < start) {
                    end.setDate(end.getDate() + 1);
                }

                // Calculate difference in milliseconds
                var diff = end - start;

                // Convert to hours and minutes
                var hours = Math.floor(diff / (1000 * 60 * 60));
                var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                // Update the display
                $('#durasi-text').text(hours + ' jam ' + minutes + ' menit');
            } else {
                $('#durasi-text').text('-');
            }
        }
    });

</script>
@stop
