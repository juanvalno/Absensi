@extends('adminlte::page')

@section('title', 'Cetak Slip Gaji Massal')

@section('content_header')
    <h1><i class="fas fa-print mr-2 text-primary"></i>Cetak Slip Gaji Massal</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Periode: {{ $activePeriod->nama_periode }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('penggajian.process-mass-payslip') }}" method="POST">
                @csrf
                <input type="hidden" name="periode_id" value="{{ $activePeriod->id }}">

                <!-- Add Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="department">Departemen</label>
                            <select class="form-control" id="department">
                                <option value="">Semua Departemen</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->name_departemen }}">{{ $dept->name_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status Karyawan</label>
                            <select class="form-control" id="status">
                                <option value="">Semua Status</option>
                                <option value="Bulanan">Bulanan</option>
                                <option value="Harian">Harian</option>
                                <option value="Borongan">Borongan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Cari Karyawan</label>
                            <input type="text" class="form-control" id="search" placeholder="Nama karyawan...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="payslipTable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="checkAll">
                                        <label class="custom-control-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th>Karyawan</th>
                                <th>Departemen</th>
                                <th>Status</th>
                                <th>Gaji Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penggajians as $penggajian)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input payslip-checkbox"
                                                id="check{{ $penggajian->id }}" name="penggajian_ids[]"
                                                value="{{ $penggajian->id }}">
                                            <label class="custom-control-label" for="check{{ $penggajian->id }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $penggajian->karyawan->nama_karyawan }}</td>
                                    <td>{{ $penggajian->karyawan->departemen['name_departemen'] ?? '-' }}</td>
                                    <td>{{ $penggajian->karyawan->statuskaryawan }}</td>
                                    <td>{{ $penggajian->formatCurrency($penggajian->gaji_bersih) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="printSelected" disabled>
                        <i class="fas fa-print mr-1"></i> Cetak Slip Gaji Terpilih
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function() {
            let table = $('#payslipTable').DataTable({
                pageLength: 25,
                dom: 'rt<"bottom"p>',
                language: {
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    }
                }
            });

            // Handle "Check All" functionality
            $('#checkAll').change(function() {
                $('.payslip-checkbox').prop('checked', $(this).prop('checked'));
                updatePrintButton();
            });

            // Handle individual checkbox changes
            $('.payslip-checkbox').change(function() {
                updatePrintButton();
            });

            // Filter functionality
            $('#department, #status, #search').on('change keyup', function() {
                const department = $('#department').val().toLowerCase();
                const status = $('#status').val().toLowerCase();
                const search = $('#search').val().toLowerCase();

                table.rows().every(function() {
                    const row = this.node();
                    const rowData = {
                        name: $(row).find('td:eq(1)').text().toLowerCase(),
                        dept: $(row).find('td:eq(2)').text().toLowerCase(),
                        status: $(row).find('td:eq(3)').text().toLowerCase()
                    };

                    const matchesDepartment = !department || rowData.dept.includes(department);
                    const matchesStatus = !status || rowData.status === status;
                    const matchesSearch = !search || rowData.name.includes(search);

                    if (matchesDepartment && matchesStatus && matchesSearch) {
                        $(row).show();
                    } else {
                        $(row).hide();
                    }
                });
            });

            // Update print button state
            function updatePrintButton() {
                const checkedCount = $('.payslip-checkbox:checked').length;
                $('#printSelected').prop('disabled', checkedCount === 0);
            }
        });
    </script>
@stop
