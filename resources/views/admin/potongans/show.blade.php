@extends('adminlte::page')

@section('title', 'Potongan Details')

@section('content_header')
<h1>Potongan Details</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Potongan Information</h3>
        <div class="card-tools">
            <a href="{{ route('potongans.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width: 200px">ID</th>
                <td>{{ $potongan->id }}</td>
            </tr>
            <tr>
                <th>Nama Potongan</th>
                <td>{{ $potongan->nama_potongan }}</td>
            </tr>
            <tr>
                <th>Tipe</th>
                <td>
                    @if($potongan->type == 'wajib')
                        <span class="badge badge-primary">Wajib</span>
                    @else
                        <span class="badge badge-info">Tidak Wajib</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $potongan->created_at }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $potongan->updated_at }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('potongans.edit', $potongan->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form action="{{ route('potongans.destroy', $potongan->id) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this potongan?')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>
</div>
@stop