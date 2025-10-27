@extends('adminlte::page')

@section('title', 'Persetujuan Lembur')

@section('content_header')
<h1>Persetujuan Pengajuan Lembur</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Pengajuan Lembur</h3>
                <div class="card-tools">
                    <a href="{{ route('lemburs.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="180">Nama Karyawan</th>
                        <td>{{ $lembur->karyawan ? $lembur->karyawan->nama_karyawan : '-' }}</td>
                    </tr>
                    <tr>
                        <th>NIK Karyawan</th>
                        <td>{{ $lembur->karyawan ? $lembur->karyawan->nik_karyawan : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>{{ $lembur->karyawan && $lembur->karyawan->departemen ? $lembur->karyawan->departemen->name_departemen : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Bagian</th>
                        <td>{{ $lembur->karyawan && $lembur->karyawan->bagian ? $lembur->karyawan->bagian->name_bagian : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>{{ $lembur->karyawan && $lembur->karyawan->jabatan ? $lembur->karyawan->jabatan->name_jabatan : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Supervisor</th>
                        <td>{{ $lembur->supervisor ? $lembur->supervisor->nama_karyawan : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Lembur</th>
                        <td>{{ $lembur->jenis_lembur }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Lembur</th>
                        <td>{{ $lembur->tanggal_lembur_formatted }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Lembur</th>
                        <td>{{ $lembur->jam_mulai_formatted }} - {{ $lembur->jam_selesai_formatted }} ({{ $lembur->total_lembur }})</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $lembur->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge {{ $lembur->status_badge_class }}">
                                {{ $lembur->status }}
                            </span>
                        </td>
                    </tr>
                    @if($lembur->status != 'Menunggu Persetujuan')
                    <tr>
                        <th>Tanggal Approval</th>
                        <td>{{ $lembur->tanggal_approval ? $lembur->tanggal_approval->format('d-m-Y H:i:s') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Diapprove Oleh</th>
                        <td>{{ $lembur->approver ? $lembur->approver->nama_karyawan : '-' }}</td>
                    </tr>
                    @if($lembur->status == 'Ditolak')
                    <tr>
                        <th>Alasan Penolakan</th>
                        <td>{{ $lembur->keterangan_tolak ?? '-' }}</td>
                    </tr>
                    @endif
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Persetujuan</h3>
            </div>
            <div class="card-body">
                @if($lembur->status == 'Menunggu Persetujuan')
                <form action="{{ route('lemburs.approve', $lembur) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Status Persetujuan <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status-disetujui" value="Disetujui" checked>
                            <label class="form-check-label" for="status-disetujui">
                                <span class="badge badge-success">Disetujui</span>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="status" id="status-ditolak" value="Ditolak">
                            <label class="form-check-label" for="status-ditolak">
                                <span class="badge badge-danger">Ditolak</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="section-lembur-disetujui">
                        <label for="lembur_disetujui">Lembur Disetujui <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lembur_disetujui" name="lembur_disetujui" value="{{ $lembur->total_lembur }}">
                        <small class="form-text text-muted">Format: jam dan menit (contoh: 3 jam 30 menit)</small>
                    </div>

                    <div class="form-group" id="section-keterangan-tolak" style="display: none;">
                        <label for="keterangan_tolak">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keterangan_tolak" name="keterangan_tolak" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Proses Persetujuan
                        </button>
                        <a href="{{ route('lemburs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Pengajuan lembur ini sudah diproses dengan status
                    <strong>{{ $lembur->status }}</strong>
                    @if($lembur->status == 'Disetujui')
                    dan disetujui sebanyak <strong>{{ $lembur->lembur_disetujui }}</strong>.
                    @endif
                </div>
                <a href="{{ route('lemburs.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                @endif
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
    $(function() {
        // Toggle sections based on selected approval status
        $('input[name="status"]').on('change', function() {
            const status = $(this).val();

            if (status === 'Disetujui') {
                $('#section-lembur-disetujui').show();
                $('#section-keterangan-tolak').hide();
                $('#keterangan_tolak').prop('required', false);
                $('#lembur_disetujui').prop('required', true);
            } else {
                $('#section-lembur-disetujui').hide();
                $('#section-keterangan-tolak').show();
                $('#keterangan_tolak').prop('required', true);
                $('#lembur_disetujui').prop('required', false);
            }
        });
    });

</script>
@stop
