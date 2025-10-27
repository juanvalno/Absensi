@extends('adminlte::page')

@section('title', 'Detail Pengajuan Cuti')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-calendar-alt text-primary mr-2"></i>Detail Pengajuan Cuti</h1>
        <div>
            <a href="{{ route('cuti_karyawans.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if ($cutiKaryawan->status_acc == 'Menunggu Persetujuan')
                @can_show('cuti_karyawan.edit')
                <a href="{{ route('cuti_karyawans.edit', $cutiKaryawan) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endcan_show

                @can_show('cuti_karyawan.approve')
                <a href="{{ route('cuti_karyawans.approval', $cutiKaryawan) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-check"></i> Proses
                </a>
                @endcan_show
            @endif
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <!-- Data Karyawan -->
            <div class="card card-outline card-primary mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title"><i class="fas fa-user-circle mr-2"></i>Data Karyawan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        @if($cutiKaryawan->karyawan)
                            <tr>
                                <th width="150">Nama Karyawan</th>
                                <td>{{ $cutiKaryawan->karyawan->nama_karyawan }}</td>
                            </tr>
                            <tr>
                                <th>NIK Karyawan</th>
                                <td>{{ $cutiKaryawan->karyawan->nik_karyawan }}</td>
                            </tr>
                            @if($cutiKaryawan->karyawan->departemen)
                            <tr>
                                <th>Departemen</th>
                                <td>{{ $cutiKaryawan->karyawan->departemen->name_departemen }}</td>
                            </tr>
                            @endif
                            @if($cutiKaryawan->karyawan->bagian)
                            <tr>
                                <th>Bagian</th>
                                <td>{{ $cutiKaryawan->karyawan->bagian->name_bagian }}</td>
                            </tr>
                            @endif
                            @if($cutiKaryawan->karyawan->jabatan)
                            <tr>
                                <th>Jabatan</th>
                                <td>{{ $cutiKaryawan->karyawan->jabatan->name_jabatan }}</td>
                            </tr>
                            @endif
                        @endif
                    </table>
                </div>
            </div>

            <!-- Data Persetujuan -->
            <div class="card card-outline card-primary mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title"><i class="fas fa-clipboard-check mr-2"></i>Data Persetujuan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        @if($cutiKaryawan->supervisor)
                        <tr>
                            <th width="150">Supervisor</th>
                            <td>{{ $cutiKaryawan->supervisor->nama_karyawan }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge {{ $cutiKaryawan->status_badge_class }}">
                                    {{ $cutiKaryawan->status_acc }}
                                </span>
                            </td>
                        </tr>
                        @if ($cutiKaryawan->status_acc != 'Menunggu Persetujuan')
                            @if($cutiKaryawan->tanggal_approval)
                            <tr>
                                <th>Tanggal Approval</th>
                                <td>{{ $cutiKaryawan->tanggal_approval->format('d-m-Y H:i:s') }}</td>
                            </tr>
                            @endif
                            @if($cutiKaryawan->approver)
                            <tr>
                                <th>Diapprove Oleh</th>
                                <td>{{ $cutiKaryawan->approver->nama_karyawan }}</td>
                            </tr>
                            @endif
                            @if ($cutiKaryawan->status_acc == 'Ditolak' && $cutiKaryawan->alasan_penolakan)
                            <tr>
                                <th>Alasan Penolakan</th>
                                <td>{{ $cutiKaryawan->alasan_penolakan }}</td>
                            </tr>
                            @endif
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Detail Pengajuan Cuti -->
            <div class="card card-outline card-primary mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title"><i class="fas fa-calendar-day mr-2"></i>Detail Pengajuan Cuti</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="150">Jenis Cuti</th>
                            <td>{{ $cutiKaryawan->jenis_cuti }}</td>
                        </tr>
                        <tr>
                            <th>Master Cuti</th>
                            <td>{{ $cutiKaryawan->masterCuti->uraian ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td>{{ $cutiKaryawan->tanggal_mulai_cuti ? $cutiKaryawan->tanggal_mulai_cuti->format('d-m-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Akhir</th>
                            <td>{{ $cutiKaryawan->tanggal_akhir_cuti ? $cutiKaryawan->tanggal_akhir_cuti->format('d-m-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Hari</th>
                            <td>{{ $cutiKaryawan->jumlah_hari_cuti }} hari</td>
                        </tr>
                        @if($cutiKaryawan->status_acc === 'Disetujui')
                        <tr>
                            <th>Jumlah Hari Disetujui</th>
                            <td>{{ $cutiKaryawan->cuti_disetujui }} hari</td>
                        </tr>
                        @endif
                        @if($cutiKaryawan->bukti)
                        <tr>
                            <th>Bukti Pendukung</th>
                            <td>
                                <a href="{{ asset('storage/cuti/bukti/' . $cutiKaryawan->bukti) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file"></i> Lihat Dokumen
                                </a>
                            </td>
                        </tr>
                        @endif
                        @if($cutiKaryawan->keterangan_tolak && $cutiKaryawan->status_acc === 'Ditolak')
                        <tr>
                            <th>Keterangan Penolakan</th>
                            <td>{{ $cutiKaryawan->keterangan_tolak }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Sisa Cuti -->
            <div class="card-body">
                <table class="table table-bordered">
                    @if(isset($sisaCuti['kuota_awal']))
                    <tr>
                        <th width="150">Kuota Cuti Tahunan</th>
                        <td>{{ $sisaCuti['kuota_awal'] }} hari</td>
                    </tr>
                    @endif
                    @if(isset($sisaCuti['kuota_digunakan']))
                    <tr>
                        <th>Cuti Terpakai</th>
                        <td>{{ $sisaCuti['kuota_digunakan'] }} hari</td>
                    </tr>
                    @endif
                    @if(isset($sisaCuti['kuota_sisa']))
                    <tr>
                        <th>Sisa Cuti</th>
                        <td>{{ $sisaCuti['kuota_sisa'] }} hari</td>
                    </tr>
                    @endif
                    @if(isset($sisaCuti['tanggal_expired']))
                    <tr>
                        <th>Tanggal Kadaluarsa</th>
                        <td>{{ \Carbon\Carbon::parse($sisaCuti['tanggal_expired'])->format('d-m-Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Dokumen Pendukung Section -->
    @if ($cutiKaryawan->dokumen_pendukung)
        <div class="card card-outline card-primary mt-2">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="fas fa-file-alt mr-2"></i>Dokumen Pendukung</h5>
                    <div>
                        <a href="{{ asset('storage/dokumen_cuti/' . $cutiKaryawan->dokumen_pendukung) }}" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka di Tab Baru
                        </a>
                        <a href="{{ asset('storage/dokumen_cuti/' . $cutiKaryawan->dokumen_pendukung) }}" download
                            class="btn btn-sm btn-success ml-2">
                            <i class="fas fa-download mr-1"></i> Unduh
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    $documentUrl = asset('storage/dokumen_cuti/' . $cutiKaryawan->dokumen_pendukung);
                    $fileExtension = strtolower(pathinfo($cutiKaryawan->dokumen_pendukung, PATHINFO_EXTENSION));
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $isImage = in_array($fileExtension, $imageExtensions);
                    $isPdf = $fileExtension === 'pdf';
                @endphp

                @if ($isImage)
                    <div class="text-center">
                        <img src="{{ $documentUrl }}" alt="Dokumen Pendukung"
                            style="max-width: 100%; max-height: 500px; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    </div>
                @elseif($isPdf)
                    <div class="pdf-container"
                        style="height: 600px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px;">
                        <object data="{{ $documentUrl }}" type="application/pdf" width="100%" height="100%"
                            style="min-height: 600px;">
                            <embed src="{{ $documentUrl }}" type="application/pdf" width="100%" height="100%">
                            <p>Browser Anda tidak mendukung preview PDF. Silakan <a href="{{ $documentUrl }}"
                                    target="_blank">buka di tab baru</a> atau <a href="{{ $documentUrl }}"
                                    download>unduh</a> file.</p>
                            </embed>
                        </object>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Dokumen jenis {{ strtoupper($fileExtension) }} tidak dapat
                        ditampilkan secara langsung di halaman ini. Silakan klik tombol "Buka di Tab Baru" atau "Unduh"
                        untuk melihat isi dokumen.
                    </div>
                @endif
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .table th {
            background-color: #f4f6f9;
            color: #454545;
            vertical-align: middle;
        }

        .card-outline.card-primary {
            border-top: 3px solid #007bff;
        }

        .badge {
            font-weight: 500;
            padding: 5px 10px;
            font-size: 90%;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .card {
            transition: all 0.3s ease;
            border-radius: 5px;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .pdf-container {
            background-color: #f5f5f5;
        }

        .card-title {
            margin-bottom: 0;
            font-weight: 600;
        }
    </style>
@stop
