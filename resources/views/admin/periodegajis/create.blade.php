@extends('adminlte::page')

@section('title', 'Add New Periode Gaji')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="mr-2 fas fa-calendar-plus text-primary"></i>Add New Periode Gaji</h1>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Periode Gaji</h3>
    </div>
    <form action="{{ route('periodegaji.store') }}" method="POST">
        @csrf
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-group">
                <label for="nama_periode">Nama Periode</label>
                <div class="input-group">
                    <input type="text" class="form-control @error('nama_periode') is-invalid @enderror" id="nama_periode" name="nama_periode" value="{{ old('nama_periode') }}" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" id="generateName">
                            <i class="fas fa-magic"></i> Generate
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted">Klik "Generate" untuk membuat nama periode otomatis dari tanggal</small>
                @error('nama_periode')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                        @error('tanggal_mulai')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+1 month'))) }}" required>
                        @error('tanggal_selesai')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div id="date-error" class="text-danger" style="display: none;">
                            Tanggal selesai harus setelah tanggal mulai
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', 'nonaktif') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <small class="form-text text-muted">Jika status "Aktif" dipilih, semua periode lain akan dinonaktifkan</small>
                @error('status')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save mr-1"></i> Submit
            </button>
            <a href="{{ route('periodegaji.index') }}" class="btn btn-default">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
        </div>
    </form>
</div>
@stop

@section('js')
<script>
    $(function() {
        // Function to format date in Indonesian format
        function formatDate(date) {
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            const d = new Date(date);
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        // Generate name button click handler
        $('#generateName').click(function() {
            const startDate = $('#tanggal_mulai').val();
            const endDate = $('#tanggal_selesai').val();

            if (startDate && endDate) {
                const formattedStartDate = formatDate(startDate);
                const formattedEndDate = formatDate(endDate);

                $('#nama_periode').val('Gaji Periode ' + formattedStartDate + ' - ' + formattedEndDate);
            } else {
                alert('Silakan pilih tanggal mulai dan tanggal selesai terlebih dahulu');
            }
        });

        // Validate end date is after start date
        function validateDates() {
            const startDate = new Date($('#tanggal_mulai').val());
            const endDate = new Date($('#tanggal_selesai').val());

            if (endDate < startDate) {
                $('#date-error').show();
                $('#submitBtn').prop('disabled', true);
                return false;
            } else {
                $('#date-error').hide();
                $('#submitBtn').prop('disabled', false);
                return true;
            }
        }

        // Attach event listeners for date validation
        $('#tanggal_mulai, #tanggal_selesai').change(validateDates);

        // Initial validation
        validateDates();

        // Form submission validation
        $('form').submit(function(e) {
            if (!validateDates()) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    });
</script>
@stop

@section('css')
<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e3e6f0;
    }

    .btn-outline-secondary:hover {
        background-color: #e9ecef;
    }
</style>
@stop
