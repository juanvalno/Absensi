@extends('adminlte::page')

@section('title', 'Daftar Kuota Cuti Tahunan')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-calendar-check text-primary"></i> Daftar Kuota Cuti Tahunan
            </h1>
        </div>
        <div class="col-sm-6">
            <div class="float-sm-right">
                <a href="{{ route('kuota-cuti.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Tambah Kuota
                </a>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#generateMassalModal">
                    <i class="fas fa-users mr-1"></i> Generate Massal
                </button>
                <a href="{{ route('kuota-cuti.report') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar mr-1"></i> Laporan Kuota
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-1"></i> Data Kuota Cuti
            </h3>

        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control" id="year-filter">
                        <option value="">Semua Tahun</option>
                        @foreach ($years as $year => $items)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table id="kuota-cuti-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Karyawan</th>
                        <th>Tahun</th>
                        <th>Kuota Awal</th>
                        <th>Kuota Digunakan</th>
                        <th>Kuota Sisa</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kuotaCuti as $index => $kuota)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $kuota->nama_karyawan }}</td>
                            <td>{{ $kuota->tahun }}</td>
                            <td>{{ $kuota->kuota_awal }} hari</td>
                            <td>{{ $kuota->kuota_digunakan }} hari</td>
                            <td>
                                <span
                                    class="badge badge-{{ $kuota->kuota_sisa > 3 ? 'success' : ($kuota->kuota_sisa > 0 ? 'warning' : 'danger') }}">
                                    {{ $kuota->kuota_sisa }} hari
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('kuota-cuti.edit', $kuota->id) }}" class="btn btn-warning btn-sm"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('kuota-cuti.destroy', $kuota->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Generate Massal Modal -->
    <div class="modal fade" id="generateMassalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-users mr-1"></i> Generate Kuota Cuti Massal
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('kuota-cuti.generate-massal') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tahun">Tahun</label>
                                    <input type="number" class="form-control" id="tahun" name="tahun"
                                        value="{{ date('Y') }}" required min="2000" max="2099">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kuota_default">Kuota Default (Hari)</label>
                                    <input type="number" class="form-control" id="kuota_default" name="kuota_default"
                                        value="12" required min="0" max="365">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Pilih Karyawan</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="all" name="selection_type" value="all" class="custom-control-input" checked>
                                <label class="custom-control-label" for="all">Semua Karyawan Aktif</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="department" name="selection_type" value="department" class="custom-control-input">
                                <label class="custom-control-label" for="department">Per Departemen</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="selected" name="selection_type" value="selected" class="custom-control-input">
                                <label class="custom-control-label" for="selected">Pilih Karyawan</label>
                            </div>
                        </div>

                        <div id="departmentSection" class="form-group" style="display: none;">
                            <label for="department_id">Departemen</label>
                            <select class="form-control" name="department_id" id="department_id">
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name_departemen }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="karyawanSection" class="form-group" style="display: none;">
                            <label>Daftar Karyawan</label>
                            <div class="mb-2">
                                <button type="button" class="btn btn-sm btn-secondary" id="selectAll">Pilih Semua</button>
                                <button type="button" class="btn btn-sm btn-secondary" id="deselectAll">Hapus Semua</button>
                            </div>
                            <select class="form-control" name="karyawan_ids[]" id="karyawan_ids" multiple size="8">
                                @foreach($allKaryawan as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_karyawan }} - {{ $k->departemen->name_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i> Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        $(function() {
            $('#checkAll').change(function() {
                $('.karyawan-checkbox').prop('checked', $(this).prop('checked'));
            });

            $('#selectAll').click(function() {
                $('.karyawan-checkbox').prop('checked', true);
                $('#checkAll').prop('checked', true);
            });

            $('#deselectAll').click(function() {
                $('.karyawan-checkbox').prop('checked', false);
                $('#checkAll').prop('checked', false);
            });

            $('input[name="selection_type"]').change(function() {
                $('#departmentSection').hide();
                $('#karyawanSection').hide();

                if (this.value === 'department') {
                    $('#departmentSection').show();
                } else if (this.value === 'selected') {
                    $('#karyawanSection').show();
                }
            });
        });
    </script>
    @endpush

    <!-- Keep the existing Generate Massal Modal -->
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            var table = $('#kuota-cuti-table').DataTable({
                "processing": true,
                "responsive": true,
                "autoWidth": false,
                "ordering": true,
                "info": true,
                "pageLength": 10,
                "language": {
                    "url": "{{ asset('vendor/datatables/js/indonesia.json') }}"
                },
                "order": [[2, 'desc'], [1, 'asc']], // Order by year desc, then by name asc
                "columnDefs": [
                    { "orderable": false, "targets": [0, 6] } // Disable sorting for No and Actions columns
                ]
            });

            $('#year-filter').on('change', function() {
                var year = $(this).val();
                table.column(2).search(year).draw();
            });
        });
    </script>
@stop
