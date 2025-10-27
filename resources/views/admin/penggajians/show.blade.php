@extends('adminlte::page')

@section('title', 'Detail Penggajian')

@section('content_header')
    <h1>Detail Penggajian</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Penggajian</h3>
            <div class="card-tools">
                <a href="{{ route('penggajian.index') }}" class="btn btn-sm btn-default">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('penggajian.payslip', $penggajian->id) }}" class="btn btn-sm btn-primary" target="_blank">
                    <i class="fas fa-print"></i> Cetak Slip Gaji
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informasi Karyawan</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px">Nama Karyawan</th>
                            <td>{{ $penggajian->karyawan->nama_karyawan }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>{{ $penggajian->karyawan->nik }}</td>
                        </tr>
                        <tr>
                            <th>Departemen</th>
                            <td>{{ is_string($penggajian->detail_departemen) ? json_decode($penggajian->detail_departemen)->departemen ?? '-' : $penggajian->detail_departemen['departemen'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Bagian</th>
                            <td>{{ is_string($penggajian->detail_departemen) ? json_decode($penggajian->detail_departemen)->bagian ?? '-' : $penggajian->detail_departemen['bagian'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>{{ is_string($penggajian->detail_departemen) ? json_decode($penggajian->detail_departemen)->jabatan ?? '-' : $penggajian->detail_departemen['jabatan'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Profesi</th>
                            <td>{{ is_string($penggajian->detail_departemen) ? json_decode($penggajian->detail_departemen)->profesi ?? '-' : $penggajian->detail_departemen['profesi'] ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Periode</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px">Periode</th>
                            <td>{{ $penggajian->periodeGaji->nama_periode }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td>{{ is_string($penggajian->periode_awal) ? date('d-m-Y', strtotime($penggajian->periode_awal)) : $penggajian->periode_awal->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td>{{ is_string($penggajian->periode_akhir) ? date('d-m-Y', strtotime($penggajian->periode_akhir)) : $penggajian->periode_akhir->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Rincian Gaji</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 300px">Gaji Pokok</th>
                            <td>{{ $penggajian->formatCurrency($penggajian->gaji_pokok) }}</td>
                        </tr>
                        <tr>
                            <th>Total Tunjangan</th>
                            <td>{{ $penggajian->formatCurrency($penggajian->tunjangan) }}</td>
                        </tr>
                        <tr>
                            <th>Total Potongan</th>
                            <td>{{ $penggajian->formatCurrency($penggajian->potongan) }}</td>
                        </tr>
                        <tr class="bg-light">
                            <th>Gaji Bersih</th>
                            <td><strong>{{ $penggajian->formatCurrency($penggajian->gaji_bersih) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Detail Tunjangan</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Tunjangan</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($penggajian->detail_tunjangan)
                                @foreach(is_string($penggajian->detail_tunjangan) ? json_decode($penggajian->detail_tunjangan) : $penggajian->detail_tunjangan as $tunjangan)
                                    <tr>
                                        <td>{{ is_object($tunjangan) ? $tunjangan->nama : $tunjangan['nama'] }}</td>
                                        <td>{{ $penggajian->formatCurrency(is_object($tunjangan) ? $tunjangan->nominal : $tunjangan['nominal']) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada data tunjangan</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th>Total</th>
                                <th>{{ $penggajian->formatCurrency($penggajian->tunjangan) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Detail Potongan</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Potongan</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($penggajian->detail_potongan)
                                @foreach(is_string($penggajian->detail_potongan) ? json_decode($penggajian->detail_potongan) : $penggajian->detail_potongan as $potongan)
                                    <tr>
                                        <td>{{ is_object($potongan) ? $potongan->nama : $potongan['nama'] }}</td>
                                        <td>{{ $penggajian->formatCurrency(is_object($potongan) ? $potongan->nominal : $potongan['nominal']) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada data potongan</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th>Total</th>
                                <th>{{ $penggajian->formatCurrency($penggajian->potongan) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table th {
            background-color: #f4f6f9;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Any JavaScript you need
        });
    </script>
@stop