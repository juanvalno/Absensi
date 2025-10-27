@extends('adminlte::page')

@section('title', 'Tambah Data Penggajian')

@section('content_header')
    <h1>Tambah Data Penggajian</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Form Filter Karyawan</h3>
                    <div class="card-tools">
                        <a href="{{ route('penggajian.index') }}" class="btn btn-sm btn-default">
                            <i class="mr-1 fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            {{ session('error') }}
                            @if (session('errors'))
                                <ul class="mt-2">
                                    @foreach (session('errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="periode_id">Periode Gaji <span class="text-danger">*</span></label>
                                    <select name="periode_id" id="periode_id"
                                        class="form-control select2 @error('periode_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Periode Gaji --</option>
                                        @foreach ($periodeGajis as $periode)
                                            <option value="{{ $periode->id }}">
                                                {{ $periode->nama_periode }} ({{ $periode->tanggal_mulai->format('d/m/Y') }}
                                                - {{ $periode->tanggal_selesai->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('periode_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departemen_id">Departemen (Opsional)</label>
                                    <select name="departemen_id" id="departemen_id"
                                        class="form-control select2 @error('departemen_id') is-invalid @enderror">
                                        <option value="">-- Semua Departemen --</option>
                                        @foreach ($departemens as $departemen)
                                            <option value="{{ $departemen->id }}">{{ $departemen->name_departemen }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departemen_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status Karyawan (Opsional)</label>
                                    <select name="status" id="status"
                                        class="form-control select2 @error('status') is-invalid @enderror">
                                        <option value="">-- Semua Status --</option>
                                        @foreach ($statusOptions as $status)
                                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 row">
                            <div class="text-center col-md-12">
                                <button type="button" id="btnFilter" class="btn btn-primary">
                                    <i class="mr-1 fas fa-filter"></i> Filter Karyawan
                                </button>
                                <button type="button" id="btnBatchProcess" class="ml-2 btn btn-success">
                                    <i class="mr-1 fas fa-tasks"></i> Proses Batch
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="karyawan-container" style="display: none;">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Karyawan</h3>
                        <div class="card-tools">
                            <span id="totalKaryawan" class="badge badge-primary">0 karyawan</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="createPenggajianForm" action="{{ route('penggajian.review') }}" method="POST">
                            @csrf
                            <input type="hidden" name="periode_id" id="form_periode_id">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="karyawanTable">
                                    <thead>
                                        <tr>
                                            <th width="5%">
                                                <div class="icheck-primary">
                                                    <input type="checkbox" id="checkAll">
                                                    <label for="checkAll"></label>
                                                </div>
                                            </th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Departemen</th>
                                            <th>Bagian</th>
                                            <th>Jabatan</th>
                                            <th>Status</th>
                                            <th>Gaji Pokok</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="karyawanList">
                                        <!-- Data karyawan akan dimuat disini secara dinamis -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 row">
                                <div class="text-center col-md-12">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="mr-1 fas fa-save"></i> Proses Penggajian
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Karyawan -->
    <div class="modal fade" id="karyawanDetailModal" tabindex="-1" role="dialog"
        aria-labelledby="karyawanDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="karyawanDetailModalLabel">Detail Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="text-center col-md-4">
                            <img id="modalFotoKaryawan" src="" alt="Foto Karyawan"
                                class="mb-3 rounded img-fluid" style="max-height: 200px;">
                        </div>
                        <div class="col-md-8">
                            <div class="employee-details">
                                <h4 id="modalNamaKaryawan" class="mb-3"></h4>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="20%">NIK</th>
                                        <td width="30%" id="modalNik">-</td>
                                        <th width="20%">Jabatan</th>
                                        <td width="30%" id="modalJabatan">-</td>
                                    </tr>
                                    <tr>
                                        <th>Departemen</th>
                                        <td id="modalDepartemen">-</td>
                                        <th>Profesi</th>
                                        <td id="modalProfesi">-</td>
                                    </tr>
                                    <tr>
                                        <th>Bagian</th>
                                        <td id="modalBagian">-</td>
                                        <th>Status</th>
                                        <td id="modalStatus">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Detail Gaji</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Gaji Pokok</th>
                                        <td id="modalGajiPokok"></td>
                                    </tr>
                                    <tr>
                                        <th>Tunjangan</th>
                                        <td>
                                            <ul id="modalTunjangan" class="list-unstyled"></ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Tunjangan</th>
                                        <td id="modalTotalTunjangan"></td>
                                    </tr>
                                    <tr>
                                        <th>Total Gaji</th>
                                        <td id="modalTotalGaji" class="font-weight-bold"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnProsesKaryawan">Proses Penggajian</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            let dataTable;
            let selectedKaryawanId = null;

            // Fungsi untuk memfilter karyawan
            $('#btnFilter').on('click', function() {
                const periodeId = $('#periode_id').val();
                const departemenId = $('#departemen_id').val();
                const status = $('#status').val();

                if (!periodeId) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Silakan pilih periode gaji terlebih dahulu',
                        icon: 'error'
                    });
                    return;
                }

                // Update hidden input form
                $('#form_periode_id').val(periodeId);

                // Show loading
                Swal.fire({
                    title: 'Loading...',
                    text: 'Sedang memuat data karyawan',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Get filtered karyawans via AJAX
                $.ajax({
                    url: "{{ route('penggajian.getFilteredKaryawans') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        periode_id: periodeId,
                        departemen_id: departemenId,
                        status: status
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.success) {
                            const karyawans = response.data;
                            // Filter out employees who have resigned (tahun_keluar is not null)
                            const activeKaryawans = karyawans.filter(k => !k.tahun_keluar);
                            $('#totalKaryawan').text(activeKaryawans.length + ' karyawan');

                            // Tampilkan container karyawan
                            $('#karyawan-container').show();

                            // Bersihkan tabel
                            if (dataTable) {
                                dataTable.destroy();
                            }

                            const tableBody = $('#karyawanList');
                            tableBody.empty();

                            if (activeKaryawans.length === 0) {
                                tableBody.append(`
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada karyawan yang belum diproses pada periode ini</td>
                                </tr>
                            `);
                            } else {
                                // Populate table with karyawan data
                                activeKaryawans.forEach(function(karyawan) {
                                    const karyawanId = karyawan.id;
                                    const gajiPokok = karyawan.jabatan ? karyawan
                                        .jabatan.gaji_pokok : 0;
                                    const formattedGajiPokok = formatRupiah(gajiPokok);

                                    tableBody.append(`
                                    <tr>
                                        <td class="text-center">
                                            <div class="icheck-primary">
                                                <input type="checkbox" class="karyawan-checkbox" id="karyawan_${karyawanId}" name="karyawan_ids[]" value="${karyawanId}">
                                                <label for="karyawan_${karyawanId}"></label>
                                            </div>
                                        </td>
                                        <td>${karyawan.nik || '-'}</td>
                                        <td>${karyawan.nama_karyawan || '-'}</td>
                                        <td>${karyawan.departemen ? karyawan.departemen.name_departemen : '-'}</td>
                                        <td>${karyawan.bagian ? karyawan.bagian.name_bagian : '-'}</td>
                                        <td>${karyawan.jabatan ? karyawan.jabatan.name_jabatan : '-'}</td>
                                        <td>
                                            <span class="badge badge-${getStatusBadgeClass(karyawan.statuskaryawan)}">
                                                ${karyawan.statuskaryawan || '-'}
                                            </span>
                                        </td>
                                        <td>${formattedGajiPokok}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-info btn-view-detail" data-id="${karyawanId}" data-karyawan='${JSON.stringify(karyawan)}'>
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                `);
                                });

                                // Initialize DataTable
                                dataTable = $('#karyawanTable').DataTable({
                                    responsive: true,
                                    autoWidth: false,
                                    language: {
                                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                                        zeroRecords: "Tidak ada data yang ditemukan",
                                        info: "Menampilkan _START_ s.d _END_ dari _TOTAL_ data",
                                        infoEmpty: "Tidak ada data yang tersedia",
                                        infoFiltered: "(difilter dari _MAX_ total data)",
                                        search: "Cari:",
                                        paginate: {
                                            first: "Pertama",
                                            last: "Terakhir",
                                            next: "Selanjutnya",
                                            previous: "Sebelumnya"
                                        }
                                    }
                                });
                            }
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Gagal memuat data karyawan',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        console.error(xhr);

                        let errorMessage = 'Terjadi kesalahan saat memuat data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            });

            // Helper function to get badge class based on status
            function getStatusBadgeClass(status) {
                switch (status) {
                    case 'aktif':
                        return 'success';
                    case 'nonaktif':
                        return 'danger';
                    case 'cuti':
                        return 'warning';
                    default:
                        return 'secondary';
                }
            }

            // Format currency to Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Fungsi untuk menampilkan modal detail karyawan
            $(document).on('click', '.btn-view-detail', function() {
                const karyawanId = $(this).data('id');
                const karyawan = $(this).data('karyawan');
                selectedKaryawanId = karyawanId;

                // Foto karyawan
                const fotoUrl = karyawan.foto_karyawan ?
                    `{{ asset('storage/karyawan/foto') }}/${karyawan.foto_karyawan}` :
                    '{{ asset('images/default-avatar.png') }}';

                // Detail karyawan
                $('#modalFotoKaryawan').attr('src', fotoUrl);
                $('#modalNamaKaryawan').text(karyawan.nama_karyawan || '-');
                $('#modalNik').text(karyawan.nik || '-');
                $('#modalDepartemen').text(karyawan.departemen ? karyawan.departemen.name_departemen : '-');
                $('#modalBagian').text(karyawan.bagian ? karyawan.bagian.name_bagian : '-');
                $('#modalJabatan').text(karyawan.jabatan ? karyawan.jabatan.name_jabatan : '-');
                $('#modalProfesi').text(karyawan.profesi ? karyawan.profesi.name_profesi : '-');
                $('#modalStatus').html(
                    `<span class="badge badge-${getStatusBadgeClass(karyawan.statuskaryawan)}">${karyawan.statuskaryawan || '-'}</span>`
                );

                // Detail gaji
                const gajiPokok = karyawan.jabatan ? karyawan.jabatan.gaji_pokok : 0;
                $('#modalGajiPokok').text(formatRupiah(gajiPokok));

                // Tunjangan
                let totalTunjangan = 0;
                const tunjanganList = $('#modalTunjangan');
                tunjanganList.empty();

                // Tunjangan jabatan
                if (karyawan.jabatan && karyawan.jabatan.tunjangan_jabatan > 0) {
                    tunjanganList.append(
                        `<li>Tunjangan Jabatan: ${formatRupiah(karyawan.jabatan.tunjangan_jabatan)}</li>`
                    );
                    totalTunjangan += karyawan.jabatan.tunjangan_jabatan;
                }

                // Tunjangan profesi
                if (karyawan.profesi && karyawan.profesi.tunjangan_profesi > 0) {
                    tunjanganList.append(
                        `<li>Tunjangan Profesi: ${formatRupiah(karyawan.profesi.tunjangan_profesi)}</li>`
                    );
                    totalTunjangan += karyawan.profesi.tunjangan_profesi;
                }

                // Jika tidak ada tunjangan
                if (totalTunjangan === 0) {
                    tunjanganList.append('<li>Tidak ada tunjangan</li>');
                }

                $('#modalTotalTunjangan').text(formatRupiah(totalTunjangan));
                $('#modalTotalGaji').text(formatRupiah(gajiPokok + totalTunjangan));

                // Tampilkan modal
                $('#karyawanDetailModal').modal('show');
            });

            // Proses penggajian untuk karyawan yang dipilih di modal
            $('#btnProsesKaryawan').on('click', function() {
                if (selectedKaryawanId) {
                    // Buat hidden input untuk karyawan_id (bukan karyawan_ids[])
                    if ($('#karyawan_id').length) {
                        $('#karyawan_id').val(selectedKaryawanId);
                    } else {
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'karyawan_id',
                            name: 'karyawan_id',
                            value: selectedKaryawanId
                        }).appendTo('#createPenggajianForm');
                    }

                    // Submit form
                    $('#createPenggajianForm').submit();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Tidak ada karyawan yang dipilih',
                        icon: 'error'
                    });
                }
            });

            // Fungsi untuk proses batch
            $('#btnBatchProcess').on('click', function() {
                const periodeId = $('#periode_id').val();
                const departemenId = $('#departemen_id').val();
                const status = $('#status').val();

                if (!periodeId) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Silakan pilih periode gaji terlebih dahulu',
                        icon: 'error'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin memproses penggajian secara batch? Semua karyawan yang sesuai filter akan diproses penggajiannya.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Proses',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to batch process route
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('penggajian.batchProcess') }}";

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = "{{ csrf_token() }}";
                        form.appendChild(csrfToken);

                        const periodeInput = document.createElement('input');
                        periodeInput.type = 'hidden';
                        periodeInput.name = 'periode_id';
                        periodeInput.value = periodeId;
                        form.appendChild(periodeInput);

                        if (departemenId) {
                            const departemenInput = document.createElement('input');
                            departemenInput.type = 'hidden';
                            departemenInput.name = 'departemen_id';
                            departemenInput.value = departemenId;
                            form.appendChild(departemenInput);
                        }

                        if (status) {
                            const statusInput = document.createElement('input');
                            statusInput.type = 'hidden';
                            statusInput.name = 'status';
                            statusInput.value = status;
                            form.appendChild(statusInput);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            // Fungsi check all checkbox
            $('#checkAll').on('click', function() {
                $('.karyawan-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Validasi sebelum submit dan arahkan ke review
            $('#createPenggajianForm').on('submit', function(e) {
                const checkedKaryawans = $('.karyawan-checkbox:checked').length;

                if (checkedKaryawans === 0) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Silakan pilih minimal satu karyawan untuk diproses',
                        icon: 'error'
                    });
                    return false;
                }

                // Ubah action ke review jika belum
                if ($(this).attr('action') !== "{{ route('penggajian.review') }}") {
                    $(this).attr('action', "{{ route('penggajian.review') }}");
                }

                return true;
            });
        });
    </script>
@stop
