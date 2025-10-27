@extends('adminlte::page')

@section('title', 'Edit Departemen')

@section('content_header')
<h1>Edit Departemen</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Departemen Details</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('departemens.update', $departemen) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name_departemen">Nama Departemen</label>
                <input type="text" class="form-control @error('name_departemen') is-invalid @enderror" id="name_departemen" name="name_departemen" value="{{ old('name_departemen', $departemen->name_departemen) }}" required>
                @error('name_departemen')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <a href="{{ route('departemens.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@stop
