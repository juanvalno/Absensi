@extends('adminlte::page')

@section('title', 'Edit Menu')

@section('content_header')
<h1>Edit Menu</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('menu.update', $menu) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="text">Menu Text</label>
                        <input type="text" class="form-control @error('text') is-invalid @enderror" id="text" name="text" value="{{ old('text', $menu->text) }}" required>
                        @error('text')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="header" {{ (old('type', $menu->type) == 'header') ? 'selected' : '' }}>Header</option>
                            <option value="menu" {{ (old('type', $menu->type) == 'menu') ? 'selected' : '' }}>Menu Item</option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group menu-item-field">
                        <label for="icon">Icon (FontAwesome)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="{{ $menu->icon ?: 'fas fa-icons' }}"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $menu->icon) }}" placeholder="Example: fas fa-user">
                        </div>
                        @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            You can find icons at <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a>
                        </small>
                    </div>

                    <div class="form-group menu-item-field">
                        <label for="route">Route</label>
                        <input type="text" class="form-control @error('route') is-invalid @enderror" id="route" name="route" value="{{ old('route', $menu->route) }}" placeholder="Example: admin.users.index">
                        @error('route')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="parent_id">Parent Menu</label>
                        <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="">No Parent</option>
                            @foreach($parentMenus as $parent)
                            <option value="{{ $parent->id }}" {{ (old('parent_id', $menu->parent_id) == $parent->id) ? 'selected' : '' }}>
                                {{ $parent->text }}
                            </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group menu-item-field">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="has_submenu" name="has_submenu" value="1" {{ old('has_submenu') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="has_submenu">This menu has submenu (parent menu)</label>
                        </div>
                    </div>

                    <div class="form-group menu-item-field">
                        <label for="permission">Permission Required</label>
                        <select class="form-control @error('permission') is-invalid @enderror" id="permission" name="permission">
                            <option value="">No Permission Required</option>
                            @foreach($permissions as $permission)
                            <option value="{{ $permission->name }}" {{ (old('permission', $menu->permission) == $permission->name) ? 'selected' : '' }}>
                                {{ $permission->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('permission')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="order">Order</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $menu->order) }}" required>
                        @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('menu.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Preview icon when input changes
        $('#icon').on('input', function() {
            let icon = $(this).val() || 'fas fa-icons';
            $(this).prev('.input-group-prepend').find('i').attr('class', icon);
        });

        // Toggle menu item fields based on type
        function toggleMenuFields() {
            if ($('#type').val() === 'header') {
                $('.menu-item-field').hide();
            } else {
                $('.menu-item-field').show();
            }
        }

        $('#type').change(toggleMenuFields);
        toggleMenuFields();
    });

    $('#has_submenu').change(function() {
        if ($(this).is(':checked')) {
            $('#route').prop('required', false);
            $('#route').closest('.form-group').find('label').text('Route (optional for parent menu)');
        } else {
            $('#route').prop('required', true);
            $('#route').closest('.form-group').find('label').text('Route *');
        }
    });

</script>
@stop
