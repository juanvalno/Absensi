@extends('adminlte::page')

@section('title', 'Detail Karyawan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="mr-2 fas fa-user text-primary"></i>Detail Karyawan</h1>
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

    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ $karyawan->nama_karyawan }}
                    @if ($karyawan->tahun_keluar)
                        <span class="badge badge-danger">RESIGN</span>
                    @else
                        <span class="badge badge-success">AKTIF</span>
                    @endif
                </h3>
                <div class="card-tools">
                    <a href="{{ route('karyawans.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @can_show('karyawan.edit')
                    @if (!$karyawan->tahun_keluar)
                        <a href="{{ route('karyawans.edit', $karyawan) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endif
                    @endcan_show
                </div>
            </div>
        </div>
        <div class="card-body position-relative">
            @if ($karyawan->tahun_keluar)
                <div class="resign-watermark">RESIGN</div>
            @endif

            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/karyawan/foto/' . $karyawan->foto_karyawan) }}"
                                alt="{{ $karyawan->nama_karyawan }}" class="rounded img-fluid mb-3"
                                style="max-height: 250px;">
                            <h5 class="mb-0">{{ $karyawan->nama_karyawan }}</h5>
                            <p class="text-muted">{{ $karyawan->nik_karyawan }}</p>
                            <div class="mt-3">
                                <span
                                    class="badge badge-info">{{ $karyawan->jabatan ? $karyawan->jabatan->name_jabatan : '-' }}</span>
                                <span
                                    class="badge badge-primary">{{ $karyawan->departemen ? $karyawan->departemen->name_departemen : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($karyawan->upload_ktp)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Dokumen KTP</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ asset('storage/karyawan/ktp/' . $karyawan->upload_ktp) }}"
                                    alt="KTP {{ $karyawan->nama_karyawan }}" class="rounded img-fluid"
                                    style="max-height: 150px;">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="karyawan-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal"
                                        role="tab">
                                        <i class="fas fa-user mr-1"></i> Informasi Pribadi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="job-tab" data-toggle="tab" href="#job" role="tab">
                                        <i class="fas fa-briefcase mr-1"></i> Informasi Pekerjaan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="additional-tab" data-toggle="tab" href="#additional"
                                        role="tab">
                                        <i class="fas fa-info-circle mr-1"></i> Informasi Tambahan
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content" id="karyawan-tab-content">
                                <!-- Personal Information Tab -->
                                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th width="150">NIK Karyawan</th>
                                                    <td><span
                                                            class="badge badge-light">{{ $karyawan->nik_karyawan }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Nama</th>
                                                    <td>{{ $karyawan->nama_karyawan }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status Karyawan</th>
                                                    <td>
                                                        @if ($karyawan->statuskaryawan == 'Tetap')
                                                            <span
                                                                class="badge badge-success">{{ $karyawan->statuskaryawan }}</span>
                                                        @elseif($karyawan->statuskaryawan == 'Kontrak')
                                                            <span
                                                                class="badge badge-warning">{{ $karyawan->statuskaryawan }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-info">{{ $karyawan->statuskaryawan }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Status Kontrak</th>
                                                    <td>
                                                        @if($karyawan->iskontrak)
                                                            <span class="badge badge-warning">Karyawan Kontrak</span>
                                                        @else
                                                            <span class="badge badge-success">Karyawan Tetap</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>NIK (KTP)</th>
                                                    <td>{{ $karyawan->nik }}</td>
                                                </tr>
                                                <tr>
                                                    <th>No. KK</th>
                                                    <td>{{ $karyawan->kk }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status Perkawinan</th>
                                                    <td>{{ $karyawan->statuskawin }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Jumlah Anggota KK</th>
                                                    <td>{{ $karyawan->jml_anggotakk }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th width="150">Nama Ayah</th>
                                                    <td>{{ $karyawan->ortu_bapak }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nama Ibu</th>
                                                    <td>{{ $karyawan->ortu_ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th>No. HP</th>
                                                    <td>{{ $karyawan->no_hp }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nomor Rekening</th>
                                                    <td>{{ $karyawan->nomor_rekening }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nama Bank</th>
                                                    <td>{{ $karyawan->nama_bank }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nama Pemilik Rekening</th>
                                                    <td>{{ $karyawan->nama_pemilik_rekening }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Information Tab -->
                                <div class="tab-pane fade" id="job" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th width="150">Departemen</th>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            {{ $karyawan->departemen ? $karyawan->departemen->name_departemen : '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Bagian</th>
                                                    <td>{{ $karyawan->bagian ? $karyawan->bagian->name_bagian : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Jabatan</th>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ $karyawan->jabatan ? $karyawan->jabatan->name_jabatan : '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Profesi</th>
                                                    <td>{{ $karyawan->profesi ? $karyawan->profesi->name_profesi : '-' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th width="150">Tanggal Masuk</th>
                                                    <td>{{ $karyawan->tgl_awalmmasuk ? date('d-m-Y', strtotime($karyawan->tgl_awalmmasuk)) : '-' }}
                                                    </td>
                                                </tr>
                                                @if ($karyawan->tahun_keluar)
                                                    <tr>
                                                        <th>Tanggal Resign</th>
                                                        <td class="text-danger">
                                                            {{ is_string($karyawan->tahun_keluar) ? date('d-m-Y', strtotime($karyawan->tahun_keluar)) : $karyawan->tahun_keluar->format('d-m-Y') }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th>Pendidikan</th>
                                                    <td>{{ $karyawan->pendidikan_terakhir }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Program Studi / Jurusan</th>
                                                    <td>
                                                        <span class="badge badge-secondary">
                                                            {{ $karyawan->programStudi ? $karyawan->programStudi->name_programstudi : '-' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information Tab -->
                                <div class="tab-pane fade" id="additional" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th width="150">Ukuran Kemeja</th>
                                                    <td>{{ $karyawan->ukuran_kemeja }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ukuran Celana</th>
                                                    <td>{{ $karyawan->ukuran_celana }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ukuran Sepatu</th>
                                                    <td>{{ $karyawan->ukuran_sepatu }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th width="150">Terdaftar Pada</th>
                                                    <td>{{ $karyawan->created_at->format('d-m-Y H:i:s') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Terakhir Diupdate</th>
                                                    <td>{{ $karyawan->updated_at->format('d-m-Y H:i:s') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .position-relative {
            position: relative;
            overflow: hidden;
        }

        .resign-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            font-weight: bold;
            color: rgba(255, 0, 0, 0.2);
            white-space: nowrap;
            pointer-events: none;
            z-index: 10;
            text-transform: uppercase;
            letter-spacing: 10px;
        }

        .card-header-tabs {
            margin-right: -1rem;
            margin-bottom: -0.75rem;
            margin-left: -1rem;
            border-bottom: 0;
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .badge {
            font-size: 90%;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            // Handle tab navigation
            $('#karyawan-tabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@stop
