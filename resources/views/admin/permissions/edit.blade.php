@extends('adminlte::page')

@section('title', 'Edit Permission')

@section('content_header')
<h1>Edit Permission</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="module">Module</label>
                        <input type="text" class="form-control @error('module') is-invalid @enderror" id="module" name="module" value="{{ old('module', $permission->module) }}" placeholder="Example: users, roles, articles" required>
                        <small class="form-text text-muted">
                            This will be used as prefix for permission name (example: users.create)
                        </small>
                        @error('module')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name">Permission Action</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->action) }}" placeholder="Example: create, read, update, delete" required>
                        <small class="form-text text-muted">
                            This will be the action part of the permission (example: create in users.create)
                        </small>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Permission Preview</label>
                        <div class="alert alert-info" id="permissionPreview">
                            {{ $permission->name }}
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Live preview permission name
    function updatePreview() {
        let module = $('#module').val() || 'module';
        let action = $('#name').val() || 'action';
        $('#permissionPreview').text(`${module}.${action}`);
    }

    $('#module, #name').on('input', updatePreview);

</script>
@stop
