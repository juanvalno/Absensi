@extends('adminlte::page')

@section('title', 'Add Shift')

@section('content_header')
<h1>Add Shift</h1>
@stop

@section('content')
<div class="row">
    <!-- Form Column (8) -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Shift Form</h3>
                <div class="card-tools">
                    <a href="{{ route('shifts.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
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

                <form action="{{ route('shifts.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label>Tipe Jam Kerja <span class="text-danger">*</span></label>
                        <div class="d-flex">
                            <div class="mr-4 custom-control custom-radio">
                                <input type="radio" id="tipe_shift" name="tipe_jam_kerja" class="custom-control-input" value="shift" checked>
                                <label class="custom-control-label" for="tipe_shift">Shift</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="tipe_normal" name="tipe_jam_kerja" class="custom-control-input" value="normal">
                                <label class="custom-control-label" for="tipe_normal">Jam Normal</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="kode_shift">Kode Shift <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="hidden" id="full_kode_shift" name="kode_shift" value="{{ old('kode_shift') }}">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="kode_prefix">SHIFT-</span>
                            </div>
                            <input type="text" class="form-control @error('kode_shift') is-invalid @enderror" id="kode_number" value="{{ old('kode_shift') ? preg_replace('/^(SHIFT-|KERJA-)/', '', old('kode_shift')) : '' }}" readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                    <i class="fas fa-sync-alt"></i> Generate
                                </button>
                            </div>
                        </div>
                        @error('kode_shift')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jenis_shift">Nama Jam Kerja<span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('jenis_shift') is-invalid @enderror" id="jenis_shift" name="jenis_shift" value="{{ old('jenis_shift') }}" required>
                        @error('jenis_shift')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_masuk">Jam Masuk <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('jam_masuk') is-invalid @enderror" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk') }}" required>
                                @error('jam_masuk')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_pulang">Jam Pulang <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('jam_pulang') is-invalid @enderror" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang') }}" required>
                                @error('jam_pulang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Instructions Column (4) -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Petunjuk Penggunaan</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Informasi Jam Kerja</h5>
                    <p>Jam kerja yang diinput harus memiliki durasi 8 jam.</p>
                </div>
                
                <h5>Langkah-langkah:</h5>
                <ol>
                    <li>Pilih tipe jam kerja (Shift atau Jam Normal)</li>
                    <li>Klik tombol "Generate" untuk mendapatkan kode unik</li>
                    <li>Masukkan nama jam kerja</li>
                    <li>Tentukan jam masuk dan jam pulang</li>
                    <li>Klik "Save" untuk menyimpan data</li>
                </ol>
                
                <div class="mt-3 alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                    <p>Kode shift harus unik dan akan otomatis dibuat dengan format:</p>
                    <ul>
                        <li><strong>SHIFT-XXX</strong> untuk tipe Shift</li>
                        <li><strong>KERJA-XXX</strong> untuk tipe Jam Normal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<!-- Add CSS if needed -->
@stop

@section('js')
<script>
    $(function() {
        // Generate code on page load
        generateShiftCode();
        
        // Generate code when button is clicked
        $('#generateCode').on('click', function() {
            generateShiftCode();
        });
        
        // Update prefix when radio button changes
        $('input[name="tipe_jam_kerja"]').on('change', function() {
            updatePrefix();
            generateShiftCode();
        });
        
        // Calculate and validate duration when time changes
        $('#jam_masuk, #jam_pulang').on('change', function() {
            validateDuration();
        });

        function updatePrefix() {
            var tipeJamKerja = $('input[name="tipe_jam_kerja"]:checked').val();
            if (tipeJamKerja === 'shift') {
                $('#kode_prefix').text('SHIFT-');
            } else {
                $('#kode_prefix').text('KERJA-');
            }
            updateFullCode();
        }
        
        function generateShiftCode() {
            var prefix = $('input[name="tipe_jam_kerja"]:checked').val() === 'shift' ? 'SHIFT-' : 'KERJA-';
            $('#kode_prefix').text(prefix);
            
            // Use a direct AJAX call to a dedicated endpoint for getting the next code
            $.ajax({
                url: '{{ route("shifts.getNextCode") }}',
                type: 'GET',
                data: { prefix: prefix },
                dataType: 'json',
                success: function(response) {
                    if (response && response.nextCode) {
                        $('#kode_number').val(response.nextCode);
                        updateFullCode();
                        console.log("Generated next code:", prefix + response.nextCode);
                    } else {
                        // Fallback
                        $('#kode_number').val('001');
                        updateFullCode();
                        console.log("Using default code:", prefix + '001');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching next code:", error);
                    // Fallback
                    $('#kode_number').val('001');
                    updateFullCode();
                }
            });
        }
        
        function updateFullCode() {
            var prefix = $('#kode_prefix').text();
            var number = $('#kode_number').val();
            $('#full_kode_shift').val(prefix + number);
        }

        function validateDuration() {
            // Existing validation code remains unchanged
            var start = $('#jam_masuk').val();
            var end = $('#jam_pulang').val();

            if (start && end) {
                // Convert to Date objects for comparison
                var startDate = new Date('2000-01-01T' + start + ':00');
                var endDate = new Date('2000-01-01T' + end + ':00');

                // If end time is before start time, assume it's the next day
                if (endDate < startDate) {
                    endDate.setDate(endDate.getDate() + 1);
                }

                // Calculate duration in minutes
                var durationMinutes = (endDate - startDate) / (1000 * 60);
                var hours = Math.floor(durationMinutes / 60);
                var minutes = durationMinutes % 60;

                // Format the duration
                var durationText = hours + ' jam ' + minutes + ' menit';
                
                // Check if duration is 8 hours
                if (hours !== 8 || minutes !== 0) {
                    alert('Perhatian: Durasi shift harus tepat 8 jam. Durasi saat ini: ' + durationText);
                    
                    // You can add visual indication
                    $('#jam_masuk, #jam_pulang').addClass('is-invalid');
                    
                    // Optionally, suggest a correction
                    if (confirm('Apakah Anda ingin menyesuaikan jam pulang untuk durasi 8 jam?')) {
                        // Calculate new end time (8 hours from start)
                        var newEndDate = new Date(startDate.getTime() + (8 * 60 * 60 * 1000));
                        var newHours = newEndDate.getHours().toString().padStart(2, '0');
                        var newMinutes = newEndDate.getMinutes().toString().padStart(2, '0');
                        $('#jam_pulang').val(newHours + ':' + newMinutes);
                        $('#jam_masuk, #jam_pulang').removeClass('is-invalid');
                    }
                } else {
                    // Duration is correct
                    $('#jam_masuk, #jam_pulang').removeClass('is-invalid');
                }
            }
        }
    });
</script>
@stop