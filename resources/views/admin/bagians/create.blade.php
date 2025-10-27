@extends('adminlte::page')

@section('title', 'Add Bagian')

@section('content_header')
<h1>Add Bagian</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Bagian Form</h3>
        <div class="card-tools">
            <a href="{{ route('bagians.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <form action="{{ route('bagians.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="id_departemen">Departemen</label>
                <select class="form-control select2 @error('id_departemen') is-invalid @enderror" id="id_departemen" name="id_departemen" required>
                    <option value="">-- Select Departemen --</option>
                    @foreach($departemens as $departemen)
                    <option value="{{ $departemen->id }}" {{ old('id_departemen') == $departemen->id ? 'selected' : '' }}>
                        {{ $departemen->name_departemen }}
                    </option>
                    @endforeach
                </select>
                @error('id_departemen')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <label for="name_bagian">Nama Bagian <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name_bagian') is-invalid @enderror" id="name_bagian" name="name_bagian" value="{{ old('name_bagian') }}" required>
                @error('name_bagian')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <a href="{{ route('bagians.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@stop

@section('js')
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function() {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    });

</script>
@stop
