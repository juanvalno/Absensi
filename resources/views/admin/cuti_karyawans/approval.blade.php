@extends('adminlte::page')

@section('title', 'Persetujuan Cuti Karyawan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-dark"><i class="fas fa-calendar-check text-primary"></i> Persetujuan Cuti</h1>
        <a href="{{ route('cuti_karyawans.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <!-- Employee Information -->
                <div class="col-lg-4">
                    <div class="p-3 bg-light rounded">
                        <h5 class="border-bottom pb-2">Informasi Karyawan</h5>
                        <div class="mb-3">
                            <small class="text-muted d-block">Nama Karyawan</small>
                            <strong>{{ $cutiKaryawan->karyawan->nama_karyawan }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">NIK</small>
                            <strong>{{ $cutiKaryawan->karyawan->nik_karyawan }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Departemen</small>
                            <strong>{{ $cutiKaryawan->karyawan->departemen->name_departemen ?? '-' }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Bagian</small>
                            <strong>{{ $cutiKaryawan->karyawan->bagian->name_bagian ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Leave Details -->
                <div class="col-lg-4">
                    <div class="p-3 bg-light rounded">
                        <h5 class="border-bottom pb-2">Detail Cuti</h5>
                        <div class="mb-3">
                            <small class="text-muted d-block">Jenis Cuti</small>
                            <strong>{{ $cutiKaryawan->masterCuti->uraian ?? $cutiKaryawan->jenis_cuti }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Periode Cuti</small>
                            <strong>{{ \Carbon\Carbon::parse($cutiKaryawan->tanggal_mulai_cuti)->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($cutiKaryawan->tanggal_akhir_cuti)->format('d M Y') }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Jumlah Hari</small>
                            <strong>{{ $cutiKaryawan->jumlah_hari_cuti }} hari</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Status</small>
                            @if ($cutiKaryawan->status_acc == 'Menunggu Persetujuan')
                                <span class="badge badge-warning">Menunggu Persetujuan</span>
                            @elseif($cutiKaryawan->status_acc == 'Disetujui')
                                <span class="badge badge-success">Disetujui</span>
                            @else
                                <span class="badge badge-danger">Ditolak</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Approval Section -->
                <div class="col-lg-4">
                    <div class="p-3 bg-light rounded">
                        <h5 class="border-bottom pb-2">Persetujuan</h5>
                        @if ($cutiKaryawan->status_acc == 'Menunggu Persetujuan')
                            <form action="{{ route('cuti_karyawans.approve', $cutiKaryawan->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <select name="status_acc" id="status_acc" class="form-control form-control-sm">
                                        <option value="">-- Pilih Keputusan --</option>
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>

                                <div id="cuti_disetujui_group">
                                    <div class="form-group">
                                        <input type="number" name="cuti_disetujui" id="cuti_disetujui"
                                            class="form-control form-control-sm" placeholder="Jumlah hari disetujui"
                                            min="1" max="{{ $cutiKaryawan->jumlah_hari_cuti }}"
                                            value="{{ $cutiKaryawan->jumlah_hari_cuti }}">
                                    </div>
                                </div>

                                <div id="keterangan_tolak_group" style="display: none;">
                                    <div class="form-group">
                                        <textarea name="keterangan_tolak" id="keterangan_tolak" class="form-control form-control-sm" rows="3"
                                            placeholder="Alasan penolakan"></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-check-circle"></i> Simpan Keputusan
                                </button>
                            </form>
                        @else
                            <div class="mb-3">
                                <small class="text-muted d-block">Catatan</small>
                                <strong>{{ $cutiKaryawan->keterangan_tolak ?? '-' }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Disetujui/Ditolak Oleh</small>
                                <strong>{{ $cutiKaryawan->approver->nama_karyawan ?? '-' }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Tanggal Keputusan</small>
                                <strong>{{ $cutiKaryawan->tanggal_approval ? \Carbon\Carbon::parse($cutiKaryawan->tanggal_approval)->format('d M Y H:i') : '-' }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($cutiKaryawan->bukti || isset($cutiKaryawan->dokumen_pendukung))
                @php
                    $documentUrl = $cutiKaryawan->bukti
                        ? asset('storage/cuti/bukti/' . $cutiKaryawan->bukti)
                        : asset('storage/cuti_karyawan/dokumen/' . $cutiKaryawan->dokumen_pendukung);

                    $fileName = $cutiKaryawan->bukti ?? $cutiKaryawan->dokumen_pendukung;
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $isImage = in_array($fileExtension, $imageExtensions);
                    $isPdf = $fileExtension === 'pdf';
                @endphp

                <div class="mt-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title m-0">
                                    <i class="fas fa-file-alt mr-2"></i>Dokumen Pendukung
                                </h5>
                                <div>
                                    <a href="{{ $documentUrl }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka di Tab Baru
                                    </a>
                                    <a href="{{ $documentUrl }}" download class="btn btn-sm btn-success ml-2">
                                        <i class="fas fa-download mr-1"></i> Unduh
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($isImage)
                                <div class="text-center">
                                    <img src="{{ $documentUrl }}" alt="Preview Dokumen"
                                        style="max-width: 100%; max-height: 500px; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                </div>
                            @elseif ($isPdf)
                                <!-- Embed PDF menggunakan tag object/embed standar HTML5 -->
                                <div class="pdf-container"
                                    style="height: 600px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px;">
                                    <object data="{{ $documentUrl }}" type="application/pdf" width="100%" height="100%"
                                        style="min-height: 600px;">
                                        <embed src="{{ $documentUrl }}" type="application/pdf" width="100%"
                                            height="100%">
                                        <p>Browser Anda tidak mendukung preview PDF. Silakan <a href="{{ $documentUrl }}"
                                                target="_blank">buka di tab baru</a> atau <a href="{{ $documentUrl }}"
                                                download>unduh</a> file.</p>
                                        </embed>
                                    </object>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-1"></i> Dokumen jenis {{ strtoupper($fileExtension) }}
                                    tidak dapat ditampilkan secara langsung di halaman ini. Silakan klik tombol "Buka di Tab
                                    Baru" atau "Unduh" untuk melihat isi dokumen.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle form fields based on approval decision
            const statusSelect = document.getElementById('status_acc');
            const cutiDiSetujuiGroup = document.getElementById('cuti_disetujui_group');
            const keteranganTolakGroup = document.getElementById('keterangan_tolak_group');

            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    if (this.value === 'Disetujui') {
                        cutiDiSetujuiGroup.style.display = 'block';
                        keteranganTolakGroup.style.display = 'none';
                    } else if (this.value === 'Ditolak') {
                        cutiDiSetujuiGroup.style.display = 'none';
                        keteranganTolakGroup.style.display = 'block';
                    } else {
                        cutiDiSetujuiGroup.style.display = 'none';
                        keteranganTolakGroup.style.display = 'none';
                    }
                });
            }
        });
    </script>
@stop

@section('css')
    <style>
        .badge {
            font-weight: 500;
            padding: 5px 10px;
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

        /* Styling for document section */
        .card {
            transition: all 0.3s ease;
            border-radius: 5px;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        /* PDF container styling */
        .pdf-container {
            background-color: #f5f5f5;
        }
    </style>
@stop
