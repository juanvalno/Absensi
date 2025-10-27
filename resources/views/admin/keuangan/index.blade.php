@extends('adminlte::page')

@section('title', 'Kelola Pengajuan Keuangan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="mr-2 fas fa-money-check-alt text-primary"></i>Kelola Pengajuan Keuangan</h1>
    </div>
@stop

@section('content')
    {{-- Tampilkan pesan sukses jika ada --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mr-1 fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="bg-white card-header">
            <h3 class="card-title">Daftar Pengajuan Keuangan</h3>
        </div>
        <div class="card-body">
            {{-- Tabel untuk menampilkan data pengajuan keuangan --}}
            <table id="keuangan-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Periode</th>
                        <th>Total Gaji</th>
                        <th>Status</th>
                        <th>Verifikator</th>
                        <th>Tanggal Verifikasi</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop untuk menampilkan setiap data keuangan --}}
                    @foreach ($keuangans as $index => $keuangan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $keuangan->kode_keuangan }}</td>
                            <td>{{ $keuangan->periode->nama_periode }}</td>
                            <td>Rp {{ number_format($keuangan->total_gaji, 0, ',', '.') }}</td>
                            <td>
                                @if ($keuangan->status == 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif ($keuangan->status == 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>{{ $keuangan->verifikator->name ?? '-' }}</td>
                            <td>{{ $keuangan->tanggal_verifikasi ? $keuangan->tanggal_verifikasi->format('d-m-Y H:i:s') : '-' }}
                            </td>
                            {{-- Tombol aksi berdasarkan status --}}
                            <td>
                                <div class="btn-group">
                                    {{-- Tombol lihat detail selalu tersedia --}}
                                    <a href="{{ route('keuangan.show', $keuangan->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- Tombol export excel hanya untuk status disetujui --}}
                                    @if ($keuangan->status == 'disetujui')
                                        <a href="{{ route('keuangan.export', ['id' => $keuangan->id]) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-file-excel"></i>
                                        </a>
                                    @endif

                                    {{-- Tombol setuju dan tolak hanya untuk status menunggu --}}
                                    @if ($keuangan->status == 'menunggu')
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                            data-target="#approveModal{{ $keuangan->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                            data-target="#rejectModal{{ $keuangan->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>

                                {{-- Modal untuk menyetujui pengajuan --}}
                                <div class="modal fade" id="approveModal{{ $keuangan->id }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('keuangan.approve', $keuangan->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Setujui Pengajuan</h4>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="catatan">Catatan (Opsional)</label>
                                                        <textarea class="form-control" name="catatan" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Setujui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal untuk menolak pengajuan --}}
                                <div class="modal fade" id="rejectModal{{ $keuangan->id }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('keuangan.reject', $keuangan->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Tolak Pengajuan</h4>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="catatan">Catatan (Wajib)</label>
                                                        <textarea class="form-control" name="catatan" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

{{-- Include CSS DataTables --}}
@section('css')
    <link rel="stylesheet" href="/vendor/datatables-bs4/css/dataTables.bootstrap4.min.css">
@stop

{{-- Include JavaScript DataTables --}}
@section('js')
    <script src="/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#keuangan-table').DataTable();
        });
    </script>
@stop
