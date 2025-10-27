@extends('adminlte::page')

@section('title', 'Ajukan Cuti')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-alt text-primary mr-2"></i> Ajukan Cuti</h1>

    </div>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-alt mr-1"></i> Form Pengajuan Cuti</h3>
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

            <form action="{{ route('cuti_karyawans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user mr-1"></i> Informasi Karyawan</h3>
                            </div>
                            <div class="card-body">
                                @if (!$userDepartemen)
                                    <div class="form-group">
                                        <label for="id_departemen">
                                            <i class="fas fa-building text-secondary mr-1"></i>
                                            Departemen <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control select2 @error('id_departemen') is-invalid @enderror"
                                            id="id_departemen" name="id_departemen"
                                            data-placeholder="-- Pilih Departemen --">
                                            <option value=""></option>
                                            @foreach ($departemens as $departemen)
                                                <option value="{{ $departemen->id }}">
                                                    {{ $departemen->name_departemen }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="id_karyawan">
                                        <i class="fas fa-id-badge text-secondary mr-1"></i>
                                        Karyawan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2 @error('id_karyawan') is-invalid @enderror"
                                        id="id_karyawan" name="id_karyawan" required
                                        data-placeholder="-- Pilih Karyawan --">
                                        <option value=""></option>
                                        @if ($userDepartemen)
                                            @foreach ($karyawans as $karyawan)
                                                <option value="{{ $karyawan->id }}">
                                                    {{ $karyawan->nik_karyawan }} - {{ $karyawan->nama_karyawan }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('id_karyawan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="id_supervisor">
                                        <i class="fas fa-user-tie text-secondary mr-1"></i>
                                        Supervisor <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2 @error('id_supervisor') is-invalid @enderror"
                                        id="id_supervisor" name="id_supervisor" required
                                        {{ !auth()->user()->hasRole('admin') ? 'readonly' : '' }}
                                        data-placeholder="-- Pilih Supervisor --">
                                        <option value=""></option>
                                        @if (auth()->user()->hasRole('admin'))
                                            @foreach ($karyawans as $karyawan)
                                                <option value="{{ $karyawan->id }}"
                                                    {{ old('id_supervisor') == $karyawan->id ? 'selected' : '' }}>
                                                    {{ $karyawan->nik_karyawan }} - {{ $karyawan->nama_karyawan }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="{{ auth()->user()->karyawan->id }}" selected>
                                                {{ auth()->user()->karyawan->nik_karyawan }} -
                                                {{ auth()->user()->karyawan->nama_karyawan }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('id_supervisor')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
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
                                                {{ old('jenis_cuti') == $jenis ? 'selected' : '' }}>
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
                                                    value="{{ old('tanggal_mulai_cuti') }}" required>
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
                                                    value="{{ old('tanggal_akhir_cuti') }}" required>
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
                                                id="jumlah-hari-text">-</span> hari</h5>
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
                                        Bukti Pendukung <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('bukti') is-invalid @enderror"
                                                id="bukti" name="bukti" required>
                                            <label class="custom-file-label" for="bukti">Pilih file</label>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i> Format: JPG, PNG, PDF. Max: 2MB.
                                    </small>
                                    @error('bukti')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>
                                    <p class="mb-0">Pastikan data yang diinput sudah benar sebelum mengajukan cuti.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-paper-plane mr-2"></i> Ajukan Cuti
                    </button>
                    <button type="reset" class="btn btn-secondary btn-lg ml-2 px-5">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </button>
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
    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize bs-custom-file-input
            bsCustomFileInput.init();

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
                var masterCutiId = $('#master_cuti_id').val();
                var startDate = $('#tanggal_mulai_cuti').val();
                var jenisCuti = $('#jenis_cuti').val();

                if (!masterCutiId || !startDate || !jenisCuti) {
                    $('#tanggal_akhir_cuti').val('').removeAttr('max');
                    return;
                }

                var selectedOption = $('#master_cuti_id option:selected');
                var maxDays = jenisCuti === 'Cuti' ?
                    parseInt(selectedOption.data('cuti-max')) :
                    parseInt(selectedOption.data('izin-max'));

                if (!maxDays) {
                    $('#tanggal_akhir_cuti').val('').removeAttr('max');
                    return;
                }

                var endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + (maxDays - 1));

                var formattedEndDate = endDate.toISOString().split('T')[0];
                $('#tanggal_akhir_cuti').val(formattedEndDate).attr('max', formattedEndDate);
                calculateDays();
            }

            function calculateDays() {
                var startDate = $('#tanggal_mulai_cuti').val();
                var endDate = $('#tanggal_akhir_cuti').val();

                if (startDate && endDate) {
                    var start = new Date(startDate);
                    var end = new Date(endDate);
                    var diffTime = Math.abs(end - start);
                    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    $('#jumlah-hari-text').text(diffDays);
                } else {
                    $('#jumlah-hari-text').text('-');
                }
            }

            $('#id_departemen').on('change', function() {
                var departemenId = $(this).val();
                // Update both karyawan and supervisor dropdowns
                $('#id_karyawan, #id_supervisor').html('<option value="">Loading...</option>').trigger(
                    'change');

                if (departemenId) {
                    // Get karyawan list
                    $.ajax({
                        url: '/admin/get-karyawan-by-departemen/' + departemenId,
                        type: 'GET',
                        success: function(response) {
                            var options = '<option value="">-- Pilih Karyawan --</option>';
                            response.data.forEach(function(karyawan) {
                                options += `<option value="${karyawan.id}">
                                    ${karyawan.nik_karyawan} - ${karyawan.nama_karyawan}
                                </option>`;
                            });
                            $('#id_karyawan').html(options).trigger('change.select2');
                        }
                    });

                    // Get supervisors list
                    $.ajax({
                        url: '/admin/get-supervisors-by-departemen/' + departemenId,
                        type: 'GET',
                        success: function(response) {
                            var options = '<option value="">-- Pilih Supervisor --</option>';
                            response.data.forEach(function(supervisor) {
                                options += `<option value="${supervisor.id}">
                                    ${supervisor.nik_karyawan} - ${supervisor.nama_karyawan}
                                </option>`;
                            });
                            $('#id_supervisor').html(options).trigger('change.select2');
                        }
                    });
                } else {
                    $('#id_karyawan, #id_supervisor').html('<option value="">-- Pilih --</option>').trigger(
                        'change.select2');
                }
            });

            $('#id_karyawan').on('change', function() {
                var karyawanId = $(this).val();
                if (karyawanId) {
                    $('#master_cuti_id').html('<option value="">Loading...</option>').trigger('change');

                    $.ajax({
                        url: '/get-karyawan-status/' + karyawanId,
                        type: 'GET',
                        success: function(response) {
                            var options =
                                '<option value="">-- Pilih Keterangan Cuti / Izin --</option>';
                            @json($masterCutis).forEach(function(cuti) {
                                var isCutiBulanan = cuti.is_bulanan ? 1 : 0;

                                if (response.is_bulanan === isCutiBulanan) {
                                    options += `<option value="${cuti.id}" data-cuti-max="${cuti.cuti_max}" data-izin-max="${cuti.izin_max}">
                                        ${cuti.uraian} (Cuti Max: ${cuti.cuti_max}, Izin Max: ${cuti.izin_max})
                                    </option>`;
                                }
                            });

                            $('#master_cuti_id').html(options).trigger('change.select2');
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
        });
    </script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop
