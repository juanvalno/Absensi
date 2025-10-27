@extends('adminlte::page')

@section('title', 'Kuota Izin Tahunan')

@section('content_header')
    <h1>Kuota Izin Tahunan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Generate Kuota Izin Massal</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('kuota-izin.generate-massal') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tahun">Tahun</label>
                                    <input type="number" name="tahun" class="form-control" value="{{ $tahun }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kuota_awal">Kuota Awal (Hari)</label>
                                    <input type="number" name="kuota_awal" class="form-control" value="6"
                                        min="0" max="6" required>
                                    <small class="text-muted">Maksimal 6 hari per tahun</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-sync-alt"></i> Generate Kuota
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kuota Izin</h3>
                    <div class="card-tools">
                        <a href="{{ route('kuota-izin.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Kuota
                        </a>
                        <a href="{{ route('kuota-izin.report') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-file-alt"></i> Laporan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Karyawan</th>
                                    <th>Tahun</th>
                                    <th>Kuota Awal</th>
                                    <th>Digunakan</th>
                                    <th>Sisa</th>
                                    <th>Expired</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kuotaIzin as $index => $kuota)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $kuota->nama_karyawan }}</td>
                                        <td>{{ $kuota->tahun }}</td>
                                        <td>{{ $kuota->kuota_awal }}</td>
                                        <td>{{ $kuota->kuota_digunakan }}</td>
                                        <td>{{ $kuota->kuota_sisa }}</td>
                                        <td>{{ $kuota->tanggal_expired ? date('d/m/Y', strtotime($kuota->tanggal_expired)) : '-' }}
                                        </td>
                                        <td>

                                            <a href="{{ route('kuota-izin.edit', $kuota->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('kuota-izin.destroy', $kuota->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
    </script>
@stop
