@extends('adminlte::page')

@section('title', 'Detail Pengajuan Keuangan')

@section('content_header')
    <h1>Detail Pengajuan Keuangan</h1>
@stop

@section('content')
    <div class="card rounded-lg overflow-hidden">
        <div class="card-header bg-white border-bottom">
            <h3 class="card-title font-weight-bold text-primary mb-0">Informasi Pengajuan Keuangan</h3>
            <div class="card-tools">
                <a href="{{ route('keuangan.index') }}" class="btn btn-light btn-sm rounded-pill hover-scale">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 modern-table">
                    <tr>
                        <th style="width: 200px">Kode Keuangan</th>
                        <td>{{ $keuangan->kode_keuangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>{{ $keuangan->periode->nama_periode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Pengajuan</th>
                        <td>
                            @if ($keuangan->status == 'menunggu')
                                <span class="badge badge-warning">Sedang Menunggu Persetujuan</span>
                            @elseif($keuangan->status == 'disetujui')
                                <span class="badge badge-success">Telah Disetujui</span>
                            @else
                                <span class="badge badge-danger">Tidak Disetujui</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Nama Verifikator</th>
                        <td>{{ $keuangan->verifikator->name ?? 'Belum ada verifikator' }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Verifikasi</th>
                        <td>{{ $keuangan->tanggal_verifikasi ? \Carbon\Carbon::parse($keuangan->tanggal_verifikasi)->translatedFormat('l, d F Y H:i:s') : 'Belum diverifikasi' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Catatan Verifikasi</th>
                        <td class="border-top border-bottom">{{ $keuangan->catatan ?? 'Tidak ada catatan' }}</td>
                    </tr>
                    <tr>
                        <th>Ringkasan Penggajian</th>
                        <td>
                            <button class="btn btn-sm btn-default mb-2" onclick="toggleTable()">
                                <i class="fas fa-eye"></i> <span id="toggleText">Tampilkan</span>
                            </button>
                            <div class="table-responsive" id="summaryTable" style="display: none;">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr class="border-top border-bottom">
                                            <th>Departemen</th>
                                            <th>Status</th>
                                            <th>Jumlah Karyawan</th>
                                            <th>Total Gaji</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $summary = $keuangan->summary ?? [];
                                            $totalKaryawan = 0;
                                            $grandTotal = 0;
                                        @endphp

                                        @foreach ($summary as $dept => $deptData)
                                            @php
                                                $statusData = $deptData['status'] ?? [];
                                                $isFirstDept = true;
                                                $deptTotalKaryawan = 0;
                                                $deptTotalGaji = 0;
                                            @endphp

                                            @foreach ($statusData as $status => $data)
                                                @php
                                                    $deptTotalKaryawan += $data['count'] ?? 0;
                                                    $deptTotalGaji += $data['total'] ?? 0;
                                                    $totalKaryawan += $data['count'] ?? 0;
                                                    $grandTotal += $data['total'] ?? 0;
                                                @endphp
                                                <tr>
                                                    @if ($isFirstDept)
                                                        <td rowspan="{{ count($statusData) }}">{{ $dept }}</td>
                                                        @php $isFirstDept = false; @endphp
                                                    @endif
                                                    <td>{{ $status }}</td>
                                                    <td class="text-center">{{ $data['count'] ?? 0 }} orang</td>
                                                    <td class="text-right">Rp
                                                        {{ number_format($data['total'] ?? 0, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach

                                            <tr class="table-info border-top border-bottom">
                                                <td colspan="2"><strong>Total {{ $dept }}</strong></td>
                                                <td class="text-center"><strong>{{ $deptTotalKaryawan }} orang</strong>
                                                </td>
                                                <td class="text-right"><strong>Rp
                                                        {{ number_format($deptTotalGaji, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        @endforeach

                                        <tr class="table-success border-bottom">
                                            <td colspan="2"><strong>Total Keseluruhan</strong></td>
                                            <td class="text-center"><strong>{{ $totalKaryawan }} orang</strong></td>
                                            <td class="text-right"><strong>Rp
                                                    {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Waktu Pembuatan</th>
                        <td>{{ $keuangan->created_at ? \Carbon\Carbon::parse($keuangan->created_at)->translatedFormat('l, d F Y H:i:s') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Terakhir Diperbarui</th>
                        <td>{{ $keuangan->updated_at ? \Carbon\Carbon::parse($keuangan->updated_at)->translatedFormat('l, d F Y H:i:s') : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @stop

    @push('css')
        <style>
            .modern-table {
                --bs-table-hover-bg: rgba(0, 123, 255, 0.05);
            }

            .modern-table th {
                background: #f8f9fa;
                font-weight: 600;
                border-bottom: 2px solid #dee2e6;
            }

            .modern-table td {
                padding: 1rem;
                vertical-align: middle;
            }

            .modern-table tr:last-child td {
                border-bottom: none;
            }

            .badge {
                padding: 0.5em 1em;
                font-weight: 500;
            }

            .hover-scale {
                transition: transform 0.2s;
            }

            .hover-scale:hover {
                transform: scale(1.05);
            }
        </style>
    @endpush

    @push('js')
        <script>
            function toggleTable() {
                const table = document.getElementById('summaryTable');
                const toggleText = document.getElementById('toggleText');

                if (table.style.display === 'none') {
                    table.style.display = 'block';
                    toggleText.textContent = 'Sembunyikan';
                    table.style.opacity = '0';
                    setTimeout(() => {
                        table.style.opacity = '1';
                    }, 50);
                } else {
                    table.style.opacity = '0';
                    setTimeout(() => {
                        table.style.display = 'none';
                    }, 200);
                    toggleText.textContent = 'Tampilkan';
                }
            }
        </script>
    @endpush
