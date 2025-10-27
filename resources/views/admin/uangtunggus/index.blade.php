@extends('adminlte::page')

@section('title', 'Data Uang Tunggu')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="mr-2 fas fa-money-bill-wave text-primary"></i>Data Uang Tunggu</h1>
    @can_show('uang_tunggu.create')
    <a href="{{ route('uangtunggus.create') }}" class="btn btn-primary">
        <i class="mr-1 fas fa-plus"></i> Tambah Uang Tunggu
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
                    <a href="{{ route('uangtunggus.index') }}" class="btn btn-default {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                        Semua <span class="badge badge-light ml-1">{{ $totalCount }}</span>
                    </a>
                    <a href="{{ route('uangtunggus.index', ['status' => 'active']) }}" class="btn btn-default {{ request('status') == 'active' ? 'active' : '' }}">
                        Aktif <span class="badge badge-success ml-1">{{ $activeCount }}</span>
                    </a>
                    <a href="{{ route('uangtunggus.index', ['status' => 'expired']) }}" class="btn btn-default {{ request('status') == 'expired' ? 'active' : '' }}">
                        Berakhir <span class="badge badge-secondary ml-1">{{ $expiredCount }}</span>
                    </a>
                </div>
            </div>
            <form action="{{ route('uangtunggus.index') }}" method="GET" class="d-flex flex-wrap">
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
                
                <div class="mr-2 mb-2">
                    <label class="small text-muted d-block mb-1">Karyawan</label>
                    <select class="form-control select2" name="karyawan_id" style="width: 200px;">
                        <option value="">-- Semua Karyawan --</option>
                        @foreach ($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}" {{ request('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                {{ $karyawan->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-2">
                    <label class="small text-muted d-block mb-1">Pencarian</label>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari uang tunggu..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-default" type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="{{ route('uangtunggus.index') }}" class="btn btn-default">
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
                        <th width="25%">Karyawan</th>
                        <th width="15%">Tanggal Mulai</th>
                        <th width="15%">Tanggal Selesai</th>
                        <th width="15%">Nominal</th>
                        <th width="10%">Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($uangTunggus as $index => $uangTunggu)
                    <tr>
                        <td>{{ $uangTunggus->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $uangTunggu->karyawan->nama_karyawan ?? 'N/A' }}</strong>
                            @if($uangTunggu->karyawan)
                                <div class="small text-muted">{{ $uangTunggu->karyawan->nik_karyawan }}</div>
                            @endif
                        </td>
                        <td>{{ $uangTunggu->tanggal_mulai->format('d-m-Y') }}</td>
                        <td>{{ $uangTunggu->tanggal_selesai->format('d-m-Y') }}</td>
                        <td>Rp {{ number_format($uangTunggu->nominal, 0, ',', '.') }}</td>
                        <td>
                            @if($uangTunggu->tanggal_selesai >= now())
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Berakhir</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('uangtunggus.show', $uangTunggu) }}" class="btn btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can_show('uang_tunggu.edit')
                                <a href="{{ route('uangtunggus.edit', $uangTunggu) }}" class="btn btn-primary" title="Edit Uang Tunggu">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan_show
                                @can_show('uang_tunggu.delete')
                                <form action="{{ route('uangtunggus.destroy', $uangTunggu) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Hapus Uang Tunggu" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan_show
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-3">
                            <i class="mr-1 fas fa-info-circle"></i> Tidak ada data uang tunggu yang ditemukan
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
                Menampilkan {{ $uangTunggus->firstItem() ?? 0 }} sampai {{ $uangTunggus->lastItem() ?? 0 }} dari {{ $uangTunggus->total() }} data
            </div>
            <div>
                {{ $uangTunggus->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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

    .badge-success {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-secondary {
        background-color: #e2e3e5;
        color: #383d41;
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
    
    .select2-container .select2-selection--single {
        height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
    }
</style>
@stop

@section('js')
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        // Fade out alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Auto-submit form when select changes
        $('select[name="karyawan_id"]').change(function() {
            $(this).closest('form').submit();
        });
    });
</script>
@stop
