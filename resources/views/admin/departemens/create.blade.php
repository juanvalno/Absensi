@extends('adminlte::page')

@section('title', 'Add New Departemen')

@section('content_header')
<h1>Add New Departemen</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Departemen Details</h3>
        <div class="card-tools">
            <a href="{{ route('departemens.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('departemens.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name_departemen">Nama Departemen</label>
                <input type="text" class="form-control @error('name_departemen') is-invalid @enderror" id="name_departemen" name="name_departemen" value="{{ old('name_departemen') }}" style="text-transform: uppercase;" required>
                @error('name_departemen')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <a href="{{ route('departemens.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@stop
