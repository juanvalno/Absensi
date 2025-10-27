@extends('adminlte::page')

@section('title', 'Riwayat Ajuan Penggajian')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="mr-2 fas fa-history text-info"></i>Riwayat Ajuan Penggajian</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Riwayat Ajuan Penggajian</h3>
                        <div class="card-tools">
                            <a href="{{ route('penggajian.exportAllHistoryExcel') }}" class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-file-excel mr-1"></i> Export Semua Data
                            </a>
                            <a href="{{ route('penggajian.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="history-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Kode Ajuan</th>
                                    <th>Tanggal Ajuan</th>
                                    <th>Periode Gaji</th>
                                    <th>Total Gaji</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($history as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->kode_keuangan }}</td>
                                        <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ $item->periode->nama_periode ?? '-' }}</td>
                                        <td>Rp {{ number_format($item->total_gaji, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if ($item->status === 'disetujui')
                                                <span class="badge badge-success">Disetujui</span>
                                            @elseif($item->status === 'ditolak')
                                                <span class="badge badge-danger">Ditolak</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('keuangan.show', $item->id) }}"
                                                    class="btn btn-info btn-xs">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('penggajian.exportHistoryExcel', ['id' => $item->id]) }}"
                                                    class="btn btn-success btn-xs">
                                                    <i class="fas fa-file-excel"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#history-table').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        });
    </script>
@stop
