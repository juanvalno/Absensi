@extends('adminlte::page')

@section('title', 'Data Cuti Karyawan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="mr-2 fas fa-calendar-alt text-primary"></i>Pengajuan Cuti Karyawan</h1>
        @can_show('cuti_karyawan.create')
        <a href="{{ route('cuti_karyawans.create') }}" class="btn btn-primary">
            <i class="mr-1 fas fa-plus"></i> Ajukan Cuti
        </a>
        @endcan_show
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mr-1 fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mr-1 fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex mb-2">
                    <div class="btn-group mr-3">
                        <a href="{{ route('cuti_karyawans.index') }}"
                            class="btn btn-default {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                            Semua <span class="badge badge-light ml-1">{{ $totalCount }}</span>
                        </a>
                        <a href="{{ route('cuti_karyawans.index', ['status' => 'Menunggu Persetujuan']) }}"
                            class="btn btn-default {{ request('status') == 'Menunggu Persetujuan' ? 'active' : '' }}">
                            Menunggu <span class="badge badge-warning ml-1">{{ $pendingCount }}</span>
                        </a>
                        <a href="{{ route('cuti_karyawans.index', ['status' => 'Disetujui']) }}"
                            class="btn btn-default {{ request('status') == 'Disetujui' ? 'active' : '' }}">
                            Disetujui <span class="badge badge-success ml-1">{{ $approvedCount }}</span>
                        </a>
                        <a href="{{ route('cuti_karyawans.index', ['status' => 'Ditolak']) }}"
                            class="btn btn-default {{ request('status') == 'Ditolak' ? 'active' : '' }}">
                            Ditolak <span class="badge badge-danger ml-1">{{ $rejectedCount }}</span>
                        </a>
                    </div>
                </div>
                <form action="{{ route('cuti_karyawans.index') }}" method="GET" class="d-flex flex-wrap">
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="d-flex mr-2 mb-2">
                        <div class="mr-2">
                            <label class="small text-muted d-block mb-1">Tanggal Mulai</label>
                            <div class="input-group" style="width: 200px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div>
                            <label class="small text-muted d-block mb-1">Tanggal Akhir</label>
                            <div class="input-group" style="width: 200px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="small text-muted d-block mb-1">Pencarian</label>
                        <div class="input-group" style="width: 300px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari pengajuan..."
                                value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-default" type="submit">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <a href="{{ route('cuti_karyawans.index') }}" class="btn btn-default">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Karyawan</th>
                            <th width="20%">Jenis Cuti</th>
                            <th width="20%">Periode</th>
                            <th width="10%">Durasi</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cutiKaryawans as $index => $cuti)
                            <tr>
                                <td>{{ $cutiKaryawans->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $cuti->karyawan ? $cuti->karyawan->nama_karyawan : '-' }}</strong>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-{{ $cuti->jenis_cuti == 'Cuti' ? 'info' : 'secondary' }} mr-1">{{ $cuti->jenis_cuti }}</span>
                                    {{ $cuti->masterCuti ? $cuti->masterCuti->uraian : 'Pengajuan Cuti' }}
                                </td>
                                <td>{{ $cuti->tanggal_mulai_cuti->format('d-m-Y') }} s/d
                                    {{ $cuti->tanggal_akhir_cuti->format('d-m-Y') }}</td>
                                <td>{{ $cuti->jumlah_hari_cuti }} hari</td>
                                <td>
                                    @if ($cuti->status_acc == 'Menunggu Persetujuan')
                                        <span class="badge badge-warning">Menunggu</span>
                                    @elseif($cuti->status_acc == 'Disetujui')
                                        <span class="badge badge-success">Disetujui</span>
                                    @elseif($cuti->status_acc == 'Ditolak')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if ($cuti->status_acc == 'Menunggu Persetujuan')
                                            <a href="{{ route('cuti_karyawans.show', $cuti) }}" class="btn btn-info"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @can_show('cuti_karyawan.edit')
                                            <a href="{{ route('cuti_karyawans.edit', $cuti) }}" class="btn btn-primary"
                                                title="Edit Pengajuan">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan_show

                                            @can_show('cuti_karyawan.approve')
                                            <a href="{{ route('cuti_karyawans.approval', $cuti) }}"
                                                class="btn btn-success" title="Proses Pengajuan">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endcan_show

                                            @can_show('cuti_karyawan.delete')
                                            <form action="{{ route('cuti_karyawans.destroy', $cuti) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus Pengajuan"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan_show
                                        @else
                                            @can_show('cuti_karyawan.approve')
                                            <a href="{{ route('cuti_karyawans.approval', $cuti) }}" class="btn btn-info"
                                                title="Lihat Detail Persetujuan">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            @endcan_show
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3">
                                    <i class="mr-1 fas fa-info-circle"></i> Tidak ada pengajuan cuti yang ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">


                <div class="col-md-4">
                    <div class="text-muted">
                        Showing {{ $cutiKaryawans->firstItem() ?? 0 }} to {{ $cutiKaryawans->lastItem() ?? 0 }} of
                        {{ $cutiKaryawans->total() }} entries
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Page navigation">
                            {{ $cutiKaryawans->appends(request()->except('page'))->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>
                </div>
                {{--
                <div>
                    {{ $cutiKaryawans->appends(request()->query())->links() }}
                </div> --}}
            </div>


        </div>
    </div>
@stop

@section('css')
    <style>
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .table td {
            border-top: none;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
            padding: 5px 8px;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .btn-group-sm>.btn {
            padding: .25rem .5rem;
        }

        .btn-default.active {
            background-color: #007bff;
            color: white;
        }

        .pagination {
            margin-bottom: 0;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            // Fade out alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@stop
