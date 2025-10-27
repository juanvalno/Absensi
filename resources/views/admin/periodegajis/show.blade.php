@extends('adminlte::page')

@section('title', 'Detail Periode Gaji')

@section('content_header')
<h1>Detail Periode Gaji</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Periode Gaji</h3>
        <div class="card-tools">
            <a href="{{ route('periodegaji.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('periodegaji.edit', $periodegaji->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width: 200px">Nama Periode</th>
                <td>{{ $periodegaji->nama_periode }}</td>
            </tr>
            <tr>
                <th>Tanggal Mulai</th>
                <td>{{ $periodegaji->tanggal_mulai->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Tanggal Selesai</th>
                <td>{{ $periodegaji->tanggal_selesai->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Durasi</th>
                <td>{{ $periodegaji->tanggal_mulai->diffInDays($periodegaji->tanggal_selesai) + 1 }} hari</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($periodegaji->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Nonaktif</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $periodegaji->keterangan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $periodegaji->created_at->format('d-m-Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Diperbarui Pada</th>
                <td>{{ $periodegaji->updated_at->format('d-m-Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
</div>
@stop
