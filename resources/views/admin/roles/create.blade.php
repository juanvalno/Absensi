@extends('adminlte::page')

@section('title', 'Add New Role')

@section('content_header')
<h1>Add New Role</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Role Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Permissions</label>
                        <div class="row">
                            @foreach($permissions->groupBy(function($item) {
                            return explode('.', $item->name)[0];
                            }) as $group => $items)
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input group-checkbox" id="group-{{ $group }}">
                                            <label class="custom-control-label" for="group-{{ $group }}">
                                                <strong>{{ ucfirst($group) }}</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @foreach($items as $permission)
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input permission-checkbox" id="permission-{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" data-group="{{ $group }}" {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="permission-{{ $permission->id }}">
                                                {{ ucwords(str_replace(['.', '-', '_'], ' ', explode('.', $permission->name)[1])) }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('permissions')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Handle group checkbox
    $('.group-checkbox').change(function() {
        let group = $(this).attr('id').replace('group-', '');
        let isChecked = $(this).prop('checked');
        $(`input[data-group="${group}"]`).prop('checked', isChecked);
    });

    // Handle individual permissions
    $('.permission-checkbox').change(function() {
        let group = $(this).data('group');
        let groupCheckbox = $(`#group-${group}`);
        let groupPermissions = $(`input[data-group="${group}"]`);
        let checkedPermissions = groupPermissions.filter(':checked');

        groupCheckbox.prop('checked', groupPermissions.length === checkedPermissions.length);
    });

</script>
@stop
