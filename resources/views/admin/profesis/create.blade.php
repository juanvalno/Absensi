@extends('adminlte::page')

@section('title', 'Add Profesi')

@section('content_header')
<h1>Add Profesi</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Profesi Form</h3>
        <div class="card-tools">
            <a href="{{ route('profesis.index') }}" class="btn btn-default btn-sm">
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

        <form action="{{ route('profesis.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name_profesi">Nama Profesi <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name_profesi') is-invalid @enderror" id="name_profesi" name="name_profesi" value="{{ old('name_profesi') }}" style="text-transform: uppercase;" required>
                @error('name_profesi')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <label for="tunjangan_profesi">Tunjangan Profesi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" class="form-control rupiah @error('tunjangan_profesi') is-invalid @enderror" id="tunjangan_profesi" name="tunjangan_profesi" value="{{ old('tunjangan_profesi') }}">
                    @error('tunjangan_profesi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <small class="form-text text-muted">Format: Rp. 1.000.000</small>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<!-- Add CSS for the currency input mask -->
@stop

@section('js')
<!-- Include the inputmask library -->
<script src="{{ asset('vendor/inputmask/jquery.inputmask.min.js') }}"></script>
<script>
    $(function() {
        // Apply the currency mask to the rupiah input
        $('.rupiah').inputmask({
            alias: 'numeric'
            , groupSeparator: '.'
            , radixPoint: ','
            , autoGroup: true
            , prefix: ''
            , digits: 0
            , rightAlign: false
            , removeMaskOnSubmit: true
        });
    });

</script>
@stop
