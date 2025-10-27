@extends('adminlte::page')

@section('title', 'Edit Pengajuan Cuti')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit text-primary mr-2"></i> Edit Pengajuan Cuti</h1>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-alt mr-1"></i> Form Edit Pengajuan Cuti</h3>
            <div class="card-tools">
                <a href="{{ route('cuti_karyawans.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="icon fas fa-ban"></i> Ada kesalahan pada input!</h5>
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('cuti_karyawans.update', $cutiKaryawan->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user mr-1"></i> Informasi Karyawan</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_karyawan">
                                        <i class="fas fa-id-badge text-secondary mr-1"></i>
                                        Karyawan
                                    </label>
                                    <input type="hidden" name="id_karyawan" value="{{ $cutiKaryawan->id_karyawan }}">
                                    <input type="text" class="form-control" value="{{ $cutiKaryawan->karyawan->nik_karyawan }} - {{ $cutiKaryawan->karyawan->nama_karyawan }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="id_supervisor">
                                        <i class="fas fa-user-tie text-secondary mr-1"></i>
                                        Supervisor
                                    </label>
                                    <input type="hidden" name="id_supervisor" value="{{ $cutiKaryawan->id_supervisor }}">
                                    <input type="text" class="form-control" value="{{ $cutiKaryawan->supervisor->nik_karyawan }} - {{ $cutiKaryawan->supervisor->nama_karyawan }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Detail Cuti</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="jenis_cuti">
                                        <i class="fas fa-list-alt text-secondary mr-1"></i>
                                        Jenis Cuti <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('jenis_cuti') is-invalid @enderror" id="jenis_cuti"
                                        name="jenis_cuti" required>
                                        <option value="">-- Pilih Jenis Cuti --</option>
                                        @foreach ($jenisCuti as $jenis)
                                            <option value="{{ $jenis }}"
                                                {{ old('jenis_cuti', $cutiKaryawan->jenis_cuti) == $jenis ? 'selected' : '' }}>
                                                {{ $jenis }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jenis_cuti')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="master_cuti_id">
                                        <i class="fas fa-clipboard-list text-secondary mr-1"></i>
                                        Keterangan Cuti / Izin <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2 @error('master_cuti_id') is-invalid @enderror"
                                        id="master_cuti_id" name="master_cuti_id"
                                        data-placeholder="-- Pilih Keterangan Cuti / Izin --">
                                        <option value=""></option>
                                        @foreach ($masterCutis as $masterCuti)
                                            <option value="{{ $masterCuti->id }}"
                                                data-cuti-max="{{ $masterCuti->cuti_max }}"
                                                data-izin-max="{{ $masterCuti->izin_max }}"
                                                {{ old('master_cuti_id', $cutiKaryawan->master_cuti_id) == $masterCuti->id ? 'selected' : '' }}>
                                                {{ $masterCuti->uraian }} (Cuti Max: {{ $masterCuti->cuti_max }}, Izin
                                                Max:
                                                {{ $masterCuti->izin_max }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('master_cuti_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-day mr-1"></i> Periode Cuti</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_mulai_cuti">
                                                <i class="fas fa-calendar-check text-secondary mr-1"></i>
                                                Tanggal Mulai <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="date"
                                                    class="form-control @error('tanggal_mulai_cuti') is-invalid @enderror"
                                                    id="tanggal_mulai_cuti" name="tanggal_mulai_cuti"
                                                    value="{{ old('tanggal_mulai_cuti', $cutiKaryawan->tanggal_mulai_cuti ? date('Y-m-d', strtotime($cutiKaryawan->tanggal_mulai_cuti)) : '') }}"
                                                    required>
                                            </div>
                                            @error('tanggal_mulai_cuti')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_akhir_cuti">
                                                <i class="fas fa-calendar-times text-secondary mr-1"></i>
                                                Tanggal Akhir <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="date"
                                                    class="form-control @error('tanggal_akhir_cuti') is-invalid @enderror"
                                                    id="tanggal_akhir_cuti" name="tanggal_akhir_cuti"
                                                    value="{{ old('tanggal_akhir_cuti', $cutiKaryawan->tanggal_akhir_cuti ? date('Y-m-d', strtotime($cutiKaryawan->tanggal_akhir_cuti)) : '') }}"
                                                    required>
                                            </div>
                                            @error('tanggal_akhir_cuti')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-calculator text-secondary mr-1"></i>
                                        Jumlah Hari
                                    </label>
                                    <div class="callout callout-info py-2" id="jumlah-hari">
                                        <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i> <span
                                                id="jumlah-hari-text">{{ $cutiKaryawan->jumlah_hari_cuti }}</span> hari
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-paperclip mr-1"></i> Dokumen Pendukung</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="bukti">
                                        <i class="fas fa-file-upload text-secondary mr-1"></i>
                                        Bukti Pendukung
                                    </label>

                                    @if ($cutiKaryawan->bukti)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center border rounded p-3">
                                                @php
                                                    $extension = pathinfo($cutiKaryawan->bukti, PATHINFO_EXTENSION);
                                                    $isPdf = strtolower($extension) === 'pdf';
                                                @endphp

                                                @if ($isPdf)
                                                    <i class="fas fa-file-pdf fa-2x text-danger mr-3"></i>
                                                @else
                                                    <img src="{{ Storage::url('public/cuti/bukti/' . $cutiKaryawan->bukti) }}" 
                                                         alt="Preview" class="img-thumbnail mr-3" style="max-height: 100px;">
                                                @endif

                                                <div>
                                                    <h6 class="mb-1">{{ $cutiKaryawan->bukti }}</h6>
                                                    <div class="btn-group">
                                                        <a href="{{ Storage::url('public/cuti/bukti/' . $cutiKaryawan->bukti) }}"
                                                            target="_blank" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye mr-1"></i> Preview
                                                        </a>
                                                        <a href="{{ Storage::url('public/cuti/bukti/' . $cutiKaryawan->bukti) }}"
                                                            download class="btn btn-success btn-sm">
                                                            <i class="fas fa-download mr-1"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('bukti') is-invalid @enderror"
                                                id="bukti" name="bukti">
                                            <label class="custom-file-label"
                                                for="bukti">{{ $cutiKaryawan->bukti ? 'Pilih file baru' : 'Pilih file' }}</label>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, PDF. Max: 2MB. Biarkan
                                        kosong jika tidak ingin mengubah file.
                                    </small>
                                    @error('bukti')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                                    <p class="mb-0">Pastikan data yang diinput sudah benar sebelum menyimpan perubahan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('cuti_karyawans.index') }}" class="btn btn-secondary btn-lg ml-2 px-5">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }

        .callout {
            border-radius: 0.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12), 0 1px 2px rgba(0, 0, 0, .24);
            border-left: 5px solid #17a2b8;
        }

        .card-outline {
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .form-group label {
            font-weight: 500;
        }

        .text-danger {
            font-weight: bold;
        }

        .btn {
            border-radius: 4px;
        }

        .alert {
            border-radius: 4px;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize bs-custom-file-input
            bsCustomFileInput.init();

            // Object untuk menyimpan data master cuti
            const masterCutiData = {};
            @foreach ($masterCutis as $masterCuti)
                masterCutiData[{{ $masterCuti->id }}] = {
                    id: {{ $masterCuti->id }},
                    cuti_max: {{ $masterCuti->cuti_max ?? 0 }},
                    izin_max: {{ $masterCuti->izin_max ?? 0 }}
                };
            @endforeach

            // Event listeners for date and cuti type changes
            $('#jenis_cuti, #master_cuti_id, #tanggal_mulai_cuti').on('change', function() {
                calculateEndDate();
                calculateDays();
            });

            $('#tanggal_mulai_cuti').on('change', function() {
                var startDate = $(this).val();
                $('#tanggal_akhir_cuti').attr('min', startDate);
                calculateEndDate();
                calculateDays();
            });

            $('#tanggal_akhir_cuti').on('change', function() {
                var startDate = $('#tanggal_mulai_cuti').val();
                var endDate = $(this).val();
                var maxEndDate = $(this).attr('max');

                if (endDate < startDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Tanggal akhir tidak boleh kurang dari tanggal mulai',
                        confirmButtonColor: '#007bff'
                    });
                    $(this).val(startDate);
                } else if (maxEndDate && endDate > maxEndDate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Tanggal akhir tidak boleh melebihi batas maksimal: ' + maxEndDate,
                        confirmButtonColor: '#007bff'
                    });
                    $(this).val(maxEndDate);
                }
                calculateDays();
            });

            function calculateEndDate() {
                const masterCutiId = $('#master_cuti_id').val();
                const startDate = $('#tanggal_mulai_cuti').val();
                const jenisCuti = $('#jenis_cuti').val();
                const endDateInput = $('#tanggal_akhir_cuti');

                if (masterCutiId && startDate && jenisCuti) {
                    const masterCuti = masterCutiData[masterCutiId];
                    // Get max days based on jenis_cuti
                    const maxDays = jenisCuti === 'Cuti' ? masterCuti.cuti_max : masterCuti.izin_max;

                    if (maxDays) {
                        const start = new Date(startDate);
                        const maxEnd = new Date(start);
                        maxEnd.setDate(start.getDate() + (maxDays - 1)); // Subtract 1 because we count inclusively

                        // Format max date to YYYY-MM-DD
                        const maxEndFormatted = maxEnd.toISOString().split('T')[0];

                        // Set min and max attributes
                        endDateInput.attr('min', startDate);
                        endDateInput.attr('max', maxEndFormatted);

                        // Set the end date to max allowed if not set or if current is beyond max
                        const currentEndDate = endDateInput.val();
                        if (!currentEndDate || currentEndDate > maxEndFormatted) {
                            endDateInput.val(maxEndFormatted);
                        }

                        calculateDays();
                    }
                }
            }

            function calculateDays() {
                var startDate = $('#tanggal_mulai_cuti').val();
                var endDate = $('#tanggal_akhir_cuti').val();

                if (startDate && endDate) {
                    var start = new Date(startDate);
                    var end = new Date(endDate);

                    // Calculate difference in days (inclusive)
                    var timeDiff = Math.abs(end.getTime() - start.getTime());
                    var diffDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;

                    $('#jumlah-hari-text').text(diffDays);
                } else {
                    $('#jumlah-hari-text').text('-');
                }
            }

            // Handle id_karyawan change
            $('#id_karyawan').on('change', function() {
                var karyawanId = $(this).val();
                if (karyawanId) {
                    // Jika sudah ada master_cuti_id yang dipilih, simpan untuk nanti
                    const selectedMasterCutiId = $('#master_cuti_id').val();

                    // Tampilkan loading di dropdown master_cuti_id
                    $('#master_cuti_id').html('<option value="">Loading...</option>').trigger('change');

                    // Ambil data master cuti yang sesuai dengan karyawan
                    $.ajax({
                        url: '/get-karyawan-status/' + karyawanId,
                        type: 'GET',
                        success: function(response) {
                            var options =
                                '<option value="">-- Pilih Keterangan Cuti / Izin --</option>';
                            // Buat array master cuti
                            const masterCutis = [];
                            @foreach ($masterCutis as $cuti)
                                masterCutis.push({
                                    id: {{ $cuti->id }},
                                    uraian: "{{ $cuti->uraian }}",
                                    cuti_max: {{ $cuti->cuti_max ?? 0 }},
                                    izin_max: {{ $cuti->izin_max ?? 0 }},
                                    is_bulanan: {{ $cuti->is_bulanan ? 1 : 0 }}
                                });
                            @endforeach

                            masterCutis.forEach(function(cuti) {
                                var isCutiBulanan = cuti.is_bulanan ? 1 : 0;

                                if (response.is_bulanan === isCutiBulanan) {
                                    options += `<option value="${cuti.id}"
                                        data-cuti-max="${cuti.cuti_max}"
                                        data-izin-max="${cuti.izin_max}"
                                        ${selectedMasterCutiId == cuti.id ? 'selected' : ''}>
                                        ${cuti.uraian} (Cuti Max: ${cuti.cuti_max}, Izin Max: ${cuti.izin_max})
                                    </option>`;
                                }
                            });

                            $('#master_cuti_id').html(options).trigger('change.select2');

                            // Recalculate end date limit after loading options
                            calculateEndDate();
                        },
                        error: function(xhr, status, error) {
                            console.error('Ajax Error:', error);
                            $('#master_cuti_id').html(
                                '<option value="">-- Pilih Keterangan Cuti / Izin --</option>'
                            ).trigger('change');
                        }
                    });
                } else {
                    $('#master_cuti_id').html(
                        '<option value="">-- Pilih Keterangan Cuti / Izin --</option>').trigger(
                        'change.select2');
                }
            });

            // Initialize calculations and validations on page load
            calculateDays();

            // Trigger karyawan change to load master cuti options if not already loaded
            if ($('#id_karyawan').val()) {
                // Only trigger if there's no master_cuti options yet
                if ($('#master_cuti_id option').length <= 1) {
                    $('#id_karyawan').trigger('change');
                } else {
                    // Otherwise just calculate end date
                    calculateEndDate();
                }
            }
        });
    </script>
@stop
