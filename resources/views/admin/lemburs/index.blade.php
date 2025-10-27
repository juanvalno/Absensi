@extends('adminlte::page')

@section('title', 'Data Lembur Karyawan')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="mr-2 fas fa-clock text-primary"></i>Pengajuan Lembur Karyawan</h1>
    @can_show('lemburs.create')
    <a href="{{ route('lemburs.create') }}" class="btn btn-primary">
        <i class="mr-1 fas fa-plus"></i> Ajukan Lembur
    </a>
    @endcan_show
</div>
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="mr-1 fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
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
                    <a href="{{ route('lemburs.index') }}" class="btn btn-default {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                        Semua <span class="badge badge-light ml-1">{{ $totalCount }}</span>
                    </a>
                    <a href="{{ route('lemburs.index', ['status' => 'Menunggu Persetujuan']) }}" class="btn btn-default {{ request('status') == 'Menunggu Persetujuan' ? 'active' : '' }}">
                        Menunggu <span class="badge badge-warning ml-1">{{ $pendingCount }}</span>
                    </a>
                    <a href="{{ route('lemburs.index', ['status' => 'Disetujui']) }}" class="btn btn-default {{ request('status') == 'Disetujui' ? 'active' : '' }}">
                        Disetujui <span class="badge badge-success ml-1">{{ $approvedCount }}</span>
                    </a>
                    <a href="{{ route('lemburs.index', ['status' => 'Ditolak']) }}" class="btn btn-default {{ request('status') == 'Ditolak' ? 'active' : '' }}">
                        Ditolak <span class="badge badge-danger ml-1">{{ $rejectedCount }}</span>
                    </a>
                </div>
            </div>
            <form action="{{ route('lemburs.index') }}" method="GET" class="d-flex flex-wrap">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="d-flex mr-2 mb-2">
                    <div class="mr-2">
                        <label class="small text-muted d-block mb-1">Tanggal Mulai</label>
                        <div class="input-group" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div>
                        <label class="small text-muted d-block mb-1">Tanggal Akhir</label>
                        <div class="input-group" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="small text-muted d-block mb-1">Pencarian</label>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari pengajuan..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-default" type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="{{ route('lemburs.index') }}" class="btn btn-default">
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
                        <th width="15%">Jenis Lembur</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Waktu</th>
                        <th width="10%">Durasi</th>
                        <th width="10%">Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lemburs as $index => $lembur)
                    <tr>
                        <td>{{ $lemburs->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $lembur->karyawan ? $lembur->karyawan->nama_karyawan : '-' }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-{{ $lembur->jenis_lembur == 'Hari Libur' ? 'info' : 'secondary' }} mr-1">{{ $lembur->jenis_lembur }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d-m-Y') }}</td>
                        <td>{{ $lembur->jam_mulai }} - {{ $lembur->jam_selesai }}</td>
                        <td>{{ $lembur->total_lembur }}</td>
                        <td>
                            @if($lembur->status == 'Menunggu Persetujuan')
                                <span class="badge badge-warning">Menunggu</span>
                            @elseif($lembur->status == 'Disetujui')
                                <span class="badge badge-success">Disetujui</span>
                            @elseif($lembur->status == 'Ditolak')
                                <span class="badge badge-danger">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if($lembur->status == 'Menunggu Persetujuan')
                                    <a href="{{ route('lemburs.show', $lembur) }}" class="btn btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @can_show('lemburs.edit')
                                    <a href="{{ route('lemburs.edit', $lembur) }}" class="btn btn-primary" title="Edit Pengajuan">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan_show
                                    
                                    @can_show('lemburs.approve')
                                    <a href="{{ route('lemburs.approval', $lembur) }}" class="btn btn-success" title="Proses Pengajuan">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @endcan_show
                                    
                                    @can_show('lemburs.delete')
                                    <form action="{{ route('lemburs.destroy', $lembur) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Hapus Pengajuan" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan_show
                                @else
                                    <a href="{{ route('lemburs.approval', $lembur) }}" class="btn btn-info" title="Lihat Detail Persetujuan">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-3">
                            <i class="mr-1 fas fa-info-circle"></i> Tidak ada pengajuan lembur yang ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Menampilkan {{ $lemburs->firstItem() ?? 0 }} sampai {{ $lemburs->lastItem() ?? 0 }} dari {{ $lemburs->total() }} data
            </div>
            <div>
                {{ $lemburs->appends(request()->query())->links() }}
            </div>
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

    .btn-group-sm > .btn {
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